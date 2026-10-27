<?php
require __DIR__ . '/includes/data.php';
require __DIR__ . '/includes/cards.php';
require_role('employee');

$accepted = $_SESSION['accepted'] ?? [];
$rejected = $_SESSION['rejected'] ?? [];

/* Swipe decision (confirm or reject) */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id       = $_POST['candidate'] ?? '';
    $decision = $_POST['decision'] ?? '';
    $match    = in_array($id, request_ids(), true) ? job_by_id($id) : null;
    $decided  = in_array($id, $accepted, true) || in_array($id, $rejected, true);

    if ($match && !$decided && $decision === 'accept') {
        $_SESSION['accepted'][] = $id;
        $_SESSION['flash'] = $match['title'] . ' at ' . $match['company'] . ' moved to Interview.';
        go('interview.php?tab=unscheduled');
    }
    if ($match && !$decided && $decision === 'reject') {
        $_SESSION['rejected'][] = $id;
    }
    $qs = $_SERVER['QUERY_STRING'] ?? '';
    go('employee-home.php' . ($qs !== '' ? '?' . $qs : ''));
}

/* Requests that have not been decided yet */
$all = [];
foreach (request_ids() as $id) {
    $j = job_by_id($id);
    if ($j) {
        $all[] = $j;
    }
}
$pool = [];
foreach ($all as $j) {
    if (!in_array($j['id'], $accepted, true) && !in_array($j['id'], $rejected, true)) {
        $pool[] = $j;
    }
}

/* Search and filters */
$q   = trim($_GET['q'] ?? '');
$loc = $_GET['location'] ?? '';
$ind = $_GET['industry'] ?? '';

$locations = array_values(array_unique(array_column($all, 'location')));
sort($locations);

$shown = [];
foreach ($pool as $j) {
    if ($q !== '' && stripos($j['title'], $q) === false && stripos($j['company'], $q) === false) {
        continue;
    }
    if ($loc !== '' && $j['location'] !== $loc) {
        continue;
    }
    if ($ind !== '' && $j['industry'] !== $ind) {
        continue;
    }
    $shown[] = $j;
}
$filtered = ($q !== '' || $loc !== '' || $ind !== '');

$notice  = flash();
$title   = 'Home';
$active  = 'home';
$scripts = ['assets/js/swipe.js'];
$showNotifications = true;
include __DIR__ . '/includes/shell-start.php';
?>
<?php if ($notice): ?><p class="banner banner-ok" role="status"><?= e($notice) ?></p><?php endif; ?>

<h1>Requests</h1>

<form method="get" class="filters">
    <div class="field">
        <label for="q">Search</label>
        <input type="text" id="q" name="q" placeholder="Search by job or company" value="<?= e($q) ?>">
    </div>
    <div class="field">
        <label for="location">Location</label>
        <select id="location" name="location">
            <option value="">All locations</option>
            <?php foreach ($locations as $l): ?>
                <option value="<?= e($l) ?>"<?= $loc === $l ? ' selected' : '' ?>><?= e($l) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="field">
        <label for="industry">Industry</label>
        <select id="industry" name="industry">
            <option value="">All industries</option>
            <?php foreach (INDUSTRIES as $i): ?>
                <option value="<?= e($i) ?>"<?= $ind === $i ? ' selected' : '' ?>><?= e($i) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="filter-actions">
        <button type="submit" class="btn btn-primary">Search</button>
        <?php if ($filtered): ?><a class="btn btn-ghost" href="employee-home.php">Clear</a><?php endif; ?>
    </div>
</form>

<p class="lead results"><?= count($shown) ?> of <?= count($pool) ?> requests</p>

<?php if ($shown): ?>
    <div class="grid">
        <?php foreach ($shown as $j) { render_request_card($j); } ?>
    </div>
<?php else: ?>
    <p class="empty">
        <?= $pool ? 'No requests match these filters.' : 'You have gone through every request.' ?>
        <?php if ($filtered): ?><a href="employee-home.php">Clear filters</a><?php endif; ?>
    </p>
<?php endif; ?>

<?php foreach ($shown as $j): ?>
    <template id="tpl-<?= e($j['id']) ?>"><?php render_request_detail($j); ?></template>
<?php endforeach; ?>

<dialog id="detail" class="detail-dialog" aria-label="Request details">
    <div class="dialog-wrap">
    <div class="dialog-inner">
        <div class="dialog-bar">
            <p class="hint">Swipe left to reject, right to confirm.</p>
            <button type="button" class="link link-btn" data-close>Close</button>
        </div>
        <div class="swipe-stage">
            <div class="swipe-card" id="swipe-card">
                <span class="stamp stamp-no" aria-hidden="true">Reject</span>
                <span class="stamp stamp-yes" aria-hidden="true">Confirm</span>
                <div id="detail-body"></div>
            </div>
        </div>
        <form method="post" action="" id="decision-form" class="swipe-actions">
            <input type="hidden" name="candidate" id="decision-candidate" value="">
            <input type="hidden" name="decision" id="decision-value" value="">
            <button type="button" class="btn btn-reject" data-decide="reject">Reject</button>
            <button type="button" class="btn btn-accept" data-decide="accept">Confirm</button>
        </form>
    </div>
    </div>
</dialog>
<?php include __DIR__ . '/includes/shell-end.php'; ?>
