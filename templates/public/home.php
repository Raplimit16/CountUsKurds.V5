<!DOCTYPE html>
<html lang="<?= e($locale) ?>" dir="<?= e($dir) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    <meta name="description" content="<?= e($pageDescription) ?>">
    
    <!-- Kurdish flag colors: Red #ED1C24, White #FFFFFF, Green #21924F, Yellow/Gold #F9DD16 -->
    <style>
        :root {
            --kurd-red: #ED1C24;
            --kurd-green: #21924F;
            --kurd-yellow: #F9DD16;
            --kurd-white: #FFFFFF;
            --kurd-dark: #1a1a2e;
            --kurd-darker: #0f0f1a;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Noto Sans', Arial, sans-serif;
            background: var(--kurd-darker);
            color: var(--kurd-white);
            line-height: 1.6;
        }
        
        /* Header */
        .header {
            background: rgba(26, 26, 46, 0.95);
            backdrop-filter: blur(10px);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            border-bottom: 2px solid var(--kurd-red);
        }
        
        .header-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: var(--kurd-white);
            font-weight: 700;
            font-size: 1.25rem;
        }
        
        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--kurd-red), var(--kurd-green));
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .nav { display: flex; gap: 2rem; align-items: center; }
        .nav a {
            color: var(--kurd-white);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        .nav a:hover { color: var(--kurd-yellow); }
        
        .lang-selector {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            cursor: pointer;
        }
        
        /* Hero Section */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 6rem 2rem 4rem;
            background: 
                linear-gradient(135deg, rgba(237,28,36,0.1) 0%, rgba(33,146,79,0.1) 100%),
                radial-gradient(ellipse at top, rgba(249,221,22,0.05) 0%, transparent 50%),
                var(--kurd-darker);
            position: relative;
            overflow: hidden;
        }
        
        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, var(--kurd-yellow) 1px, transparent 1px);
            background-size: 50px 50px;
            opacity: 0.03;
            animation: float 20s infinite linear;
        }
        
        @keyframes float {
            0% { transform: translate(0, 0); }
            100% { transform: translate(50px, 50px); }
        }
        
        .hero-content {
            max-width: 800px;
            position: relative;
            z-index: 1;
        }
        
        .hero h1 {
            font-size: clamp(2.5rem, 6vw, 4rem);
            font-weight: 800;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, var(--kurd-white), var(--kurd-yellow));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .hero p {
            font-size: 1.25rem;
            color: rgba(255,255,255,0.8);
            margin-bottom: 2rem;
        }
        
        .hero-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--kurd-red), #c41920);
            color: white;
            box-shadow: 0 4px 20px rgba(237,28,36,0.4);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 30px rgba(237,28,36,0.5);
        }
        
        .btn-secondary {
            background: rgba(255,255,255,0.1);
            color: white;
            border: 2px solid rgba(255,255,255,0.3);
        }
        
        .btn-secondary:hover {
            background: rgba(255,255,255,0.2);
            border-color: var(--kurd-yellow);
        }
        
        /* Stats Bar */
        .stats-bar {
            background: linear-gradient(135deg, var(--kurd-green), #1a7a3f);
            padding: 3rem 2rem;
        }
        
        .stats-grid {
            max-width: 1000px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            text-align: center;
        }
        
        .stat-item h3 {
            font-size: 3rem;
            font-weight: 800;
            color: var(--kurd-yellow);
        }
        
        .stat-item p {
            font-size: 1rem;
            opacity: 0.9;
        }
        
        /* Sections */
        .section {
            padding: 5rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 3rem;
            color: var(--kurd-yellow);
        }
        
        /* Mission */
        .mission-content {
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
            font-size: 1.2rem;
            color: rgba(255,255,255,0.85);
        }
        
        /* Why Section */
        .why-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
        }
        
        .why-card {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 16px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .why-card:hover {
            transform: translateY(-5px);
            border-color: var(--kurd-yellow);
            background: rgba(249,221,22,0.05);
        }
        
        .why-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 1rem;
            background: linear-gradient(135deg, var(--kurd-red), var(--kurd-green));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        
        .why-card h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: var(--kurd-yellow);
        }
        
        .why-card p {
            color: rgba(255,255,255,0.7);
        }
        
        /* CTA Section */
        .cta-section {
            background: linear-gradient(135deg, var(--kurd-red), #c41920);
            padding: 5rem 2rem;
            text-align: center;
        }
        
        .cta-section h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .cta-section p {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 2rem;
        }
        
        .cta-section .btn {
            background: var(--kurd-yellow);
            color: var(--kurd-dark);
        }
        
        .cta-section .btn:hover {
            background: #fff;
        }
        
        /* Footer */
        .footer {
            background: var(--kurd-dark);
            padding: 3rem 2rem;
            text-align: center;
            border-top: 2px solid var(--kurd-green);
        }
        
        .footer-links {
            display: flex;
            gap: 2rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
        }
        
        .footer-links a {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
        }
        
        .footer-links a:hover {
            color: var(--kurd-yellow);
        }
        
        .footer p {
            color: rgba(255,255,255,0.5);
            font-size: 0.9rem;
        }
        
        /* Kurdish Sun Symbol */
        .sun-symbol {
            width: 80px;
            height: 80px;
            margin: 0 auto 1rem;
        }
        
        /* Mobile */
        @media (max-width: 768px) {
            .header-inner { padding: 1rem; }
            .nav { display: none; }
            .hero { padding: 5rem 1rem 3rem; }
            .hero h1 { font-size: 2rem; }
            .section { padding: 3rem 1rem; }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-inner">
            <a href="/" class="logo">
                <div class="logo-icon">☀️</div>
                Count Us Kurds
            </a>
            <nav class="nav">
                <a href="/"><?= __('nav.home', $locale) ?></a>
                <a href="/register"><?= __('nav.register', $locale) ?></a>
                <a href="/about"><?= __('nav.about', $locale) ?></a>
                <a href="/contact"><?= __('nav.contact', $locale) ?></a>
                <select class="lang-selector" onchange="location.href='?lang='+this.value">
                    <?php foreach ($languages as $code => $lang): ?>
                        <option value="<?= e($code) ?>" <?= $locale === $code ? 'selected' : '' ?>>
                            <?= e($lang['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </nav>
        </div>
    </header>
    
    <!-- Hero -->
    <section class="hero">
        <div class="hero-content">
            <h1><?= __('home.hero.title', $locale) ?></h1>
            <p><?= __('home.hero.subtitle', $locale) ?></p>
            <div class="hero-buttons">
                <a href="/register" class="btn btn-primary"><?= __('home.hero.cta', $locale) ?></a>
                <a href="#mission" class="btn btn-secondary"><?= __('home.hero.learn_more', $locale) ?></a>
            </div>
        </div>
    </section>
    
    <!-- Stats -->
    <section class="stats-bar">
        <div class="stats-grid">
            <div class="stat-item">
                <h3><?= number_format($stats['total_applications']) ?></h3>
                <p><?= __('home.stats.registered', $locale) ?></p>
            </div>
            <div class="stat-item">
                <h3><?= count($stats['countries']) ?>+</h3>
                <p><?= __('home.stats.countries', $locale) ?></p>
            </div>
            <div class="stat-item">
                <h3><?= number_format($stats['organization_count']) ?></h3>
                <p><?= __('home.stats.organizations', $locale) ?></p>
            </div>
        </div>
    </section>
    
    <!-- Mission -->
    <section id="mission" class="section">
        <h2 class="section-title"><?= __('home.mission.title', $locale) ?></h2>
        <div class="mission-content">
            <p><?= __('home.mission.text', $locale) ?></p>
        </div>
    </section>
    
    <!-- Why -->
    <section class="section">
        <h2 class="section-title"><?= __('home.why.title', $locale) ?></h2>
        <div class="why-grid">
            <div class="why-card">
                <div class="why-icon">👁️</div>
                <h3><?= __('home.why.visibility', $locale) ?></h3>
                <p><?= __('home.why.visibility_text', $locale) ?></p>
            </div>
            <div class="why-card">
                <div class="why-icon">⚖️</div>
                <h3><?= __('home.why.rights', $locale) ?></h3>
                <p><?= __('home.why.rights_text', $locale) ?></p>
            </div>
            <div class="why-card">
                <div class="why-icon">🤝</div>
                <h3><?= __('home.why.unity', $locale) ?></h3>
                <p><?= __('home.why.unity_text', $locale) ?></p>
            </div>
        </div>
    </section>
    
    <!-- CTA -->
    <section class="cta-section">
        <h2><?= __('home.cta.title', $locale) ?></h2>
        <p><?= __('home.cta.text', $locale) ?></p>
        <a href="/register" class="btn"><?= __('home.cta.button', $locale) ?></a>
    </section>
    
    <!-- Footer -->
    <footer class="footer">
        <div class="footer-links">
            <a href="/about"><?= __('nav.about', $locale) ?></a>
            <a href="/contact"><?= __('nav.contact', $locale) ?></a>
            <a href="/privacy"><?= __('nav.privacy', $locale) ?></a>
        </div>
        <p>&copy; <?= date('Y') ?> Count Us Kurds. <?= __('footer.rights', $locale) ?></p>
        <p><?= __('footer.made_with', $locale) ?> ❤️</p>
    </footer>
</body>
</html>
