<?php
require __DIR__ . '/includes/data.php';
require __DIR__ . '/includes/cards.php';

$role = $_SESSION['role'] ?? '';
if ($role !== 'employer' && $role !== 'employee') {
    go('index.php');
}
require_role($role);

$user = $_SESSION['user'];
$view = $_GET['view'] ?? 'profile';
if (!in_array($view, ['profile', 'edit', 'notifications'], true)) {
    $view = 'profile';
}

$errors = [];
$old    = $user;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    /* Employer: edit company details, the same fields and rules as registration */
    if ($action === 'details' && $role === 'employer') {
        $view = 'edit';
        $old  = $_POST;
        $logo = $user['logo'];
        if (($_FILES['logo']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $new = read_image('logo', 'company logo', $errors);
            if ($new !== null) {
                $logo = $new;
            }
        }

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
            $_SESSION['user'] = array_merge($user, [
                'logo' => $logo, 'details' => $details,
                'size' => $size, 'industry' => $industry, 'about' => $about,
            ]);
            $_SESSION['flash'] = 'Your details are updated.';
            go('profile.php');
        }
        $old['logo'] = $logo;
    }

    /* Employee: edit profile details, the same fields and rules as registration */
    if ($action === 'details' && $role === 'employee') {
        $view  = 'edit';
        $old   = $_POST;
        $image = $user['image'];
        if (($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $new = read_image('image', 'profile image', $errors);
            if ($new !== null) {
                $image = $new;
            }
        }

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
        $dob     = trim($_POST['dob'] ?? '');
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
            $_SESSION['user'] = array_merge($user, [
                'image' => $image, 'experience' => (int) $experience, 'salary' => (int) $salary,
                'gender' => $gender, 'dob' => $dob, 'industry' => $industry,
                'job_type' => $jobType, 'location' => $location,
            ]);
            $_SESSION['flash'] = 'Your details are updated.';
            go('profile.php');
        }
        $old['image'] = $image;
    }

    /* Choose which notifications to receive */
    if ($action === 'notifications') {
        $chosen = $_POST['types'] ?? [];
        $chosen = is_array($chosen) ? $chosen : [];
        $_SESSION['notif_prefs'] = array_values(array_intersect(array_keys(notification_types()), $chosen));
        $_SESSION['flash'] = $_SESSION['notif_prefs']
            ? 'Notification settings saved.'
            : 'Notification settings saved. You will not receive any notifications.';
        go('profile.php?view=notifications');
    }
}

$prefs  = notification_prefs();
$notice = flash();
$title  = 'Profile';
$active = 'profile';
include __DIR__ . '/includes/shell-start.php';
?>
<?php if ($notice): ?><p class="banner banner-ok" role="status"><?= e($notice) ?></p><?php endif; ?>

<h1>Profile</h1>

<nav class="tabs" aria-label="Profile sections">
    <a class="tab" href="profile.php"<?= $view === 'profile' ? ' aria-current="page"' : '' ?>><?= $role === 'employer' ? 'Company' : 'Profile' ?></a>
    <a class="tab" href="profile.php?view=edit"<?= $view === 'edit' ? ' aria-current="page"' : '' ?>>Edit details</a>
    <a class="tab" href="profile.php?view=notifications"<?= $view === 'notifications' ? ' aria-current="page"' : '' ?>>Notification settings</a>
</nav>

<?php if ($view === 'profile'): ?>
    <?php if ($role === 'employer') { render_employer_card($user, true); } else { render_employee_card($user, true); } ?>
    <p class="profile-actions"><a class="btn btn-primary" href="profile.php?view=edit">Edit details</a></p>

<?php elseif ($view === 'edit' && $role === 'employer'): ?>
    <?php if ($errors): ?>
        <p class="banner banner-error" role="alert">Fix the highlighted fields and try again.</p>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="form-card">
        <input type="hidden" name="action" value="details">

        <div class="field field-wide">
            <label for="logo">Company logo</label>
            <div class="upload">
                <img id="logo-preview" class="preview preview-square" src="<?= e($old['logo'] ?? $user['logo']) ?>" alt="Current logo" width="88" height="88">
                <div>
                    <input type="file" id="logo" name="logo" accept="image/png,image/jpeg,image/webp" data-preview="logo-preview">
                    <p class="hint">Leave empty to keep the current logo. JPG, PNG or WebP, up to 2 MB.</p>
                </div>
            </div>
            <?php err($errors, 'logo'); ?>
        </div>

        <div class="field field-wide">
            <label for="details">Employer details</label>
            <input type="text" id="details" name="details" maxlength="150" value="<?= v($old, 'details') ?>" required>
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
            <button type="submit" class="btn btn-primary">Save changes</button>
            <a class="btn btn-ghost" href="profile.php">Cancel</a>
        </div>
    </form>

<?php elseif ($view === 'edit'): ?>
    <?php if ($errors): ?>
        <p class="banner banner-error" role="alert">Fix the highlighted fields and try again.</p>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="form-card">
        <input type="hidden" name="action" value="details">

        <div class="field field-wide">
            <label for="image">Profile image</label>
            <div class="upload">
                <img id="image-preview" class="preview preview-round" src="<?= e($old['image'] ?? $user['image']) ?>" alt="Current profile image" width="88" height="88">
                <div>
                    <input type="file" id="image" name="image" accept="image/png,image/jpeg,image/webp" data-preview="image-preview">
                    <p class="hint">Leave empty to keep the current image. JPG, PNG or WebP, up to 2 MB.</p>
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
            <button type="submit" class="btn btn-primary">Save changes</button>
            <a class="btn btn-ghost" href="profile.php">Cancel</a>
        </div>
    </form>

<?php else: ?>
    <p class="lead">Choose the notifications you want in the bell on Home. Turn off a type to stop seeing it.</p>

    <form method="post" class="form-card notif-form">
        <input type="hidden" name="action" value="notifications">
        <fieldset class="field field-wide opt-list">
            <legend class="sr-only">Notification types</legend>
            <?php foreach (notification_types() as $key => $info): ?>
                <label class="opt-row" for="nt-<?= e($key) ?>">
                    <input type="checkbox" id="nt-<?= e($key) ?>" name="types[]" value="<?= e($key) ?>"<?= in_array($key, $prefs, true) ? ' checked' : '' ?>>
                    <span><strong><?= e($info[0]) ?></strong><span class="hint"><?= e($info[1]) ?></span></span>
                </label>
            <?php endforeach; ?>
        </fieldset>
        <div class="actions field-wide">
            <button type="submit" class="btn btn-primary">Save settings</button>
            <a class="btn btn-ghost" href="profile.php">Cancel</a>
        </div>
    </form>
<?php endif; ?>
<?php include __DIR__ . '/includes/shell-end.php'; ?>
