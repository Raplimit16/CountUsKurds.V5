<?php
declare(strict_types=1);

namespace CountUsKurds\Support;

use RuntimeException;

class Translator
{
    private string $defaultLocale;

    /**
     * @var array<string, array{label: string, dir: 'ltr'|'rtl'}>
     */
    private array $supportedLocales;

    /**
     * @var array<string, array>
     */
    private array $loaded = [];

    public function __construct(string $defaultLocale, array $supportedLocales)
    {
        $this->defaultLocale = $defaultLocale;
        $this->supportedLocales = $supportedLocales;
    }

    public function defaultLocale(): string
    {
        return $this->defaultLocale;
    }

    public function get(string $locale, string $key, array $replace = []): string
    {
        $translations = $this->localeData($locale);
        $fallback = $this->localeData($this->defaultLocale);

        $value = array_get($translations, $key, array_get($fallback, $key, $key));

        foreach ($replace as $needle => $replacement) {
            $value = str_replace(':' . $needle, (string) $replacement, $value);
        }

        return (string) $value;
    }

    public function all(string $locale): array
    {
        return $this->localeData($locale);
    }

    public function supportedLocales(): array
    {
        return $this->supportedLocales;
    }

    public function direction(string $locale): string
    {
        return $this->supportedLocales[$locale]['dir'] ?? $this->supportedLocales[$this->defaultLocale]['dir'] ?? 'ltr';
    }

    private function localeData(string $locale): array
    {
        if (isset($this->loaded[$locale])) {
            return $this->loaded[$locale];
        }

        $path = resource_path('lang' . DIRECTORY_SEPARATOR . $locale . '.php');
        if (!is_file($path)) {
            if ($locale === $this->defaultLocale) {
                throw new RuntimeException("Missing default translation file for [$locale].");
            }
            return $this->localeData($this->defaultLocale);
        }

        /** @var array $translations */
        $translations = require $path;
        $this->loaded[$locale] = $translations;

        return $translations;
    }
}
