<?php
require __DIR__ . '/includes/data.php';

if (!empty($_SESSION['role'])) {
    go(home_for($_SESSION['role']));
}

$errors = [];
$old = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = $_POST;
    $image = read_image('image', 'profile image', $errors);

    $experience = trim($_POST['experience'] ?? '');
    if ($experience === '' || !ctype_digit($experience) || (int) $experience > 50) {
        $errors['experience'] = 'Enter whole years of experience, from 0 to 50.';
    }

    $salary = trim($_POST['salary'] ?? '');
    if ($salary === '' || !ctype_digit($salary) || (int) $salary < 1) {
        $errors['salary'] = 'Enter your expected yearly salary as a number.';
    }

    $gender = $_POST['gender'] ?? '';
    if (!in_array($gender, GENDERS, true)) {
        $errors['gender'] = 'Select a gender.';
    }

    $dob = trim($_POST['dob'] ?? '');
    $dobTime = strtotime($dob);
    if ($dob === '' || !$dobTime) {
        $errors['dob'] = 'Enter your date of birth.';
    } elseif ($dobTime > strtotime('-18 years')) {
        $errors['dob'] = 'You must be at least 18 years old.';
    }

    $industry = $_POST['industry'] ?? '';
    if (!in_array($industry, INDUSTRIES, true)) {
        $errors['industry'] = 'Select an industry.';
    }

    $jobType = $_POST['job_type'] ?? '';
    if (!in_array($jobType, JOB_TYPES, true)) {
        $errors['job_type'] = 'Select a job type.';
    }

    $location = trim($_POST['location'] ?? '');
    if ($location === '' || mb_strlen($location) > 80) {
        $errors['location'] = 'Enter your location (up to 80 characters).';
    }

    if (!$errors) {
        $_SESSION['role'] = 'employee';
        $_SESSION['user'] = [
            'id' => 'new', 'image' => $image, 'experience' => (int) $experience,
            'salary' => (int) $salary, 'gender' => $gender, 'dob' => $dob,
            'industry' => $industry, 'job_type' => $jobType, 'location' => $location,
        ];
        $_SESSION['flash'] = 'Your profile is created. Here are the companies that sent you requests.';
        go('employee-home.php');
    }
}

$title = 'Register as an employee';
include __DIR__ . '/includes/head.php';
?>
<header class="topbar">
    <div class="topbar-inner">
        <?php include __DIR__ . '/includes/brand.php'; ?>
        <a class="link" href="register.php">Back</a>
    </div>
</header>

<main class="page">
    <h1>Register as an employee</h1>

    <?php if ($errors): ?>
        <p class="banner banner-error" role="alert">Fix the highlighted fields and try again.</p>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="form-card">
        <div class="field field-wide">
            <label for="image">Profile image</label>
            <div class="upload">
                <img id="image-preview" class="preview preview-round" src="<?= e(placeholder_uri()) ?>" alt="Image preview" width="88" height="88">
                <div>
                    <input type="file" id="image" name="image" accept="image/png,image/jpeg,image/webp" data-preview="image-preview" required>
                    <p class="hint">JPG, PNG or WebP, up to 2 MB.</p>
                </div>
            </div>
            <?php err($errors, 'image'); ?>
        </div>

        <div class="field">
            <label for="experience">Experience (years)</label>
            <input type="number" id="experience" name="experience" min="0" max="50" step="1" value="<?= v($old, 'experience') ?>" required>
            <?php err($errors, 'experience'); ?>
        </div>

        <div class="field">
            <label for="salary">Expected salary (₹ per year)</label>
            <input type="number" id="salary" name="salary" min="1" step="1000" value="<?= v($old, 'salary') ?>" required>
            <?php err($errors, 'salary'); ?>
        </div>

        <fieldset class="field">
            <legend>Gender</legend>
            <div class="pills">
                <?php foreach (GENDERS as $g): ?>
                    <label class="pill"><input type="radio" name="gender" value="<?= e($g) ?>"<?= chk($old, 'gender', $g) ?> required><span><?= e($g) ?></span></label>
                <?php endforeach; ?>
            </div>
            <?php err($errors, 'gender'); ?>
        </fieldset>

        <div class="field">
            <label for="dob">Date of birth</label>
            <input type="date" id="dob" name="dob" max="<?= e(date('Y-m-d', strtotime('-18 years'))) ?>" value="<?= v($old, 'dob') ?>" required>
            <?php err($errors, 'dob'); ?>
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

        <div class="field">
            <label for="job_type">Job type</label>
            <select id="job_type" name="job_type" required>
                <option value="">Select a job type</option>
                <?php foreach (JOB_TYPES as $j): ?>
                    <option value="<?= e($j) ?>"<?= sel($old, 'job_type', $j) ?>><?= e($j) ?></option>
                <?php endforeach; ?>
            </select>
            <?php err($errors, 'job_type'); ?>
        </div>

        <div class="field field-wide">
            <label for="location">Location</label>
            <input type="text" id="location" name="location" maxlength="80" value="<?= v($old, 'location') ?>" required>
            <?php err($errors, 'location'); ?>
        </div>

        <div class="actions field-wide">
            <button type="submit" class="btn btn-primary">Create profile</button>
            <a class="btn btn-ghost" href="index.php">Cancel</a>
        </div>
    </form>
</main>
<?php include __DIR__ . '/includes/foot.php'; ?>
