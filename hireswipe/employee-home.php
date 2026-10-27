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

<section class="deck-view">
    <div class="deck-head">
        <div>
            <h1>Requests</h1>
            <p class="lead"><span id="deck-count"><?= count($shown) ?></span> requests left<?= $filtered ? ' with these filters' : '' ?></p>
        </div>
        <?php render_filter_sheet('Search by job or company', $q, $loc, $ind, $locations, $filtered, 'employee-home.php'); ?>
    </div>

    <div class="deck-stage" id="deck" data-yes="Confirm">
        <?php foreach ($shown as $i => $j) { render_request_swipe($j, $i); } ?>
        <div class="deck-empty" id="deck-empty"<?= $shown ? ' hidden' : '' ?>>
            <p class="deck-empty-title"><?= $pool ? 'No requests match these filters.' : 'You have gone through every request.' ?></p>
            <?php if ($filtered): ?><a class="btn btn-primary" href="employee-home.php">Clear filters</a><?php endif; ?>
        </div>
    </div>

    <?php render_deck_controls('Confirm'); ?>
</section>
<?php include __DIR__ . '/includes/shell-end.php'; ?>
