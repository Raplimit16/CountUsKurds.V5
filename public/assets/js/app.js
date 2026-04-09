(() => {
    const docEl = document.documentElement;
    if (docEl && docEl.classList.contains('no-js')) {
        docEl.classList.remove('no-js');
    }
    if (docEl && !docEl.classList.contains('has-js')) {
        docEl.classList.add('has-js');
    }

    const storageKey = 'cuk:last-locale';

    const parseSupportedLocales = () => {
        if (!docEl) {
            return [];
        }
        const raw = docEl.dataset.supportedLocales;
        if (!raw) {
            return [];
        }
        try {
            const parsed = JSON.parse(raw);
            return Array.isArray(parsed)
                ? parsed.map((code) => code.toString().toLowerCase())
                : [];
        } catch (error) {
            return [];
        }
    };

    const supportedLocales = parseSupportedLocales();
    const defaultLocale = (docEl?.dataset.defaultLocale || 'en').toLowerCase();

    const persistLocale = (locale) => {
        if (!locale) {
            return;
        }
        try {
            localStorage.setItem(storageKey, locale);
        } catch (error) {
            /* ignore storage failures */
        }
    };

    const resolveDeviceLocale = () => {
        const candidates = [];
        if (Array.isArray(navigator.languages) && navigator.languages.length > 0) {
            candidates.push(...navigator.languages);
        } else if (navigator.language) {
            candidates.push(navigator.language);
        } else if (navigator.userLanguage) {
            candidates.push(navigator.userLanguage);
        }

        const expanded = candidates.flatMap((entry) => {
            const value = (entry || '').toLowerCase();
            if (!value) {
                return [];
            }
            const base = value.split('-')[0];
            return base && base !== value ? [value, base] : [value];
        });

        const match = expanded.find((code) => supportedLocales.includes(code));
        if (match) {
            return match;
        }

        if (supportedLocales.includes(defaultLocale)) {
            return defaultLocale;
        }

        return supportedLocales[0] || 'en';
    };

    const ensureLocaleFromDevice = () => {
        if (!supportedLocales.length) {
            return;
        }

        const url = new URL(window.location.href);
        const pathSegments = url.pathname.split('/').filter(Boolean);

        if (pathSegments.length > 0) {
            const firstSegment = pathSegments[0].toLowerCase();
            if (supportedLocales.includes(firstSegment)) {
                persistLocale(firstSegment);
            }
            return;
        }

        const langParam = url.searchParams.get('lang');
        if (langParam) {
            const normalized = langParam.toLowerCase();
            if (supportedLocales.includes(normalized)) {
                persistLocale(normalized);
                if (url.pathname === '/' || url.pathname === '') {
                    window.location.replace(`/${normalized}`);
                }
            }
            return;
        }

        let stored = null;
        try {
            stored = localStorage.getItem(storageKey);
        } catch (error) {
            stored = null;
        }
        if (stored && supportedLocales.includes(stored)) {
            window.location.replace(`/${stored}`);
            return;
        }

        const deviceLocale = resolveDeviceLocale();
        persistLocale(deviceLocale);
        window.location.replace(`/${deviceLocale}`);
    };

    ensureLocaleFromDevice();

    const navToggle = document.querySelector('.nav-toggle');
    const nav = document.getElementById('main-nav');

    let lastDesktopState = null;

    const syncNavState = () => {
        if (!nav) {
            return;
        }
        const desktop = window.matchMedia('(min-width: 640px)').matches;
        if (lastDesktopState === desktop) {
            if (desktop) {
                nav.setAttribute('data-collapsed', 'false');
                if (navToggle) {
                    navToggle.setAttribute('aria-expanded', 'true');
                }
            }
            return;
        }
        lastDesktopState = desktop;

        if (desktop) {
            nav.setAttribute('data-collapsed', 'false');
            if (navToggle) {
                navToggle.setAttribute('aria-expanded', 'true');
                navToggle.setAttribute('hidden', 'hidden');
            }
        } else {
            if (navToggle) {
                navToggle.removeAttribute('hidden');
                nav.setAttribute('data-collapsed', 'true');
                navToggle.setAttribute('aria-expanded', 'false');
            } else {
                nav.setAttribute('data-collapsed', 'false');
            }
        }
    };

    if (navToggle && nav) {
        nav.setAttribute('data-collapsed', nav.getAttribute('data-collapsed') ?? 'true');
        navToggle.setAttribute('aria-expanded', nav.getAttribute('data-collapsed') === 'false' ? 'true' : 'false');

        navToggle.addEventListener('click', () => {
            const isCollapsed = nav.getAttribute('data-collapsed') === 'true';
            nav.setAttribute('data-collapsed', String(!isCollapsed));
            navToggle.setAttribute('aria-expanded', String(isCollapsed));
        });
    }

    syncNavState();
    window.addEventListener('resize', syncNavState);

    const tabs = document.querySelectorAll('.form-tab');
    const modeInput = document.getElementById('application_mode');
    const modes = document.querySelectorAll('.mode');

    tabs.forEach((tab) => {
        tab.addEventListener('click', () => {
            const target = tab.getAttribute('data-target');
            if (!target || !modeInput) return;

            modeInput.value = target;
            tabs.forEach((btn) => btn.classList.toggle('is-active', btn === tab));
            modes.forEach((section) => {
                const match = section.getAttribute('data-mode') === target;
                section.classList.toggle('is-hidden', !match);

                section.querySelectorAll('[data-required="true"]').forEach((el) => {
                    el.toggleAttribute('required', match);
                });
            });
        });
    });

    const navigateToLocale = (locale) => {
        if (!locale) {
            return;
        }
        const normalized = locale.toString().trim().toLowerCase();
        if (!normalized) {
            return;
        }
        const current = document.documentElement.getAttribute('lang');
        if (current && current.toLowerCase() === normalized) {
            return;
        }
        const safeLocale = normalized.replace(/^\/+/, '');
        persistLocale(safeLocale);
        const target = `/${safeLocale}`;
        window.location.assign(target);
    };

    const autoSubmitLanguage = (form) => {
        if (!form) return;
        const select = form.querySelector('[data-lang-select]');
        if (!select) return;
        select.addEventListener('change', () => navigateToLocale(select.value));
    };

    autoSubmitLanguage(document.getElementById('language-form'));

    const langToggle = document.querySelector('[data-lang-toggle]');
    const langOverlay = document.querySelector('[data-lang-overlay]');
    const langClosers = document.querySelectorAll('[data-lang-close]');
    const body = document.body;

    const setLanguageOverlayState = (open) => {
        if (!langOverlay || !langToggle) {
            return;
        }
        langOverlay.classList.toggle('is-visible', open);
        langOverlay.setAttribute('aria-hidden', String(!open));
        langToggle.setAttribute('aria-expanded', String(open));
        if (body) {
            body.classList.toggle('language-overlay-open', open);
        }
        if (open) {
            const focusTarget = langOverlay.querySelector('.language-overlay__list button.is-active')
                || langOverlay.querySelector('.language-overlay__list button');
            if (focusTarget) {
                requestAnimationFrame(() => focusTarget.focus());
            }
        } else {
            langToggle.focus();
        }
    };

    if (langToggle && langOverlay) {
        langToggle.addEventListener('click', () => {
            const nextState = !langOverlay.classList.contains('is-visible');
            setLanguageOverlayState(nextState);
        });

        langClosers.forEach((el) => {
            el.addEventListener('click', () => setLanguageOverlayState(false));
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && langOverlay.classList.contains('is-visible')) {
                setLanguageOverlayState(false);
            }
        });

        const desktopMedia = window.matchMedia('(min-width: 640px)');
        const handleViewportChange = (event) => {
            if (event.matches) {
                setLanguageOverlayState(false);
            }
        };
        desktopMedia.addEventListener('change', handleViewportChange);
    }

    document.querySelectorAll('[data-lang-choice]').forEach((button) => {
        button.addEventListener('click', () => {
            navigateToLocale(button.getAttribute('data-lang-choice'));
        });
    });
})();
