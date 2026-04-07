<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>

    <!-- ✅ ADD THIS -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body>

<h2>Admin Login</h2>

<input id="email" placeholder="Email"><br><br>
<input id="password" type="password" placeholder="Password"><br><br>

<button onclick="login()">Login</button>

<script>
// ✅ ADD THIS (very important)
axios.defaults.headers.common['X-CSRF-TOKEN'] =
    document.querySelector('meta[name="csrf-token"]').content;

async function login() {
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    try {
        const res = await axios.post('/admin/login', {
            email,
            password
        });

        console.log(res);

        // optional if using token
        if (res.data.token) {
            localStorage.setItem('admin_token', res.data.token);
        }

        // redirect after login
        window.location.href = '/admin/rewards';

    } catch (err) {
        console.log(err.response); // 🔍 debug
        alert('Login failed');
    }
}
</script>

</body>
</html>