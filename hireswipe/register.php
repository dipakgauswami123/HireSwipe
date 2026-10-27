<?php
require __DIR__ . '/includes/data.php';
require __DIR__ . '/includes/cards.php';

if (!empty($_SESSION['role'])) {
    go(home_for($_SESSION['role']));
}

$title = 'Register';
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
            <h1>Register</h1>
            <p class="lead">Choose how you want to register.</p>

            <div class="choices">
                <a class="choice" href="register-employee.php">
                    <img class="avatar" src="<?= e(avatar_uri('priya.mehta', 0)) ?>" alt="" width="56" height="56">
                    <span class="choice-text">
                        <strong>Register as an employee</strong>
                        <span>Create a profile that employers can find.</span>
                    </span>
                </a>
                <a class="choice" href="register-employer.php">
                    <img class="logo" src="<?= e(logo_uri(2)) ?>" alt="" width="56" height="56">
                    <span class="choice-text">
                        <strong>Register as an employer</strong>
                        <span>Tell candidates about your company.</span>
                    </span>
                </a>
            </div>

            <p class="switch">Already registered? <a href="index.php">Log in</a></p>
        </div>
    </main>
</div>
<?php include __DIR__ . '/includes/foot.php'; ?>
