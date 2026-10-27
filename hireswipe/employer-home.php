<?php
require __DIR__ . '/includes/data.php';
require __DIR__ . '/includes/cards.php';
require_role('employer');

$all      = employees();
$accepted = $_SESSION['accepted'] ?? [];
$rejected = $_SESSION['rejected'] ?? [];

/* Swipe decision (accept or reject) */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id       = $_POST['candidate'] ?? '';
    $decision = $_POST['decision'] ?? '';
    $match    = null;
    foreach ($all as $emp) {
        if ($emp['id'] === $id) {
            $match = $emp;
        }
    }
    $decided = in_array($id, $accepted, true) || in_array($id, $rejected, true);

    if ($match && !$decided && $decision === 'accept') {
        $_SESSION['accepted'][] = $id;
        $_SESSION['flash'] = $match['name'] . ' moved to Interview.';
        go('interview.php');
    }
    if ($match && !$decided && $decision === 'reject') {
        $_SESSION['rejected'][] = $id;
    }
    $qs = $_SERVER['QUERY_STRING'] ?? '';
    go('employer-home.php' . ($qs !== '' ? '?' . $qs : ''));
}

/* Search and filters */
$q   = trim($_GET['q'] ?? '');
$loc = $_GET['location'] ?? '';
$ind = $_GET['industry'] ?? '';

$pool = [];
foreach ($all as $emp) {
    if (!in_array($emp['id'], $accepted, true) && !in_array($emp['id'], $rejected, true)) {
        $pool[] = $emp;
    }
}

$locations = array_values(array_unique(array_column($all, 'location')));
sort($locations);

$shown = [];
foreach ($pool as $emp) {
    if ($q !== '' && stripos($emp['name'], $q) === false) {
        continue;
    }
    if ($loc !== '' && $emp['location'] !== $loc) {
        continue;
    }
    if ($ind !== '' && $emp['industry'] !== $ind) {
        continue;
    }
    $shown[] = $emp;
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

<h1>Employees</h1>

<form method="get" class="filters">
    <div class="field">
        <label for="q">Search</label>
        <input type="text" id="q" name="q" placeholder="Search by name" value="<?= e($q) ?>">
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
        <?php if ($filtered): ?><a class="btn btn-ghost" href="employer-home.php">Clear</a><?php endif; ?>
    </div>
</form>

<p class="lead results"><?= count($shown) ?> of <?= count($pool) ?> employees</p>

<?php if ($shown): ?>
    <div class="grid">
        <?php foreach ($shown as $emp) { render_candidate_card($emp); } ?>
    </div>
<?php else: ?>
    <p class="empty">
        <?= $pool ? 'No employees match these filters.' : 'You have gone through every employee.' ?>
        <?php if ($filtered): ?><a href="employer-home.php">Clear filters</a><?php endif; ?>
    </p>
<?php endif; ?>

<?php foreach ($shown as $emp): ?>
    <template id="tpl-<?= e($emp['id']) ?>"><?php render_detail($emp); ?></template>
<?php endforeach; ?>

<dialog id="detail" class="detail-dialog" aria-label="Employee details">
    <div class="dialog-wrap">
    <div class="dialog-inner">
        <div class="dialog-bar">
            <p class="hint">Swipe left to reject, right to accept.</p>
            <button type="button" class="link link-btn" data-close>Close</button>
        </div>
        <div class="swipe-stage">
            <div class="swipe-card" id="swipe-card">
                <span class="stamp stamp-no" aria-hidden="true">Reject</span>
                <span class="stamp stamp-yes" aria-hidden="true">Accept</span>
                <div id="detail-body"></div>
            </div>
        </div>
        <form method="post" action="" id="decision-form" class="swipe-actions">
            <input type="hidden" name="candidate" id="decision-candidate" value="">
            <input type="hidden" name="decision" id="decision-value" value="">
            <button type="button" class="btn btn-reject" data-decide="reject">Reject</button>
            <button type="button" class="btn btn-accept" data-decide="accept">Accept</button>
        </form>
    </div>
    </div>
</dialog>
<?php include __DIR__ . '/includes/shell-end.php'; ?>
