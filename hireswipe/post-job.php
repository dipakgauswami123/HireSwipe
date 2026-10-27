<?php
require __DIR__ . '/includes/data.php';
require __DIR__ . '/includes/cards.php';
require __DIR__ . '/includes/config.php';
require_role('employer');

$errors = [];
$old = [];
$view = (($_GET['view'] ?? '') === 'new' || $_SERVER['REQUEST_METHOD'] === 'POST') ? 'new' : 'posted';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = $_POST;

    $jobTitle = trim($_POST['title'] ?? '');
    if (mb_strlen($jobTitle) < 3 || mb_strlen($jobTitle) > 100) {
        $errors['title'] = 'Enter a job title (3 to 100 characters).';
    }

    $company = trim($_POST['company'] ?? '');
    if (mb_strlen($company) < 2 || mb_strlen($company) > 100) {
        $errors['company'] = 'Enter the company name (2 to 100 characters).';
    }

    $employment = $_POST['employment'] ?? '';
    if (!in_array($employment, EMPLOYMENT_TYPES, true)) {
        $errors['employment'] = 'Select an employment type.';
    }

    $experience = $_POST['experience'] ?? '';
    if (!in_array($experience, EXPERIENCE_RANGES, true)) {
        $errors['experience'] = 'Select the experience needed.';
    }

    $salary = trim($_POST['salary'] ?? '');
    if (!is_numeric($salary) || (float) $salary <= 0 || (float) $salary > 500) {
        $errors['salary'] = 'Enter the salary in LPA, for example 6.5.';
    }

    $location = trim($_POST['location'] ?? '');
    if (mb_strlen($location) < 2 || mb_strlen($location) > 120) {
        $errors['location'] = 'Enter the job location (2 to 120 characters).';
    }

    // Map pin (optional): set by the Google Maps picker on the form.
    $lat = null;
    $lng = null;
    $latRaw = trim($_POST['lat'] ?? '');
    $lngRaw = trim($_POST['lng'] ?? '');
    if ($latRaw !== '' || $lngRaw !== '') {
        if (is_numeric($latRaw) && is_numeric($lngRaw) && abs((float) $latRaw) <= 90 && abs((float) $lngRaw) <= 180) {
            $lat = round((float) $latRaw, 6);
            $lng = round((float) $lngRaw, 6);
        } else {
            $errors['location'] = 'The map pin is not valid. Pin the location again.';
        }
    }

    $jobType = $_POST['job_type'] ?? '';
    if (!in_array($jobType, POST_JOB_TYPES, true)) {
        $errors['job_type'] = 'Select a job type.';
    }

    $workplace = $_POST['workplace'] ?? '';
    if (!in_array($workplace, WORKPLACE_TYPES, true)) {
        $errors['workplace'] = 'Select a workplace type.';
    }

    if (!$errors) {
        $_SESSION['jobs'][] = [
            'title' => $jobTitle, 'company' => $company, 'employment' => $employment,
            'experience' => $experience, 'salary' => round((float) $salary, 2),
            'location' => $location, 'job_type' => $jobType, 'workplace' => $workplace,
            'lat' => $lat, 'lng' => $lng,
        ];
        $_SESSION['flash'] = 'Job posted: ' . $jobTitle . '.';
        go('post-job.php');
    }
}

$jobs   = all_jobs();
$notice = flash();
$title  = 'Post Job';
$active = 'post-job';
$mapsOn  = GOOGLE_MAPS_API_KEY !== '';
$scripts = ($mapsOn && $view === 'new') ? ['assets/js/job-location.js'] : [];
include __DIR__ . '/includes/shell-start.php';
?>
<?php if ($notice): ?><p class="banner banner-ok" role="status"><?= e($notice) ?></p><?php endif; ?>

<h1>Post Job</h1>

<nav class="tabs" aria-label="Post Job sections">
    <a class="tab" href="post-job.php"<?= $view === 'posted' ? ' aria-current="page"' : '' ?>>Posted jobs (<?= count($jobs) ?>)</a>
    <a class="tab" href="post-job.php?view=new"<?= $view === 'new' ? ' aria-current="page"' : '' ?>>Post new job</a>
</nav>

<?php if ($view === 'posted'): ?>
    <div class="grid">
        <?php foreach ($jobs as $job) { render_job_card($job); } ?>
    </div>
<?php else: ?>
    <?php if ($errors): ?>
        <p class="banner banner-error" role="alert">Fix the highlighted fields and try again.</p>
    <?php endif; ?>

    <form method="post" action="" class="form-card">
        <div class="field field-wide">
            <label for="title">Job title</label>
            <input type="text" id="title" name="title" maxlength="100" value="<?= v($old, 'title') ?>" required>
            <?php err($errors, 'title'); ?>
        </div>

        <div class="field">
            <label for="company">Company</label>
            <input type="text" id="company" name="company" maxlength="100" value="<?= v($old, 'company') ?>" required>
            <?php err($errors, 'company'); ?>
        </div>

        <div class="field">
            <label for="employment">Employment type</label>
            <select id="employment" name="employment" required>
                <option value="">Select an employment type</option>
                <?php foreach (EMPLOYMENT_TYPES as $t): ?>
                    <option value="<?= e($t) ?>"<?= sel($old, 'employment', $t) ?>><?= e($t) ?></option>
                <?php endforeach; ?>
            </select>
            <?php err($errors, 'employment'); ?>
        </div>

        <div class="field">
            <label for="experience">Experience</label>
            <select id="experience" name="experience" required>
                <option value="">Select experience</option>
                <?php foreach (EXPERIENCE_RANGES as $x): ?>
                    <option value="<?= e($x) ?>"<?= sel($old, 'experience', $x) ?>><?= e($x) ?></option>
                <?php endforeach; ?>
            </select>
            <?php err($errors, 'experience'); ?>
        </div>

        <div class="field">
            <label for="salary">Expected salary (LPA)</label>
            <input type="number" id="salary" name="salary" min="0.1" max="500" step="0.1" value="<?= v($old, 'salary') ?>" required>
            <?php err($errors, 'salary'); ?>
        </div>

        <div class="field field-wide">
            <label for="location">Job location</label>
            <?php if ($mapsOn): ?>
                <div class="location-picker" id="location-picker" data-key="<?= e(GOOGLE_MAPS_API_KEY) ?>">
                    <div class="input-row">
                        <input type="text" id="location" name="location" maxlength="120" autocomplete="off" aria-describedby="map-status" value="<?= v($old, 'location') ?>" required>
                        <button type="button" class="btn btn-ghost map-btn" id="find-on-map">Find on map</button>
                    </div>
                    <div class="map-frame">
                        <div id="map" class="map" aria-label="Map for choosing the job location"></div>
                        <span class="map-pin" aria-hidden="true"></span>
                    </div>
                    <p class="hint" id="map-status" aria-live="polite">Type a place and choose Find on map, or move the map so the pin sits on the job location.</p>
                    <input type="hidden" name="lat" id="lat" value="<?= v($old, 'lat') ?>">
                    <input type="hidden" name="lng" id="lng" value="<?= v($old, 'lng') ?>">
                </div>
            <?php else: ?>
                <div class="input-row">
                    <input type="text" id="location" name="location" maxlength="120" value="<?= v($old, 'location') ?>" required>
                    <a class="btn btn-ghost map-btn" id="map-link" href="<?= e(maps_url($old['location'] ?? '')) ?>" target="_blank" rel="noopener" data-maps-input="location">Open Google Maps<span class="sr-only"> in a new tab</span></a>
                </div>
                <p class="hint">To pick the location on a map here, add a Google Maps API key in includes/config.php.</p>
            <?php endif; ?>
            <?php err($errors, 'location'); ?>
        </div>

        <fieldset class="field">
            <legend>Job type</legend>
            <div class="pills">
                <?php foreach (POST_JOB_TYPES as $t): ?>
                    <label class="pill"><input type="radio" name="job_type" value="<?= e($t) ?>"<?= chk($old, 'job_type', $t) ?> required><span><?= e($t) ?></span></label>
                <?php endforeach; ?>
            </div>
            <?php err($errors, 'job_type'); ?>
        </fieldset>

        <fieldset class="field">
            <legend>Workplace type</legend>
            <div class="pills">
                <?php foreach (WORKPLACE_TYPES as $w): ?>
                    <label class="pill"><input type="radio" name="workplace" value="<?= e($w) ?>"<?= chk($old, 'workplace', $w) ?> required><span><?= e($w) ?></span></label>
                <?php endforeach; ?>
            </div>
            <?php err($errors, 'workplace'); ?>
        </fieldset>

        <div class="actions field-wide">
            <button type="submit" class="btn btn-primary">Post job</button>
            <a class="btn btn-ghost" href="post-job.php">Cancel</a>
        </div>
    </form>
<?php endif; ?>
<?php include __DIR__ . '/includes/shell-end.php'; ?>
