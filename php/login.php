<?php
session_start();
header('Content-Type: application/json');
require __DIR__ . '/db.php';

$em = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));
$pw = $_POST['password'] ?? '';

if (!filter_var($em, FILTER_VALIDATE_EMAIL) || !$pw) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Email and password are required.']);
    exit;
}

$stmt = $conn->prepare('SELECT id,fullname,email,password,role FROM users WHERE email=?');
$stmt->bind_param('s', $em);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();
$conn->close();

if (!$row || !password_verify($pw, $row['password'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Invalid email or password.']);
    exit;
}

session_regenerate_id(true);
$_SESSION['user_id']       = $row['id'];
$_SESSION['user_name']     = $row['fullname'];
$_SESSION['user_email']    = $row['email'];
$_SESSION['user_role']     = $row['role'];
$_SESSION['last_activity'] = time();

$redirect = ($row['role'] === 'admin') ? '/pages/admin/' : '/pages/dashboard.php';
echo json_encode(['success' => true, 'redirect' => $redirect]);
