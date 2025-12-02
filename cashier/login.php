<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Kasir - SiMakmur POS</title>
    <link rel="stylesheet" href="../assets/css/variables.css">
    <link rel="stylesheet" href="../assets/css/reset.css">
    <link rel="stylesheet" href="../assets/css/typography.css">
    <style>
        body {
            background: var(--c-bg-body);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .login-card {
            background: #fff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: var(--shadow-float);
            width: 100%;
            max-width: 400px;
            text-align: center;
            border: 1px solid #dcd6c5;
        }
        .logo {
            width: 80px;
            height: 80px;
            background: var(--c-primary);
            color: var(--c-accent-gold);
            border-radius: 20px;
            display: grid;
            place-items: center;
            font-family: var(--font-serif);
            font-weight: 900;
            font-size: 32px;
            margin: 0 auto 20px;
        }
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--c-text-primary);
        }
        .form-input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
        }
        .btn-login {
            width: 100%;
            padding: 14px;
            background: var(--c-primary);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.2s;
        }
        .btn-login:hover {
            background: #8a242c;
        }
        .error-msg {
            color: red;
            margin-bottom: 15px;
            font-size: 14px;
            display: none;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="logo">SM</div>
        <h2 class="font-serif text-maroon" style="margin-bottom: 10px;">Login Kasir</h2>
        <p style="color: #666; margin-bottom: 30px;">Masuk untuk memulai shift</p>

        <div class="error-msg" id="errorMsg">Username atau password salah!</div>

        <form id="loginForm" onsubmit="handleLogin(event)">
            <div class="form-group">
                <label class="form-label">Username</label>
                <input type="text" id="username" class="form-input" placeholder="Contoh: kasir1" required>
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" id="password" class="form-input" placeholder="******" required>
            </div>
            <button type="submit" class="btn-login" id="btnLogin">MASUK</button>
        </form>
    </div>

    <script src="../assets/js/api.js"></script>
    <script>
        async function handleLogin(e) {
            e.preventDefault();
            
            const btn = document.getElementById('btnLogin');
            const errorMsg = document.getElementById('errorMsg');
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;

            btn.disabled = true;
            btn.innerText = "Memproses...";
            errorMsg.style.display = 'none';

            try {
                const response = await API.post('/auth/login.php', {
                    username,
                    password
                });

                if (response && response.status === 'success') {
                    window.location.href = 'index.php';
                } else {
                    errorMsg.innerText = response.message || "Login gagal";
                    errorMsg.style.display = 'block';
                }
            } catch (err) {
                console.error(err);
                errorMsg.innerText = "Terjadi kesalahan koneksi";
                errorMsg.style.display = 'block';
            } finally {
                btn.disabled = false;
                btn.innerText = "MASUK";
            }
        }
    </script>
</body>
</html>
