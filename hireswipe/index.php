<?php
require __DIR__ . '/includes/data.php';
require __DIR__ . '/includes/cards.php';

if (!empty($_SESSION['role'])) {
    go(home_for($_SESSION['role']));
}

$error = '';
$identifier = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier'] ?? '');
    $password   = $_POST['password'] ?? '';
    $found      = $identifier !== '' ? find_user($identifier) : null;

    if ($found && hash_equals(DEMO_PASSWORD, $password)) {
        $_SESSION['role'] = $found[0];
        $_SESSION['user'] = $found[1];
        go(home_for($found[0]));
    }
    $error = 'Email, phone number or password is incorrect.';
}

$title = 'Log in';
include __DIR__ . '/includes/head.php';
?>
<div class="auth">
    <aside class="auth-art">
        <?php include __DIR__ . '/includes/brand.php'; ?>
        <?php render_deck(); ?>
        <p class="art-line">Find the right people. Or the right company.</p>
    </aside>

    <main class="auth-main">
        <div class="auth-panel">
            <h1>Log in</h1>

            <?php if ($error): ?>
                <p class="banner banner-error" role="alert"><?= e($error) ?></p>
            <?php endif; ?>

            <form method="post" class="stack">
                <div class="field">
                    <label for="identifier">Email or phone number</label>
                    <input type="text" id="identifier" name="identifier" value="<?= e($identifier) ?>" autocomplete="username" required>
                </div>
                <div class="field">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" autocomplete="current-password" required>
                </div>
                <button type="submit" class="btn btn-primary">Log in</button>
            </form>

            <p class="switch">New to HireSwipe? <a href="register.php">Register</a></p>

            <div class="demo">
                <p class="demo-title">Demo logins (password for both: <strong><?= e(DEMO_PASSWORD) ?></strong>)</p>
                <p>Employee: <code>priya.mehta@example.com</code></p>
                <p>Employer: <code>hr@kirantextiles.example</code></p>
            </div>
        </div>
    </main>
</div>
<?php include __DIR__ . '/includes/foot.php'; ?>
