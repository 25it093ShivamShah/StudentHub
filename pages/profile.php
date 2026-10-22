<?php
require_once __DIR__ . '/../php/auth.php';
require_once __DIR__ . '/../php/db.php';

$uid = $_SESSION['user_id'];
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = $_POST['old_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $cnf = $_POST['confirm_new'] ?? '';

    $s = $conn->prepare('SELECT password FROM users WHERE id=?');
    $s->bind_param('i', $uid);
    $s->execute();
    $hash = $s->get_result()->fetch_assoc()['password'];
    $s->close();

    if (!password_verify($old, $hash)) {
        $msg = '<span style="color:red;">Current password is incorrect.</span>';
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/', $new)) {
        $msg = '<span style="color:red;">New password too weak.</span>';
    } elseif ($new !== $cnf) {
        $msg = '<span style="color:red;">New passwords do not match.</span>';
    } else {
        $upd = $conn->prepare('UPDATE users SET password=? WHERE id=?');
        $newHash = password_hash($new, PASSWORD_DEFAULT);
        $upd->bind_param('si', $newHash, $uid);
        $upd->execute();
        $upd->close();
        $msg = '<span style="color:green; font-weight:bold;">Password updated successfully.</span>';
    }
}

$stmt = $conn->prepare('SELECT * FROM users WHERE id=?');
$stmt->bind_param('i', $uid);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();
$conn->close();

$dept = ['cs'=>'Computer Science & Engineering','it'=>'Information Technology','ece'=>'Electronics & Communication','mech'=>'Mechanical Engineering','business'=>'Business Administration','arts'=>'Arts & Humanities'];
$yr   = ['1'=>'1st Year','2'=>'2nd Year','3'=>'3rd Year','4'=>'4th Year','graduate'=>'Graduate / Post-Graduate'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub - My Profile</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="icon" type="image/png" href="/images/favicon.png">
</head>
<body>
    <header>
        <img src="/images/logo.png" alt="Studenthub Logo" height="100">
        <h1>StudentHub</h1>
        <p>Your One Stop Platform For Campus Engagement &amp; Events.</p>
    </header>
    <nav>
        <a href="/pages/index.html" title="Home">Home</a>
        <a href="/pages/about.html" title="About" target="_blank">About</a>
        <a href="/pages/register.html" title="Register" target="_blank">Register</a>
        <a href="/pages/login.html" title="Login" target="_blank">Login</a>
        <a href="/pages/dashboard.php" title="Dashboard" target="_blank">Dashboard</a>
        <a href="/pages/events.html" title="Events" target="_blank">Events</a>
        <a href="/pages/profile.php" title="Profile">Profile</a>
        <a href="/pages/contact.html" title="Contact" target="_blank">Contact</a>
        <?php if ($_SESSION['user_role'] === 'admin'): ?>
        <a href="/pages/admin/" title="Admin" target="_blank">Admin</a>
        <?php endif; ?>
        <a href="/pages/faq.html" title="FAQ" target="_blank">FAQ</a>
        <a href="/pages/feedback.html" title="Feedback">Feedback</a>
        <a href="/php/logout.php">Logout</a>
    </nav>
    <hr>
    <main>
        <section>
            <h2 style="text-decoration: underline; color:#1a365d ;">My Profile</h2>
            <table border="1" cellpadding="8" cellspacing="0">
                <tr><th>Full Name</th><td><?= htmlspecialchars($user['fullname']) ?></td></tr>
                <tr><th>Email</th><td><?= htmlspecialchars($user['email']) ?></td></tr>
                <tr><th>Mobile</th><td><?= htmlspecialchars($user['mobile']) ?></td></tr>
                <tr><th>Gender</th><td><?= htmlspecialchars($user['gender']) ?></td></tr>
                <tr><th>Student ID</th><td><?= htmlspecialchars($user['studentid']) ?></td></tr>
                <tr><th>Department</th><td><?= htmlspecialchars($dept[$user['department']] ?? $user['department']) ?></td></tr>
                <tr><th>Year</th><td><?= htmlspecialchars($yr[$user['year']] ?? $user['year']) ?></td></tr>
                <tr><th>Interests</th><td><?= htmlspecialchars($user['interests'] ?: 'None specified') ?></td></tr>
                <tr><th>Role</th><td><?= htmlspecialchars(ucfirst($user['role'])) ?></td></tr>
                <tr><th>Member Since</th><td><?= htmlspecialchars($user['created_at']) ?></td></tr>
            </table>
        </section>
        <section>
            <h3 style="text-decoration: underline; color:#1a365d ;">Change Password</h3>
            <?= $msg ?>
            <form method="POST" action="" style="margin-top:10px;">
                <p>
                    <label for="old_password">Current Password:</label><br>
                    <input type="password" id="old_password" name="old_password" required>
                </p>
                <p>
                    <label for="new_password">New Password:</label><br>
                    <input type="password" id="new_password" name="new_password" required>
                </p>
                <p>
                    <label for="confirm_new">Confirm New Password:</label><br>
                    <input type="password" id="confirm_new" name="confirm_new" required>
                </p>
                <p>
                    <button type="submit">Update Password</button>
                    <button type="reset">Clear</button>
                </p>
            </form>
        </section>
    </main>
    <hr>
    <footer>
        <p>&copy; 2026 StudentHub. All Rights Reserved.</p>
    </footer>
</body>
</html>
