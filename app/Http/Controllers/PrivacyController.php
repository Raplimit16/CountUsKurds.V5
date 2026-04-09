<?php
declare(strict_types=1);

namespace CountUsKurds\Http\Controllers;

use CountUsKurds\Support\Translator;
use RuntimeException;

class PrivacyController
{
    private Translator $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function handle(): void
    {
        $supportedLocales = $this->translator->supportedLocales();
        $currentLocale = $this->resolveLocale($supportedLocales);
        $_SESSION['locale'] = $currentLocale;

        $policyData = $this->policyData();
        if ($policyData === []) {
            throw new RuntimeException('Missing privacy policy content.');
        }

        $policyEntry = $policyData[$currentLocale] ?? $policyData['en'] ?? reset($policyData);
        $translations = $this->translator->all($currentLocale);
        $dir = $this->translator->direction($currentLocale);
        $pageTitle = array_get($translations, 'privacy.page_title', $policyEntry['title'] ?? 'Privacy & Data Ownership');
        $pageDescription = array_get($translations, 'privacy.page_description', $policyEntry['summary'] ?? '');
        $csrfToken = csrf_token();
        $updatedAt = date('F j, Y');
        $heroBannerAlt = array_get($translations, 'hero.banner_alt', 'Count Us Kurds global unity banner');

        include resource_path('views/privacy.php');
    }

    /**
     * @param array<string, array{label: string, dir: string}> $supportedLocales
     */
    private function resolveLocale(array $supportedLocales): string
    {
        $default = array_key_first($supportedLocales) ?: 'en';

        $queryLocale = $this->normalizeLocale($_GET['lang'] ?? null, $supportedLocales);
        if ($queryLocale !== null) {
            return $queryLocale;
        }

        $sessionLocale = $this->normalizeLocale($_SESSION['locale'] ?? null, $supportedLocales);
        if ($sessionLocale !== null) {
            return $sessionLocale;
        }

        return $default;
    }

    private function normalizeLocale(?string $locale, array $supported): ?string
    {
        if ($locale === null) {
            return null;
        }

        $locale = strtolower(trim($locale));

        return isset($supported[$locale]) ? $locale : null;
    }

    /**
     * @return array<string, array>
     */
    private function policyData(): array
    {
        $path = resource_path('privacy' . DIRECTORY_SEPARATOR . 'policy.json');
        if (!is_file($path)) {
            return [];
        }

        $contents = file_get_contents($path);
        if ($contents === false) {
            return [];
        }

        /** @var array|null $decoded */
        $decoded = json_decode($contents, true);

        return is_array($decoded) ? $decoded : [];
    }
}
