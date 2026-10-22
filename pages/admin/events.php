<?php
require_once __DIR__ . '/../../php/auth_admin.php';
require_once __DIR__ . '/../../php/db.php';

$uploadDir = __DIR__ . '/../../uploads/events/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

$msg     = '';
$msgType = 'green';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id     = (int)($_POST['id'] ?? 0);
    $title  = trim(strip_tags($_POST['title'] ?? ''));
    $cat    = trim(strip_tags($_POST['category'] ?? ''));
    $desc   = trim(strip_tags($_POST['description'] ?? ''));
    $date   = trim(strip_tags($_POST['date'] ?? ''));
    $time   = trim(strip_tags($_POST['time'] ?? ''));
    $venue  = trim(strip_tags($_POST['venue'] ?? ''));
    $speaker= trim(strip_tags($_POST['speaker'] ?? ''));
    $seats  = max(1, (int)($_POST['seats'] ?? 50));
    $fee    = trim(strip_tags($_POST['fee'] ?? 'Free'));

    $errors = [];
    if (!$title) $errors[] = 'Title required.';
    if (!in_array($cat, ['workshop','hackathon','seminar','cultural'])) $errors[] = 'Valid category required.';
    if (!$desc)  $errors[] = 'Description required.';
    if (!$date)  $errors[] = 'Date required.';
    if (!$time)  $errors[] = 'Time required.';
    if (!$venue) $errors[] = 'Venue required.';

    $posterName = null;
    if (!empty($_FILES['poster']['name'])) {
        $allowed   = ['image/jpeg','image/png','image/gif','image/webp'];
        $maxSize   = 2 * 1024 * 1024;
        $ftype     = mime_content_type($_FILES['poster']['tmp_name']);
        $fsize     = $_FILES['poster']['size'];
        $fext      = strtolower(pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION));
        $allowedExt= ['jpg','jpeg','png','gif','webp'];

        if (!in_array($ftype, $allowed) || !in_array($fext, $allowedExt)) {
            $errors[] = 'Poster must be JPG, PNG, GIF, or WEBP image.';
        } elseif ($fsize > $maxSize) {
            $errors[] = 'Poster image must be under 2MB.';
        } else {
            $posterName = uniqid('ev_') . '.' . $fext;
        }
    }

    if ($errors) {
        $msg = implode(' | ', $errors);
        $msgType = 'red';
    } elseif ($action === 'add') {
        if ($posterName) move_uploaded_file($_FILES['poster']['tmp_name'], $uploadDir . $posterName);
        $ins = $conn->prepare('INSERT INTO events (title,category,description,date,time,venue,speaker,seats,fee,poster) VALUES (?,?,?,?,?,?,?,?,?,?)');
        $ins->bind_param('sssssssiss', $title, $cat, $desc, $date, $time, $venue, $speaker, $seats, $fee, $posterName);
        $ins->execute();
        $ins->close();
        $msg = "Event \"$title\" added successfully.";
    } elseif ($action === 'edit' && $id) {
        if ($posterName) {
            move_uploaded_file($_FILES['poster']['tmp_name'], $uploadDir . $posterName);
            $upd = $conn->prepare('UPDATE events SET title=?,category=?,description=?,date=?,time=?,venue=?,speaker=?,seats=?,fee=?,poster=? WHERE id=?');
            $upd->bind_param('sssssssissi', $title, $cat, $desc, $date, $time, $venue, $speaker, $seats, $fee, $posterName, $id);
        } else {
            $upd = $conn->prepare('UPDATE events SET title=?,category=?,description=?,date=?,time=?,venue=?,speaker=?,seats=?,fee=? WHERE id=?');
            $upd->bind_param('sssssssisi', $title, $cat, $desc, $date, $time, $venue, $speaker, $seats, $fee, $id);
        }
        $upd->execute();
        $upd->close();
        $msg = "Event updated successfully.";
    } elseif ($action === 'delete' && $id) {
        $row = $conn->prepare('SELECT poster FROM events WHERE id=?');
        $row->bind_param('i', $id);
        $row->execute();
        $old = $row->get_result()->fetch_assoc();
        $row->close();
        if ($old && $old['poster'] && file_exists($uploadDir . $old['poster'])) unlink($uploadDir . $old['poster']);
        $del = $conn->prepare('DELETE FROM events WHERE id=?');
        $del->bind_param('i', $id);
        $del->execute();
        $del->close();
        $msg = 'Event deleted successfully.';
    }
}

$events = $conn->query('SELECT * FROM events ORDER BY date ASC')->fetch_all(MYSQLI_ASSOC);

$editData = null;
if (isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    $es  = $conn->prepare('SELECT * FROM events WHERE id=?');
    $es->bind_param('i', $eid);
    $es->execute();
    $editData = $es->get_result()->fetch_assoc();
    $es->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub - Manage Events</title>
    <link rel="stylesheet" href="/STUDENTHUB/css/style3.css">
    <link rel="icon" type="image/png" href="/STUDENTHUB/images/favicon.png">
</head>
<body>
    <header>
        <img src="/STUDENTHUB/images/logo.png" alt="Studenthub Logo" height="100">
        <h1>StudentHub</h1>
        <p>Your One Stop Platform For Campus Engagement &amp; Events.</p>
    </header>
    <nav>
        <a href="/STUDENTHUB/pages/index.html">Home</a>
        <a href="/STUDENTHUB/pages/admin/">Admin Dashboard</a>
        <a href="/STUDENTHUB/pages/admin/students.php">Manage Students</a>
        <a href="/STUDENTHUB/pages/admin/events.php">Manage Events</a>
        <a href="/STUDENTHUB/pages/dashboard.php">Student View</a>
        <a href="/STUDENTHUB/php/logout.php">Logout (<?= htmlspecialchars($_SESSION['user_name']) ?>)</a>
    </nav>
    <hr>
    <main>
        <section>
            <h2 style="text-decoration: underline; color:#1a365d ;">Event Management</h2>
            <?php if ($msg): ?>
            <p style="color:<?= $msgType ?>; font-weight:bold; border:1px solid <?= $msgType ?>; padding:8px; border-radius:4px;"><?= htmlspecialchars($msg) ?></p>
            <?php endif; ?>

            <details <?= $editData ? 'open' : '' ?>>
                <summary style="cursor:pointer; font-weight:bold; color:#1a365d; padding:6px 0;">
                    <?= $editData ? 'Edit Event: ' . htmlspecialchars($editData['title']) : 'Add New Event' ?>
                </summary>
                <form method="POST" action="" enctype="multipart/form-data" style="margin-top:10px;">
                    <input type="hidden" name="action" value="<?= $editData ? 'edit' : 'add' ?>">
                    <?php if ($editData): ?>
                    <input type="hidden" name="id" value="<?= $editData['id'] ?>">
                    <?php endif; ?>
                    <p>
                        <label for="ev_title">Event Title:</label><br>
                        <input type="text" id="ev_title" name="title" required placeholder="e.g., Web Dev Workshop" value="<?= htmlspecialchars($editData['title'] ?? '') ?>">
                    </p>
                    <p>
                        <label for="ev_cat">Category:</label><br>
                        <select id="ev_cat" name="category" required>
                            <option value="">--Select Category--</option>
                            <?php foreach (['workshop'=>'Workshop & Training','hackathon'=>'Hackathon & Competition','seminar'=>'Seminar & Guest Lecture','cultural'=>'Cultural & Social'] as $k => $v): ?>
                            <option value="<?= $k ?>" <?= ($editData['category'] ?? '') === $k ? 'selected' : '' ?>><?= $v ?></option>
                            <?php endforeach; ?>
                        </select>
                    </p>
                    <p>
                        <label for="ev_desc">Description:</label><br>
                        <textarea id="ev_desc" name="description" rows="3" required placeholder="Event details..."><?= htmlspecialchars($editData['description'] ?? '') ?></textarea>
                    </p>
                    <p>
                        <label for="ev_date">Date:</label><br>
                        <input type="date" id="ev_date" name="date" required value="<?= htmlspecialchars($editData['date'] ?? '') ?>">
                    </p>
                    <p>
                        <label for="ev_time">Time:</label><br>
                        <input type="time" id="ev_time" name="time" required value="<?= htmlspecialchars(substr($editData['time'] ?? '', 0, 5)) ?>">
                    </p>
                    <p>
                        <label for="ev_venue">Venue:</label><br>
                        <input type="text" id="ev_venue" name="venue" required placeholder="e.g., Lab 402" value="<?= htmlspecialchars($editData['venue'] ?? '') ?>">
                    </p>
                    <p>
                        <label for="ev_speaker">Speaker / Host:</label><br>
                        <input type="text" id="ev_speaker" name="speaker" placeholder="e.g., Campus Coding Club" value="<?= htmlspecialchars($editData['speaker'] ?? '') ?>">
                    </p>
                    <p>
                        <label for="ev_seats">Available Seats:</label><br>
                        <input type="number" id="ev_seats" name="seats" min="1" value="<?= htmlspecialchars($editData['seats'] ?? '50') ?>">
                    </p>
                    <p>
                        <label for="ev_fee">Fee:</label><br>
                        <input type="text" id="ev_fee" name="fee" placeholder="e.g., Free or ₹100" value="<?= htmlspecialchars($editData['fee'] ?? 'Free') ?>">
                    </p>
                    <p>
                        <label for="ev_poster">Event Poster (JPG/PNG/GIF/WEBP, max 2MB):</label><br>
                        <input type="file" id="ev_poster" name="poster" accept="image/jpeg,image/png,image/gif,image/webp">
                        <?php if (!empty($editData['poster'])): ?>
                        <br><small>Current: <a href="/uploads/events/<?= htmlspecialchars($editData['poster']) ?>" target="_blank"><?= htmlspecialchars($editData['poster']) ?></a></small>
                        <?php endif; ?>
                    </p>
                    <p>
                        <button type="submit"><?= $editData ? 'Update Event' : 'Add Event' ?></button>
                        <?php if ($editData): ?>
                        <a href="/STUDENTHUB/pages/admin/events.php"><button type="button">Cancel</button></a>
                        <?php else: ?>
                        <button type="reset">Clear</button>
                        <?php endif; ?>
                    </p>
                </form>
            </details>
        </section>

        <section>
            <h3 style="text-decoration: underline; color:#1a365d ;">All Events (<?= count($events) ?>)</h3>
            <table border="1" cellpadding="8" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th><th>Title</th><th>Category</th><th>Date</th>
                        <th>Venue</th><th>Seats</th><th>Poster</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($events)): ?>
                    <tr><td colspan="8" style="text-align:center;">No events found.</td></tr>
                    <?php else: ?>
                    <?php foreach ($events as $ev): ?>
                    <tr>
                        <td><?= $ev['id'] ?></td>
                        <td><?= htmlspecialchars($ev['title']) ?></td>
                        <td style="text-transform:capitalize;"><?= htmlspecialchars($ev['category']) ?></td>
                        <td><?= htmlspecialchars($ev['date']) ?></td>
                        <td><?= htmlspecialchars($ev['venue']) ?></td>
                        <td><?= htmlspecialchars($ev['seats']) ?></td>
                        <td>
                            <?php if ($ev['poster']): ?>
                            <a href="/uploads/events/<?= htmlspecialchars($ev['poster']) ?>" target="_blank">View</a>
                            <?php else: ?>
                            <em>None</em>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="?edit=<?= $ev['id'] ?>"><button type="button">Edit</button></a>
                            <form method="POST" action="" style="display:inline;" onsubmit="return confirm('Delete event: <?= htmlspecialchars(addslashes($ev['title'])) ?>?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $ev['id'] ?>">
                                <button type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>
    <hr>
    <footer><p>&copy; 2026 StudentHub. All Rights Reserved.</p></footer>
</body>
</html>
