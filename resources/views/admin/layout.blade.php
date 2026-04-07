<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <link rel="shortcut icon" href="{{asset("images/favicon.ico")}}" type="image/x-icon">

    <style>
        body { font-family: Arial; background:#f5f6fa; padding:20px; }
        .card { background:#fff; padding:20px; border-radius:10px; margin-bottom:20px; }
        table { width:100%; border-collapse: collapse; }
        th, td { padding:10px; border-bottom:1px solid #ddd; }
        button { padding:6px 10px; margin-right:5px; cursor:pointer; }
        .btn-add { background:#2ecc71; color:white; }
        .btn-edit { background:#3498db; color:white; }
        .btn-delete { background:#e74c3c; color:white; }
    </style>
</head>
<body>

<div class="card">
    <div style="background:#2c3e50; padding:10px; margin-bottom:20px; border-radius:8px; display:flex; justify-content:space-between; align-items:center;">

        <div>
            <a href="/admin/rewards" style="color:white; margin-right:15px;">🎁 Rewards</a>
            {{-- <a href="/admin/spins" style="color:white;">🎡 Spins</a> --}}
        </div>

        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button style="background:#e74c3c; color:white; border:none; padding:6px 12px; border-radius:5px;">
                Logout
            </button>
        </form>

    </div>
    @yield('content')
</div>

</body>
</html>