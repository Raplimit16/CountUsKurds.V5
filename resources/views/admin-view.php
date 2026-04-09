<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visa ansökan #<?= $application['id'] ?> - Count Us Kurds</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f3f4f6;
            color: #111827;
        }
        .header {
            background: linear-gradient(135deg, #ed1c24, #ff684f);
            color: white;
            padding: 20px 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }
        .header h1 {
            font-size: 24px;
            font-weight: 800;
        }
        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            display: inline-block;
            border: none;
            cursor: pointer;
        }
        .btn-white {
            background: white;
            color: #ed1c24;
        }
        .btn-primary {
            background: #ed1c24;
            color: white;
        }
        .btn-danger {
            background: #dc2626;
            color: white;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 24px;
        }
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 32px;
            margin-bottom: 24px;
        }
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 2px solid #f3f4f6;
            flex-wrap: wrap;
            gap: 16px;
        }
        .card-header h2 {
            font-size: 28px;
            color: #111827;
        }
        .badge {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
        }
        .badge-individual {
            background: #dbeafe;
            color: #1e40af;
        }
        .badge-group {
            background: #fef3c7;
            color: #92400e;
        }
        .field {
            margin-bottom: 24px;
        }
        .field-label {
            font-size: 13px;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 8px;
        }
        .field-value {
            font-size: 16px;
            color: #111827;
            line-height: 1.6;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .field-value.large {
            font-size: 20px;
            font-weight: 600;
        }
        .actions {
            display: flex;
            gap: 12px;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 2px solid #f3f4f6;
            flex-wrap: wrap;
        }
        @media (max-width: 768px) {
            .header h1 { font-size: 18px; }
            .card { padding: 20px; }
            .card-header h2 { font-size: 22px; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <h1>📄 Ansökan #<?= $application['id'] ?></h1>
            <a href="/admin.php?action=dashboard" class="btn btn-white">← Tillbaka</a>
        </div>
    </div>
    
    <div class="container">
        <div class="card">
            <div class="card-header">
                <div>
                    <h2><?= htmlspecialchars($application['name']) ?></h2>
                    <p style="color:#6b7280;margin-top:4px;"><?= htmlspecialchars($application['email']) ?></p>
                </div>
                <span class="badge badge-<?= $application['application_type'] ?>">
                    <?= $application['application_type'] === 'individual' ? 'Individuell' : 'Organisation' ?>
                </span>
            </div>
            
            <div class="field">
                <div class="field-label">Region / Diaspora</div>
                <div class="field-value large"><?= htmlspecialchars(ucfirst($application['region'])) ?></div>
            </div>
            
            <?php if ($application['application_type'] === 'individual' && $application['individual_contribution']): ?>
                <div class="field">
                    <div class="field-label">Bidrag</div>
                    <div class="field-value"><?= htmlspecialchars($application['individual_contribution']) ?></div>
                </div>
            <?php endif; ?>
            
            <?php if ($application['application_type'] === 'group'): ?>
                <div class="field">
                    <div class="field-label">Organisationsnamn</div>
                    <div class="field-value large"><?= htmlspecialchars($application['org_name'] ?? '') ?></div>
                </div>
                
                <div class="field">
                    <div class="field-label">Organisations bidrag</div>
                    <div class="field-value"><?= htmlspecialchars($application['org_contribution'] ?? '') ?></div>
                </div>
                
                <div class="field">
                    <div class="field-label">Motiv</div>
                    <div class="field-value"><?= htmlspecialchars($application['org_motive'] ?? '') ?></div>
                </div>
            <?php endif; ?>
            
            <div class="field">
                <div class="field-label">GDPR-samtycke</div>
                <div class="field-value"><?= $application['gdpr_consent'] ? '✅ Ja' : '❌ Nej' ?></div>
            </div>
            
            <div class="actions">
                <a href="/admin.php?action=reply&id=<?= $application['id'] ?>" class="btn btn-primary">📧 Svara via email</a>
                <a href="mailto:<?= htmlspecialchars($application['email']) ?>" class="btn btn-white">📬 Öppna i email-klient</a>
                <form method="POST" action="/admin.php?action=delete&id=<?= $application['id'] ?>" style="margin-left:auto;" onsubmit="return confirm('Vill du verkligen radera denna ansökan? Detta kan inte ångras.')">
                    <input type="hidden" name="confirm" value="1">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
                    <button type="submit" class="btn btn-danger">🗑️ Radera</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
