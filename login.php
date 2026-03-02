<?php
session_start();
if (isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sari-Sari Inventory</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .login-container { max-width: 320px; margin: 80px auto; padding: 20px; background:#fff; border:1px solid #ccc; border-radius:4px; }
        .login-container h2 { text-align:center; margin-bottom:20px; }
        .login-container label { display:block; margin:8px 0 4px; }
        .login-container input { width:100%; padding:6px 8px; }
        .login-container button { width:100%; padding:8px; margin-top:12px; }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <form id="login-form">
            <label>Username:</label>
            <input type="text" id="username" required>
            <label>Password:</label>
            <input type="password" id="password" required>
            <button type="submit">Login</button>
        </form>
        <div id="login-error" style="color:red; margin-top:10px;"></div>
    </div>

    <script>
        document.getElementById('login-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const user = document.getElementById('username').value.trim();
            const pass = document.getElementById('password').value;
            if (!user || !pass) return;
            const res = await fetch('api/auth.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ username: user, password: pass })
            });
            if (res.ok) {
                window.location = 'index.php';
            } else {
                const data = await res.json();
                document.getElementById('login-error').textContent = data.error || 'Login failed';
            }
        });
    </script>
</body>
</html>