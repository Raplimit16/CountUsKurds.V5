<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Count Us Kurds Admin</title>
    <style>
        :root {
            --primary: #ED1C24;
            --bg: #0f0f1a;
            --card-bg: #1a1a2e;
            --text: #ffffff;
            --text-muted: rgba(255,255,255,0.6);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .container { width: 100%; max-width: 420px; }
        .card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 2.5rem;
            box-shadow: 0 25px 50px rgba(0,0,0,0.5);
            border: 1px solid rgba(255,255,255,0.1);
        }
        .logo { text-align: center; margin-bottom: 2rem; }
        .logo h1 { color: var(--text); font-size: 1.5rem; margin-top: 1rem; }
        .logo p { color: var(--text-muted); }
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }
        .alert-error { background: rgba(237,28,36,0.1); border: 1px solid var(--primary); color: #ff6b6b; }
        .alert-success { background: rgba(33,146,79,0.1); border: 1px solid #21924F; color: #69db7c; }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label {
            display: block;
            color: var(--text);
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        .form-control {
            width: 100%;
            padding: 0.875rem 1rem;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 8px;
            color: var(--text);
            font-size: 1rem;
        }
        .form-control:focus { outline: none; border-color: var(--primary); }
        .btn-submit {
            width: 100%;
            padding: 1rem;
            background: var(--primary);
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
        }
        .btn-submit:hover { background: #c41920; }
        .form-footer { text-align: center; margin-top: 1.5rem; }
        .form-footer a { color: #F9DD16; text-decoration: none; font-size: 0.9rem; }
        .info-box {
            background: rgba(249,221,22,0.1);
            border: 1px solid rgba(249,221,22,0.3);
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            font-size: 0.85rem;
            color: #F9DD16;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="logo">
                <h1>Reset Password</h1>
                <p>Use your 2FA code to reset</p>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?= e($error) ?></div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?= e($success) ?></div>
            <?php endif; ?>
            
            <div class="info-box">
                Enter your username and current 2FA code from your authenticator app to reset your password.
            </div>
            
            <form method="POST" action="/admin?action=reset-password">
                <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
                
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="totp_code">2FA Code</label>
                    <input type="text" id="totp_code" name="totp_code" class="form-control" 
                           maxlength="6" pattern="[0-9]{6}" inputmode="numeric" required>
                </div>
                
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" class="form-control" 
                           minlength="8" required>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" 
                           minlength="8" required>
                </div>
                
                <button type="submit" class="btn-submit">Reset Password</button>
            </form>
            
            <div class="form-footer">
                <a href="/admin?action=login">← Back to Login</a>
            </div>
        </div>
    </div>
</body>
</html>
