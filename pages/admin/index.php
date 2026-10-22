<?php
require_once __DIR__ . '/../../php/auth_admin.php';
require_once __DIR__ . '/../../php/db.php';

$total_students  = $conn->query('SELECT COUNT(*) FROM users WHERE role="student"')->fetch_row()[0];
$total_events    = $conn->query('SELECT COUNT(*) FROM events')->fetch_row()[0];
$upcoming_events = $conn->query('SELECT COUNT(*) FROM events WHERE date >= CURDATE()')->fetch_row()[0];
$total_regs      = $conn->query('SELECT COUNT(*) FROM registrations')->fetch_row()[0];
$confirmed_regs  = $conn->query('SELECT COUNT(*) FROM registrations WHERE status="confirmed"')->fetch_row()[0];
$pending_regs    = $conn->query('SELECT COUNT(*) FROM registrations WHERE status="pending"')->fetch_row()[0];

$dept_stats = $conn->query(
    'SELECT department, COUNT(*) as cnt FROM users WHERE role="student" GROUP BY department ORDER BY cnt DESC'
)->fetch_all(MYSQLI_ASSOC);

$event_stats = $conn->query(
    'SELECT category, COUNT(*) as cnt FROM events GROUP BY category ORDER BY cnt DESC'
)->fetch_all(MYSQLI_ASSOC);

$recent_students = $conn->query(
    'SELECT fullname, email, department, created_at FROM users WHERE role="student" ORDER BY created_at DESC LIMIT 5'
)->fetch_all(MYSQLI_ASSOC);

$upcoming_list = $conn->query(
    'SELECT id, title, category, date, venue, seats FROM events WHERE date >= CURDATE() ORDER BY date ASC LIMIT 5'
)->fetch_all(MYSQLI_ASSOC);

$conn->close();

$deptMap = ['cs'=>'CS & Engineering','it'=>'Info. Technology','ece'=>'Electronics & Comm','mech'=>'Mechanical Engg','business'=>'Business Admin','arts'=>'Arts & Humanities'];
$catMap  = ['workshop'=>'Workshop','hackathon'=>'Hackathon','seminar'=>'Seminar','cultural'=>'Cultural'];

$menu = [
    ['href'=>'/pages/admin/','label'=>'Dashboard','icon'=>'📊'],
    ['href'=>'/pages/admin/students.php','label'=>'Manage Students','icon'=>'👥'],
    ['href'=>'/pages/admin/events.php','label'=>'Manage Events','icon'=>'📅'],
    ['href'=>'/pages/ajax_manager.php','label'=>'AJAX Manager','icon'=>'⚡'],
    ['href'=>'/pages/dashboard.php','label'=>'Student View','icon'=>'🎓'],
    ['href'=>'/pages/contact.html','label'=>'Support','icon'=>'📬'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub - Admin Dashboard</title>
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
        <?php foreach ($menu as $item): ?>
        <a href="<?= $item['href'] ?>"><?= $item['icon'] ?> <?= $item['label'] ?></a>
        <?php endforeach; ?>
        <a href="/STUDENTHUB/php/logout.php">🚪 Logout (<?= htmlspecialchars($_SESSION['user_name']) ?>)</a>
    </nav>
    <hr>
    <main>
        <section>
            <h2 style="text-decoration: underline; color:#1a365d ;">Administrator Dashboard</h2>
            <p>Welcome, <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong> &mdash; Role: <strong>Admin</strong> &mdash; Session started: <strong><?= date('d M Y, h:i A') ?></strong></p>
        </section>

        <section>
            <h3 style="text-decoration: underline; color:#1a365d ;">System Overview</h3>
            <table border="1" cellpadding="8" cellspacing="0">
                <thead>
                    <tr>
                        <th>Total Students</th>
                        <th>Total Events</th>
                        <th>Upcoming Events</th>
                        <th>Total Registrations</th>
                        <th>Confirmed</th>
                        <th>Pending</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong><?= $total_students ?></strong></td>
                        <td><strong><?= $total_events ?></strong></td>
                        <td><strong><?= $upcoming_events ?></strong></td>
                        <td><strong><?= $total_regs ?></strong></td>
                        <td><strong><?= $confirmed_regs ?></strong></td>
                        <td><strong><?= $pending_regs ?></strong></td>
                    </tr>
                </tbody>
            </table>
        </section>

        <section>
            <h3 style="text-decoration: underline; color:#1a365d ;">Quick Control Panel</h3>
            <ul>
                <?php foreach ($menu as $item): ?>
                <li><a href="<?= $item['href'] ?>"><?= $item['icon'] ?> <?= $item['label'] ?></a></li>
                <?php endforeach; ?>
            </ul>
        </section>

        <section>
            <h3 style="text-decoration: underline; color:#1a365d ;">Students by Department</h3>
            <table border="1" cellpadding="8" cellspacing="0">
                <thead><tr><th>Department</th><th>Count</th></tr></thead>
                <tbody>
                    <?php if (empty($dept_stats)): ?>
                    <tr><td colspan="2">No data yet.</td></tr>
                    <?php else: ?>
                    <?php foreach ($dept_stats as $d): ?>
                    <tr>
                        <td><?= htmlspecialchars($deptMap[$d['department']] ?? strtoupper($d['department'])) ?></td>
                        <td><?= $d['cnt'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>

        <section>
            <h3 style="text-decoration: underline; color:#1a365d ;">Events by Category</h3>
            <table border="1" cellpadding="8" cellspacing="0">
                <thead><tr><th>Category</th><th>Count</th></tr></thead>
                <tbody>
                    <?php if (empty($event_stats)): ?>
                    <tr><td colspan="2">No events yet.</td></tr>
                    <?php else: ?>
                    <?php foreach ($event_stats as $ev): ?>
                    <tr>
                        <td><?= htmlspecialchars($catMap[$ev['category']] ?? ucfirst($ev['category'])) ?></td>
                        <td><?= $ev['cnt'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>

        <section>
            <h3 style="text-decoration: underline; color:#1a365d ;">Recent Student Registrations</h3>
            <table border="1" cellpadding="8" cellspacing="0">
                <thead>
                    <tr><th>Full Name</th><th>Email</th><th>Department</th><th>Date Registered</th></tr>
                </thead>
                <tbody>
                    <?php if (empty($recent_students)): ?>
                    <tr><td colspan="4" style="text-align:center;">No students registered yet.</td></tr>
                    <?php else: ?>
                    <?php foreach ($recent_students as $s): ?>
                    <tr>
                        <td><?= htmlspecialchars($s['fullname']) ?></td>
                        <td><?= htmlspecialchars($s['email']) ?></td>
                        <td><?= htmlspecialchars($deptMap[$s['department']] ?? strtoupper($s['department'])) ?></td>
                        <td><?= htmlspecialchars(substr($s['created_at'], 0, 10)) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <p><a href="/STUDENTHUB/pages/admin/students.php">View All Students &rarr;</a></p>
        </section>

        <section>
            <h3 style="text-decoration: underline; color:#1a365d ;">Upcoming Events</h3>
            <table border="1" cellpadding="8" cellspacing="0">
                <thead>
                    <tr><th>ID</th><th>Title</th><th>Category</th><th>Date</th><th>Venue</th><th>Seats</th></tr>
                </thead>
                <tbody>
                    <?php if (empty($upcoming_list)): ?>
                    <tr><td colspan="6" style="text-align:center;">No upcoming events.</td></tr>
                    <?php else: ?>
                    <?php foreach ($upcoming_list as $ev): ?>
                    <tr>
                        <td><?= $ev['id'] ?></td>
                        <td><?= htmlspecialchars($ev['title']) ?></td>
                        <td style="text-transform:capitalize;"><?= htmlspecialchars($ev['category']) ?></td>
                        <td><?= htmlspecialchars($ev['date']) ?></td>
                        <td><?= htmlspecialchars($ev['venue']) ?></td>
                        <td><?= htmlspecialchars($ev['seats']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <p><a href="/STUDENTHUB/pages/admin/events.php">Manage All Events &rarr;</a></p>
        </section>

        <section>
            <h3 style="text-decoration: underline; color:#1a365d ;">Recent Activity Log</h3>
            <ul>
                <li><strong>Admin (<?= htmlspecialchars($_SESSION['user_name']) ?>)</strong> logged in &mdash; <time><?= date('d M Y \a\t h:i A') ?></time></li>
                <li>System has <strong><?= $total_students ?></strong> registered students and <strong><?= $total_events ?></strong> events.</li>
                <li><strong><?= $upcoming_events ?></strong> upcoming event(s) scheduled from today.</li>
            </ul>
        </section>
    </main>
    <hr>
    <footer>
        <p>&copy; 2026 StudentHub. All Rights Reserved.</p>
    </footer>
</body>
</html>
