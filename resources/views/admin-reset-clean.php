<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Count Us Kurds</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f3f4f6; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .container { background: white; border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,0.12); padding: 32px; width: 100%; max-width: 440px; }
        h1 { font-size: 24px; color: #111827; margin-bottom: 8px; text-align: center; }
        p { color: #6b7280; margin-bottom: 18px; text-align: center; font-size: 14px; }
        .error { background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 14px; font-size: 14px; }
        .form-group { margin-bottom: 14px; }
        label { display: block; margin-bottom: 6px; font-size: 14px; font-weight: 600; color: #374151; }
        input { width: 100%; padding: 12px 14px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 15px; }
        button { width: 100%; padding: 14px; background: linear-gradient(135deg, #ed1c24, #ff684f); color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 700; cursor: pointer; }
        .helper { margin-top: 14px; text-align: center; font-size: 13px; }
        .helper a { color: #1d4ed8; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Aterstall Admin-losenord</h1>
        <p>Verifiera med anvandarnamn och 2FA-kod.</p>

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
                <label>2FA-kod</label>
                <input type="text" name="totp_code" required inputmode="numeric" pattern="[0-9]{6}" maxlength="6">
            </div>

            <div class="form-group">
                <label>Nytt losenord</label>
                <input type="password" name="new_password" required minlength="12">
            </div>

            <div class="form-group">
                <label>Bekrafta nytt losenord</label>
                <input type="password" name="confirm_password" required minlength="12">
            </div>

            <button type="submit">Uppdatera losenord</button>
        </form>

        <div class="helper">
            <a href="/admin.php?action=login">Tillbaka till inloggning</a>
        </div>
    </div>
</body>
</html>
