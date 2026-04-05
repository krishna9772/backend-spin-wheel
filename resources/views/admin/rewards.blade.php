@extends('admin.layout')

@section('content')
<h2>Rewards Management</h2>

<table class="table table-bordered" id="rewardsTable">
    <thead>
        <tr>
            <th>Label</th>
            <th>Chance (%)</th>
            <th>Stock</th>
            <th>Active</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <!-- Rewards will be injected here -->
    </tbody>
</table>

<script>
const apiUrl = '/api/admin/rewards';
const token = 'YOUR_AUTH_TOKEN'; // Replace with a valid token

async function fetchRewards() {
    const res = await axios.get(apiUrl, { headers: { Authorization: 'Bearer ' + token } });
    const tbody = document.querySelector('#rewardsTable tbody');
    tbody.innerHTML = '';
    res.data.forEach(r => {
        tbody.innerHTML += '<tr>' +
            '<td>' + r.label + '</td>' +
            '<td>' + r.chance + '</td>' +
            '<td>' + r.stock + '</td>' +
            '<td>' + (r.is_active ? 'Yes' : 'No') + '</td>' +
            '<td>' +
                '<button onclick="refillStock(' + r.id + ')">Refill</button>' +
            '</td>' +
        '</tr>';
    });
}

async function refillStock(id) {
    const amount = prompt('Enter stock refill amount:');
    if (!amount) return;
    await axios.post(apiUrl + '/' + id + '/refill', { stock: parseInt(amount) }, { headers: { Authorization: 'Bearer ' + token } });
    fetchRewards();
}

fetchRewards();
</script>
@endsection