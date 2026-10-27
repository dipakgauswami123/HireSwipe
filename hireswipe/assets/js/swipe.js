// Detail view with swipe to accept or reject. Buttons and arrow keys play the same animation.
(function () {
    var dialog = document.getElementById('detail');
    if (!dialog || typeof dialog.showModal !== 'function') { return; }

    var card = document.getElementById('swipe-card');
    var body = document.getElementById('detail-body');
    var form = document.getElementById('decision-form');
    var candidate = document.getElementById('decision-candidate');
    var decision = document.getElementById('decision-value');
    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    var THRESHOLD = 110;
    var busy = false;
    var startX = null;
    var dx = 0;

    function setStamps(yes, no) {
        card.style.setProperty('--yes', yes);
        card.style.setProperty('--no', no);
    }

    function reset() {
        busy = false;
        startX = null;
        dx = 0;
        card.classList.remove('is-dragging');
        card.style.transform = '';
        card.style.opacity = '';
        setStamps(0, 0);
    }

    function openDetail(id) {
        var tpl = document.getElementById('tpl-' + id);
        if (!tpl) { return; }
        body.replaceChildren(tpl.content.cloneNode(true));
        candidate.value = id;
        reset();
        dialog.showModal();
    }

    function decide(direction) {
        if (busy) { return; }
        busy = true;
        var sign = direction === 'accept' ? 1 : -1;
        card.classList.remove('is-dragging');
        setStamps(sign > 0 ? 1 : 0, sign < 0 ? 1 : 0);
        card.style.transform = 'translateX(' + (sign * 130) + '%) rotate(' + (sign * 16) + 'deg)';
        card.style.opacity = '0';
        decision.value = direction;
        window.setTimeout(function () { form.submit(); }, reduceMotion ? 0 : 400);
    }

    document.querySelectorAll('[data-open]').forEach(function (btn) {
        btn.addEventListener('click', function () { openDetail(btn.dataset.open); });
    });
    document.querySelectorAll('[data-decide]').forEach(function (btn) {
        btn.addEventListener('click', function () { decide(btn.dataset.decide); });
    });
    document.querySelectorAll('[data-close]').forEach(function (btn) {
        btn.addEventListener('click', function () { dialog.close(); });
    });

    // Clicking the dimmed backdrop closes the dialog.
    dialog.addEventListener('click', function (e) {
        if (e.target === dialog || e.target.classList.contains('dialog-wrap')) { dialog.close(); }
    });
    dialog.addEventListener('close', reset);

    // Keyboard alternative to swiping.
    dialog.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowRight') { decide('accept'); }
        if (e.key === 'ArrowLeft') { decide('reject'); }
    });

    // Drag to swipe.
    card.addEventListener('pointerdown', function (e) {
        if (busy || e.button > 0) { return; }
        startX = e.clientX;
        dx = 0;
        card.setPointerCapture(e.pointerId);
        card.classList.add('is-dragging');
    });
    card.addEventListener('pointermove', function (e) {
        if (startX === null) { return; }
        dx = e.clientX - startX;
        card.style.transform = 'translateX(' + dx + 'px) rotate(' + (dx / 18) + 'deg)';
        var progress = Math.min(Math.abs(dx) / THRESHOLD, 1);
        setStamps(dx > 0 ? progress : 0, dx < 0 ? progress : 0);
    });
    function endDrag() {
        if (startX === null) { return; }
        startX = null;
        card.classList.remove('is-dragging');
        if (dx > THRESHOLD) { decide('accept'); }
        else if (dx < -THRESHOLD) { decide('reject'); }
        else { card.style.transform = ''; setStamps(0, 0); }
    }
    card.addEventListener('pointerup', endDrag);
    card.addEventListener('pointercancel', endDrag);
})();
