// ============================
// État global
// ============================

// Variable qui stocke le menu actuellement sélectionné (null = aucun)
let selectedMenu = null;

// ============================
// Helper DOM (raccourci)
// ============================

// Fonction raccourcie : $('id') équivaut à document.getElementById('id')
const $ = (id) => document.getElementById(id);

// Affiche un message d'erreur à l'écran
function showError(msg) {
    // Insère le texte du message dans l'élément d'erreur
    $('error-msg').textContent   = msg;
    // Rend l'élément d'erreur visible
    $('error-msg').style.display = 'block';
    // Cache le message de succès s'il était affiché
    $('success-msg').style.display = 'none';
}

// Affiche un message de succès à l'écran
function showSuccess(msg) {
    // Insère le texte du message dans l'élément de succès
    $('success-msg').textContent   = msg;
    // Rend l'élément de succès visible
    $('success-msg').style.display = 'block';
    // Cache le message d'erreur s'il était affiché
    $('error-msg').style.display   = 'none';
    // Cache le formulaire de commande (commande terminée)
    $('order-form').style.display  = 'none';
}

// ============================
// Helper API (évite de répéter fetch + token partout)
// ============================

// Fonction générique pour appeler l'API avec le token d'authentification automatiquement
async function apiFetch(path, options = {}) {
    // Envoie la requête fetch vers l'URL complète (apiUrl + chemin)
    return fetch(`${apiUrl}${path}`, {
        // Reprend toutes les options passées (method, body, etc.)
        ...options,
        headers: {
            // Ajoute toujours le token d'authentification
            'X-AUTH-TOKEN': getToken(),
            // Ajoute Content-Type: JSON uniquement si une requête a un corps (body)
            ...(options.body ? { 'Content-Type': 'application/json' } : {}),
            // Permet de surcharger/ajouter d'autres en-têtes si besoin
            ...options.headers
        }
    });
}

// ============================
// Prix
// ============================

// Recalcule et affiche le sous-total et le total estimé
function updateTotal() {
    // Ne fait rien si aucun menu n'est sélectionné
    if (!selectedMenu) return;

    // Récupère le nombre de personnes saisi (1 par défaut si vide/invalide)
    const persons  = parseInt($('order-persons').value) || 1;
    // Calcule le sous-total : prix du menu × nombre de personnes
    const subtotal = parseFloat(selectedMenu.price_menu) * persons;
    // Calcule le total : sous-total + frais fixes de 5€
    const total    = subtotal + 5.00;

    // Affiche le sous-total formaté avec 2 décimales et le symbole €
    $('subtotal').textContent       = `${subtotal.toFixed(2)} €`;
    // Affiche le total formaté avec 2 décimales et le symbole €
    $('total-estimate').textContent = `${total.toFixed(2)} €`;
}

// ============================
// Menus
// ============================

// Fonction asynchrone qui charge la liste des menus depuis l'API
async function loadMenus() {
    // Appelle l'API sur le endpoint /menus
    const response = await apiFetch('/menus');

    // Si la requête a échoué, affiche une erreur et arrête la fonction
    if (!response.ok) {
        showError('Impossible de charger les menus.');
        return;
    }

    // Convertit la réponse en tableau JSON de menus
    const menus  = await response.json();
    // Récupère l'élément <select> où ajouter les options
    const select = $('order-menu');

    // Ne garde que les menus dont il reste du stock (quantité > 0)
    menus
        .filter(menu => menu.remaining_quantity > 0)
        // Pour chaque menu restant, crée une option dans le select
        .forEach(menu => {
            // Crée un nouvel élément <option>
            const option        = document.createElement('option');
            // La valeur de l'option est l'ID du menu
            option.value         = menu.id;
            // Le texte affiché : titre du menu + prix formaté
            option.textContent   = `${menu.title_menu} — ${parseFloat(menu.price_menu).toFixed(2)} €`;
            // Stocke toutes les données du menu (en JSON) dans l'attribut dataset
            option.dataset.menu  = JSON.stringify(menu);
            // Ajoute l'option créée dans la liste déroulante
            select.appendChild(option);
        });
}

// Fonction appelée quand l'utilisateur change de menu dans le select
function handleMenuChange() {
    // Récupère l'élément select
    const select         = $('order-menu');
    // Récupère l'option actuellement sélectionnée
    const selectedOption = select.options[select.selectedIndex];

    // Si aucune valeur n'est sélectionnée (option vide)
    if (!select.value) {
        // Réinitialise le menu sélectionné à null
        selectedMenu = null;
        // Cache le récapitulatif du menu
        $('menu-summary').style.display = 'none';
        // Réinitialise l'affichage du sous-total à un tiret
        $('subtotal').textContent       = '—';
        // Réinitialise l'affichage du total à un tiret
        $('total-estimate').textContent = '—';
        // Supprime la limite maximale de personnes
        $('order-persons').removeAttribute('max');
        // Arrête la fonction ici
        return;
    }

    // Récupère et transforme en objet JS les données du menu stockées en JSON
    selectedMenu = JSON.parse(selectedOption.dataset.menu);

    // Affiche le prix du menu dans le récapitulatif
    $('summary-price').textContent  = parseFloat(selectedMenu.price_menu).toFixed(2);
    // Affiche la quantité restante en stock dans le récapitulatif
    $('summary-stock').textContent  = selectedMenu.remaining_quantity;
    // Rend visible le bloc de récapitulatif du menu
    $('menu-summary').style.display = 'block';
    // Limite le champ "nombre de personnes" au stock disponible
    $('order-persons').max          = selectedMenu.remaining_quantity;

    // Recalcule le total avec le nouveau menu sélectionné
    updateTotal();
}

// ============================
// Validation du formulaire
// ============================

// Vérifie les données du formulaire et retourne un message d'erreur (ou null si tout est valide)
function validateOrder({ title, date, persons }) {
    // Vérifie que le titre n'est pas vide
    if (!title)                  return 'Le titre est requis.';
    // Vérifie que la date est renseignée
    if (!date)                   return 'La date de livraison est requise.';
    // Vérifie que le nombre de personnes est un nombre valide supérieur à 0
    if (!persons || persons < 1) return 'Le nombre de personnes doit être supérieur à 0.';
    // Vérifie qu'un menu a bien été choisi
    if (!selectedMenu)            return 'Veuillez choisir un menu.';
    // Vérifie que la quantité demandée ne dépasse pas le stock disponible
    if (persons > selectedMenu.remaining_quantity) {
        return `Stock insuffisant. Maximum : ${selectedMenu.remaining_quantity} personnes.`;
    }
    // Vérifie que l'utilisateur est bien connecté
    if (!isConnected()) return 'Vous devez être connecté pour commander.';
    // Si toutes les vérifications passent, aucune erreur : retourne null
    return null;
}

// ============================
// Appels liés à la commande
// ============================

// Crée une nouvelle commande via l'API et retourne la commande créée
async function createOrder({ title, date, persons, userId }) {
    // Envoie une requête POST pour créer la commande
    const response = await apiFetch('/orders', {
        // Méthode HTTP POST (création)
        method: 'POST',
        // Corps de la requête converti en JSON
        body: JSON.stringify({
            // Titre de la commande
            title,
            // Date de livraison convertie au format ISO
            delivery_datetime: new Date(date).toISOString(),
            // Nombre de personnes
            number_of_persons: persons,
            // ID de l'utilisateur qui passe la commande
            user: userId
        })
    });

    // Si la création a échoué, lève une erreur avec le message de l'API
    if (!response.ok) {
        const err = await response.json();
        throw new Error(err.message || 'Erreur lors de la création de la commande.');
    }

    // Retourne la commande créée (avec son ID) sous forme d'objet JSON
    return response.json();
}

// Ajoute un menu (article) à une commande existante
async function addOrderItem(orderId, menuId, quantity) {
    // Envoie une requête POST vers l'endpoint des items de la commande
    const response = await apiFetch(`/orders/${orderId}/items`, {
        // Méthode HTTP POST (ajout)
        method: 'POST',
        // Corps de la requête : ID du menu et quantité commandée
        body: JSON.stringify({ menu: menuId, quantity })
    });

    // Si l'ajout a échoué, lève une erreur avec le message de l'API
    if (!response.ok) {
        const err = await response.json();
        throw new Error(err.message || "Erreur lors de l'ajout du menu.");
    }
}

// ============================
// Soumission complète
// ============================

// Fonction principale exécutée lors du clic sur le bouton de soumission
async function submitOrder() {
    // Récupère et nettoie le titre saisi (supprime les espaces inutiles)
    const title   = $('order-title').value.trim();
    // Récupère la date de livraison saisie
    const date    = $('order-date').value;
    // Récupère le nombre de personnes et le convertit en entier
    const persons = parseInt($('order-persons').value);

    // Valide les données du formulaire
    const error = validateOrder({ title, date, persons });
    // Si une erreur est retournée, l'affiche et arrête la fonction
    if (error) return showError(error);

    // Désactive le bouton pour empêcher un double clic pendant l'envoi
    $('btn-submit').disabled = true;

    try {
        // Récupère le profil de l'utilisateur connecté
        const meResponse = await apiFetch('/me');
        // Si la requête échoue, affiche une erreur et arrête
        if (!meResponse.ok) return showError('Impossible de récupérer votre profil.');
        // Convertit la réponse en objet utilisateur
        const me = await meResponse.json();

        // Crée la commande avec les informations du formulaire et l'ID utilisateur
        const order = await createOrder({ title, date, persons, userId: me.id });
        // Ajoute le menu sélectionné à la commande créée
        await addOrderItem(order.id, selectedMenu.id, persons);

        // Calcule le montant total final (prix menu × personnes + frais de 5€)
        const total = (parseFloat(selectedMenu.price_menu) * persons + 5).toFixed(2);
        // Affiche le message de succès avec le numéro de commande et le total
        showSuccess(`Commande #${order.id} créée ! Total : ${total} €`);

    } catch (e) {
        // Affiche l'erreur dans la console pour le débogage
        console.error(e);
        // Affiche un message d'erreur à l'utilisateur (celui de l'erreur ou un message générique)
        showError(e.message || 'Une erreur inattendue est survenue.');
    } finally {
        // Réactive le bouton, que la commande ait réussi ou échoué
        $('btn-submit').disabled = false;
    }
}

// ============================
// Initialisation
// ============================

// Fonction qui met en place tous les écouteurs d'événements et lance le chargement initial
function init() {
    // Quand le menu sélectionné change, appelle handleMenuChange
    $('order-menu').addEventListener('change', handleMenuChange);
    // Quand le nombre de personnes change, recalcule le total
    $('order-persons').addEventListener('input', updateTotal);
    // Quand on clique sur le bouton de soumission, lance submitOrder
    $('btn-submit').addEventListener('click', submitOrder);
    // Charge la liste des menus disponibles au démarrage
    loadMenus();
}

// Lance l'initialisation dès le chargement du script
init();