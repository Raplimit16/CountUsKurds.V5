<!DOCTYPE html>
<html lang="<?= e($locale) ?>" dir="<?= e($dir) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> - Count Us Kurds</title>
    <style>
        :root {
            --kurd-red: #ED1C24;
            --kurd-green: #21924F;
            --kurd-yellow: #F9DD16;
            --kurd-dark: #1a1a2e;
            --kurd-darker: #0f0f1a;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--kurd-darker);
            color: #fff;
            min-height: 100vh;
            line-height: 1.6;
        }
        .header {
            background: rgba(26, 26, 46, 0.95);
            padding: 1rem 2rem;
            border-bottom: 2px solid var(--kurd-red);
        }
        .header-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #fff;
            text-decoration: none;
            font-weight: 700;
        }
        .logo-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--kurd-red), var(--kurd-green));
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .main-content {
            max-width: 700px;
            margin: 3rem auto;
            padding: 0 1.5rem;
        }
        .page-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .page-header h1 {
            font-size: 2rem;
            color: var(--kurd-yellow);
            margin-bottom: 0.5rem;
        }
        .page-header p { color: rgba(255,255,255,0.7); }
        .form-card {
            background: var(--kurd-dark);
            border-radius: 16px;
            padding: 2rem;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        .alert-error {
            background: rgba(237,28,36,0.1);
            border: 1px solid var(--kurd-red);
            color: #ff6b6b;
        }
        .type-selector {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .type-btn {
            flex: 1;
            padding: 1rem;
            background: rgba(255,255,255,0.05);
            border: 2px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            color: rgba(255,255,255,0.7);
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
        }
        .type-btn:hover { border-color: var(--kurd-yellow); }
        .type-btn.active {
            background: rgba(249,221,22,0.1);
            border-color: var(--kurd-yellow);
            color: #fff;
        }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        .form-group label .required { color: var(--kurd-red); }
        .form-control {
            width: 100%;
            padding: 0.875rem 1rem;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 8px;
            color: #fff;
            font-size: 1rem;
        }
        .form-control:focus {
            outline: none;
            border-color: var(--kurd-yellow);
        }
        .form-control option { background: var(--kurd-dark); }
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        .checkbox-group {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
        }
        .checkbox-group input {
            width: 20px;
            height: 20px;
            margin-top: 2px;
        }
        .checkbox-group label {
            font-size: 0.9rem;
            color: rgba(255,255,255,0.8);
        }
        .btn-submit {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, var(--kurd-red), #c41920);
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 1rem;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(237,28,36,0.4);
        }
        .error-text { color: #ff6b6b; font-size: 0.85rem; margin-top: 0.25rem; }
        .individual-fields, .organization-fields { display: none; }
        .individual-fields.active, .organization-fields.active { display: block; }
        .footer {
            text-align: center;
            padding: 2rem;
            color: rgba(255,255,255,0.5);
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-inner">
            <a href="/" class="logo">
                <div class="logo-icon">☀️</div>
                Count Us Kurds
            </a>
        </div>
    </header>
    
    <main class="main-content">
        <div class="page-header">
            <h1><?= __('register.title', $locale) ?></h1>
            <p><?= __('register.subtitle', $locale) ?></p>
        </div>
        
        <div class="form-card">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <?php foreach ($errors as $error): ?>
                        <div><?= e($error) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="/register">
                <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="application_type" id="application_type" value="individual">
                
                <div class="type-selector">
                    <button type="button" class="type-btn active" onclick="setType('individual')">
                        👤 <?= __('register.type.individual', $locale) ?>
                    </button>
                    <button type="button" class="type-btn" onclick="setType('organization')">
                        🏢 <?= __('register.type.organization', $locale) ?>
                    </button>
                </div>
                
                <!-- Common Fields -->
                <div class="form-row">
                    <div class="form-group">
                        <label><?= __('field.name', $locale) ?> <span class="required">*</span></label>
                        <input type="text" name="full_name" class="form-control" value="<?= e($old['full_name'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label><?= __('field.email', $locale) ?> <span class="required">*</span></label>
                        <input type="email" name="email" class="form-control" value="<?= e($old['email'] ?? '') ?>" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label><?= __('field.phone', $locale) ?></label>
                        <input type="tel" name="phone" class="form-control" value="<?= e($old['phone'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label><?= __('field.country', $locale) ?> <span class="required">*</span></label>
                        <input type="text" name="country" class="form-control" value="<?= e($old['country'] ?? '') ?>" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label><?= __('field.city', $locale) ?></label>
                        <input type="text" name="city" class="form-control" value="<?= e($old['city'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label><?= __('field.region', $locale) ?></label>
                        <select name="region" class="form-control">
                            <option value="">-- Select --</option>
                            <?php foreach ($regions as $key => $name): ?>
                                <option value="<?= e($key) ?>" <?= ($old['region'] ?? '') === $key ? 'selected' : '' ?>><?= e($name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <!-- Individual Fields -->
                <div class="individual-fields active">
                    <div class="form-row">
                        <div class="form-group">
                            <label><?= __('field.birth_year', $locale) ?></label>
                            <input type="number" name="birth_year" class="form-control" min="1900" max="2025" value="<?= e($old['birth_year'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label><?= __('field.household', $locale) ?></label>
                            <input type="number" name="household_size" class="form-control" min="1" max="50" value="<?= e($old['household_size'] ?? '1') ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label><?= __('field.gender', $locale) ?></label>
                            <select name="gender" class="form-control">
                                <option value="">-- Select --</option>
                                <option value="male"><?= __('gender.male', $locale) ?></option>
                                <option value="female"><?= __('gender.female', $locale) ?></option>
                                <option value="other"><?= __('gender.other', $locale) ?></option>
                                <option value="prefer_not_to_say"><?= __('gender.prefer_not', $locale) ?></option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label><?= __('field.dialect', $locale) ?></label>
                            <select name="kurdish_dialect" class="form-control">
                                <option value="">-- Select --</option>
                                <option value="kurmanji"><?= __('dialect.kurmanji', $locale) ?></option>
                                <option value="sorani"><?= __('dialect.sorani', $locale) ?></option>
                                <option value="pehlewani"><?= __('dialect.pehlewani', $locale) ?></option>
                                <option value="zazaki"><?= __('dialect.zazaki', $locale) ?></option>
                                <option value="gorani"><?= __('dialect.gorani', $locale) ?></option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Organization Fields -->
                <div class="organization-fields">
                    <div class="form-row">
                        <div class="form-group">
                            <label><?= __('field.org_name', $locale) ?> <span class="required">*</span></label>
                            <input type="text" name="org_name" class="form-control" value="<?= e($old['org_name'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label><?= __('field.org_type', $locale) ?></label>
                            <input type="text" name="org_type" class="form-control" value="<?= e($old['org_type'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label><?= __('field.org_members', $locale) ?></label>
                            <input type="number" name="org_member_count" class="form-control" min="1" value="<?= e($old['org_member_count'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label><?= __('field.org_kurdish_pct', $locale) ?></label>
                            <input type="number" name="org_kurdish_percentage" class="form-control" min="0" max="100" value="<?= e($old['org_kurdish_percentage'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label><?= __('field.org_website', $locale) ?></label>
                        <input type="url" name="org_website" class="form-control" value="<?= e($old['org_website'] ?? '') ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label><?= __('field.message', $locale) ?></label>
                    <textarea name="message" class="form-control" rows="3"><?= e($old['message'] ?? '') ?></textarea>
                </div>
                
                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" name="gdpr_consent" id="gdpr" value="1" required>
                        <label for="gdpr"><?= __('field.gdpr', $locale) ?> <span class="required">*</span></label>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" name="newsletter_consent" id="newsletter" value="1">
                        <label for="newsletter"><?= __('field.newsletter', $locale) ?></label>
                    </div>
                </div>
                
                <button type="submit" class="btn-submit"><?= __('field.submit', $locale) ?></button>
            </form>
        </div>
    </main>
    
    <footer class="footer">
        &copy; <?= date('Y') ?> Count Us Kurds. <?= __('footer.rights', $locale) ?>
    </footer>
    
    <script>
        function setType(type) {
            document.getElementById('application_type').value = type;
            document.querySelectorAll('.type-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            document.querySelector('.individual-fields').classList.toggle('active', type === 'individual');
            document.querySelector('.organization-fields').classList.toggle('active', type === 'organization');
        }
    </script>
</body>
</html>
