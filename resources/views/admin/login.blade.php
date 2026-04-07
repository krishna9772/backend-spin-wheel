<!DOCTYPE html>
<html lang="my">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Lucky Splash Wheel</title>
    
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <style>
        /* 1. Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            min-height: 100dvh;
            font-family: 'Pyidaungsu', sans-serif;
            /* A clean dark gradient to make the login card pop */
            background: linear-gradient(135deg, #1a1a1a 0%, #333333 100%);
            padding: 20px;
        }

        /* 2. The Login Card Container */
        .login-card {
            background: #ffffff;
            width: 100%;
            max-width: 400px;
            padding: 40px 30px;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.6);
            text-align: center;
        }

        .login-card h2 {
            color: #440000;
            margin-bottom: 30px;
            font-size: clamp(24px, 5vw, 28px);
        }

        /* 3. Input Styling */
        .input-group {
            margin-bottom: 20px;
        }

        .input-group input {
            width: 100%;
            padding: 15px;
            font-size: 16px;
            border: 2px solid #ddd;
            border-radius: 8px;
            transition: border-color 0.3s ease;
            outline: none;
            background: #f9f9f9;
        }

        .input-group input:focus {
            border-color: #ff9500;
            background: #ffffff;
        }

        /* 4. Button Styling (Matching your wheel theme) */
        .login-btn {
            width: 100%;
            padding: 15px;
            font-size: 18px;
            font-weight: bold;
            color: #440000;
            background: linear-gradient(#ffea00, #ff9500);
            border: none;
            border-radius: 8px;
            cursor: pointer;
            box-shadow: 0 4px 0 #b36b00;
            transition: 0.2s;
            margin-top: 10px;
        }

        .login-btn:active {
            transform: translateY(4px);
            box-shadow: 0 0 0 #b36b00;
        }

        .login-btn:disabled {
            background: #cccccc;
            color: #666666;
            box-shadow: 0 4px 0 #999999;
            cursor: not-allowed;
            transform: none;
        }

        /* 5. Error Message UI */
        .error-msg {
            color: #e61919;
            margin-top: 15px;
            font-size: 14px;
            font-weight: bold;
            display: none; /* Hidden by default */
        }
    </style>
</head>
<body>

    <div class="login-card">
        <h2>Admin Panel</h2>

        <div class="input-group">
            <input id="email" type="email" placeholder="Email Address" required>
        </div>

        <div class="input-group">
            <input id="password" type="password" placeholder="Password" required>
        </div>

        <button id="loginBtn" class="login-btn" onclick="login()">Login</button>
        
        <div id="errorText" class="error-msg">Login failed. Please check your credentials.</div>
    </div>

    <script>
        // Global Axios setup for CSRF
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;
        axios.defaults.headers.common['Accept'] = 'application/json';

        async function login() {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const loginBtn = document.getElementById('loginBtn');
            const errorText = document.getElementById('errorText');

            // Reset UI states
            errorText.style.display = 'none';
            loginBtn.disabled = true;
            loginBtn.innerText = 'Verifying...';

            try {
                const res = await axios.post('/admin/login', {
                    email,
                    password
                });

                // Store Sanctum token if it exists
                if (res.data.admin_token || res.data.token) {
                    const tokenToStore = res.data.admin_token || res.data.token;
                    localStorage.setItem('admin_token', tokenToStore);
                }

                // Success! Redirecting...
                loginBtn.innerText = 'Success! Redirecting...';
                window.location.href = '/admin/rewards';

            } catch (err) {
                console.error("Login Error:", err.response);
                
                // Show Error UI
                errorText.style.display = 'block';
                if (err.response && err.response.data && err.response.data.message) {
                    errorText.innerText = err.response.data.message;
                } else {
                    errorText.innerText = "Login failed. Please try again.";
                }

                // Re-enable button
                loginBtn.disabled = false;
                loginBtn.innerText = 'Login';
            }
        }
    </script>

</body>
</html>