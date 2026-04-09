<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Count Us Kurds</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: linear-gradient(135deg, #0f172a, #1e293b); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 16px; }
        .container { background: white; border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); padding: 28px 24px; width: 100%; max-width: 460px; }
        h1 { color: #ed1c24; font-size: 28px; margin-bottom: 8px; text-align: center; }
        p { color: #6b7280; font-size: 14px; text-align: center; margin-bottom: 24px; }
        .notice { background: #ecfdf5; color: #065f46; padding: 12px; border-radius: 8px; margin-bottom: 16px; font-size: 14px; }
        .error { background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 16px; font-size: 14px; }
        .form-group { margin-bottom: 12px; }
        label { display: block; margin-bottom: 6px; color: #374151; font-weight: 600; font-size: 14px; }
        input, select { width: 100%; padding: 12px 14px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 15px; }
        input:focus, select:focus { outline: none; border-color: #ed1c24; }
        button { width: 100%; padding: 14px; background: linear-gradient(135deg, #ed1c24, #ff684f); color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 700; cursor: pointer; }
        button:hover { opacity: 0.9; }
        .helper { margin-top: 14px; text-align: center; font-size: 13px; }
        .helper a { color: #1d4ed8; text-decoration: none; }
        .mode-hint { font-size: 12px; color: #6b7280; margin-top: 2px; }
        .is-hidden { display: none; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Count Us Kurds</h1>
        <p>Admin Panel</p>

        <?php if ($resetOk): ?>
            <div class="notice">Losenordet ar uppdaterat. Logga in med ditt nya losenord.</div>
        <?php endif; ?>

        <?php if ($errorMessage): ?>
            <div class="error"><?= htmlspecialchars((string) $errorMessage, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="_token" value="<?= htmlspecialchars((string) $csrfToken, ENT_QUOTES, 'UTF-8') ?>">

            <div class="form-group">
                <label>Anvandarnamn</label>
                <input type="text" name="username" required autofocus>
            </div>

            <div class="form-group">
                <label for="login_method">Inloggningsmetod</label>
                <select id="login_method" name="login_method" onchange="toggleMethod(this.value)">
                    <option value="password">Losenord</option>
                    <option value="totp">2FA-kod</option>
                </select>
            </div>

            <div class="form-group" id="password_wrap">
                <label>Losenord (vid losenordsinloggning)</label>
                <input type="password" name="password" autocomplete="current-password">
            </div>

            <div class="form-group is-hidden" id="totp_wrap">
                <label>2FA-kod</label>
                <input type="text" name="totp_code" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" placeholder="123456">
                <div class="mode-hint">Ange koden fran din authenticator-app.</div>
            </div>

            <button type="submit">Logga in</button>
        </form>

        <div class="helper">
            <a href="/admin.php?action=reset_password">Glomt losenord? Aterstall med 2FA-kod</a>
        </div>
    </div>

    <script>
        function toggleMethod(mode) {
            var passwordWrap = document.getElementById('password_wrap');
            var totpWrap = document.getElementById('totp_wrap');
            var passwordInput = passwordWrap ? passwordWrap.querySelector('input[name="password"]') : null;
            var totpInput = totpWrap ? totpWrap.querySelector('input[name="totp_code"]') : null;
            if (mode === 'totp') {
                if (passwordWrap) passwordWrap.classList.add('is-hidden');
                if (totpWrap) totpWrap.classList.remove('is-hidden');
                if (passwordInput) passwordInput.required = false;
                if (totpInput) totpInput.required = true;
            } else {
                if (passwordWrap) passwordWrap.classList.remove('is-hidden');
                if (totpWrap) totpWrap.classList.add('is-hidden');
                if (passwordInput) passwordInput.required = true;
                if (totpInput) totpInput.required = false;
            }
        }
        toggleMethod(document.getElementById('login_method').value);
    </script>
</body>
</html>
