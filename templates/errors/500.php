<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Error - Count Us Kurds</title>
    <style>
        :root { --primary: #ED1C24; --bg: #0f0f1a; --text: #fff; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .error-container { text-align: center; max-width: 500px; }
        .error-code { font-size: 6rem; font-weight: 800; color: var(--primary); line-height: 1; }
        .error-title { font-size: 1.5rem; margin: 1rem 0; }
        .error-message { color: rgba(255,255,255,0.6); margin-bottom: 2rem; }
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: var(--primary);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">500</div>
        <h1 class="error-title">Server Error</h1>
        <p class="error-message">Something went wrong on our end. Please try again later.</p>
        <a href="/" class="btn">Go Home</a>
    </div>
</body>
</html>
