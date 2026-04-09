<!DOCTYPE html>
<html lang="<?= e($locale) ?>" dir="<?= e($dir) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> - Count Us Kurds</title>
    <style>
        :root { --kurd-red: #ED1C24; --kurd-green: #21924F; --kurd-yellow: #F9DD16; --kurd-dark: #1a1a2e; --kurd-darker: #0f0f1a; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: var(--kurd-darker); color: #fff; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem; }
        .container { text-align: center; max-width: 500px; }
        .icon { font-size: 4rem; margin-bottom: 1rem; }
        h1 { color: var(--kurd-yellow); font-size: 2rem; margin-bottom: 1rem; }
        p { color: rgba(255,255,255,0.8); margin-bottom: 2rem; font-size: 1.1rem; }
        .btn { display: inline-block; padding: 1rem 2rem; background: var(--kurd-red); color: white; text-decoration: none; border-radius: 50px; font-weight: 600; }
        .btn:hover { background: #c41920; }
        .share { margin-top: 2rem; }
        .share-title { color: rgba(255,255,255,0.6); margin-bottom: 1rem; font-size: 0.9rem; }
        .share-buttons { display: flex; gap: 1rem; justify-content: center; }
        .share-btn { padding: 0.5rem 1rem; background: rgba(255,255,255,0.1); border-radius: 8px; color: white; text-decoration: none; font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">✅</div>
        <h1><?= __('thankyou.title', $locale) ?></h1>
        <p><?= __('thankyou.message', $locale) ?></p>
        <a href="/" class="btn"><?= __('thankyou.back', $locale) ?></a>
        
        <div class="share">
            <p class="share-title"><?= __('thankyou.share', $locale) ?></p>
            <div class="share-buttons">
                <a href="https://twitter.com/intent/tweet?text=<?= urlencode(__('home.hero.subtitle', $locale)) ?>&url=<?= urlencode(url('/')) ?>" target="_blank" class="share-btn">Twitter</a>
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(url('/')) ?>" target="_blank" class="share-btn">Facebook</a>
            </div>
        </div>
    </div>
</body>
</html>
