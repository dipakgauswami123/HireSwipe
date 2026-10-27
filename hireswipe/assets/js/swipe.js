// Card deck: drag the top card, press the buttons or use the arrow keys.
// Pass is saved in the background so the next card appears at once; accept goes to Interview.
(function () {
    var deck = document.getElementById('deck');
    if (!deck) { return; }

    var form = document.getElementById('decision-form');
    var candidate = document.getElementById('decision-candidate');
    var decision = document.getElementById('decision-value');
    var counter = document.getElementById('deck-count');
    var empty = document.getElementById('deck-empty');
    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var THRESHOLD = 110;

    var busy = false, startX = null, startY = null, dx = 0;

    function cards() { return Array.prototype.slice.call(deck.querySelectorAll('.swipe-card')); }
    function top() { return cards()[0] || null; }

    function layout() {
        var list = cards();
        list.forEach(function (c, i) { c.style.setProperty('--i', Math.min(i, 3)); });
        if (counter) { counter.textContent = list.length; }
        if (empty) { empty.hidden = list.length > 0; }
        document.querySelectorAll('[data-decide]').forEach(function (b) { b.disabled = list.length === 0; });
    }

    function setStamps(card, yes, no) {
        card.style.setProperty('--yes', yes);
        card.style.setProperty('--no', no);
    }

    function decide(direction) {
        var card = top();
        if (busy || !card) { return; }
        busy = true;
        var sign = direction === 'accept' ? 1 : -1;
        card.classList.remove('is-dragging');
        card.classList.add('is-leaving');
        setStamps(card, sign > 0 ? 1 : 0, sign < 0 ? 1 : 0);
        card.style.transform = 'translate(' + (sign * 140) + '%, -6%) rotate(' + (sign * 18) + 'deg)';
        card.style.opacity = '0';
        candidate.value = card.dataset.id;
        decision.value = direction;

        window.setTimeout(function () {
            if (direction === 'accept') { form.submit(); return; }
            var body = new FormData(form);
            fetch(window.location.href, { method: 'POST', body: body, credentials: 'same-origin' })
                .catch(function () { /* the next page load shows the real state */ });
            card.remove();
            busy = false;
            layout();
        }, reduceMotion ? 0 : 380);
    }

    document.querySelectorAll('[data-decide]').forEach(function (btn) {
        btn.addEventListener('click', function () { decide(btn.dataset.decide); });
    });

    document.addEventListener('keydown', function (e) {
        var tag = (e.target.tagName || '').toLowerCase();
        if (tag === 'input' || tag === 'select' || tag === 'textarea') { return; }
        if (e.key === 'ArrowRight') { decide('accept'); }
        if (e.key === 'ArrowLeft') { decide('reject'); }
    });

    // Drag the top card.
    deck.addEventListener('pointerdown', function (e) {
        var card = top();
        if (!card || busy || e.button > 0 || !card.contains(e.target)) { return; }
        startX = e.clientX; startY = e.clientY; dx = 0;
        card.setPointerCapture(e.pointerId);
        card.classList.add('is-dragging');
    });
    deck.addEventListener('pointermove', function (e) {
        var card = top();
        if (startX === null || !card) { return; }
        dx = e.clientX - startX;
        var dy = (e.clientY - startY) * 0.15;
        card.style.transform = 'translate(' + dx + 'px, ' + dy + 'px) rotate(' + (dx / 20) + 'deg)';
        var p = Math.min(Math.abs(dx) / THRESHOLD, 1);
        setStamps(card, dx > 0 ? p : 0, dx < 0 ? p : 0);
    });
    function endDrag() {
        var card = top();
        if (startX === null || !card) { return; }
        startX = null;
        card.classList.remove('is-dragging');
        if (dx > THRESHOLD) { decide('accept'); }
        else if (dx < -THRESHOLD) { decide('reject'); }
        else { card.style.transform = ''; setStamps(card, 0, 0); }
    }
    deck.addEventListener('pointerup', endDrag);
    deck.addEventListener('pointercancel', endDrag);

    layout();
})();
