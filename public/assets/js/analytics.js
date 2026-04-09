(function () {
    var root = document.documentElement;
    if (!root) {
        return;
    }

    var measurementId = root.getAttribute('data-ga-measurement-id');
    if (!measurementId) {
        return;
    }

    window.dataLayer = window.dataLayer || [];
    window.gtag = window.gtag || function () {
        window.dataLayer.push(arguments);
    };

    window.gtag('js', new Date());
    window.gtag('config', measurementId, {
        anonymize_ip: true
    });
})();
