<?php
header('Content-Type: application/json');
require __DIR__ . '/db.php';

$fn  = trim(strip_tags($_POST['fullname'] ?? ''));
$em  = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));
$mb  = trim(strip_tags($_POST['mobile'] ?? ''));
$gn  = trim(strip_tags($_POST['gender'] ?? ''));
$pw  = $_POST['password'] ?? '';
$cpw = $_POST['confirm_password'] ?? '';
$sid = trim(strip_tags($_POST['studentid'] ?? ''));
$dep = trim(strip_tags($_POST['department'] ?? ''));
$yr  = trim(strip_tags($_POST['year'] ?? ''));
$int = isset($_POST['interests']) && is_array($_POST['interests'])
       ? implode(',', array_map('strip_tags', $_POST['interests'])) : '';
$trm = isset($_POST['terms']);

$errors = [];
if (!$fn || !preg_match('/^[A-Za-z\s]{3,50}$/', $fn))                            $errors[] = 'Valid full name required.';
if (!filter_var($em, FILTER_VALIDATE_EMAIL))                                       $errors[] = 'Valid email required.';
if (!preg_match('/^[6-9]\d{9}$/', $mb))                                           $errors[] = 'Valid 10-digit mobile required.';
if (!in_array($gn, ['Male', 'Female', 'Other']))                                  $errors[] = 'Gender selection required.';
if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/', $pw)) $errors[] = 'Password too weak (min 8 chars, upper, lower, digit, special).';
if ($pw !== $cpw)                                                                  $errors[] = 'Passwords do not match.';
if (!$sid)                                                                         $errors[] = 'Student ID required.';
if (!in_array($dep, ['cs', 'it', 'ece', 'mech', 'business', 'arts']))            $errors[] = 'Valid department required.';
if (!in_array($yr, ['1', '2', '3', '4', 'graduate']))                            $errors[] = 'Valid year required.';
if (!$trm)                                                                         $errors[] = 'You must accept terms and conditions.';

if ($errors) { http_response_code(400); echo json_encode(['success' => false, 'errors' => $errors]); exit; }

$chk = $conn->prepare('SELECT id FROM users WHERE email=? OR studentid=?');
$chk->bind_param('ss', $em, $sid);
$chk->execute();
$chk->store_result();
if ($chk->num_rows > 0) {
    http_response_code(409);
    echo json_encode(['success' => false, 'errors' => ['Email or Student ID already registered.']]);
    exit;
}
$chk->close();

$hash = password_hash($pw, PASSWORD_DEFAULT);
$ins  = $conn->prepare('INSERT INTO users (fullname,email,mobile,gender,password,studentid,department,year,interests) VALUES (?,?,?,?,?,?,?,?,?)');
$ins->bind_param('sssssssss', $fn, $em, $mb, $gn, $hash, $sid, $dep, $yr, $int);
$ins->execute();
$ins->close();
$conn->close();

echo json_encode(['success' => true, 'message' => 'Registration successful! You can now <a href="login.html">login</a>.']);
