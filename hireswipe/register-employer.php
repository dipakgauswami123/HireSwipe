<?php
require __DIR__ . '/includes/data.php';

if (!empty($_SESSION['role'])) {
    go(home_for($_SESSION['role']));
}

$errors = [];
$old = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = $_POST;
    $logo = read_image('logo', 'company logo', $errors);

    $details = trim($_POST['details'] ?? '');
    if (mb_strlen($details) < 5 || mb_strlen($details) > 150) {
        $errors['details'] = 'Enter employer details (5 to 150 characters).';
    }

    $size = $_POST['size'] ?? '';
    if (!in_array($size, COMPANY_SIZES, true)) {
        $errors['size'] = 'Select a company size.';
    }

    $industry = $_POST['industry'] ?? '';
    if (!in_array($industry, INDUSTRIES, true)) {
        $errors['industry'] = 'Select an industry.';
    }

    $about = trim($_POST['about'] ?? '');
    if (mb_strlen($about) < 20 || mb_strlen($about) > 500) {
        $errors['about'] = 'Write 20 to 500 characters about the company.';
    }

    if (!$errors) {
        $_SESSION['role'] = 'employer';
        $_SESSION['user'] = [
            'id' => 'new', 'logo' => $logo, 'details' => $details,
            'size' => $size, 'industry' => $industry, 'about' => $about,
        ];
        $_SESSION['flash'] = 'Your company is registered. Here are the employees on HireSwipe.';
        go('employer-home.php');
    }
}

$title = 'Register as an employer';
include __DIR__ . '/includes/head.php';
?>
<header class="topbar">
    <div class="topbar-inner">
        <?php include __DIR__ . '/includes/brand.php'; ?>
        <a class="link" href="register.php">Back</a>
    </div>
</header>

<main class="page">
    <h1>Register as an employer</h1>

    <?php if ($errors): ?>
        <p class="banner banner-error" role="alert">Fix the highlighted fields and try again.</p>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="form-card">
        <div class="field field-wide">
            <label for="logo">Company logo</label>
            <div class="upload">
                <img id="logo-preview" class="preview preview-square" src="<?= e(placeholder_uri()) ?>" alt="Logo preview" width="88" height="88">
                <div>
                    <input type="file" id="logo" name="logo" accept="image/png,image/jpeg,image/webp" data-preview="logo-preview" required>
                    <p class="hint">JPG, PNG or WebP, up to 2 MB.</p>
                </div>
            </div>
            <?php err($errors, 'logo'); ?>
        </div>

        <div class="field field-wide">
            <label for="details">Employer details</label>
            <input type="text" id="details" name="details" maxlength="150" placeholder="Your name, role and company name" value="<?= v($old, 'details') ?>" required>
            <?php err($errors, 'details'); ?>
        </div>

        <div class="field">
            <label for="size">Company size</label>
            <select id="size" name="size" required>
                <option value="">Select a company size</option>
                <?php foreach (COMPANY_SIZES as $s): ?>
                    <option value="<?= e($s) ?>"<?= sel($old, 'size', $s) ?>><?= e($s) ?> people</option>
                <?php endforeach; ?>
            </select>
            <?php err($errors, 'size'); ?>
        </div>

        <div class="field">
            <label for="industry">Industry</label>
            <select id="industry" name="industry" required>
                <option value="">Select an industry</option>
                <?php foreach (INDUSTRIES as $i): ?>
                    <option value="<?= e($i) ?>"<?= sel($old, 'industry', $i) ?>><?= e($i) ?></option>
                <?php endforeach; ?>
            </select>
            <?php err($errors, 'industry'); ?>
        </div>

        <div class="field field-wide">
            <label for="about">About the company</label>
            <textarea id="about" name="about" rows="5" maxlength="500" required><?= v($old, 'about') ?></textarea>
            <?php err($errors, 'about'); ?>
        </div>

        <div class="actions field-wide">
            <button type="submit" class="btn btn-primary">Register company</button>
            <a class="btn btn-ghost" href="index.php">Cancel</a>
        </div>
    </form>
</main>
<?php include __DIR__ . '/includes/foot.php'; ?>
