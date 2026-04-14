let selectedMenu = null;

// --- Affichage erreur / succès ---
function showError(msg) {
    const el = document.getElementById('error-msg');
    el.textContent   = msg;
    el.style.display = 'block';
    document.getElementById('success-msg').style.display = 'none';
}

function showSuccess(msg) {
    const el = document.getElementById('success-msg');
    el.textContent   = msg;
    el.style.display = 'block';
    document.getElementById('error-msg').style.display   = 'none';
    document.getElementById('order-form').style.display  = 'none';
}

// --- Mise à jour du récapitulatif prix ---
function updateTotal() {
    if (!selectedMenu) return;
    const persons  = parseInt(document.getElementById('order-persons').value) || 1;
    const subtotal = parseFloat(selectedMenu.price_menu) * persons;
    const total    = subtotal + 5.00;

    document.getElementById('subtotal').textContent       = subtotal.toFixed(2) + ' €';
    document.getElementById('total-estimate').textContent = total.toFixed(2) + ' €';
}

// --- Chargement des menus dans le select ---
async function loadMenus() {
    const response = await fetch(`${apiUrl}/menus`, {
        headers: { 'X-AUTH-TOKEN': getToken() }
    });

    if (!response.ok) {
        showError('Impossible de charger les menus.');
        return;
    }

    const menus  = await response.json();
    const select = document.getElementById('order-menu');

    menus.forEach(menu => {
        if (menu.remaining_quantity > 0) {
            const option = document.createElement('option');
            option.value            = menu.id;
            option.textContent      = `${menu.title_menu} — ${parseFloat(menu.price_menu).toFixed(2)} €`;
            option.dataset.menu     = JSON.stringify(menu);
            select.appendChild(option);
        }
    });
}

// --- Événement changement de menu ---
document.getElementById('order-menu').addEventListener('change', function () {
    const selectedOption = this.options[this.selectedIndex];

    if (!this.value) {
        selectedMenu = null;
        document.getElementById('menu-summary').style.display = 'none';
        document.getElementById('subtotal').textContent        = '—';
        document.getElementById('total-estimate').textContent  = '—';
        document.getElementById('order-persons').removeAttribute('max');
        return;
    }

    selectedMenu = JSON.parse(selectedOption.dataset.menu);

    document.getElementById('summary-price').textContent  = parseFloat(selectedMenu.price_menu).toFixed(2);
    document.getElementById('summary-stock').textContent  = selectedMenu.remaining_quantity;
    document.getElementById('menu-summary').style.display = 'block';

    // Limite le nombre de personnes au stock disponible
    document.getElementById('order-persons').max = selectedMenu.remaining_quantity;

    updateTotal();
});

// --- Recalcul à chaque changement du nombre de personnes ---
document.getElementById('order-persons').addEventListener('input', updateTotal);

// --- Soumission ---
document.getElementById('btn-submit').addEventListener('click', async () => {

    const title   = document.getElementById('order-title').value.trim();
    const date    = document.getElementById('order-date').value;
    const persons = parseInt(document.getElementById('order-persons').value);

    if (!title)                  return showError('Le titre est requis.');
    if (!date)                   return showError('La date de livraison est requise.');
    if (!persons || persons < 1) return showError('Le nombre de personnes doit être supérieur à 0.');
    if (!selectedMenu)           return showError('Veuillez choisir un menu.');
    if (persons > selectedMenu.remaining_quantity) {
        return showError(`Stock insuffisant. Maximum : ${selectedMenu.remaining_quantity} personnes.`);
    }
    if (!isConnected()) return showError('Vous devez être connecté pour commander.');

    document.getElementById('btn-submit').disabled = true;

    try {
        // 1. Récupération du profil connecté
        const meResponse = await fetch(`${apiUrl}/me`, {
            headers: { 'X-AUTH-TOKEN': getToken() }
        });

        if (!meResponse.ok) return showError('Impossible de récupérer votre profil.');
        const me = await meResponse.json();

        // 2. Création de la commande
        const orderResponse = await fetch(`${apiUrl}/orders`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-AUTH-TOKEN': getToken()
            },
            body: JSON.stringify({
                title:             title,
                delivery_datetime: new Date(date).toISOString(),
                number_of_persons: persons,
                user:              me.id
            })
        });

        if (!orderResponse.ok) {
            const err = await orderResponse.json();
            return showError(err.message || 'Erreur lors de la création de la commande.');
        }

        const order = await orderResponse.json();

        // 3. Ajout de l'item — quantity = number_of_persons
        const itemResponse = await fetch(`${apiUrl}/orders/${order.id}/items`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-AUTH-TOKEN': getToken()
            },
            body: JSON.stringify({
                menu:     selectedMenu.id,
                quantity: persons  // ← nombre de personnes = quantité commandée
            })
        });

        if (!itemResponse.ok) {
            const err = await itemResponse.json();
            return showError(err.message || 'Erreur lors de l\'ajout du menu.');
        }

        // 4. Succès
        const total = (parseFloat(selectedMenu.price_menu) * persons + 5).toFixed(2);
        showSuccess(`Commande #${order.id} créée ! Total : ${total} €`);

    } catch (e) {
        console.error(e);
        showError('Une erreur inattendue est survenue.');
    } finally {
        document.getElementById('btn-submit').disabled = false;
    }
});

// --- Init ---
loadMenus();