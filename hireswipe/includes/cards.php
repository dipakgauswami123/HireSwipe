<?php
function render_employee_card($e, $mine = false)
{ ?>
    <article class="card<?= $mine ? ' card-mine' : '' ?>">
        <?php if ($mine): ?><p class="mine-tag">Your profile</p><?php endif; ?>
        <header class="card-head">
            <img class="avatar" src="<?= e($e['image']) ?>" alt="" width="56" height="56">
            <div>
                <h3><?= e($e['industry']) ?></h3>
                <span class="chip"><?= e($e['job_type']) ?></span>
            </div>
        </header>
        <dl class="facts">
            <div><dt>Experience</dt><dd><?= e(years($e['experience'])) ?></dd></div>
            <div><dt>Expected salary</dt><dd><?= e(inr($e['salary'])) ?> a year</dd></div>
            <div><dt>Gender</dt><dd><?= e($e['gender']) ?></dd></div>
            <div><dt>Date of birth</dt><dd><?= e(pretty_date($e['dob'])) ?></dd></div>
            <div><dt>Location</dt><dd><?= e($e['location']) ?></dd></div>
        </dl>
    </article>
<?php }

function render_employer_card($e, $mine = false)
{ ?>
    <article class="card<?= $mine ? ' card-mine' : '' ?>">
        <?php if ($mine): ?><p class="mine-tag">Your company</p><?php endif; ?>
        <header class="card-head">
            <img class="logo" src="<?= e($e['logo']) ?>" alt="" width="56" height="56">
            <div>
                <h3><?= e($e['details']) ?></h3>
                <span class="chip"><?= e($e['industry']) ?></span>
            </div>
        </header>
        <dl class="facts">
            <div><dt>Company size</dt><dd><?= e($e['size']) ?> people</dd></div>
        </dl>
        <p class="about"><?= e($e['about']) ?></p>
    </article>
<?php }

/** Overlapping sample cards for the sign-in and register screens. */
function render_deck()
{
    $emps = employees();
    $emrs = employers();
    $a = $emps[0];
    $b = $emrs[2];
    $c = $emps[7];
    ?>
    <div class="deck" aria-hidden="true">
        <div class="deck-card" style="--x:34px;--y:150px;--r:5deg;--d:.05s">
            <img class="logo" src="<?= e($b['logo']) ?>" alt="" width="48" height="48">
            <p class="deck-title"><?= e($b['industry']) ?></p>
            <p class="deck-sub"><?= e($b['size']) ?> people</p>
        </div>
        <div class="deck-card" style="--x:6px;--y:78px;--r:-4deg;--d:.2s">
            <img class="avatar" src="<?= e($c['image']) ?>" alt="" width="48" height="48">
            <p class="deck-title"><?= e($c['industry']) ?></p>
            <p class="deck-sub"><?= e($c['job_type']) ?>, <?= e($c['location']) ?></p>
        </div>
        <div class="deck-card" style="--x:0px;--y:0px;--r:-1deg;--d:.35s">
            <img class="avatar" src="<?= e($a['image']) ?>" alt="" width="48" height="48">
            <p class="deck-title"><?= e($a['industry']) ?></p>
            <p class="deck-sub"><?= e($a['job_type']) ?>, <?= e($a['location']) ?></p>
        </div>
    </div>
<?php }

/** Employee card for the employer screens: image, name, experience, industry, expected salary in LPA. */
function render_candidate_card($e, $clickable = true)
{ ?>
    <article class="card">
        <header class="card-head">
            <img class="avatar" src="<?= e($e['image']) ?>" alt="" width="56" height="56">
            <h3>
                <?php if ($clickable): ?>
                    <button type="button" class="card-open" data-open="<?= e($e['id']) ?>"><?= e($e['name']) ?></button>
                <?php else: ?>
                    <?= e($e['name']) ?>
                <?php endif; ?>
            </h3>
        </header>
        <dl class="facts">
            <div><dt>Experience</dt><dd><?= e(years($e['experience'])) ?></dd></div>
            <div><dt>Industry</dt><dd><?= e($e['industry']) ?></dd></div>
            <div><dt>Expected salary</dt><dd><?= e(lpa($e['salary'])) ?></dd></div>
        </dl>
    </article>
<?php }

/** Full profile shown inside the swipe card. */
function render_detail($e)
{ ?>
    <div class="detail">
        <img class="avatar detail-photo" src="<?= e($e['image']) ?>" alt="" width="112" height="112">
        <h2><?= e($e['name']) ?></h2>
        <span class="chip"><?= e($e['industry']) ?></span>
        <dl class="facts">
            <div><dt>Experience</dt><dd><?= e(years($e['experience'])) ?></dd></div>
            <div><dt>Expected salary</dt><dd><?= e(lpa($e['salary'])) ?></dd></div>
            <div><dt>Job type</dt><dd><?= e($e['job_type']) ?></dd></div>
            <div><dt>Gender</dt><dd><?= e($e['gender']) ?></dd></div>
            <div><dt>Date of birth</dt><dd><?= e(pretty_date($e['dob'])) ?></dd></div>
            <div><dt>Location</dt><dd><?= e($e['location']) ?></dd></div>
        </dl>
    </div>
<?php }

/** Card for a posted job. The location opens Google Maps. */
function render_job_card($j)
{ ?>
    <article class="card">
        <header class="job-head">
            <h3><?= e($j['title']) ?></h3>
            <p class="job-company"><?= e($j['company']) ?></p>
        </header>
        <p class="chips">
            <span class="chip"><?= e($j['job_type']) ?></span>
            <span class="chip"><?= e($j['workplace']) ?></span>
            <span class="chip"><?= e($j['employment']) ?></span>
        </p>
        <dl class="facts">
            <div><dt>Experience</dt><dd><?= e($j['experience']) ?></dd></div>
            <div><dt>Expected salary</dt><dd><?= e(lpa_value($j['salary'])) ?></dd></div>
            <div><dt>Location</dt><dd><a class="map-link" href="<?= e(maps_url($j['location'], $j['lat'] ?? null, $j['lng'] ?? null)) ?>" target="_blank" rel="noopener"><?= e($j['location']) ?><span class="sr-only"> (opens Google Maps in a new tab)</span></a></dd></div>
        </dl>
    </article>
<?php }

/** Date and time fields with a submit button, used to schedule or reschedule an interview. */
function render_schedule_form($id, $tab, $date, $time, $button, $error = '')
{ ?>
    <form method="post" action="interview.php?tab=<?= e($tab) ?>" class="iv-form">
        <input type="hidden" name="candidate" value="<?= e($id) ?>">
        <input type="hidden" name="action" value="schedule">
        <div class="iv-fields">
            <div class="field">
                <label for="iv-date-<?= e($id) ?>">Date</label>
                <input type="date" id="iv-date-<?= e($id) ?>" name="date" value="<?= e($date) ?>" min="<?= e(date('Y-m-d')) ?>" required>
            </div>
            <div class="field">
                <label for="iv-time-<?= e($id) ?>">Time</label>
                <input type="time" id="iv-time-<?= e($id) ?>" name="time" value="<?= e($time) ?>" required>
            </div>
        </div>
        <?php if ($error): ?><p class="err" role="alert"><?= e($error) ?></p><?php endif; ?>
        <button type="submit" class="btn btn-primary"><?= e($button) ?></button>
    </form>
<?php }

/** Interview card: who, when, and the one next action for its tab. */
function render_interview_card($e, $iv, $status, $tab, $error = '', $old = [])
{
    $overdue = $status === 'scheduled' && $iv['date'] < date('Y-m-d');
    ?>
    <article class="card iv-card iv-<?= e($status) ?>">
        <header class="card-head">
            <img class="avatar" src="<?= e($e['image']) ?>" alt="" width="56" height="56">
            <div>
                <h3><?= e($e['name']) ?></h3>
                <span class="chip"><?= e($e['industry']) ?></span>
            </div>
        </header>

        <?php if ($status !== 'unscheduled'): ?>
            <p class="iv-slot">
                <span class="iv-day"><?= e($status === 'done' ? pretty_date($iv['date']) : day_label($iv['date'])) ?></span>
                <span class="iv-time"><?= e(pretty_time($iv['time'])) ?></span>
                <?php if ($status === 'done'): ?><span class="chip chip-ok">Done</span><?php endif; ?>
                <?php if ($overdue): ?><span class="chip chip-late">Past due</span><?php endif; ?>
            </p>
        <?php endif; ?>

        <dl class="facts">
            <div><dt>Experience</dt><dd><?= e(years($e['experience'])) ?></dd></div>
            <div><dt>Expected salary</dt><dd><?= e(lpa($e['salary'])) ?></dd></div>
        </dl>

        <?php if ($status === 'unscheduled'): ?>
            <?php render_schedule_form($e['id'], $tab, $old['date'] ?? '', $old['time'] ?? '', 'Schedule interview', $error); ?>
        <?php elseif ($status !== 'done'): ?>
            <form method="post" action="interview.php?tab=<?= e($tab) ?>" class="iv-done">
                <input type="hidden" name="candidate" value="<?= e($e['id']) ?>">
                <input type="hidden" name="action" value="done">
                <button type="submit" class="btn btn-accept">Mark as done</button>
            </form>
            <details class="iv-resched"<?= $error ? ' open' : '' ?>>
                <summary>Reschedule</summary>
                <?php render_schedule_form($e['id'], $tab, $old['date'] ?? $iv['date'], $old['time'] ?? $iv['time'], 'Save new time', $error); ?>
            </details>
        <?php endif; ?>
    </article>
<?php }

/* ---------- employee side: company and job cards ---------- */

/** Chips and facts shared by the job cards. The location opens Google Maps. */
function render_job_facts($j)
{ ?>
    <p class="chips">
        <span class="chip"><?= e($j['job_type']) ?></span>
        <span class="chip"><?= e($j['workplace']) ?></span>
        <span class="chip"><?= e($j['employment']) ?></span>
    </p>
    <dl class="facts">
        <div><dt>Industry</dt><dd><?= e($j['industry']) ?></dd></div>
        <div><dt>Experience</dt><dd><?= e($j['experience']) ?></dd></div>
        <div><dt>Expected salary</dt><dd><?= e(lpa_value($j['salary'])) ?></dd></div>
        <div><dt>Location</dt><dd><a class="map-link" href="<?= e(maps_url($j['location'], $j['lat'] ?? null, $j['lng'] ?? null)) ?>" target="_blank" rel="noopener"><?= e($j['location']) ?><span class="sr-only"> (opens Google Maps in a new tab)</span></a></dd></div>
    </dl>
<?php }

/** Request card on the employee Home screen: company logo, job, industry, salary in LPA. Opens the swipe view. */
function render_request_card($j)
{ ?>
    <article class="card">
        <header class="card-head">
            <img class="logo" src="<?= e($j['logo']) ?>" alt="" width="56" height="56">
            <div>
                <h3><button type="button" class="card-open" data-open="<?= e($j['id']) ?>"><?= e($j['title']) ?></button></h3>
                <p class="job-company"><?= e($j['company']) ?></p>
            </div>
        </header>
        <dl class="facts">
            <div><dt>Industry</dt><dd><?= e($j['industry']) ?></dd></div>
            <div><dt>Experience</dt><dd><?= e($j['experience']) ?></dd></div>
            <div><dt>Expected salary</dt><dd><?= e(lpa_value($j['salary'])) ?></dd></div>
            <div><dt>Location</dt><dd><?= e($j['location']) ?></dd></div>
        </dl>
    </article>
<?php }

/** Full request shown inside the swipe card. The location is plain text here so dragging never hits a link. */
function render_request_detail($j)
{ ?>
    <div class="detail">
        <img class="logo detail-photo" src="<?= e($j['logo']) ?>" alt="" width="112" height="112">
        <h2><?= e($j['title']) ?></h2>
        <p class="job-company"><?= e($j['company']) ?></p>
        <p class="chips">
            <span class="chip"><?= e($j['job_type']) ?></span>
            <span class="chip"><?= e($j['workplace']) ?></span>
            <span class="chip"><?= e($j['employment']) ?></span>
        </p>
        <dl class="facts">
            <div><dt>Industry</dt><dd><?= e($j['industry']) ?></dd></div>
            <div><dt>Experience</dt><dd><?= e($j['experience']) ?></dd></div>
            <div><dt>Expected salary</dt><dd><?= e(lpa_value($j['salary'])) ?></dd></div>
            <div><dt>Location</dt><dd><?= e($j['location']) ?></dd></div>
            <div><dt>Request from</dt><dd><?= e($j['sender']) ?></dd></div>
        </dl>
        <p class="about"><?= e($j['about']) ?></p>
    </div>
<?php }

/** Card in Job Search, with an Apply button. */
function render_open_job_card($j)
{ ?>
    <article class="card">
        <header class="card-head">
            <img class="logo" src="<?= e($j['logo']) ?>" alt="" width="56" height="56">
            <div>
                <h3><?= e($j['title']) ?></h3>
                <p class="job-company"><?= e($j['company']) ?></p>
            </div>
        </header>
        <?php render_job_facts($j); ?>
        <form method="post" action="" class="apply-form">
            <input type="hidden" name="job" value="<?= e($j['id']) ?>">
            <button type="submit" class="btn btn-primary">Apply</button>
        </form>
    </article>
<?php }

/** Card in Applied Jobs, with the day the employee applied. */
function render_applied_card($j, $date)
{ ?>
    <article class="card">
        <header class="card-head">
            <img class="logo" src="<?= e($j['logo']) ?>" alt="" width="56" height="56">
            <div>
                <h3><?= e($j['title']) ?></h3>
                <p class="job-company"><?= e($j['company']) ?></p>
            </div>
        </header>
        <?php render_job_facts($j); ?>
        <p class="applied-on"><span class="chip chip-ok">Applied</span> <?= e(day_label($date)) ?></p>
    </article>
<?php }

/** Interview card for an employee: the job, when, and the one next action for its tab. */
function render_job_interview_card($j, $iv, $status, $tab, $error = '', $old = [])
{
    $overdue = $status === 'scheduled' && $iv['date'] < date('Y-m-d');
    ?>
    <article class="card iv-card iv-<?= e($status) ?>">
        <header class="card-head">
            <img class="logo" src="<?= e($j['logo']) ?>" alt="" width="56" height="56">
            <div>
                <h3><?= e($j['title']) ?></h3>
                <p class="job-company"><?= e($j['company']) ?></p>
            </div>
        </header>

        <?php if ($status !== 'unscheduled'): ?>
            <p class="iv-slot">
                <span class="iv-day"><?= e($status === 'done' ? pretty_date($iv['date']) : day_label($iv['date'])) ?></span>
                <span class="iv-time"><?= e(pretty_time($iv['time'])) ?></span>
                <?php if ($status === 'done'): ?><span class="chip chip-ok">Done</span><?php endif; ?>
                <?php if ($overdue): ?><span class="chip chip-late">Past due</span><?php endif; ?>
            </p>
        <?php endif; ?>

        <dl class="facts">
            <div><dt>Experience</dt><dd><?= e($j['experience']) ?></dd></div>
            <div><dt>Expected salary</dt><dd><?= e(lpa_value($j['salary'])) ?></dd></div>
            <div><dt>Location</dt><dd><?= e($j['location']) ?></dd></div>
        </dl>

        <?php if ($status === 'unscheduled'): ?>
            <?php render_schedule_form($j['id'], $tab, $old['date'] ?? '', $old['time'] ?? '', 'Schedule interview', $error); ?>
        <?php elseif ($status !== 'done'): ?>
            <form method="post" action="interview.php?tab=<?= e($tab) ?>" class="iv-done">
                <input type="hidden" name="candidate" value="<?= e($j['id']) ?>">
                <input type="hidden" name="action" value="done">
                <button type="submit" class="btn btn-accept">Mark as done</button>
            </form>
            <details class="iv-resched"<?= $error ? ' open' : '' ?>>
                <summary>Reschedule</summary>
                <?php render_schedule_form($j['id'], $tab, $old['date'] ?? $iv['date'], $old['time'] ?? $iv['time'], 'Save new time', $error); ?>
            </details>
        <?php endif; ?>
    </article>
<?php }

/* ---------- swipe deck cards (Home screens) ---------- */

/** One tile in the facts grid of a deck card. */
function render_tile($label, $value)
{ ?>
    <div class="sc-tile"><span><?= e($label) ?></span><b><?= e($value) ?></b></div>
<?php }

/** Employee Home: a company request as a full swipe card. $i is its place in the stack. */
function render_request_swipe($j, $i)
{ ?>
    <article class="swipe-card" data-id="<?= e($j['id']) ?>" style="--i:<?= (int) min($i, 3) ?>;--hue:<?= (int) (crc32($j['id']) % 360) ?>">
        <span class="stamp stamp-no" aria-hidden="true">Pass</span>
        <span class="stamp stamp-yes" aria-hidden="true">Confirm</span>
        <div class="sc-hero">
            <img class="logo sc-photo" src="<?= e($j['logo']) ?>" alt="" width="84" height="84" draggable="false">
            <p class="sc-eyebrow">Request from <?= e($j['sender']) ?></p>
            <h2><?= e($j['title']) ?></h2>
            <p class="sc-sub"><?= e($j['company']) ?></p>
        </div>
        <div class="sc-body">
            <p class="chips">
                <span class="chip"><?= e($j['job_type']) ?></span>
                <span class="chip"><?= e($j['workplace']) ?></span>
                <span class="chip"><?= e($j['employment']) ?></span>
            </p>
            <div class="sc-tiles">
                <?php render_tile('Industry', $j['industry']); ?>
                <?php render_tile('Experience', $j['experience']); ?>
                <?php render_tile('Expected salary', lpa_value($j['salary'])); ?>
                <?php render_tile('Location', $j['location']); ?>
            </div>
            <p class="sc-about"><?= e($j['about']) ?></p>
        </div>
    </article>
<?php }

/** Employer Home: an employee as a full swipe card. */
function render_candidate_swipe($e, $i)
{ ?>
    <article class="swipe-card" data-id="<?= e($e['id']) ?>" style="--i:<?= (int) min($i, 3) ?>;--hue:<?= (int) (crc32($e['id']) % 360) ?>">
        <span class="stamp stamp-no" aria-hidden="true">Pass</span>
        <span class="stamp stamp-yes" aria-hidden="true">Accept</span>
        <div class="sc-hero">
            <img class="avatar sc-photo" src="<?= e($e['image']) ?>" alt="" width="84" height="84" draggable="false">
            <p class="sc-eyebrow"><?= e($e['job_type']) ?> &middot; <?= e($e['location']) ?></p>
            <h2><?= e($e['name']) ?></h2>
            <p class="sc-sub"><?= e($e['industry']) ?></p>
        </div>
        <div class="sc-body">
            <div class="sc-tiles">
                <?php render_tile('Experience', years($e['experience'])); ?>
                <?php render_tile('Expected salary', lpa($e['salary'])); ?>
                <?php render_tile('Gender', $e['gender']); ?>
                <?php render_tile('Date of birth', pretty_date($e['dob'])); ?>
            </div>
        </div>
    </article>
<?php }

/** Pass and confirm buttons plus the hidden form the swipe script submits. */
function render_deck_controls($yes)
{ ?>
    <div class="deck-actions">
        <button type="button" class="round-btn round-no" data-decide="reject" aria-label="Pass">
            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg>
            <span>Pass</span>
        </button>
        <button type="button" class="round-btn round-yes" data-decide="accept" aria-label="<?= e($yes) ?>">
            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12.5 4.5 4.5L19 7.5"/></svg>
            <span><?= e($yes) ?></span>
        </button>
    </div>
    <p class="deck-hint">Drag the card, or use the <kbd>&larr;</kbd> <kbd>&rarr;</kbd> keys.</p>
    <form method="post" action="" id="decision-form" hidden>
        <input type="hidden" name="candidate" id="decision-candidate" value="">
        <input type="hidden" name="decision" id="decision-value" value="">
    </form>
<?php }

/** Collapsible search and filter panel above the deck. */
function render_filter_sheet($searchLabel, $q, $loc, $ind, $locations, $filtered, $homeUrl)
{ ?>
    <details class="filter-sheet"<?= $filtered ? ' open' : '' ?>>
        <summary>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 7h16M7 12h10M10 17h4"/></svg>
            Filters<?php if ($filtered): ?> <span class="chip chip-on">on</span><?php endif; ?>
        </summary>
        <form method="get" class="filters">
            <div class="field">
                <label for="q">Search</label>
                <input type="text" id="q" name="q" placeholder="<?= e($searchLabel) ?>" value="<?= e($q) ?>">
            </div>
            <div class="field">
                <label for="location">Location</label>
                <select id="location" name="location">
                    <option value="">All locations</option>
                    <?php foreach ($locations as $l): ?>
                        <option value="<?= e($l) ?>"<?= $loc === $l ? ' selected' : '' ?>><?= e($l) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field">
                <label for="industry">Industry</label>
                <select id="industry" name="industry">
                    <option value="">All industries</option>
                    <?php foreach (INDUSTRIES as $i): ?>
                        <option value="<?= e($i) ?>"<?= $ind === $i ? ' selected' : '' ?>><?= e($i) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">Apply</button>
                <?php if ($filtered): ?><a class="btn btn-ghost" href="<?= e($homeUrl) ?>">Clear</a><?php endif; ?>
            </div>
        </form>
    </details>
<?php }
