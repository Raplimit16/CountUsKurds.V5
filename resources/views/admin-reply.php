<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Svara på ansökan - Count Us Kurds</title>
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
            font-size: 16px;
            padding: 14px 32px;
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
        .card h2 {
            font-size: 20px;
            margin-bottom: 16px;
            color: #111827;
        }
        .info-box {
            background: #f9fafb;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 24px;
        }
        .info-box p {
            margin: 4px 0;
            color: #374151;
        }
        .info-box strong {
            color: #111827;
        }
        .form-group {
            margin-bottom: 24px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #374151;
            font-size: 14px;
        }
        input[type="text"],
        textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 15px;
            font-family: inherit;
        }
        input:focus,
        textarea:focus {
            outline: none;
            border-color: #ed1c24;
        }
        textarea {
            min-height: 300px;
            resize: vertical;
        }
        .alert {
            padding: 16px 20px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-weight: 600;
        }
        .alert-success {
            background: #f0fdf4;
            color: #166534;
            border: 2px solid #bbf7d0;
        }
        .alert-error {
            background: #fef2f2;
            color: #991b1b;
            border: 2px solid #fecaca;
        }
        .template-buttons {
            display: flex;
            gap: 8px;
            margin-bottom: 12px;
            flex-wrap: wrap;
        }
        .btn-template {
            padding: 8px 16px;
            background: #f3f4f6;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
        }
        .btn-template:hover {
            background: #e5e7eb;
        }
        @media (max-width: 768px) {
            .header h1 { font-size: 18px; }
            .card { padding: 20px; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <h1>📧 Svara på ansökan</h1>
            <a href="/admin.php?action=view&id=<?= $application['id'] ?>" class="btn btn-white">← Tillbaka</a>
        </div>
    </div>
    
    <div class="container">
        <?php if ($message): ?>
            <div class="alert alert-<?= $message['type'] ?>">
                <?= htmlspecialchars($message['text'], ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <h2>Mottagare</h2>
            <div class="info-box">
                <p><strong>Namn:</strong> <?= htmlspecialchars($application['name']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($application['email']) ?></p>
                <p><strong>Typ:</strong> <?= $application['application_type'] === 'individual' ? 'Individuell' : 'Organisation' ?></p>
                <p><strong>Region:</strong> <?= htmlspecialchars(ucfirst($application['region'])) ?></p>
            </div>
        </div>
        
        <div class="card">
            <h2>Skriv ditt svar</h2>
            
            <div class="form-group">
                <label for="reply_lang">Svarsspråk</label>
                <select id="reply_lang" name="reply_lang" class="form-control">
                    <option value="en">English</option>
                    <option value="sv" selected>Svenska</option>
                </select>
                <p id="lang-hint" style="display:none;font-size:13px;color:#666;margin-top:4px;">Mallar uppdateras när du väljer mall igen</p>
            </div>
            
            <div class="template-buttons">
                <strong style="display:block;margin-bottom:8px;font-size:14px;">Svenska mallar:</strong>
                <button class="btn-template" onclick="insertTemplate('accept', 'sv')">✅ Acceptera (SV)</button>
                <button class="btn-template" onclick="insertTemplate('more_info', 'sv')">❓ Begär info (SV)</button>
                <button class="btn-template" onclick="insertTemplate('reject', 'sv')">❌ Avslå (SV)</button>
            </div>
            
            <div class="template-buttons" style="margin-top:12px;">
                <strong style="display:block;margin-bottom:8px;font-size:14px;">English templates:</strong>
                <button class="btn-template" onclick="insertTemplate('accept', 'en')">✅ Accept (EN)</button>
                <button class="btn-template" onclick="insertTemplate('more_info', 'en')">❓ Request info (EN)</button>
                <button class="btn-template" onclick="insertTemplate('reject', 'en')">❌ Reject (EN)</button>
            </div>
            
            <form method="POST" style="margin-top:24px;">
                <input type="hidden" name="_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" id="reply_lang_hidden" name="reply_lang" value="sv">
                <div class="form-group">
                    <label for="subject">Ämnesrad</label>
                    <input type="text" id="subject" name="subject" required 
                           value="Re: Din ansökan till Count Us Kurds Foundation Team">
                </div>
                
                <div class="form-group">
                    <label for="body">Meddelande</label>
                    <textarea id="body" name="body" required></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">🚀 Skicka email</button>
            </form>
        </div>
    </div>
    
    <script>
        const templates = <?= json_encode($templates) ?>;
        
        function insertTemplate(type, lang) {
            const name = <?= json_encode($application['name']) ?>;
            const bodyField = document.getElementById('body');
            const subjectField = document.getElementById('subject');
            const langSelect = document.getElementById('reply_lang');
            
            langSelect.value = lang;
            
            if (templates[lang] && templates[lang][type]) {
                subjectField.value = templates[lang][type].subject;
                bodyField.value = templates[lang][type].body.replace(':name', name);
            }
        }
        
        // Auto-switch language
        document.getElementById('reply_lang').addEventListener('change', function() {
            document.getElementById('lang-hint').style.display = 'block';
        });
    </script>
</body>
</html>
