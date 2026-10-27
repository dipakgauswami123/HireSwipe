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
$icons = [
    'home'       => '<path d="M4 11.5 12 5l8 6.5V19a1 1 0 0 1-1 1h-4v-5H9v5H5a1 1 0 0 1-1-1v-7.5Z"/>',
    'job-search' => '<circle cx="11" cy="11" r="6"/><path d="m20 20-4.2-4.2"/>',
    'applied'    => '<path d="M6 4h9l3 3v13H6V4Z"/><path d="m9 13 2 2 4-4"/>',
    'post-job'   => '<circle cx="12" cy="12" r="8"/><path d="M12 8v8M8 12h8"/>',
    'interview'  => '<rect x="4" y="5" width="16" height="15" rx="3"/><path d="M4 10h16M9 3v4M15 3v4"/>',
    'profile'    => '<circle cx="12" cy="9" r="4"/><path d="M4.5 20a7.5 7.5 0 0 1 15 0"/>',
];
$userName = $role === 'employee' ? ($_SESSION['user']['name'] ?? 'You') : ($_SESSION['user']['details'] ?? 'Company');

include __DIR__ . '/head.php';
$renderNav = function ($class) use ($navItems, $active, $icons, $interviewCount) {
    foreach ($navItems as $key => $item): ?>
        <a class="<?= $class ?><?= $key === $active ? ' is-active' : '' ?>" href="<?= e($item[1]) ?>"<?= $key === $active ? ' aria-current="page"' : '' ?>>
            <svg class="nav-ico" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><?= $icons[$key] ?></svg>
            <span class="nav-text"><?= e($item[0]) ?></span>
            <?php if ($key === 'interview' && $interviewCount > 0): ?><span class="badge"><?= (int) $interviewCount ?></span><?php endif; ?>
        </a>
    <?php endforeach;
};
?>
<div class="shell">
    <aside class="sidebar">
        <?php include __DIR__ . '/brand.php'; ?>
        <nav class="side-nav" aria-label="Main">
            <?php $renderNav('side-link'); ?>
        </nav>
        <a class="side-logout" href="logout.php">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10 5H6a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h4M15 8l4 4-4 4M19 12H9"/></svg>
            Log out
        </a>
    </aside>

    <div class="shell-main">
        <header class="topline">
            <div class="topline-brand"><?php include __DIR__ . '/brand.php'; ?></div>
            <div class="topline-right">
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
                <a class="me-chip" href="profile.php" aria-label="Your profile">
                    <span class="me-dot" aria-hidden="true"><?= e(strtoupper(substr((string) $userName, 0, 1))) ?></span>
                </a>
            </div>
        </header>
        <main class="content">
