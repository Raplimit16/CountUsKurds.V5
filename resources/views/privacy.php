<?php
declare(strict_types=1);

$supportLocales = $supportedLocales ?? [];
$policySections = $policyEntry['sections'] ?? [];
$gaMeasurementId = (string) env('GA_MEASUREMENT_ID', '');
$strings = static function (array $translations, string $key, mixed $default = null) {
    return array_get($translations, $key, $default);
};
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLocale, ENT_QUOTES, 'UTF-8') ?>" dir="<?= htmlspecialchars($dir, ENT_QUOTES, 'UTF-8') ?>" class="no-js" data-ga-measurement-id="<?= htmlspecialchars($gaMeasurementId, ENT_QUOTES, 'UTF-8') ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="description" content="<?= htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8') ?>">
    <?php if ($gaMeasurementId !== ''): ?>
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?= htmlspecialchars($gaMeasurementId, ENT_QUOTES, 'UTF-8') ?>"></script>
        <script src="<?= asset('assets/js/analytics.js?v=1') ?>" defer></script>
    <?php endif; ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Cairo:wght@400;600;700&display=swap">
    <link rel="stylesheet" href="<?= asset('assets/css/app.css?v=1') ?>">
</head>
<body class="page policy-page">
    <header class="policy-header">
        <div class="container policy-header__inner">
            <a class="brand policy-brand" href="<?= asset('') ?>">
                <img class="brand-logo" src="<?= asset('assets/img/count-us-kurds-logo.png') ?>" alt="Count Us Kurds emblem">
                <div class="brand-text">
                    <div class="brand-name">
                        <span class="brand-segment brand-red">Count</span>
                        <span class="brand-segment brand-gold">Us</span>
                        <span class="brand-segment brand-green">Kurds</span>
                    </div>
                    <p class="brand-tagline"><?= $strings($translations, 'hero.eyebrow', 'Global Kurdish initiative') ?></p>
                </div>
            </a>
            <nav class="policy-nav">
                <a href="<?= asset('') ?>"><?= $strings($translations, 'nav.back_home', 'Back to main site') ?></a>
            </nav>
            <form method="get" class="language-switcher policy-language-switcher" id="language-form">
                <label class="sr-only" for="policy-language"><?= $strings($translations, 'nav.language_label', 'Language') ?></label>
                <select id="policy-language" name="lang">
                    <?php foreach ($supportLocales as $code => $meta): ?>
                        <option value="<?= htmlspecialchars($code, ENT_QUOTES, 'UTF-8') ?>" <?= $code === $currentLocale ? 'selected' : '' ?>>
                            <?= htmlspecialchars($meta['label'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
    </header>

    <main class="policy-main">
        <section class="policy-hero">
            <div class="hero-banner">
                <img src="<?= asset('assets/img/banner-full169.jpeg') ?>" alt="<?= htmlspecialchars($heroBannerAlt, ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <p class="policy-language-label">
                <?= $strings($translations, 'privacy.language_label', 'Language version') ?> · <?= htmlspecialchars($policyEntry['language'] ?? '', ENT_QUOTES, 'UTF-8') ?>
            </p>
            <h1><?= htmlspecialchars($policyEntry['title'] ?? $pageTitle, ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="policy-summary"><?= htmlspecialchars($policyEntry['summary'] ?? $pageDescription, ENT_QUOTES, 'UTF-8') ?></p>
            <p class="policy-updated">
                <?= htmlspecialchars($policyEntry['updated_label'] ?? $strings($translations, 'privacy.updated', 'Updated'), ENT_QUOTES, 'UTF-8') ?>
                <?= htmlspecialchars($updatedAt, ENT_QUOTES, 'UTF-8') ?>
            </p>
        </section>

        <section class="policy-sections">
            <?php foreach ($policySections as $section): ?>
                <article class="policy-section">
                    <h2><?= htmlspecialchars($section['heading'] ?? '', ENT_QUOTES, 'UTF-8') ?></h2>
                    <?php if (!empty($section['intro'])): ?>
                        <p><?= htmlspecialchars($section['intro'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                    <?php foreach (($section['body'] ?? []) as $paragraph): ?>
                        <p><?= htmlspecialchars($paragraph, ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endforeach; ?>
                    <?php if (!empty($section['bullets'])): ?>
                        <ul>
                            <?php foreach ($section['bullets'] as $bullet): ?>
                                <li><?= htmlspecialchars($bullet, ENT_QUOTES, 'UTF-8') ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </section>
    </main>

    <footer class="policy-footer">
        <div class="container">
            <p><?= htmlspecialchars($policyEntry['contact'] ?? 'Contact us at info@countuskurds.com if you have questions about privacy or data ownership.', ENT_QUOTES, 'UTF-8') ?></p>
            <a class="footer-mail" href="mailto:info@countuskurds.com">info@countuskurds.com</a>
        </div>
    </footer>

    <script src="<?= asset('assets/js/app.js') ?>" defer></script>
</body>
</html>
