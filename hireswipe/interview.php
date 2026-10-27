<?php
require __DIR__ . '/includes/data.php';
require __DIR__ . '/includes/cards.php';

$role = $_SESSION['role'] ?? '';
if ($role !== 'employer' && $role !== 'employee') {
    go('index.php');
}
require_role($role);

/* Employers interview employees. Employees interview for jobs. */
$byId = [];
if ($role === 'employer') {
    foreach (employees() as $emp) {
        $byId[$emp['id']] = $emp;
    }
} else {
    foreach (job_pool() as $job) {
        $byId[$job['id']] = $job;
    }
}
$who = function ($item) use ($role) {
    return $role === 'employer'
        ? 'Interview with ' . $item['name']
        : 'Interview for ' . $item['title'] . ' at ' . $item['company'];
};

$tabs = [
    'today'       => 'Scheduled today',
    'scheduled'   => 'Scheduled',
    'unscheduled' => 'Not scheduled',
    'done'        => 'Done',
];
$tab = $_GET['tab'] ?? 'today';
if (!isset($tabs[$tab])) {
    $tab = 'today';
}

$errors = [];
$old    = [];

/* Schedule, reschedule or mark an interview as done */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id     = $_POST['candidate'] ?? '';
    $action = $_POST['action'] ?? '';
    $iv     = $_SESSION['interviews'][$id] ?? null;

    if (!isset($byId[$id]) || !in_array($id, $_SESSION['accepted'] ?? [], true)) {
        go('interview.php?tab=' . $tab);
    }

    if ($action === 'schedule') {
        $date = trim($_POST['date'] ?? '');
        $time = trim($_POST['time'] ?? '');
        $old[$id] = ['date' => $date, 'time' => $time];

        $d = DateTime::createFromFormat('!Y-m-d', $date);
        $t = DateTime::createFromFormat('!H:i', $time);

        if (!$d || $d->format('Y-m-d') !== $date) {
            $errors[$id] = 'Choose a date.';
        } elseif (!$t || $t->format('H:i') !== $time) {
            $errors[$id] = 'Choose a time.';
        } elseif ($date < date('Y-m-d')) {
            $errors[$id] = 'Pick today or a later date.';
        } elseif ($date === date('Y-m-d') && $time <= date('H:i')) {
            $errors[$id] = 'Pick a time later than now.';
        } else {
            $_SESSION['interviews'][$id] = ['date' => $date, 'time' => $time, 'done' => false];
            $_SESSION['flash'] = $who($byId[$id]) . ' set for ' . day_label($date) . ' at ' . pretty_time($time) . '.';
            go('interview.php?tab=' . ($date === date('Y-m-d') ? 'today' : 'scheduled'));
        }
    } elseif ($action === 'done') {
        if (interview_status($iv) === 'today' || interview_status($iv) === 'scheduled') {
            $_SESSION['interviews'][$id]['done'] = true;
            $_SESSION['flash'] = $who($byId[$id]) . ' marked as done.';
            go('interview.php?tab=done');
        }
        go('interview.php?tab=' . $tab);
    } else {
        go('interview.php?tab=' . $tab);
    }
}

/* Sort the accepted items into the four tabs */
$buckets = ['today' => [], 'scheduled' => [], 'unscheduled' => [], 'done' => []];
foreach (($_SESSION['accepted'] ?? []) as $id) {
    if (!isset($byId[$id])) {
        continue;
    }
    $iv = $_SESSION['interviews'][$id] ?? null;
    $buckets[interview_status($iv)][] = ['item' => $byId[$id], 'iv' => $iv];
}
$bySlot = function ($a, $b) {
    return strcmp($a['iv']['date'] . $a['iv']['time'], $b['iv']['date'] . $b['iv']['time']);
};
usort($buckets['today'], $bySlot);
usort($buckets['scheduled'], $bySlot);
usort($buckets['done'], function ($a, $b) use ($bySlot) { return $bySlot($b, $a); });

if ($role === 'employer') {
    $empty = [
        'today'       => 'No interviews are scheduled for today.',
        'scheduled'   => 'No upcoming interviews are scheduled.',
        'unscheduled' => 'Everyone you accepted has a time. Accept more employees on <a href="employer-home.php">Home</a>.',
        'done'        => 'No interviews are done yet.',
    ];
    $totalLabel = 'accepted';
} else {
    $empty = [
        'today'       => 'No interviews are scheduled for today.',
        'scheduled'   => 'No upcoming interviews are scheduled.',
        'unscheduled' => 'Every request you confirmed has a time. Confirm more requests on <a href="employee-home.php">Home</a>.',
        'done'        => 'No interviews are done yet.',
    ];
    $totalLabel = 'confirmed';
}
$total   = count($_SESSION['accepted'] ?? []);
$notice  = flash();
$title   = 'Interview';
$active  = 'interview';
include __DIR__ . '/includes/shell-start.php';
?>
<?php if ($notice): ?><p class="banner banner-ok" role="status"><?= e($notice) ?></p><?php endif; ?>

<h1>Interview</h1>
<p class="lead results"><?= (int) $total ?> <?= e($totalLabel) ?></p>

<nav class="tabs" aria-label="Interview sections">
    <?php foreach ($tabs as $key => $label): ?>
        <a class="tab" href="interview.php?tab=<?= e($key) ?>"<?= $key === $tab ? ' aria-current="page"' : '' ?>><?= e($label) ?> (<?= count($buckets[$key]) ?>)</a>
    <?php endforeach; ?>
</nav>

<?php if ($buckets[$tab]): ?>
    <div class="grid">
        <?php foreach ($buckets[$tab] as $row) {
            $id = $row['item']['id'];
            if ($role === 'employer') {
                render_interview_card($row['item'], $row['iv'], $tab, $tab, $errors[$id] ?? '', $old[$id] ?? []);
            } else {
                render_job_interview_card($row['item'], $row['iv'], $tab, $tab, $errors[$id] ?? '', $old[$id] ?? []);
            }
        } ?>
    </div>
<?php else: ?>
    <p class="empty"><?= $empty[$tab] ?></p>
<?php endif; ?>
<?php include __DIR__ . '/includes/shell-end.php'; ?>
