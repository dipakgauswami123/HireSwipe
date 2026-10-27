<?php
require __DIR__ . '/includes/data.php';
require __DIR__ . '/includes/cards.php';
require_role('employee');

/* Apply to a job */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['job'] ?? '';
    $j  = job_by_id($id);
    $isOpen = false;
    foreach (open_jobs() as $o) {
        if ($o['id'] === $id) {
            $isOpen = true;
        }
    }
    if ($j && $isOpen) {
        $_SESSION['applied'][$id] = date('Y-m-d');
        $_SESSION['flash'] = 'Applied to ' . $j['title'] . ' at ' . $j['company'] . '.';
    }
    $qs = $_SERVER['QUERY_STRING'] ?? '';
    go('job-search.php' . ($qs !== '' ? '?' . $qs : ''));
}

/* Search and filters */
$q   = trim($_GET['q'] ?? '');
$loc = $_GET['location'] ?? '';
$ind = $_GET['industry'] ?? '';

$pool      = open_jobs();
$locations = array_values(array_unique(array_column(job_pool(), 'location')));
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

$notice = flash();
$title  = 'Job Search';
$active = 'job-search';
include __DIR__ . '/includes/shell-start.php';
?>
<?php if ($notice): ?><p class="banner banner-ok" role="status"><?= e($notice) ?> <a href="applied-jobs.php">See Applied Jobs</a></p><?php endif; ?>

<h1>Job Search</h1>

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
        <?php if ($filtered): ?><a class="btn btn-ghost" href="job-search.php">Clear</a><?php endif; ?>
    </div>
</form>

<p class="lead results"><?= count($shown) ?> of <?= count($pool) ?> jobs</p>

<?php if ($shown): ?>
    <div class="grid">
        <?php foreach ($shown as $j) { render_open_job_card($j); } ?>
    </div>
<?php else: ?>
    <p class="empty">
        <?= $pool ? 'No jobs match these filters.' : 'You have applied to every open job.' ?>
        <?php if ($filtered): ?><a href="job-search.php">Clear filters</a><?php endif; ?>
    </p>
<?php endif; ?>
<?php include __DIR__ . '/includes/shell-end.php'; ?>
