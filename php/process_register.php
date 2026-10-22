<?php
$data_dir = __DIR__ . '/../data/';
$file = $data_dir . 'registrations.json';

$fullname  = trim(strip_tags($_POST['fullname'] ?? ''));
$email     = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));
$mobile    = trim(strip_tags($_POST['mobile'] ?? ''));
$gender    = trim(strip_tags($_POST['gender'] ?? ''));
$password  = $_POST['password'] ?? '';
$confirm   = $_POST['confirm_password'] ?? '';
$studentid = trim(strip_tags($_POST['studentid'] ?? ''));
$department= trim(strip_tags($_POST['department'] ?? ''));
$year      = trim(strip_tags($_POST['year'] ?? ''));
$interests = isset($_POST['interests']) && is_array($_POST['interests'])
             ? array_map('strip_tags', $_POST['interests']) : [];
$terms     = isset($_POST['terms']);

$errors = [];
if (!$fullname || !preg_match('/^[A-Za-z\s]{3,50}$/', $fullname))           $errors[] = 'Valid full name required.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL))                               $errors[] = 'Valid email required.';
if (!preg_match('/^[6-9]\d{9}$/', $mobile))                                  $errors[] = 'Valid 10-digit mobile required.';
if (!in_array($gender, ['Male', 'Female', 'Other']))                         $errors[] = 'Gender selection required.';
if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/', $password)) $errors[] = 'Weak password.';
if ($password !== $confirm)                                                   $errors[] = 'Passwords do not match.';
if (!$studentid)                                                              $errors[] = 'Student ID required.';
if (!in_array($department, ['cs','it','ece','mech','business','arts']))       $errors[] = 'Valid department required.';
if (!in_array($year, ['1','2','3','4','graduate']))                           $errors[] = 'Valid year required.';
if (!$terms)                                                                  $errors[] = 'Accept terms and conditions.';

if ($errors) {
    http_response_code(400);
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

$records = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

foreach ($records as $r) {
    if (strtolower($r['email']) === strtolower($email)) {
        http_response_code(409);
        echo json_encode(['success' => false, 'errors' => ['Email already registered.']]);
        exit;
    }
}

$records[] = [
    'id'         => count($records) + 1,
    'fullname'   => $fullname,
    'email'      => strtolower($email),
    'mobile'     => $mobile,
    'gender'     => $gender,
    'studentid'  => $studentid,
    'department' => $department,
    'year'       => $year,
    'interests'  => $interests,
    'timestamp'  => date('Y-m-d H:i:s'),
];
file_put_contents($file, json_encode($records, JSON_PRETTY_PRINT));

echo json_encode(['success' => true, 'message' => 'Registration submitted successfully!']);
