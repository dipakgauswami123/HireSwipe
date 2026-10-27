// Dropdowns: the Menu button and the notification bell.
(function () {
    var toggles = document.querySelectorAll('[data-dropdown]');
    if (!toggles.length) { return; }

    function panelFor(btn) { return document.getElementById(btn.getAttribute('aria-controls')); }

    function close(btn) {
        btn.setAttribute('aria-expanded', 'false');
        panelFor(btn).hidden = true;
    }

    function closeOthers(except) {
        toggles.forEach(function (btn) { if (btn !== except) { close(btn); } });
    }

    toggles.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var wasOpen = btn.getAttribute('aria-expanded') === 'true';
            closeOthers(btn);
            if (wasOpen) {
                close(btn);
            } else {
                btn.setAttribute('aria-expanded', 'true');
                panelFor(btn).hidden = false;
            }
        });
    });

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.dropdown')) { closeOthers(null); }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Escape') { return; }
        toggles.forEach(function (btn) {
            if (btn.getAttribute('aria-expanded') === 'true') { close(btn); btn.focus(); }
        });
    });
})();
