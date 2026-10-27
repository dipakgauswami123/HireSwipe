<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set('Asia/Kolkata');

const INDUSTRIES = [
    'Information Technology', 'Finance', 'Healthcare', 'Education', 'Manufacturing',
    'Marketing', 'Sales', 'Design', 'Logistics', 'Retail', 'Hospitality',
    'Human Resources', 'Real Estate', 'Media & Entertainment',
];
const JOB_TYPES     = ['Full-time', 'Part-time', 'Contract', 'Internship', 'Freelance'];
const GENDERS       = ['Female', 'Male', 'Other'];
const COMPANY_SIZES = ['1-10', '11-50', '51-200', '201-500', '501-1000', '1000+'];
const DEMO_PASSWORD = 'Hire@123';

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function go($url)
{
    header('Location: ' . $url);
    exit;
}

function home_for($role)
{
    return $role === 'employer' ? 'employer-home.php' : 'employee-home.php';
}

function require_role($role)
{
    if (($_SESSION['role'] ?? '') !== $role) {
        go('index.php');
    }
    if ($role === 'employer') {
        seed_interviews();
    } elseif ($role === 'employee') {
        seed_employee();
    }
}

function flash()
{
    $msg = $_SESSION['flash'] ?? '';
    unset($_SESSION['flash']);
    return $msg;
}

/* ---------- formatting ---------- */

function inr($amount)
{
    $s = (string) (int) $amount;
    if (strlen($s) > 3) {
        $last = substr($s, -3);
        $rest = substr($s, 0, -3);
        $rest = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $rest);
        $s = $rest . ',' . $last;
    }
    return '₹' . $s;
}

function lpa($amount)
{
    $n = number_format(((float) $amount) / 100000, 2, '.', '');
    return rtrim(rtrim($n, '0'), '.') . ' LPA';
}

function years($n)
{
    $n = (int) $n;
    if ($n === 0) {
        return 'Fresher';
    }
    return $n . ($n === 1 ? ' year' : ' years');
}

function pretty_date($ymd)
{
    $t = strtotime($ymd);
    return $t ? date('d M Y', $t) : '';
}

/* ---------- generated images (no files needed for dummy data) ---------- */

function svg_uri($svg)
{
    return 'data:image/svg+xml;charset=utf-8,' . rawurlencode($svg);
}

function palette()
{
    // [background, foreground]
    return [
        ['#2D3BFF', '#FFFFFF'],
        ['#FFCF33', '#12141F'],
        ['#12141F', '#FFCF33'],
        ['#FF5A4E', '#FFFFFF'],
        ['#1FB58B', '#12141F'],
        ['#B9A7FF', '#12141F'],
    ];
}

function avatar_uri($label, $i)
{
    list($bg, $fg) = palette()[$i % 6];
    $parts = preg_split('/[._ ]+/', $label);
    $initials = strtoupper(substr($parts[0], 0, 1) . substr($parts[1] ?? '', 0, 1));
    $svg = "<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 96 96'>"
        . "<rect width='96' height='96' fill='$bg'/>"
        . "<text x='48' y='62' font-family='Arial,Helvetica,sans-serif' font-size='38' font-weight='700' text-anchor='middle' fill='$fg'>$initials</text>"
        . "</svg>";
    return svg_uri($svg);
}

function logo_uri($i)
{
    list($bg, $fg) = palette()[($i + 1) % 6];
    $base = "<rect width='96' height='96' fill='$bg'/>";
    switch ($i % 4) {
        case 0:
            $shape = "<circle cx='48' cy='48' r='26' fill='$fg'/>";
            break;
        case 1:
            $shape = "<path d='M16 78 L48 18 L80 78 Z' fill='$fg'/>";
            break;
        case 2:
            $shape = "<rect x='24' y='24' width='48' height='48' rx='8' fill='$fg' transform='rotate(45 48 48)'/>";
            break;
        default:
            $shape = "<circle cx='36' cy='48' r='22' fill='$fg'/><circle cx='60' cy='48' r='22' fill='$fg' opacity='.65'/>";
    }
    return svg_uri("<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 96 96'>$base$shape</svg>");
}

function placeholder_uri()
{
    return svg_uri("<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 96 96'><rect width='96' height='96' fill='#E6E8F2'/><circle cx='48' cy='38' r='15' fill='#B7BBD0'/><path d='M18 84 C20 62 76 62 78 84 Z' fill='#B7BBD0'/></svg>");
}

/* ---------- form helpers ---------- */

function v($old, $key)
{
    return e($old[$key] ?? '');
}

function sel($old, $key, $value)
{
    return (($old[$key] ?? '') === $value) ? ' selected' : '';
}

function chk($old, $key, $value)
{
    return (($old[$key] ?? '') === $value) ? ' checked' : '';
}

function err($errors, $key)
{
    if (isset($errors[$key])) {
        echo '<p class="err" id="err-' . e($key) . '" role="alert">' . e($errors[$key]) . '</p>';
    }
}

/** Reads an uploaded image into a data URI (nothing is saved to disk). */
function read_image($key, $label, &$errors)
{
    $f = $_FILES[$key] ?? null;
    if (!$f || $f['error'] === UPLOAD_ERR_NO_FILE) {
        $errors[$key] = "Add a $label.";
        return null;
    }
    if ($f['error'] !== UPLOAD_ERR_OK || $f['size'] > 2 * 1024 * 1024) {
        $errors[$key] = 'The file must be an image under 2 MB.';
        return null;
    }
    $info = @getimagesize($f['tmp_name']);
    $allowed = ['image/jpeg', 'image/png', 'image/webp'];
    if (!$info || !in_array($info['mime'], $allowed, true)) {
        $errors[$key] = 'Use a JPG, PNG or WebP image.';
        return null;
    }
    return 'data:' . $info['mime'] . ';base64,' . base64_encode(file_get_contents($f['tmp_name']));
}

/* ---------- job posting ---------- */

const EMPLOYMENT_TYPES  = ['Permanent', 'Contract', 'Temporary', 'Internship', 'Freelance'];
const EXPERIENCE_RANGES = ['Fresher', '1-3 years', '3-5 years', '5-10 years', '10+ years'];
const POST_JOB_TYPES    = ['Full-time', 'Part-time'];
const WORKPLACE_TYPES   = ['On-site', 'Hybrid', 'Remote'];

/** Salary already stored in LPA, for example 4.5 becomes "4.5 LPA". */
function lpa_value($n)
{
    return rtrim(rtrim(number_format((float) $n, 2, '.', ''), '0'), '.') . ' LPA';
}

function maps_url($place, $lat = null, $lng = null)
{
    if ($lat !== null && $lng !== null) {
        return 'https://www.google.com/maps/search/?api=1&query=' . number_format((float) $lat, 6, '.', '') . ',' . number_format((float) $lng, 6, '.', '');
    }
    $place = trim($place);
    if ($place === '') {
        return 'https://www.google.com/maps';
    }
    return 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($place);
}

/* ---------- interviews ---------- */

/**
 * Puts a few dummy interviews in the session the first time an employer is seen,
 * so every Interview tab has something in it. Dates are relative to today.
 */
function seed_interviews()
{
    if (!empty($_SESSION['interviews_seeded'])) {
        return;
    }
    $_SESSION['interviews_seeded'] = true;

    // id => [days from today, time, done]. null means accepted but not scheduled yet.
    $plan = [
        'emp3'  => [0,  '10:30', false],
        'emp8'  => [0,  '15:00', false],
        'emp11' => [1,  '11:00', false],
        'emp17' => [3,  '16:30', false],
        'emp5'  => null,
        'emp22' => null,
        'emp14' => [-2, '11:00', true],
        'emp19' => [-1, '14:30', true],
    ];
    $rejected = $_SESSION['rejected'] ?? [];
    $today    = new DateTimeImmutable('today');

    foreach ($plan as $id => $p) {
        if (in_array($id, $rejected, true)) {
            continue;
        }
        if (!in_array($id, $_SESSION['accepted'] ?? [], true)) {
            $_SESSION['accepted'][] = $id;
        }
        if ($p !== null && !isset($_SESSION['interviews'][$id])) {
            $_SESSION['interviews'][$id] = [
                'date' => $today->modify(($p[0] >= 0 ? '+' : '') . $p[0] . ' days')->format('Y-m-d'),
                'time' => $p[1],
                'done' => $p[2],
            ];
        }
    }
}

/** One of: unscheduled, today, scheduled, done. */
function interview_status($iv)
{
    if (!$iv || empty($iv['date'])) {
        return 'unscheduled';
    }
    if (!empty($iv['done'])) {
        return 'done';
    }
    return $iv['date'] === date('Y-m-d') ? 'today' : 'scheduled';
}

function pretty_time($hm)
{
    $t = strtotime($hm);
    return $t ? date('g:i A', $t) : '';
}

/** "Today", "Tomorrow" or a short date such as "Fri, 16 Oct". */
function day_label($ymd)
{
    if ($ymd === date('Y-m-d')) {
        return 'Today';
    }
    if ($ymd === date('Y-m-d', strtotime('+1 day'))) {
        return 'Tomorrow';
    }
    $t = strtotime($ymd);
    return $t ? date('D, d M', $t) : '';
}

/* ---------- notification settings ---------- */

const NOTIFICATION_TYPES = [
    'profiles'   => ['New employee profiles',          'When new employees join in your industries or cities.'],
    'jobs'       => ['Job post activity',              'Views and activity on the jobs you posted.'],
    'interviews' => ['Interview and decision reminders', 'Employees waiting for your decision or for an interview.'],
    'updates'    => ['Tips and updates',               'Welcome notes and news about HireSwipe.'],
];

const EMPLOYEE_NOTIFICATION_TYPES = [
    'requests'   => ['Company requests',     'When a company sends you a request.'],
    'jobs'       => ['New jobs',             'New jobs in your industry or city.'],
    'interviews' => ['Interview reminders',  'Requests waiting for an interview or interviews coming up.'],
    'updates'    => ['Tips and updates',     'Welcome notes and news about HireSwipe.'],
];

/** The notification types for whoever is logged in. */
function notification_types()
{
    return (($_SESSION['role'] ?? '') === 'employee') ? EMPLOYEE_NOTIFICATION_TYPES : NOTIFICATION_TYPES;
}

/** Keys of the notification types the user chose to receive. All are on until changed. */
function notification_prefs()
{
    $types = array_keys(notification_types());
    $prefs = $_SESSION['notif_prefs'] ?? $types;
    return array_values(array_intersect($types, $prefs));
}

/* ---------- employee dummy activity ---------- */

/**
 * Puts dummy applications and interviews in the session the first time an employee is seen,
 * so Applied Jobs and every Interview tab have something in them. Dates are relative to today.
 */
function seed_employee()
{
    if (!empty($_SESSION['employee_seeded'])) {
        return;
    }
    $_SESSION['employee_seeded'] = true;
    $today = new DateTimeImmutable('today');

    // job id => days ago the employee applied
    $applied = [
        'job2' => 1, 'job8' => 2, 'job12' => 3, 'job14' => 4, 'job20' => 5,
        'job30' => 6, 'job36' => 8, 'job44' => 9, 'job61' => 11, 'job67' => 12,
    ];
    foreach ($applied as $id => $ago) {
        if (!isset($_SESSION['applied'][$id])) {
            $_SESSION['applied'][$id] = $today->modify('-' . $ago . ' days')->format('Y-m-d');
        }
    }

    // job id => [days from today, time, done]. null means confirmed but not scheduled yet.
    $plan = [
        'job6'  => [0,  '11:00', false],
        'job11' => [0,  '14:30', false],
        'job27' => [0,  '16:30', false],
        'job15' => [1,  '10:30', false],
        'job19' => [2,  '15:00', false],
        'job23' => [3,  '11:00', false],
        'job29' => [4,  '12:00', false],
        'job43' => [5,  '10:00', false],
        'job58' => [6,  '16:00', false],
        'job17' => null,
        'job21' => null,
        'job35' => null,
        'job46' => null,
        'job24' => [-1, '10:00', true],
        'job28' => [-2, '11:30', true],
        'job32' => [-3, '15:00', true],
        'job39' => [-4, '12:00', true],
        'job47' => [-5, '14:00', true],
        'job50' => [-6, '10:30', true],
        'job64' => [-7, '13:00', true],
    ];
    $rejected = $_SESSION['rejected'] ?? [];
    foreach ($plan as $id => $p) {
        if (in_array($id, $rejected, true)) {
            continue;
        }
        if (!in_array($id, $_SESSION['accepted'] ?? [], true)) {
            $_SESSION['accepted'][] = $id;
        }
        if ($p !== null && !isset($_SESSION['interviews'][$id])) {
            $_SESSION['interviews'][$id] = [
                'date' => $today->modify(($p[0] >= 0 ? '+' : '') . $p[0] . ' days')->format('Y-m-d'),
                'time' => $p[1],
                'done' => $p[2],
            ];
        }
    }
}
