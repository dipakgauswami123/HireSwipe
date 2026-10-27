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

<section class="deck-view">
    <div class="deck-head">
        <div>
            <h1>Discover</h1>
            <p class="lead"><span id="deck-count"><?= count($shown) ?></span> people left<?= $filtered ? ' with these filters' : '' ?></p>
        </div>
        <?php render_filter_sheet('Search by name', $q, $loc, $ind, $locations, $filtered, 'employer-home.php'); ?>
    </div>

    <div class="deck-stage" id="deck" data-yes="Accept">
        <?php foreach ($shown as $i => $emp) { render_candidate_swipe($emp, $i); } ?>
        <div class="deck-empty" id="deck-empty"<?= $shown ? ' hidden' : '' ?>>
            <p class="deck-empty-title"><?= $pool ? 'No employees match these filters.' : 'You have gone through every employee.' ?></p>
            <?php if ($filtered): ?><a class="btn btn-primary" href="employer-home.php">Clear filters</a><?php endif; ?>
        </div>
    </div>

    <?php render_deck_controls('Accept'); ?>
</section>
<?php include __DIR__ . '/includes/shell-end.php'; ?>
