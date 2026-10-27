<?php
/* Layout with a top bar for employers and employees. Expects $title and $active. Optional: $showNotifications. */
$role = $_SESSION['role'] ?? 'employer';
if ($role === 'employee') {
    $navItems = [
        'home'       => ['Home', 'employee-home.php'],
        'job-search' => ['Job Search', 'job-search.php'],
        'applied'    => ['Applied Jobs', 'applied-jobs.php'],
        'interview'  => ['Interview', 'interview.php'],
        'profile'    => ['Profile', 'profile.php'],
    ];
} else {
    $navItems = [
        'home'      => ['Home', 'employer-home.php'],
        'post-job'  => ['Post Job', 'post-job.php'],
        'interview' => ['Interview', 'interview.php'],
        'profile'   => ['Profile', 'profile.php'],
    ];
}
$interviewCount    = count($_SESSION['accepted'] ?? []);
$showNotifications = $showNotifications ?? false;

if ($showNotifications) {
    $notifications = $role === 'employee' ? employee_notifications() : notifications();
    if ($interviewCount > 0) {
        array_unshift($notifications, [
            'type' => 'interviews',
            'text' => $role === 'employee'
                ? $interviewCount . ($interviewCount === 1 ? ' company is' : ' companies are') . ' waiting in Interview.'
                : $interviewCount . ($interviewCount === 1 ? ' employee is' : ' employees are') . ' waiting in Interview.',
            'time' => 'Now',
        ]);
    }
    $wanted        = notification_prefs();
    $notifications = array_values(array_filter($notifications, function ($n) use ($wanted) {
        return in_array($n['type'], $wanted, true);
    }));
    $notifCount = count($notifications);
}
include __DIR__ . '/head.php';
?>
<header class="appbar">
    <div class="appbar-inner">
        <div class="appbar-left">
            <div class="dropdown">
                <button type="button" class="menu-btn" id="menu-btn" aria-expanded="false" aria-controls="menu-panel" data-dropdown>
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M3 5h14M3 10h14M3 15h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    <span>Menu</span>
                </button>
                <nav class="dropdown-panel menu-panel" id="menu-panel" aria-label="Main" hidden>
                    <?php foreach ($navItems as $key => $item): ?>
                        <a class="menu-link<?= $key === $active ? ' is-active' : '' ?>" href="<?= e($item[1]) ?>"<?= $key === $active ? ' aria-current="page"' : '' ?>>
                            <?= e($item[0]) ?>
                            <?php if ($key === 'interview' && $interviewCount > 0): ?><span class="badge"><?= (int) $interviewCount ?></span><?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                    <a class="menu-link menu-logout" href="logout.php">Log out</a>
                </nav>
            </div>
            <?php include __DIR__ . '/brand.php'; ?>
            <nav class="nav-inline" aria-label="Primary">
                <?php foreach ($navItems as $key => $item): ?>
                    <a class="nav-link<?= $key === $active ? ' is-active' : '' ?>" href="<?= e($item[1]) ?>"<?= $key === $active ? ' aria-current="page"' : '' ?>>
                        <?= e($item[0]) ?>
                        <?php if ($key === 'interview' && $interviewCount > 0): ?><span class="badge"><?= (int) $interviewCount ?></span><?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </nav>
        </div>

        <div class="appbar-right">
        <a class="nav-logout" href="logout.php">Log out</a>
        <?php if ($showNotifications): ?>
            <div class="dropdown dropdown-right">
                <button type="button" class="icon-btn" id="notif-btn" aria-expanded="false" aria-controls="notif-panel" aria-label="Notifications, <?= (int) $notifCount ?> new" data-dropdown>
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 17h12l-1.5-2V10a4.5 4.5 0 0 0-9 0v5L6 17Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M10 20a2 2 0 0 0 4 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                    <?php if ($notifCount > 0): ?><span class="badge-dot" aria-hidden="true"><?= (int) $notifCount ?></span><?php endif; ?>
                </button>
                <section class="dropdown-panel notif-panel" id="notif-panel" aria-label="Notifications" hidden>
                    <h2 class="notif-title">Notifications</h2>
                    <?php if ($notifications): ?>
                        <ul class="notif-list">
                            <?php foreach ($notifications as $n): ?>
                                <li><p><?= e($n['text']) ?></p><span><?= e($n['time']) ?></span></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="notif-empty">No notifications for the types you chose.</p>
                    <?php endif; ?>
                    <a class="notif-edit" href="profile.php?view=notifications">Edit notification settings</a>
                </section>
            </div>
        <?php endif; ?>
        </div>
    </div>
</header>
<main class="content">
