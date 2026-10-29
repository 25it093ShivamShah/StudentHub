<?php
require_once __DIR__ . '/../php/auth.php';
require_once __DIR__ . '/../php/db.php';

$uid  = $_SESSION['user_id'];
$stmt = $conn->prepare('SELECT * FROM users WHERE id=?');
$stmt->bind_param('i', $uid);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

$regs = $conn->prepare(
    'SELECT e.title,e.date,e.time,e.venue,r.status FROM registrations r
     JOIN events e ON r.event_id=e.id WHERE r.user_id=? ORDER BY e.date ASC'
);
$regs->bind_param('i', $uid);
$regs->execute();
$regEvents = $regs->get_result()->fetch_all(MYSQLI_ASSOC);
$regs->close();
$conn->close();

$dept = ['cs'=>'Computer Science & Engineering','it'=>'Information Technology','ece'=>'Electronics & Communication','mech'=>'Mechanical Engineering','business'=>'Business Administration','arts'=>'Arts & Humanities'];
$yr   = ['1'=>'1st Year','2'=>'2nd Year','3'=>'3rd Year','4'=>'4th Year','graduate'=>'Graduate / Post-Graduate'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script>if(localStorage.getItem("theme")==="dark")document.documentElement.style.cssText="background:#0f172a";</script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub - Student Dashboard</title>
    <link rel="stylesheet" href="/STUDENTHUB/css/style5.css">
    <link rel="icon" type="image/png" href="/STUDENTHUB/images/favicon.png">
</head>
<body>
    <script>if(localStorage.getItem('theme')=='dark')document.body.classList.add('dark-mode');</script>
    <header>
    <script>if(localStorage.getItem("theme")==="dark")document.documentElement.style.cssText="background:#0f172a";</script>
        <img src="/STUDENTHUB/images/logo.png" alt="Studenthub Logo" height="100">
        <h1>StudentHub</h1>
        <p>Your One Stop Platform For Campus Engagement &amp; Events.</p>
    </header>
    <nav>
        <button id="hamburger">&#9776;</button>
        <div id="navLinks">
            <a href="/STUDENTHUB/pages/index.html" title="Home">Home</a>
            <a href="/STUDENTHUB/pages/about.html" title="About" target="_blank">About</a>
            <a href="/STUDENTHUB/pages/register.html" title="Register" target="_blank">Register</a>
            <a href="/STUDENTHUB/pages/login.html" title="Login" target="_blank">Login</a>
            <a href="/STUDENTHUB/pages/dashboard.php" title="Dashboard">Dashboard</a>
            <a href="/STUDENTHUB/pages/events.html" title="Events" target="_blank">Events</a>
            <a href="/STUDENTHUB/pages/profile.php" title="Profile" target="_blank">Profile</a>
            <a href="/STUDENTHUB/pages/contact.html" title="Contact" target="_blank">Contact</a>
            <?php if ($_SESSION['user_role'] === 'admin'): ?>
            <a href="/STUDENTHUB/pages/admin/" title="Admin" target="_blank">Admin</a>
            <?php endif; ?>
            <a href="/STUDENTHUB/pages/faq.html" title="FAQ" target="_blank">FAQ</a>
            <a href="/STUDENTHUB/pages/feedback.html" title="Feedback">Feedback</a>
            <a href="/STUDENTHUB/php/logout.php" title="Logout">Logout (<?= htmlspecialchars($_SESSION['user_name']) ?>)</a>
        </div>
    </nav>
    <hr>
    <main>
        <?php if (isset($_GET['timeout'])): ?>
        <p style="color:red; font-weight:bold;">Session expired due to inactivity. Please login again.</p>
        <?php endif; ?>

        <section>
            <h3 style="text-decoration: underline; color:#1a365d ;">Student Profile Snapshot</h3>
            <table border="1" cellpadding="8" cellspacing="0">
                <tr>
                    <th>Student ID:</th>
                    <td><?= htmlspecialchars($user['studentid']) ?></td>
                    <th>Department:</th>
                    <td><?= htmlspecialchars($dept[$user['department']] ?? $user['department']) ?></td>
                </tr>
                <tr>
                    <th>Academic Year:</th>
                    <td><?= htmlspecialchars($yr[$user['year']] ?? $user['year']) ?></td>
                    <th>Account Status:</th>
                    <td>Active / Verified</td>
                </tr>
            </table>
        </section>

        <section>
            <h2 style="text-decoration: underline; color:#1a365d ;">Welcome Back, <?= htmlspecialchars($user['fullname']) ?>!</h2>
            <p>Here is your personalized snapshot of your academic registrations and upcoming activities.</p>
            <h3 style="text-decoration: underline; color:#1a365d ;">Registered Events</h3>
            <?php if (empty($regEvents)): ?>
            <p>You have not registered for any events yet. <a href="/STUDENTHUB/pages/events.html">Browse Events &rarr;</a></p>
            <?php else: ?>
            <ul>
                <?php foreach ($regEvents as $e): ?>
                <li><?= htmlspecialchars($e['title']) ?> &mdash; <?= htmlspecialchars($e['date']) ?></li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
        </section>

        <section>
            <h3 style="text-decoration: underline; color:#1a365d ;">Event Schedule &amp; Status</h3>
            <table border="1" cellpadding="8" cellspacing="0">
                <thead>
                    <tr>
                        <th>Event Title</th>
                        <th>Date &amp; Time</th>
                        <th>Location</th>
                        <th>Ticket Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($regEvents)): ?>
                    <tr><td colspan="4" style="text-align:center;">No registered events.</td></tr>
                    <?php else: ?>
                    <?php foreach ($regEvents as $e): ?>
                    <tr>
                        <td><?= htmlspecialchars($e['title']) ?></td>
                        <td><?= htmlspecialchars($e['date']) ?> | <?= htmlspecialchars(substr($e['time'], 0, 5)) ?></td>
                        <td><?= htmlspecialchars($e['venue']) ?></td>
                        <td><?= htmlspecialchars(ucfirst($e['status'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>

        <aside>
            <h3 style="text-decoration: underline; color:#1a365d ;">Quick Actions</h3>
            <ul>
                <li><a href="/STUDENTHUB/pages/profile.php">Update Profile</a></li>
                <li><a href="/STUDENTHUB/pages/events.html">Browse New Events</a></li>
                <li><a href="/STUDENTHUB/pages/contact.html">Contact Support</a></li>
                <li><a href="/STUDENTHUB/php/logout.php">Logout</a></li>
            </ul>
        </aside>
    </main>
    <hr>
    <footer>
        <p>&copy; 2026 StudentHub. All Rights Reserved.</p>
    </footer>
    <script src="/STUDENTHUB/js/darkmode.js"></script>
</body>
</html>
