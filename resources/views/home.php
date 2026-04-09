<?php
declare(strict_types=1);

$supportLocales = $supportedLocales ?? [];
$supportedLocaleCodes = array_keys($supportLocales);
$old = static fn(string $key, string $default = ''): string => htmlspecialchars(
    $_POST[$key] ?? $default,
    ENT_QUOTES,
    'UTF-8'
);

$selectedRegion = $_POST['region'] ?? '';
$gdprChecked = isset($_POST['gdpr_consent']);
$isGroupMode = $formMode === 'group';
$gaMeasurementId = (string) env('GA_MEASUREMENT_ID', '');

$strings = static function (array $translations, string $key, mixed $default = null) {
    return array_get($translations, $key, $default);
};
?>
<!DOCTYPE html>
<html
    lang="<?= htmlspecialchars($currentLocale, ENT_QUOTES, 'UTF-8') ?>"
    dir="<?= htmlspecialchars($dir, ENT_QUOTES, 'UTF-8') ?>"
    class="no-js"
    data-supported-locales='<?= htmlspecialchars(json_encode($supportedLocaleCodes, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') ?>'
    data-default-locale="<?= htmlspecialchars($defaultLocale ?? 'en', ENT_QUOTES, 'UTF-8') ?>"
    data-current-locale="<?= htmlspecialchars($currentLocale, ENT_QUOTES, 'UTF-8') ?>"
    data-ga-measurement-id="<?= htmlspecialchars($gaMeasurementId, ENT_QUOTES, 'UTF-8') ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="description" content="<?= htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="theme-color" content="#0a0a0a">
    <?php if ($gaMeasurementId !== ''): ?>
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?= htmlspecialchars($gaMeasurementId, ENT_QUOTES, 'UTF-8') ?>"></script>
        <script src="<?= asset('assets/js/analytics.js?v=1') ?>" defer></script>
    <?php endif; ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" href="<?= asset('assets/css/app.css?v=3') ?>" as="style">
    <link rel="preload" as="image" href="<?= asset('assets/img/hero-banner.jpeg') ?>">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Cairo:wght@400;600;700;800&display=swap">
    <link rel="stylesheet" href="<?= asset('assets/css/app.css?v=3') ?>">
</head>
<body class="page">
    <a class="skip-link" href="#application-form"><?= $strings($translations, 'hero.cta', 'Skip to form') ?></a>

    <!-- HEADER - Simplified with only CTA button -->
    <header class="site-header">
        <div class="container header-inner">
            <a class="brand" href="/" aria-label="<?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?>">
                <img
                    src="<?= asset('assets/img/count-us-kurds-logo.png') ?>"
                    alt="Count Us Kurds"
                    class="brand-logo"
                    width="48"
                    height="48"
                    decoding="async"
                    loading="lazy"
                >
                <span class="brand-name">Count Us Kurds</span>
            </a>

            <div class="header-actions">
                <a class="header-cta" href="#application-form">
                    <?= $strings($translations, 'nav.application', 'Register Interest') ?>
                </a>
                
                <div class="language-switcher language-switcher--header">
                    <form method="get" id="language-form">
                        <label class="sr-only" for="language-desktop"><?= $strings($translations, 'nav.language_label', 'Language') ?></label>
                        <select id="language-desktop" name="lang" data-lang-select>
                            <?php foreach ($supportLocales as $code => $meta): ?>
                                <option value="<?= htmlspecialchars($code, ENT_QUOTES, 'UTF-8') ?>"
                                    <?= $code === $currentLocale ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($meta['label'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <main>
        <!-- HERO SECTION - Compact with immediate form access -->
        <section class="hero" id="top">
            <div class="container">
                <div class="hero-content">
                    <p class="hero-eyebrow"><?= $strings($translations, 'hero.eyebrow') ?></p>
                    <h1><?= $strings($translations, 'hero.title') ?></h1>
                    <p class="hero-subtitle"><?= $strings($translations, 'hero.subtitle') ?></p>
                    <a class="hero-cta" href="#application-form"><?= $strings($translations, 'hero.cta') ?></a>
                </div>
            </div>
        </section>

        <!-- APPLICATION FORM - Priority placement -->
        <section class="application" id="application-form">
            <div class="container">
                <header class="section-heading">
                    <h2><?= $strings($translations, 'form.heading') ?></h2>
                    <p><?= $strings($translations, 'form.description') ?></p>
                </header>

                <?php if (!empty($submissionStatus['message'])): ?>
                    <div class="alert <?= $submissionStatus['success'] ? 'alert--success' : 'alert--error' ?>">
                        <?= htmlspecialchars($submissionStatus['message'], ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>

                <div class="form-tabs" data-mode="<?= $isGroupMode ? 'group' : 'individual' ?>">
                    <button type="button"
                            class="form-tab <?= $isGroupMode ? '' : 'is-active' ?>"
                            data-target="individual">
                        <?= $strings($translations, 'form.tabs.individual') ?>
                    </button>
                    <button type="button"
                            class="form-tab <?= $isGroupMode ? 'is-active' : '' ?>"
                            data-target="group">
                        <?= $strings($translations, 'form.tabs.group') ?>
                    </button>
                </div>

                <form class="application-form" method="post" novalidate>
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="application_mode" id="application_mode" value="<?= $isGroupMode ? 'group' : 'individual' ?>">

                    <div class="form-grid">
                        <div class="form-field">
                            <label for="name"><?= $strings($translations, 'form.fields.name.label') ?></label>
                            <input id="name" name="name" type="text" autocomplete="name" required
                                   placeholder="<?= $strings($translations, 'form.fields.name.placeholder', '') ?>"
                                   value="<?= $old('name') ?>">
                        </div>

                        <div class="form-field">
                            <label for="email"><?= $strings($translations, 'form.fields.email.label') ?></label>
                            <input id="email" name="email" type="email" autocomplete="email" required
                                   placeholder="<?= $strings($translations, 'form.fields.email.placeholder', '') ?>"
                                   value="<?= $old('email') ?>">
                        </div>

                        <div class="form-field">
                            <label for="region"><?= $strings($translations, 'form.fields.region.label') ?></label>
                            <select id="region" name="region" required>
                                <option value=""><?= $strings($translations, 'form.fields.region.options.prompt') ?></option>
                                <?php foreach ($strings($translations, 'form.fields.region.options', []) as $value => $label): ?>
                                    <?php if ($value === 'prompt') continue; ?>
                                    <option value="<?= htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8') ?>"
                                        <?= $value === $selectedRegion ? 'selected' : '' ?>>
                                        <?= htmlspecialchars((string) $label, ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mode mode--individual <?= $isGroupMode ? 'is-hidden' : '' ?>" data-mode="individual">
                        <div class="form-field">
                            <label for="individual_contribution"><?= $strings($translations, 'form.fields.individual_contribution.label') ?></label>
                            <textarea id="individual_contribution"
                                      name="individual_contribution"
                                      rows="4"
                                      placeholder="<?= $strings($translations, 'form.fields.individual_contribution.placeholder', '') ?>"
                                      data-required="true"
                                      <?= $isGroupMode ? '' : 'required' ?>><?= $old('individual_contribution') ?></textarea>
                        </div>
                    </div>

                    <div class="mode mode--group <?= $isGroupMode ? '' : 'is-hidden' ?>" data-mode="group">
                        <div class="form-grid form-grid--stack">
                            <div class="form-field">
                                <label for="org_name"><?= $strings($translations, 'form.fields.org_name.label') ?></label>
                                <input id="org_name" name="org_name" type="text"
                                       placeholder="<?= $strings($translations, 'form.fields.org_name.placeholder', '') ?>"
                                       data-required="true"
                                       <?= $isGroupMode ? 'required' : '' ?>
                                       value="<?= $old('org_name') ?>">
                            </div>
                            <div class="form-field">
                                <label for="org_contribution"><?= $strings($translations, 'form.fields.org_contribution.label') ?></label>
                                <textarea id="org_contribution" name="org_contribution"
                                          rows="4"
                                          placeholder="<?= $strings($translations, 'form.fields.org_contribution.placeholder', '') ?>"
                                          data-required="true"
                                          <?= $isGroupMode ? 'required' : '' ?>><?= $old('org_contribution') ?></textarea>
                            </div>
                            <div class="form-field">
                                <label for="org_motive"><?= $strings($translations, 'form.fields.org_motive.label') ?></label>
                                <textarea id="org_motive" name="org_motive"
                                          rows="3"
                                          placeholder="<?= $strings($translations, 'form.fields.org_motive.placeholder', '') ?>"
                                          data-required="true"
                                          <?= $isGroupMode ? 'required' : '' ?>><?= $old('org_motive') ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form-consent">
                        <label class="checkbox">
                            <input type="checkbox" name="gdpr_consent" value="1" <?= $gdprChecked ? 'checked' : '' ?> required>
                            <span><?= $strings($translations, 'form.fields.gdpr.label') ?></span>
                        </label>
                        <a class="policy-link" href="<?= asset('assets/privacy-policy.html') ?>" target="_blank" rel="noopener">
                            <?= $strings($translations, 'form.buttons.policy') ?>
                        </a>
                    </div>

                    <button class="submit-btn" type="submit">
                        <?= $strings($translations, 'form.buttons.submit') ?>
                    </button>
                </form>
            </div>
        </section>

        <!-- ABOUT SECTION - Secondary content -->
        <section class="about content-deferred" id="about">
            <div class="container">
                <div class="about-grid">
                    <div class="about-image">
                        <figure class="about-banner">
                            <img
                                src="<?= asset('assets/img/hero-banner.jpeg') ?>"
                                alt="<?= $strings($translations, 'hero.banner_alt', 'Kurdish people united') ?>"
                                width="800"
                                height="450"
                                loading="lazy"
                                decoding="async"
                            >
                        </figure>
                    </div>
                    <div class="about-content">
                        <h2><?= $strings($translations, 'manifesto.heading', 'Why this census is necessary') ?></h2>
                        <div class="about-text">
                            <?php 
                            $paragraphs = $strings($translations, 'manifesto.paragraphs', []);
                            if (is_array($paragraphs)):
                                foreach ($paragraphs as $paragraph): ?>
                                    <p><?= htmlspecialchars($paragraph, ENT_QUOTES, 'UTF-8') ?></p>
                                <?php endforeach;
                            endif;
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- GOALS SECTION -->
        <section class="goals content-deferred" id="goals">
            <div class="container">
                <header class="section-heading">
                    <h2><?= $strings($translations, 'timeline.heading') ?></h2>
                    <?php $timelineDescription = $strings($translations, 'timeline.description'); ?>
                    <?php if ($timelineDescription): ?>
                        <p><?= htmlspecialchars($timelineDescription, ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                </header>
                <div class="goals-grid">
                    <?php foreach ($strings($translations, 'timeline.phases', []) as $index => $phase): ?>
                        <article class="goal-card">
                            <span class="goal-number"><?= (int) $index + 1 ?></span>
                            <h3><?= htmlspecialchars($phase['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></h3>
                            <p><?= htmlspecialchars($phase['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- PRINCIPLES SECTION -->
        <section class="principles-section content-deferred" id="principles">
            <div class="container">
                <header class="section-heading">
                    <h2><?= $strings($translations, 'vision.heading') ?></h2>
                    <p><?= $strings($translations, 'vision.body') ?></p>
                </header>
                <div class="principles-grid">
                    <?php foreach ($strings($translations, 'vision.principles', []) as $principle): ?>
                        <article class="principle-card">
                            <h3><?= htmlspecialchars($principle['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></h3>
                            <p><?= htmlspecialchars($principle['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- FINAL CTA -->
        <section class="final-cta content-deferred">
            <div class="container">
                <h2><?= $strings($translations, 'cta_section.heading', 'The time is now') ?></h2>
                <p><?= $strings($translations, 'cta_section.body') ?></p>
                <a class="hero-cta" href="#application-form"><?= $strings($translations, 'hero.cta') ?></a>
            </div>
        </section>
    </main>

    <!-- LANGUAGE FAB (Mobile) -->
    <button
        type="button"
        class="language-fab"
        data-lang-toggle
        aria-controls="language-overlay"
        aria-expanded="false">
        <span class="language-fab__icon" aria-hidden="true">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" role="presentation">
                <path d="M12 2.75a9.25 9.25 0 1 0 9.25 9.25A9.26 9.26 0 0 0 12 2.75Zm0 17a7.75 7.75 0 1 1 7.75-7.75 7.76 7.76 0 0 1-7.75 7.75Zm5.78-7.18h-3.27a22 22 0 0 1-.5 3.93 8.28 8.28 0 0 0 3.77-3.93Zm-5.78 0H6.22a8.29 8.29 0 0 0 3.78 3.93 21.84 21.84 0 0 1-.5-3.93Zm0-1.5V6.43a21.66 21.66 0 0 0-2.28 4.14ZM11.5 6.43v4.64H7.5a23.34 23.34 0 0 1 2.28-4.14A8.44 8.44 0 0 1 11.5 6.43Zm1 0a8.39 8.39 0 0 1 1.72.08 23.2 23.2 0 0 1 2.28 4.14h-4Zm0 6.64h4a23.49 23.49 0 0 1-2.28 4.14 8.47 8.47 0 0 1-1.72.08Zm0-1.5h4.72a23.06 23.06 0 0 0-.5-3.93 8.31 8.31 0 0 1 3.77 3.93Z" fill="currentColor"/>
            </svg>
        </span>
        <span class="sr-only"><?= $strings($translations, 'nav.change_language', 'Change language') ?></span>
    </button>

    <!-- LANGUAGE OVERLAY -->
    <div
        class="language-overlay"
        id="language-overlay"
        aria-hidden="true"
        data-lang-overlay>
        <div class="language-overlay__backdrop" data-lang-close></div>
        <div class="language-overlay__panel" role="dialog" aria-modal="true" aria-label="<?= $strings($translations, 'nav.language_label', 'Language') ?>">
            <div class="language-overlay__header">
                <p><?= $strings($translations, 'nav.language_label', 'Language') ?></p>
                <button type="button" class="language-overlay__close" data-lang-close aria-label="<?= $strings($translations, 'nav.close_language', 'Close') ?>">
                    &times;
                </button>
            </div>
            <form method="get" class="language-overlay__form" data-lang-form>
                <ul class="language-overlay__list">
                    <?php foreach ($supportLocales as $code => $meta): ?>
                        <li>
                            <button
                                type="button"
                                data-lang-choice="<?= htmlspecialchars($code, ENT_QUOTES, 'UTF-8') ?>"
                                class="<?= $code === $currentLocale ? 'is-active' : '' ?>">
                                <span><?= htmlspecialchars($meta['label'], ENT_QUOTES, 'UTF-8') ?></span>
                                <?php if ($code === $currentLocale): ?>
                                    <span class="language-overlay__current" aria-label="<?= $strings($translations, 'language.current', 'Current language') ?>">&#10003;</span>
                                <?php endif; ?>
                            </button>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </form>
        </div>
    </div>

    <!-- NOSCRIPT LANGUAGE SWITCHER -->
    <noscript>
        <div class="language-switcher language-switcher--noscript">
            <form method="get">
                <label class="sr-only" for="language-noscript"><?= $strings($translations, 'nav.language_label', 'Language') ?></label>
                <select id="language-noscript" name="lang">
                    <?php foreach ($supportLocales as $code => $meta): ?>
                        <option value="<?= htmlspecialchars($code, ENT_QUOTES, 'UTF-8') ?>"
                            <?= $code === $currentLocale ? 'selected' : '' ?>>
                            <?= htmlspecialchars($meta['label'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="language-submit">
                    <?= $strings($translations, 'nav.change_language', 'Change language') ?>
                </button>
            </form>
        </div>
    </noscript>

    <!-- FOOTER -->
    <footer class="site-footer">
        <div class="container footer-inner">
            <p><?= $strings($translations, 'footer.tagline') ?></p>
            <p><?= str_replace(':year', (string) date('Y'), $strings($translations, 'footer.rights')) ?></p>
            <p><?= $strings($translations, 'footer.contact') ?></p>
        </div>
    </footer>

    <script src="<?= asset('assets/js/app.js') ?>" defer></script>
</body>
</html>
