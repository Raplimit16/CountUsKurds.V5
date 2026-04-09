<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Count Us Kurds Admin</title>
    <style>
        :root {
            --primary: #ED1C24;
            --primary-dark: #c41920;
            --green: #21924F;
            --yellow: #F9DD16;
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
        
        .login-container {
            width: 100%;
            max-width: 420px;
        }
        
        .login-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 2.5rem;
            box-shadow: 0 25px 50px rgba(0,0,0,0.5);
            border: 1px solid rgba(255,255,255,0.1);
        }
        
        .logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .logo-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary), var(--green));
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .logo h1 {
            color: var(--text);
            font-size: 1.5rem;
            font-weight: 700;
        }
        
        .logo p {
            color: var(--text-muted);
            font-size: 0.9rem;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }
        
        .alert-error {
            background: rgba(237,28,36,0.1);
            border: 1px solid var(--primary);
            color: #ff6b6b;
        }
        
        .alert-success {
            background: rgba(33,146,79,0.1);
            border: 1px solid var(--green);
            color: #69db7c;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
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
            transition: all 0.3s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            background: rgba(255,255,255,0.08);
        }
        
        .form-control::placeholder {
            color: var(--text-muted);
        }
        
        .login-methods {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }
        
        .method-btn {
            flex: 1;
            padding: 0.75rem;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 8px;
            color: var(--text-muted);
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .method-btn.active {
            background: rgba(237,28,36,0.1);
            border-color: var(--primary);
            color: var(--text);
        }
        
        .method-btn:hover {
            border-color: var(--primary);
        }
        
        .btn-submit {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(237,28,36,0.4);
        }
        
        .form-footer {
            text-align: center;
            margin-top: 1.5rem;
        }
        
        .form-footer a {
            color: var(--yellow);
            text-decoration: none;
            font-size: 0.9rem;
        }
        
        .form-footer a:hover {
            text-decoration: underline;
        }
        
        #password-field, #totp-field {
            display: none;
        }
        
        #password-field.active, #totp-field.active {
            display: block;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="logo">
                <div class="logo-icon">☀️</div>
                <h1>Admin Panel</h1>
                <p>Count Us Kurds</p>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?= e($error) ?></div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?= e($success) ?></div>
            <?php endif; ?>
            
            <form method="POST" action="/admin?action=login">
                <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="login_method" id="login_method" value="password">
                
                <div class="login-methods">
                    <button type="button" class="method-btn active" onclick="setMethod('password')">
                        🔑 Password
                    </button>
                    <button type="button" class="method-btn" onclick="setMethod('totp')">
                        📱 2FA Only
                    </button>
                </div>
                
                <div class="form-group">
                    <label for="username">Username or Email</label>
                    <input type="text" id="username" name="username" class="form-control" 
                           placeholder="Enter username" required autocomplete="username">
                </div>
                
                <div class="form-group" id="password-field">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" 
                           placeholder="Enter password" autocomplete="current-password">
                </div>
                
                <div class="form-group" id="totp-field">
                    <label for="totp_code">2FA Code</label>
                    <input type="text" id="totp_code" name="totp_code" class="form-control" 
                           placeholder="6-digit code" maxlength="6" pattern="[0-9]{6}" inputmode="numeric">
                </div>
                
                <button type="submit" class="btn-submit">Sign In</button>
            </form>
            
            <div class="form-footer">
                <a href="/admin?action=reset-password">Forgot password?</a>
            </div>
        </div>
    </div>
    
    <script>
        function setMethod(method) {
            document.getElementById('login_method').value = method;
            document.querySelectorAll('.method-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            document.getElementById('password-field').classList.toggle('active', method === 'password');
            document.getElementById('totp-field').classList.toggle('active', method === 'totp');
            
            if (method === 'password') {
                document.getElementById('password').required = true;
                document.getElementById('totp_code').required = false;
            } else {
                document.getElementById('password').required = false;
                document.getElementById('totp_code').required = true;
            }
        }
        
        // Initialize
        setMethod('password');
    </script>
</body>
</html>
