<?php
require_once __DIR__ . '/helpers.php';

/** 24 dummy employees. Email, phone and the shared demo password are only used to sign in. */
function employees()
{
    static $list = null;
    if ($list !== null) {
        return $list;
    }
    $rows = [
        ['priya.mehta',     '9824010001', 5,  900000,  'Female', '1996-03-14', 'Information Technology', 'Full-time',  'Ahmedabad'],
        ['rohan.desai',     '9824010002', 2,  450000,  'Male',   '1999-07-22', 'Marketing',              'Full-time',  'Surat'],
        ['ananya.iyer',     '9824010003', 8,  1800000, 'Female', '1992-11-05', 'Finance',                'Full-time',  'Mumbai'],
        ['karan.patel',     '9824010004', 0,  240000,  'Male',   '2002-01-30', 'Information Technology', 'Internship', 'Vadodara'],
        ['sneha.joshi',     '9824010005', 4,  650000,  'Female', '1997-09-18', 'Healthcare',             'Full-time',  'Pune'],
        ['imran.shaikh',    '9824010006', 6,  720000,  'Male',   '1994-05-09', 'Manufacturing',          'Contract',   'Rajkot'],
        ['neha.kapoor',     '9824010007', 3,  520000,  'Female', '1998-12-27', 'Education',              'Part-time',  'Delhi'],
        ['vikram.rao',      '9824010008', 10, 2400000, 'Male',   '1990-02-11', 'Information Technology', 'Full-time',  'Bengaluru'],
        ['divya.nair',      '9824010009', 1,  300000,  'Female', '2001-04-03', 'Design',                 'Freelance',  'Kochi'],
        ['arjun.singh',     '9824010010', 7,  1100000, 'Male',   '1993-08-21', 'Logistics',              'Full-time',  'Gurugram'],
        ['meera.shah',      '9824010011', 12, 2800000, 'Female', '1988-06-15', 'Retail',                 'Full-time',  'Ahmedabad'],
        ['harsh.trivedi',   '9824010012', 2,  380000,  'Male',   '2000-10-08', 'Sales',                  'Full-time',  'Gandhinagar'],
        ['pooja.bansal',    '9824010013', 5,  840000,  'Female', '1995-01-25', 'Human Resources',        'Full-time',  'Jaipur'],
        ['sahil.khan',      '9824010014', 3,  480000,  'Male',   '1999-03-19', 'Hospitality',            'Full-time',  'Goa'],
        ['tanvi.pillai',    '9824010015', 9,  1500000, 'Female', '1991-07-30', 'Healthcare',             'Contract',   'Chennai'],
        ['devansh.gupta',   '9824010016', 0,  220000,  'Male',   '2003-05-12', 'Marketing',              'Internship', 'Indore'],
        ['riya.sharma',     '9824010017', 6,  960000,  'Female', '1994-09-02', 'Design',                 'Full-time',  'Hyderabad'],
        ['manish.chauhan',  '9824010018', 15, 3200000, 'Male',   '1985-12-01', 'Manufacturing',          'Full-time',  'Nashik'],
        ['aisha.khan',      '9824010019', 4,  600000,  'Female', '1997-02-16', 'Education',              'Full-time',  'Lucknow'],
        ['yash.modi',       '9824010020', 1,  280000,  'Male',   '2001-11-23', 'Sales',                  'Part-time',  'Ahmedabad'],
        ['kavya.reddy',     '9824010021', 7,  1300000, 'Female', '1993-04-28', 'Finance',                'Full-time',  'Hyderabad'],
        ['nikhil.verma',    '9824010022', 3,  540000,  'Male',   '1998-08-06', 'Logistics',              'Contract',   'Kolkata'],
        ['shreya.bhatt',    '9824010023', 2,  400000,  'Female', '2000-06-20', 'Hospitality',            'Part-time',  'Udaipur'],
        ['rahul.menon',     '9824010024', 11, 2100000, 'Male',   '1989-10-13', 'Information Technology', 'Freelance',  'Thiruvananthapuram'],
    ];
    $list = [];
    foreach ($rows as $i => $r) {
        $list[] = [
            'id'         => 'emp' . ($i + 1),
            'name'       => ucwords(str_replace('.', ' ', $r[0])),
            'email'      => $r[0] . '@example.com',
            'phone'      => $r[1],
            'image'      => avatar_uri($r[0], $i),
            'experience' => $r[2],
            'salary'     => $r[3],
            'gender'     => $r[4],
            'dob'        => $r[5],
            'industry'   => $r[6],
            'job_type'   => $r[7],
            'location'   => $r[8],
        ];
    }
    return $list;
}

/** 24 dummy employers. */
function employers()
{
    static $list = null;
    if ($list !== null) {
        return $list;
    }
    $rows = [
        ['hr@kirantextiles.example',   '9898100001', 'Meera Shah, HR Head at Kiran Textiles',               '201-500',  'Manufacturing',         'Family-run textile mill supplying fabric to export houses across India and the Gulf.'],
        ['careers@brightpath.example', '9898100002', 'Anil Verma, Director at Brightpath Learning',         '51-200',   'Education',             'Tutoring and test-prep centres for classes 8 to 12, with in-person and online batches.'],
        ['jobs@nimbuscloud.example',   '9898100003', 'Sara Dsouza, Talent Lead at Nimbus Cloudworks',       '51-200',   'Information Technology','Cloud migration and managed hosting for mid-sized businesses. Small teams, direct client work.'],
        ['hire@saffrontable.example',  '9898100004', 'Rajiv Malhotra, GM at Saffron Table Hotels',          '501-1000', 'Hospitality',           'A chain of business hotels and banquet halls in eleven cities, known for regional menus.'],
        ['people@lumenhealth.example', '9898100005', 'Dr. Farah Ansari, COO at Lumen Health Clinics',       '201-500',  'Healthcare',            'Neighbourhood clinics offering consultations, diagnostics and day-care procedures.'],
        ['talent@orbitfreight.example','9898100006', 'Gaurav Jain, VP People at Orbit Freight',             '1000+',    'Logistics',             'Road and rail freight network with warehousing hubs along the western corridor.'],
        ['hello@pixelhive.example',    '9898100007', 'Isha Kulkarni, Founder at Pixelhive Studio',          '11-50',    'Design',                'Brand identity, packaging and product design studio working with D2C companies.'],
        ['careers@ledgerline.example', '9898100008', 'Prakash Nair, Partner at Ledgerline Advisors',        '11-50',    'Finance',               'Tax, audit and virtual CFO services for startups and family businesses.'],
        ['hr@harvestmart.example',     '9898100009', 'Lata Pawar, HR Manager at Harvest Mart',              '501-1000', 'Retail',                'Supermarket group with 60 stores selling groceries, home essentials and fresh produce.'],
        ['jobs@quickcart.example',     '9898100010', 'Dev Malik, Head of Hiring at Quickcart Commerce',     '201-500',  'Retail',                'Online marketplace for home and kitchen products with next-day delivery in major cities.'],
        ['hr@vertexauto.example',      '9898100011', 'Suresh Iyer, Plant HR at Vertex Auto Components',     '1000+',    'Manufacturing',         'Precision parts supplier to two-wheeler and passenger-vehicle makers.'],
        ['team@mosaicmarketing.example','9898100012','Tanya Bose, Co-founder at Mosaic Marketing',          '11-50',    'Marketing',             'Performance marketing and content agency for consumer brands.'],
        ['hire@talentbridge.example',  '9898100013', 'Nilesh Parmar, Recruiter at Talentbridge Staffing',   '51-200',   'Human Resources',       'Staffing partner placing temporary and permanent talent in factories and offices.'],
        ['careers@greenleafpharma.example','9898100014','Dr. Anjali Rao, HR Head at Greenleaf Pharma',      '501-1000', 'Healthcare',            'Generic medicines maker with two manufacturing units and a growing export business.'],
        ['jobs@skylinerealty.example', '9898100015', 'Vishal Thakkar, Sales Director at Skyline Realty',    '51-200',   'Real Estate',           'Residential and commercial developer with projects in Ahmedabad and Gandhinagar.'],
        ['hello@codecraft.example',    '9898100016', 'Ritu Sen, Founder at Codecraft Labs',                 '1-10',     'Information Technology','Tiny product team building scheduling software for salons and clinics.'],
        ['team@urbanroots.example',    '9898100017', 'Aman Sethi, Owner at Urban Roots Cafe',               '1-10',     'Hospitality',           'Independent cafe and bakery with a busy weekend crowd and a small catering arm.'],
        ['hr@tribefitness.example',    '9898100018', 'Komal Desai, Operations Head at Tribe Fitness',       '51-200',   'Healthcare',            'Gyms and wellness studios offering strength training, yoga and physiotherapy.'],
        ['jobs@frameworks.example',    '9898100019', 'Sameer Qureshi, Producer at Frameworks Media',        '51-200',   'Media & Entertainment', 'Production house making web series, ads and corporate films.'],
        ['hr@bluewave.example',        '9898100020', 'Harini Krishnan, People Partner at Bluewave Logistics','201-500', 'Logistics',             'Last-mile delivery and cold-chain operator serving food and pharma clients.'],
        ['careers@apexbrokers.example','9898100021', 'Mohit Agarwal, Branch Head at Apex Insurance Brokers','51-200',   'Finance',               'Independent insurance brokerage covering health, motor and commercial policies.'],
        ['hr@kindredschools.example',  '9898100022', 'Sunita Rao, Principal at Kindred Schools',            '201-500',  'Education',             'Group of five CBSE schools focused on activity-based learning.'],
        ['jobs@sparksolar.example',    '9898100023', 'Chirag Shah, Plant Manager at Spark Solar',           '51-200',   'Manufacturing',         'Assembles rooftop solar panels and inverters for homes and small factories.'],
        ['hello@dwellinteriors.example','9898100024','Pallavi Joshi, Design Head at Dwell Interiors',       '11-50',    'Design',                'Interior design and execution studio for apartments and boutique offices.'],
    ];
    $list = [];
    foreach ($rows as $i => $r) {
        $list[] = [
            'id'       => 'emr' . ($i + 1),
            'email'    => $r[0],
            'phone'    => $r[1],
            'logo'     => logo_uri($i),
            'details'  => $r[2],
            'size'     => $r[3],
            'industry' => $r[4],
            'about'    => $r[5],
        ];
    }
    return $list;
}

/** Finds a dummy user by email or phone number. Returns [role, record] or null. */
function find_user($identifier)
{
    $needle = strtolower(trim($identifier));
    $digits = substr(preg_replace('/\D+/', '', $needle), -10);
    foreach (['employee' => employees(), 'employer' => employers()] as $role => $records) {
        foreach ($records as $r) {
            if (strtolower($r['email']) === $needle || ($digits !== '' && strlen($digits) === 10 && $r['phone'] === $digits)) {
                return [$role, $r];
            }
        }
    }
    return null;
}

/** 20 dummy posted jobs. */
function dummy_jobs()
{
    static $list = null;
    if ($list !== null) {
        return $list;
    }
    $rows = [
        ['Production Supervisor',        'Kiran Textiles',          'Permanent', '5-10 years', 6,   'Surat',       'Full-time', 'On-site'],
        ['Maths Teacher, Grades 8 to 10','Brightpath Learning',     'Permanent', '1-3 years',  4.5, 'Ahmedabad',   'Full-time', 'On-site'],
        ['Cloud Support Engineer',       'Nimbus Cloudworks',       'Permanent', '1-3 years',  7,   'Pune',        'Full-time', 'Hybrid'],
        ['Banquet Manager',              'Saffron Table Hotels',    'Permanent', '5-10 years', 8,   'Mumbai',      'Full-time', 'On-site'],
        ['Staff Nurse',                  'Lumen Health Clinics',    'Permanent', '1-3 years',  3.6, 'Vadodara',    'Full-time', 'On-site'],
        ['Fleet Operations Executive',   'Orbit Freight',           'Permanent', '3-5 years',  5.5, 'Gurugram',    'Full-time', 'On-site'],
        ['UI Designer',                  'Pixelhive Studio',        'Contract',  '1-3 years',  6,   'Bengaluru',   'Full-time', 'Remote'],
        ['Tax Associate',                'Ledgerline Advisors',     'Permanent', 'Fresher',    3,   'Ahmedabad',   'Full-time', 'Hybrid'],
        ['Store Manager',                'Harvest Mart',            'Permanent', '5-10 years', 7.5, 'Rajkot',      'Full-time', 'On-site'],
        ['Category Analyst',             'Quickcart Commerce',      'Permanent', '3-5 years',  9,   'Bengaluru',   'Full-time', 'Hybrid'],
        ['CNC Machine Operator',         'Vertex Auto Components',  'Permanent', '1-3 years',  3.2, 'Pune',        'Full-time', 'On-site'],
        ['Content Writer',               'Mosaic Marketing',        'Freelance', '1-3 years',  4,   'Delhi',       'Part-time', 'Remote'],
        ['Recruitment Executive',        'Talentbridge Staffing',   'Permanent', '1-3 years',  3.8, 'Jaipur',      'Full-time', 'On-site'],
        ['Quality Control Analyst',      'Greenleaf Pharma',        'Permanent', '3-5 years',  5,   'Hyderabad',   'Full-time', 'On-site'],
        ['Sales Executive',              'Skyline Realty',          'Permanent', '1-3 years',  4.2, 'Gandhinagar', 'Full-time', 'On-site'],
        ['Frontend Developer',           'Codecraft Labs',          'Permanent', '3-5 years',  12,  'Kochi',       'Full-time', 'Remote'],
        ['Barista',                      'Urban Roots Cafe',        'Permanent', 'Fresher',    2.4, 'Indore',      'Part-time', 'On-site'],
        ['Yoga Instructor',              'Tribe Fitness',           'Contract',  '1-3 years',  3,   'Chennai',     'Part-time', 'On-site'],
        ['Video Editor',                 'Frameworks Media',        'Contract',  '3-5 years',  6.5, 'Mumbai',      'Full-time', 'Hybrid'],
        ['Delivery Hub Manager',         'Bluewave Logistics',      'Permanent', '5-10 years', 8.5, 'Hyderabad',   'Full-time', 'On-site'],
    ];
    $list = [];
    foreach ($rows as $r) {
        $list[] = [
            'title' => $r[0], 'company' => $r[1], 'employment' => $r[2], 'experience' => $r[3],
            'salary' => $r[4], 'location' => $r[5], 'job_type' => $r[6], 'workplace' => $r[7],
        ];
    }
    return $list;
}

/** Jobs posted in this session come first, then the dummy ones. */
function all_jobs()
{
    return array_merge(array_reverse($_SESSION['jobs'] ?? []), dummy_jobs());
}

/** Dummy notifications for the bell on the employer Home page. */
function notifications()
{
    return [
        ['type' => 'profiles',   'text' => '3 new employee profiles were added in Information Technology.',          'time' => '2 hours ago'],
        ['type' => 'jobs',       'text' => 'Your job post for Cloud Support Engineer was viewed 48 times.',           'time' => 'Yesterday'],
        ['type' => 'interviews', 'text' => 'Review the employees waiting for your decision on Home.',                 'time' => 'Yesterday'],
        ['type' => 'profiles',   'text' => '5 new employee profiles were added in Ahmedabad.',                        'time' => '2 days ago'],
        ['type' => 'updates',    'text' => 'Welcome to HireSwipe. Swipe right on an employee to move them to Interview.', 'time' => '3 days ago'],
    ];
}

/* ---------- employee side: jobs, company requests ---------- */

/** Company name taken from an employer's details line, e.g. "Meera Shah, HR Head at Kiran Textiles". */
function company_of($employer)
{
    $d = $employer['details'];
    $p = strrpos($d, ' at ');
    return $p === false ? $d : trim(substr($d, $p + 4));
}

/**
 * 76 dummy jobs: the 20 posted jobs above plus 56 more. Each one carries its company's logo,
 * industry, about text and the employer who posted it. Ids are job1 to job76.
 */
function job_pool()
{
    static $pool = null;
    if ($pool !== null) {
        return $pool;
    }
    $rows = [
        ['Loom Operator',                 'Kiran Textiles',         'Permanent', '1-3 years',  2.8, 'Surat',       'Full-time', 'On-site'],
        ['Textile Quality Inspector',     'Kiran Textiles',         'Permanent', '3-5 years',  4,   'Surat',       'Full-time', 'On-site'],
        ['English Teacher, Grades 6 to 8','Brightpath Learning',    'Permanent', '1-3 years',  4.2, 'Vadodara',    'Full-time', 'On-site'],
        ['Academic Counsellor',           'Brightpath Learning',    'Permanent', 'Fresher',    3,   'Ahmedabad',   'Full-time', 'Hybrid'],
        ['DevOps Engineer',               'Nimbus Cloudworks',      'Permanent', '3-5 years',  11,  'Pune',        'Full-time', 'Hybrid'],
        ['Backend Developer',             'Nimbus Cloudworks',      'Permanent', '3-5 years',  13,  'Bengaluru',   'Full-time', 'Remote'],
        ['QA Engineer',                   'Nimbus Cloudworks',      'Permanent', '1-3 years',  6,   'Pune',        'Full-time', 'Hybrid'],
        ['Front Desk Executive',          'Saffron Table Hotels',   'Permanent', 'Fresher',    2.6, 'Mumbai',      'Full-time', 'On-site'],
        ['Sous Chef',                     'Saffron Table Hotels',   'Permanent', '5-10 years', 6.5, 'Goa',         'Full-time', 'On-site'],
        ['Lab Technician',                'Lumen Health Clinics',   'Permanent', '1-3 years',  3.4, 'Ahmedabad',   'Full-time', 'On-site'],
        ['Physiotherapist',               'Lumen Health Clinics',   'Permanent', '3-5 years',  5.2, 'Surat',       'Full-time', 'On-site'],
        ['Warehouse Supervisor',          'Orbit Freight',          'Permanent', '3-5 years',  5,   'Gurugram',    'Full-time', 'On-site'],
        ['Customer Support Executive',    'Orbit Freight',          'Permanent', 'Fresher',    2.8, 'Delhi',       'Full-time', 'Hybrid'],
        ['Packaging Designer',            'Pixelhive Studio',       'Permanent', '3-5 years',  7.5, 'Bengaluru',   'Full-time', 'Hybrid'],
        ['Motion Designer',               'Pixelhive Studio',       'Contract',  '1-3 years',  6,   'Mumbai',      'Full-time', 'Remote'],
        ['Audit Associate',               'Ledgerline Advisors',    'Permanent', '1-3 years',  5,   'Ahmedabad',   'Full-time', 'On-site'],
        ['Financial Analyst',             'Ledgerline Advisors',    'Permanent', '3-5 years',  9,   'Mumbai',      'Full-time', 'Hybrid'],
        ['Inventory Executive',           'Harvest Mart',           'Permanent', '1-3 years',  3.2, 'Rajkot',      'Full-time', 'On-site'],
        ['Visual Merchandiser',           'Harvest Mart',           'Permanent', '3-5 years',  4.5, 'Pune',        'Full-time', 'On-site'],
        ['Customer Success Executive',    'Quickcart Commerce',     'Permanent', '1-3 years',  5,   'Bengaluru',   'Full-time', 'Remote'],
        ['Supply Planner',                'Quickcart Commerce',     'Permanent', '3-5 years',  8,   'Gurugram',    'Full-time', 'Hybrid'],
        ['Maintenance Engineer',          'Vertex Auto Components', 'Permanent', '3-5 years',  5.8, 'Pune',        'Full-time', 'On-site'],
        ['Quality Engineer',              'Vertex Auto Components', 'Permanent', '3-5 years',  6.5, 'Chennai',     'Full-time', 'On-site'],
        ['SEO Specialist',                'Mosaic Marketing',       'Permanent', '1-3 years',  5,   'Delhi',       'Full-time', 'Remote'],
        ['Social Media Manager',          'Mosaic Marketing',       'Permanent', '3-5 years',  7,   'Mumbai',      'Full-time', 'Hybrid'],
        ['HR Generalist',                 'Talentbridge Staffing',  'Permanent', '3-5 years',  5.5, 'Jaipur',      'Full-time', 'On-site'],
        ['Payroll Executive',             'Talentbridge Staffing',  'Permanent', '1-3 years',  3.6, 'Indore',      'Full-time', 'On-site'],
        ['Production Chemist',            'Greenleaf Pharma',       'Permanent', '1-3 years',  4.4, 'Hyderabad',   'Full-time', 'On-site'],
        ['Regulatory Affairs Executive',  'Greenleaf Pharma',       'Permanent', '3-5 years',  7.2, 'Hyderabad',   'Full-time', 'Hybrid'],
        ['Property Consultant',           'Skyline Realty',         'Permanent', '1-3 years',  4,   'Ahmedabad',   'Full-time', 'On-site'],
        ['Site Engineer',                 'Skyline Realty',         'Permanent', '3-5 years',  6,   'Gandhinagar', 'Full-time', 'On-site'],
        ['Full Stack Developer',          'Codecraft Labs',         'Permanent', '3-5 years',  14,  'Kochi',       'Full-time', 'Remote'],
        ['Product Manager',               'Codecraft Labs',         'Permanent', '5-10 years', 22,  'Bengaluru',   'Full-time', 'Hybrid'],
        ['Pastry Cook',                   'Urban Roots Cafe',       'Permanent', '1-3 years',  3,   'Indore',      'Full-time', 'On-site'],
        ['Personal Trainer',              'Tribe Fitness',          'Contract',  '1-3 years',  3.6, 'Ahmedabad',   'Part-time', 'On-site'],
        ['Nutrition Coach',               'Tribe Fitness',          'Freelance', '3-5 years',  4.8, 'Chennai',     'Part-time', 'Remote'],
        ['Assistant Director',            'Frameworks Media',       'Contract',  '3-5 years',  7,   'Mumbai',      'Full-time', 'On-site'],
        ['Sound Engineer',                'Frameworks Media',       'Contract',  '3-5 years',  6,   'Mumbai',      'Full-time', 'On-site'],
        ['Cold Chain Coordinator',        'Bluewave Logistics',     'Permanent', '3-5 years',  5.6, 'Hyderabad',   'Full-time', 'On-site'],
        ['Delivery Executive',            'Bluewave Logistics',     'Permanent', 'Fresher',    2.4, 'Pune',        'Full-time', 'On-site'],
        ['Insurance Advisor',             'Apex Insurance Brokers', 'Permanent', 'Fresher',    3,   'Ahmedabad',   'Full-time', 'On-site'],
        ['Claims Executive',              'Apex Insurance Brokers', 'Permanent', '1-3 years',  4.2, 'Surat',       'Full-time', 'Hybrid'],
        ['Relationship Manager',          'Apex Insurance Brokers', 'Permanent', '3-5 years',  7,   'Mumbai',      'Full-time', 'On-site'],
        ['Primary Teacher',               'Kindred Schools',        'Permanent', '1-3 years',  3.6, 'Lucknow',     'Full-time', 'On-site'],
        ['School Counsellor',             'Kindred Schools',        'Permanent', '3-5 years',  4.8, 'Lucknow',     'Full-time', 'On-site'],
        ['Sports Coach',                  'Kindred Schools',        'Contract',  '1-3 years',  3.2, 'Kolkata',     'Part-time', 'On-site'],
        ['Solar Installation Technician', 'Spark Solar',            'Permanent', 'Fresher',    2.8, 'Nashik',      'Full-time', 'On-site'],
        ['Electrical Engineer',           'Spark Solar',            'Permanent', '3-5 years',  6.2, 'Pune',        'Full-time', 'On-site'],
        ['Sales Engineer',                'Spark Solar',            'Permanent', '1-3 years',  5,   'Ahmedabad',   'Full-time', 'Hybrid'],
        ['Interior Designer',             'Dwell Interiors',        'Permanent', '3-5 years',  7.5, 'Ahmedabad',   'Full-time', 'Hybrid'],
        ['Site Supervisor',               'Dwell Interiors',        'Permanent', '3-5 years',  5,   'Vadodara',    'Full-time', 'On-site'],
        ['3D Visualiser',                 'Dwell Interiors',        'Freelance', '1-3 years',  5.5, 'Bengaluru',   'Part-time', 'Remote'],
        ['Brand Strategist',              'Pixelhive Studio',       'Permanent', '5-10 years', 12,  'Mumbai',      'Full-time', 'Hybrid'],
        ['Medical Representative',        'Greenleaf Pharma',       'Permanent', '1-3 years',  4,   'Rajkot',      'Full-time', 'On-site'],
        ['Technical Support Lead',        'Nimbus Cloudworks',      'Permanent', '5-10 years', 10,  'Pune',        'Full-time', 'Hybrid'],
        ['Talent Acquisition Specialist', 'Talentbridge Staffing',  'Permanent', '3-5 years',  6,   'Gurugram',    'Full-time', 'Hybrid'],
    ];
    $jobs = dummy_jobs();
    foreach ($rows as $r) {
        $jobs[] = [
            'title' => $r[0], 'company' => $r[1], 'employment' => $r[2], 'experience' => $r[3],
            'salary' => $r[4], 'location' => $r[5], 'job_type' => $r[6], 'workplace' => $r[7],
        ];
    }
    $byCompany = [];
    foreach (employers() as $emr) {
        $byCompany[company_of($emr)] = $emr;
    }
    $pool = [];
    foreach ($jobs as $i => $j) {
        $emr = $byCompany[$j['company']] ?? null;
        $j['id']       = 'job' . ($i + 1);
        $j['logo']     = $emr ? $emr['logo'] : logo_uri($i);
        $j['industry'] = $emr ? $emr['industry'] : '';
        $j['about']    = $emr ? $emr['about'] : '';
        $j['sender']   = $emr ? $emr['details'] : '';
        $pool[] = $j;
    }
    return $pool;
}

function job_by_id($id)
{
    static $map = null;
    if ($map === null) {
        $map = [];
        foreach (job_pool() as $j) {
            $map[$j['id']] = $j;
        }
    }
    return $map[$id] ?? null;
}

/** Jobs that companies have sent a request for. These are the cards on the employee Home screen. */
function request_ids()
{
    return [
        'job3', 'job7', 'job10', 'job16', 'job25', 'job26', 'job34', 'job37',
        'job41', 'job45', 'job49', 'job52', 'job53', 'job57', 'job70', 'job73',
    ];
}

/** Jobs to browse in Job Search: not a request, not applied to and not in Interview. */
function open_jobs()
{
    $taken = array_merge(request_ids(), $_SESSION['accepted'] ?? [], array_keys($_SESSION['applied'] ?? []));
    $out = [];
    foreach (job_pool() as $j) {
        if (!in_array($j['id'], $taken, true)) {
            $out[] = $j;
        }
    }
    return $out;
}

/** Dummy notifications for the bell on the employee Home page. */
function employee_notifications()
{
    return [
        ['type' => 'requests',   'text' => 'Pixelhive Studio sent you a request for UI Designer.',                          'time' => '3 hours ago'],
        ['type' => 'jobs',       'text' => '6 new jobs were posted in Information Technology.',                            'time' => 'Yesterday'],
        ['type' => 'requests',   'text' => 'Quickcart Commerce sent you a request for Category Analyst.',                   'time' => 'Yesterday'],
        ['type' => 'jobs',       'text' => '4 new jobs were posted in Ahmedabad.',                                         'time' => '2 days ago'],
        ['type' => 'updates',    'text' => 'Welcome to HireSwipe. Swipe right on a request to move it to Interview.',      'time' => '3 days ago'],
    ];
}
