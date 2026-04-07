@extends('admin.layout')

@section('content')
<h2>Rewards Management</h2>

<button class="btn-add" onclick="createReward()">+ Add Reward</button>

<table id="rewardsTable">
    <thead>
        <tr>
            <th>Label</th>
            <th>Chance (%)</th>
            <th>Stock</th>
            <th>Times Won</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<hr style="margin: 40px 0; border: 1px solid #ddd;">

<h2>Recent Spins Log</h2>
<table id="spinsTable">
    <thead>
        <tr>
            <th>Reward Won</th>
            <th>IP Address</th>
            <th>Time</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<script>
const token = localStorage.getItem('admin_token');
const apiUrl = '/api/admin/rewards';
const spinsApiUrl = '/api/spins'; // NEW API URL for spins

// Axios global config
axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
axios.defaults.headers.common['Accept'] = 'application/json';
axios.defaults.headers.common['Content-Type'] = 'application/json';    

// --- REWARDS LOGIC ---
let rewardsTable;

async function fetchRewards() {
    const res = await axios.get(apiUrl);

    if (rewardsTable) {
        rewardsTable.destroy();
    }

    const tbody = document.querySelector('#rewardsTable tbody');
    tbody.innerHTML = '';

    res.data.forEach(r => {
        tbody.innerHTML += `
        <tr>
            <td>${r.label}</td>
            <td>${r.chance}</td>
            <td>${r.stock}</td>
            <td>${r.times_won}</td>
            <td>
                <button class="btn-edit" onclick="editReward(${r.id})">Edit</button>
                <button class="btn-delete" onclick="deleteReward(${r.id})">Delete</button>
                <button onclick="refillStock(${r.id})">Refill</button>
            </td>
        </tr>`;
    });

    rewardsTable = $('#rewardsTable').DataTable({
        pageLength: 5,
        responsive: true,
        order: [[1, 'desc']],
    });
}

function validateReward({ label, chance, stock }) {
    if (!label || label.trim() === '') return alert('Label is required.'), false;
    chance = parseFloat(chance);
    if (isNaN(chance) || chance < 0 || chance > 100) return alert('Chance must be 0-100.'), false;
    stock = parseInt(stock);
    if (isNaN(stock) || stock < 0) return alert('Stock must be positive.'), false;
    return true;
}

async function createReward() {
    const label = prompt('Label:');
    const chance = prompt('Chance %:');
    const stock = prompt('Stock:');
    if (!validateReward({ label, chance, stock })) return;
    await axios.post(apiUrl, { label, chance: parseFloat(chance), stock: parseInt(stock), is_active: true });
    fetchRewards();
}

async function editReward(id) {
    const label = prompt('New label:');
    const chance = prompt('New chance:');
    if (!validateReward({ label, chance, stock: 0 })) return; 
    await axios.put(`${apiUrl}/${id}`, { label, chance: parseFloat(chance) });
    fetchRewards();
}

async function refillStock(id) {
    const amount = prompt('Enter stock amount:');
    if (!amount) return;
    const stock = parseInt(amount);
    if (isNaN(stock) || stock < 0) return alert('Stock must be a non-negative integer.');
    await axios.post(`${apiUrl}/${id}/refill`, { stock });
    fetchRewards();
}

async function deleteReward(id) {
    if (!confirm('Delete this reward?')) return;
    await axios.delete(`${apiUrl}/${id}`);
    fetchRewards();
}

let spinsTable;

async function fetchSpins() {
    try {
        const res = await axios.get(spinsApiUrl);

        if (spinsTable) {
            spinsTable.destroy();
        }

        const tbody = document.querySelector('#spinsTable tbody');
        tbody.innerHTML = '';

        res.data.forEach(s => {
            const rewardName = s.reward ? s.reward.label : `Reward ID: ${s.reward_id}`;
            const date = new Date(s.created_at).toLocaleString();

            tbody.innerHTML += `
            <tr>
                <td><strong>${rewardName}</strong></td>
                <td>${s.ip_address}</td>
                <td>${date}</td>
            </tr>`;
        });

        spinsTable = $('#spinsTable').DataTable({
            pageLength: 10,
            order: [[2, 'desc']],
        });

    } catch (error) {
        console.error("Failed to load spins:", error);
    }
}

// Load both tables on startup
fetchRewards();
fetchSpins();
</script>
@endsection