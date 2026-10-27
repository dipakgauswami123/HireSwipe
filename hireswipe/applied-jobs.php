<?php
require __DIR__ . '/includes/data.php';
require __DIR__ . '/includes/cards.php';
require_role('employee');

/* Newest application first */
$rows = [];
foreach (array_reverse($_SESSION['applied'] ?? [], true) as $id => $date) {
    $j = job_by_id($id);
    if ($j) {
        $rows[] = ['job' => $j, 'date' => $date];
    }
}
usort($rows, function ($a, $b) { return strcmp($b['date'], $a['date']); });

$notice = flash();
$title  = 'Applied Jobs';
$active = 'applied';
include __DIR__ . '/includes/shell-start.php';
?>
<?php if ($notice): ?><p class="banner banner-ok" role="status"><?= e($notice) ?></p><?php endif; ?>

<h1>Applied Jobs</h1>
<p class="lead results"><?= count($rows) ?> applied</p>

<?php if ($rows): ?>
    <div class="grid">
        <?php foreach ($rows as $r) { render_applied_card($r['job'], $r['date']); } ?>
    </div>
<?php else: ?>
    <p class="empty">You have not applied to any job yet. Find one in <a href="job-search.php">Job Search</a>.</p>
<?php endif; ?>
<?php include __DIR__ . '/includes/shell-end.php'; ?>
