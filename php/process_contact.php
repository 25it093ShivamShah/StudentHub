<?php
header('Content-Type: application/json');
require __DIR__ . '/db.php';

$name    = trim(strip_tags($_POST['name'] ?? ''));
$email   = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));
$subject = trim(strip_tags($_POST['subject'] ?? ''));
$message = trim(strip_tags($_POST['message'] ?? ''));

$errors = [];
if (!$name || !preg_match('/^[A-Za-z\s]{2,80}$/', $name)) $errors[] = 'Valid name required.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL))             $errors[] = 'Valid email required.';
if (!$subject || strlen($subject) < 3)                      $errors[] = 'Subject required.';
if (!$message || strlen($message) < 10)                     $errors[] = 'Message too short.';

if ($errors) {
    http_response_code(400);
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

$data_dir = __DIR__ . '/../data/';
$file     = $data_dir . 'contacts.json';
$records  = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
$records[] = [
    'id'        => count($records) + 1,
    'name'      => $name,
    'email'     => $email,
    'subject'   => $subject,
    'message'   => $message,
    'timestamp' => date('Y-m-d H:i:s'),
];
file_put_contents($file, json_encode($records, JSON_PRETTY_PRINT));
$conn->close();

echo json_encode(['success' => true, 'message' => 'Message sent successfully!']);
