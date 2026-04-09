<?php
declare(strict_types=1);

namespace CountUsKurds\Http\Controllers;

use CountUsKurds\Services\SubmissionService;
use CountUsKurds\Support\Translator;

class ApplicationController
{
    private Translator $translator;

    private SubmissionService $submissionService;

    public function __construct(Translator $translator, SubmissionService $submissionService)
    {
        $this->translator = $translator;
        $this->submissionService = $submissionService;
    }

    public function handle(): void
    {
        $supportedLocales = $this->translator->supportedLocales();
        $currentLocale = $this->resolveLocale($supportedLocales);
        $_SESSION['locale'] = $currentLocale;

        $formMode = 'individual';
        $submissionStatus = [
            'success' => false,
            'message' => null,
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $formMode = $this->normalizeMode($_POST['application_mode'] ?? 'individual');
            $limitMax = (int) env('FORM_RATE_LIMIT_MAX', 5);
            $limitWindow = (int) env('FORM_RATE_LIMIT_WINDOW', 900);
            $limitKey = 'form_submit_' . sha1(client_ip());
            $rateLimit = rate_limit_check($limitKey, max(1, $limitMax), max(60, $limitWindow));

            if (!$rateLimit['allowed']) {
                http_response_code(429);
                $minutes = (int) ceil($rateLimit['retry_after'] / 60);
                $rateLimitMessage = $currentLocale === 'sv'
                    ? "För många försök. Vänta {$minutes} minuter och försök igen."
                    : "Too many attempts. Please wait {$minutes} minutes and try again.";
                $submissionStatus = [
                    'success' => false,
                    'message' => $rateLimitMessage,
                ];
            } elseif (!verify_csrf_token($_POST['_token'] ?? null)) {
                $submissionStatus = [
                    'success' => false,
                    'message' => $this->translator->get($currentLocale, 'form.messages.csrf_failed'),
                ];
            } else {
                $submissionStatus = $this->submissionService->handle($_POST, $currentLocale);
            }
        }

        $translations = $this->translator->all($currentLocale);
        $dir = $this->translator->direction($currentLocale);
        $pageTitle = array_get($translations, 'meta.title', 'Count Us Kurds');
        $pageDescription = array_get($translations, 'meta.description', '');
        $csrfToken = csrf_token();
        $defaultLocale = $this->translator->defaultLocale();

        include resource_path('views/home.php');
    }

    /**
     * Resolve locale from query, session, or Accept-Language header.
     *
     * @param array<string, array{label: string, dir: string}> $supportedLocales
     */
    private function resolveLocale(array $supportedLocales): string
    {
        $default = array_key_first($supportedLocales) ?: 'sv';

        $queryLocale = $this->normalizeLocale($_GET['lang'] ?? null, $supportedLocales);
        if ($queryLocale !== null) {
            return $queryLocale;
        }

        $pathLocale = $this->localeFromPath($supportedLocales);
        if ($pathLocale !== null) {
            return $pathLocale;
        }

        $sessionLocale = $this->normalizeLocale($_SESSION['locale'] ?? null, $supportedLocales);
        if ($sessionLocale !== null) {
            return $sessionLocale;
        }

        $headerLocale = $this->localeFromHeader($supportedLocales);
        if ($headerLocale !== null) {
            return $headerLocale;
        }

        return $default;
    }

    private function localeFromPath(array $supportedLocales): ?string
    {
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';
        if ($requestUri === '') {
            return null;
        }

        $path = parse_url($requestUri, PHP_URL_PATH);
        if ($path === null) {
            return null;
        }

        $segment = trim($path, '/');
        if ($segment === '') {
            return null;
        }

        $parts = explode('/', $segment);
        $candidate = strtolower($parts[0]);

        return $this->normalizeLocale($candidate, $supportedLocales);
    }

    private function normalizeLocale(?string $locale, array $supported): ?string
    {
        if ($locale === null) {
            return null;
        }

        $locale = strtolower(trim($locale));

        return isset($supported[$locale]) ? $locale : null;
    }

    private function localeFromHeader(array $supportedLocales): ?string
    {
        $acceptLanguage = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
        if ($acceptLanguage === '') {
            return null;
        }

        $candidates = explode(',', $acceptLanguage);
        foreach ($candidates as $candidate) {
            $code = strtolower(trim(explode(';', $candidate)[0]));
            if (isset($supportedLocales[$code])) {
                return $code;
            }

            // Also accept language codes without region, e.g. ar-EG -> ar
            $short = substr($code, 0, 2);
            if (isset($supportedLocales[$short])) {
                return $short;
            }
        }

        return null;
    }

    private function normalizeMode(string $mode): string
    {
        return $mode === 'group' ? 'group' : 'individual';
    }
}
