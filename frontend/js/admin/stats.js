let chartInstance = null;

// --- Fetch des stats ---
async function fetchStats(menu = '', start = '', end = '') {
    let url = `${apiUrl}/admin/stats/menus`;
    const params = new URLSearchParams();
    if (start) params.append('start', start);
    if (end)   params.append('end', end);
    if (params.toString()) url += '?' + params.toString();

    const response = await fetch(url, {
        method: 'GET',
        headers: { 'X-AUTH-TOKEN': getToken() }
    });

    if (!response.ok) {
        console.error("Erreur HTTP :", response.status);
        return [];
    }

    let data = await response.json();

    // Filtre par menu côté front (le backend ne filtre que par période)
    if (menu) {
        data = data.filter(d => d._id === menu);
    }

    return data;
}

// --- Mise à jour des KPIs ---
function updateKpis(totalCA, totalOrders) {
    document.getElementById('total-ca').textContent     = totalCA.toFixed(2) + ' €';
    document.getElementById('total-orders').textContent = totalOrders;
}

// --- Peuplement du select menus ---
function populateMenuFilter(data) {
    const select = document.getElementById('filter-menu');
    const existing = Array.from(select.options).map(o => o.value);

    data.forEach(d => {
        if (!existing.includes(d._id)) {
            const option = document.createElement('option');
            option.value       = d._id;
            option.textContent = d._id;
            select.appendChild(option);
        }
    });
}

// --- Rendu du graphique ---
function renderChart(data) {
    const noData = document.getElementById('no-data');
    const canvas  = document.getElementById('myChart');

    if (!data.length) {
        noData.style.display = 'block';
        canvas.style.display = 'none';
        updateKpis(0, 0);
        return;
    }

    noData.style.display = 'none';
    canvas.style.display = 'block';

    // Calcul des KPIs
    const totalCA     = data.reduce((sum, d) => sum + parseFloat(d.caTotal), 0);
    const totalOrders = data.reduce((sum, d) => sum + parseInt(d.nombreCommandes), 0);
    updateKpis(totalCA, totalOrders);

    // Destruction du graphique précédent si besoin
    if (chartInstance) chartInstance.destroy();

    const ctx = document.getElementById('myChart').getContext('2d');
    chartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.map(d => d._id),
            datasets: [
                {
                    label: "Chiffre d'Affaires (€)",
                    data: data.map(d => parseFloat(d.caTotal)),
                    backgroundColor: '#3498db',
                    yAxisID: 'y'
                },
                {
                    label: "Nombre de commandes",
                    data: data.map(d => parseInt(d.nombreCommandes)),
                    backgroundColor: '#2ecc71',
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
                title: { display: true, text: 'CA et commandes par menu' }
            },
            scales: {
                y: {
                    type: 'linear',
                    position: 'left',
                    title: { display: true, text: 'CA (€)' }
                },
                y1: {
                    type: 'linear',
                    position: 'right',
                    grid: { drawOnChartArea: false },
                    title: { display: true, text: 'Commandes' },
                    ticks: { stepSize: 1 }
                }
            }
        }
    });
}

// --- Chargement initial ---
async function loadStats() {
    const data = await fetchStats();
    populateMenuFilter(data);
    renderChart(data);
}

// --- Événements filtres ---
document.getElementById('btn-filter').addEventListener('click', async () => {
    const menu  = document.getElementById('filter-menu').value;
    const start = document.getElementById('filter-start').value;
    const end   = document.getElementById('filter-end').value;
    const data  = await fetchStats(menu, start, end);
    renderChart(data);
});

document.getElementById('btn-reset').addEventListener('click', async () => {
    document.getElementById('filter-menu').value  = '';
    document.getElementById('filter-start').value = '';
    document.getElementById('filter-end').value   = '';
    const data = await fetchStats();
    populateMenuFilter(data);
    renderChart(data);
});

loadStats();