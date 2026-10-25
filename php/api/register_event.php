<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../db.php';

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'You must be logged in to register for an event.', 'login_required' => true]);
    exit;
}

$user_id  = (int)$_SESSION['user_id'];
$event_id = (int)($_POST['event_id'] ?? 0);

if (!$event_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid event.']);
    exit;
}

// Check event exists and has seats
$ev = $conn->prepare('SELECT id, title, seats FROM events WHERE id = ?');
$ev->bind_param('i', $event_id);
$ev->execute();
$event = $ev->get_result()->fetch_assoc();
$ev->close();

if (!$event) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Event not found.']);
    exit;
}

if ((int)$event['seats'] <= 0) {
    http_response_code(409);
    echo json_encode(['success' => false, 'error' => 'Sorry, this event is fully booked.']);
    exit;
}

// Check already registered
$chk = $conn->prepare('SELECT id FROM registrations WHERE user_id = ? AND event_id = ?');
$chk->bind_param('ii', $user_id, $event_id);
$chk->execute();
$chk->store_result();
if ($chk->num_rows > 0) {
    $chk->close();
    http_response_code(409);
    echo json_encode(['success' => false, 'error' => 'You are already registered for this event.']);
    exit;
}
$chk->close();

// Insert registration
$ins = $conn->prepare('INSERT INTO registrations (user_id, event_id, status) VALUES (?, ?, \'confirmed\')');
$ins->bind_param('ii', $user_id, $event_id);
$ins->execute();
$ins->close();

// Decrement seats
$upd = $conn->prepare('UPDATE events SET seats = seats - 1 WHERE id = ? AND seats > 0');
$upd->bind_param('i', $event_id);
$upd->execute();
$upd->close();

$conn->close();

echo json_encode([
    'success' => true,
    'message' => 'You have successfully registered for "' . htmlspecialchars($event['title']) . '"!'
]);
