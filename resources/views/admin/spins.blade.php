@extends('admin.layout')

@section('content')
<h2>Spin Results</h2>

<table class="table table-bordered" id="spinsTable">
    <thead>
        <tr>
            <th>User</th>
            <th>Reward</th>
            <th>Result</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        <!-- Spins injected here -->
    </tbody>
</table>

<script>
const spinsApi = '/api/admin/spins';
const token = 'YOUR_AUTH_TOKEN'; // Replace with a valid token

async function fetchSpins() {
    const res = await axios.get(spinsApi, { headers: { Authorization: 'Bearer ' + token } });
    const tbody = document.querySelector('#spinsTable tbody');
    tbody.innerHTML = '';
    res.data.data.forEach(s => {
        tbody.innerHTML += '<tr>' +
            '<td>' + s.user?.name + '</td>' +
            '<td>' + s.reward?.label + '</td>' +
            '<td>' + (s.result ? 'Won' : 'Lost') + '</td>' +
            '<td>' + new Date(s.created_at).toLocaleString() + '</td>' +
        '</tr>';
    });
}

fetchSpins();
</script>
@endsection