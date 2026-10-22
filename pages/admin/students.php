<?php
require_once __DIR__ . '/../../php/auth_admin.php';
require_once __DIR__ . '/../../php/db.php';

$msg = '';
$msgType = 'green';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id     = (int)($_POST['id'] ?? 0);
    $fn     = trim(strip_tags($_POST['fullname'] ?? ''));
    $em     = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));
    $mb     = trim(strip_tags($_POST['mobile'] ?? ''));
    $gn     = trim(strip_tags($_POST['gender'] ?? ''));
    $sid    = trim(strip_tags($_POST['studentid'] ?? ''));
    $dep    = trim(strip_tags($_POST['department'] ?? ''));
    $yr     = trim(strip_tags($_POST['year'] ?? ''));
    $pw     = $_POST['password'] ?? '';

    $errors = [];
    if (!$fn)  $errors[] = 'Full name required.';
    if (!filter_var($em, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email required.';
    if (!preg_match('/^[6-9]\d{9}$/', $mb)) $errors[] = 'Valid mobile required.';
    if (!in_array($gn, ['Male','Female','Other'])) $errors[] = 'Gender required.';
    if (!$sid) $errors[] = 'Student ID required.';
    if (!in_array($dep, ['cs','it','ece','mech','business','arts'])) $errors[] = 'Department required.';
    if (!in_array($yr, ['1','2','3','4','graduate'])) $errors[] = 'Year required.';
    if ($action === 'add' && (!$pw || !preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/', $pw))) {
        $errors[] = 'Strong password required for new student.';
    }

    if ($errors) {
        $msg = implode(' | ', $errors);
        $msgType = 'red';
    } elseif ($action === 'add') {
        $chk = $conn->prepare('SELECT id FROM users WHERE email=? OR studentid=?');
        $chk->bind_param('ss', $em, $sid);
        $chk->execute();
        $chk->store_result();
        if ($chk->num_rows > 0) {
            $msg = 'Email or Student ID already exists.';
            $msgType = 'red';
        } else {
            $hash = password_hash($pw, PASSWORD_DEFAULT);
            $ins  = $conn->prepare('INSERT INTO users (fullname,email,mobile,gender,password,studentid,department,year) VALUES (?,?,?,?,?,?,?,?)');
            $ins->bind_param('ssssssss', $fn, $em, $mb, $gn, $hash, $sid, $dep, $yr);
            $ins->execute();
            $ins->close();
            $msg = "Student \"$fn\" added successfully.";
        }
        $chk->close();
    } elseif ($action === 'edit' && $id) {
        $upd = $conn->prepare('UPDATE users SET fullname=?,email=?,mobile=?,gender=?,studentid=?,department=?,year=? WHERE id=? AND role="student"');
        $upd->bind_param('sssssssi', $fn, $em, $mb, $gn, $sid, $dep, $yr, $id);
        $upd->execute();
        $upd->close();
        if ($pw && preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/', $pw)) {
            $hash = password_hash($pw, PASSWORD_DEFAULT);
            $phash = $conn->prepare('UPDATE users SET password=? WHERE id=?');
            $phash->bind_param('si', $hash, $id);
            $phash->execute();
            $phash->close();
        }
        $msg = "Student record updated successfully.";
    } elseif ($action === 'delete' && $id) {
        $del = $conn->prepare('DELETE FROM users WHERE id=? AND role="student"');
        $del->bind_param('i', $id);
        $del->execute();
        $del->close();
        $msg = 'Student deleted successfully.';
    }
}

$search = trim(strip_tags($_GET['search'] ?? ''));
$fDept  = trim(strip_tags($_GET['dept'] ?? ''));
$fYear  = trim(strip_tags($_GET['year'] ?? ''));

$where  = ['role="student"'];
$params = [];
$types  = '';
if ($search) { $where[] = '(fullname LIKE ? OR email LIKE ? OR studentid LIKE ?)'; $s = "%$search%"; $params = array_merge($params, [$s, $s, $s]); $types .= 'sss'; }
if ($fDept)  { $where[] = 'department=?'; $params[] = $fDept; $types .= 's'; }
if ($fYear)  { $where[] = 'year=?'; $params[] = $fYear; $types .= 's'; }

$sql  = 'SELECT id,fullname,email,mobile,gender,studentid,department,year,created_at FROM users WHERE ' . implode(' AND ', $where) . ' ORDER BY created_at DESC';
$stmt = $conn->prepare($sql);
if ($types) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
$students = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$editData = null;
if (isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    $es  = $conn->prepare('SELECT * FROM users WHERE id=? AND role="student"');
    $es->bind_param('i', $eid);
    $es->execute();
    $editData = $es->get_result()->fetch_assoc();
    $es->close();
}
$conn->close();

$deptMap = ['cs'=>'CS & Engineering','it'=>'Info. Technology','ece'=>'Electronics & Comm','mech'=>'Mechanical Engg','business'=>'Business Admin','arts'=>'Arts & Humanities'];
$yrMap   = ['1'=>'1st Year','2'=>'2nd Year','3'=>'3rd Year','4'=>'4th Year','graduate'=>'Graduate'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub - Manage Students</title>
    <link rel="stylesheet" href="/css/style3.css">
    <link rel="icon" type="image/png" href="/images/favicon.png">
</head>
<body>
    <header>
        <img src="/images/logo.png" alt="Studenthub Logo" height="100">
        <h1>StudentHub</h1>
        <p>Your One Stop Platform For Campus Engagement &amp; Events.</p>
    </header>
    <nav>
        <a href="/pages/index.html">Home</a>
        <a href="/pages/admin/">Admin Dashboard</a>
        <a href="/pages/admin/students.php">Manage Students</a>
        <a href="/pages/admin/events.php">Manage Events</a>
        <a href="/pages/dashboard.php">Student View</a>
        <a href="/php/logout.php">Logout (<?= htmlspecialchars($_SESSION['user_name']) ?>)</a>
    </nav>
    <hr>
    <main>
        <section>
            <h2 style="text-decoration: underline; color:#1a365d ;">Student Management</h2>
            <?php if ($msg): ?>
            <p style="color:<?= $msgType ?>; font-weight:bold; border:1px solid <?= $msgType ?>; padding:8px; border-radius:4px;"><?= htmlspecialchars($msg) ?></p>
            <?php endif; ?>

            <details <?= $editData ? 'open' : '' ?>>
                <summary style="cursor:pointer; font-weight:bold; color:#1a365d; padding:6px 0;">
                    <?= $editData ? 'Edit Student: ' . htmlspecialchars($editData['fullname']) : 'Add New Student' ?>
                </summary>
                <form method="POST" action="" style="margin-top:10px;">
                    <input type="hidden" name="action" value="<?= $editData ? 'edit' : 'add' ?>">
                    <?php if ($editData): ?>
                    <input type="hidden" name="id" value="<?= $editData['id'] ?>">
                    <?php endif; ?>
                    <p>
                        <label for="s_fn">Full Name:</label><br>
                        <input type="text" id="s_fn" name="fullname" required placeholder="John Doe" value="<?= htmlspecialchars($editData['fullname'] ?? '') ?>">
                    </p>
                    <p>
                        <label for="s_em">Email:</label><br>
                        <input type="email" id="s_em" name="email" required placeholder="student@university.edu" value="<?= htmlspecialchars($editData['email'] ?? '') ?>">
                    </p>
                    <p>
                        <label for="s_mb">Mobile:</label><br>
                        <input type="text" id="s_mb" name="mobile" required maxlength="10" placeholder="9876543210" value="<?= htmlspecialchars($editData['mobile'] ?? '') ?>">
                    </p>
                    <p>
                        <label for="s_gn">Gender:</label><br>
                        <select id="s_gn" name="gender" required>
                            <option value="">--Select--</option>
                            <?php foreach (['Male','Female','Other'] as $g): ?>
                            <option value="<?= $g ?>" <?= ($editData['gender'] ?? '') === $g ? 'selected' : '' ?>><?= $g ?></option>
                            <?php endforeach; ?>
                        </select>
                    </p>
                    <p>
                        <label for="s_sid">Student ID:</label><br>
                        <input type="text" id="s_sid" name="studentid" required placeholder="S12345" value="<?= htmlspecialchars($editData['studentid'] ?? '') ?>">
                    </p>
                    <p>
                        <label for="s_dep">Department:</label><br>
                        <select id="s_dep" name="department" required>
                            <option value="">--Select--</option>
                            <?php foreach ($deptMap as $k => $v): ?>
                            <option value="<?= $k ?>" <?= ($editData['department'] ?? '') === $k ? 'selected' : '' ?>><?= $v ?></option>
                            <?php endforeach; ?>
                        </select>
                    </p>
                    <p>
                        <label for="s_yr">Year:</label><br>
                        <select id="s_yr" name="year" required>
                            <option value="">--Select--</option>
                            <?php foreach ($yrMap as $k => $v): ?>
                            <option value="<?= $k ?>" <?= ($editData['year'] ?? '') === $k ? 'selected' : '' ?>><?= $v ?></option>
                            <?php endforeach; ?>
                        </select>
                    </p>
                    <p>
                        <label for="s_pw">Password <?= $editData ? '(leave blank to keep)' : '(required)' ?>:</label><br>
                        <input type="password" id="s_pw" name="password" <?= $editData ? '' : 'required' ?>>
                    </p>
                    <p>
                        <button type="submit"><?= $editData ? 'Update Student' : 'Add Student' ?></button>
                        <?php if ($editData): ?>
                        <a href="/pages/admin/students.php"><button type="button">Cancel</button></a>
                        <?php else: ?>
                        <button type="reset">Clear</button>
                        <?php endif; ?>
                    </p>
                </form>
            </details>
        </section>

        <section>
            <h3 style="text-decoration: underline; color:#1a365d ;">Student Records (<?= count($students) ?> found)</h3>
            <form method="GET" action="" style="margin-bottom:14px; display:flex; gap:10px; flex-wrap:wrap;">
                <input type="text" name="search" placeholder="Search name, email, ID..." value="<?= htmlspecialchars($search) ?>" style="padding:6px 10px; border:1px solid #cbd5e1; border-radius:4px; min-width:220px;">
                <select name="dept" style="padding:6px; border:1px solid #cbd5e1; border-radius:4px;">
                    <option value="">All Departments</option>
                    <?php foreach ($deptMap as $k => $v): ?>
                    <option value="<?= $k ?>" <?= $fDept === $k ? 'selected' : '' ?>><?= $v ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="year" style="padding:6px; border:1px solid #cbd5e1; border-radius:4px;">
                    <option value="">All Years</option>
                    <?php foreach ($yrMap as $k => $v): ?>
                    <option value="<?= $k ?>" <?= $fYear === $k ? 'selected' : '' ?>><?= $v ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Filter</button>
                <a href="/pages/admin/students.php"><button type="button">Reset</button></a>
            </form>

            <table border="1" cellpadding="8" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th><th>Full Name</th><th>Email</th><th>Mobile</th>
                        <th>Dept</th><th>Year</th><th>Registered</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($students)): ?>
                    <tr><td colspan="8" style="text-align:center;">No students found.</td></tr>
                    <?php else: ?>
                    <?php foreach ($students as $s): ?>
                    <tr>
                        <td><?= $s['id'] ?></td>
                        <td><?= htmlspecialchars($s['fullname']) ?></td>
                        <td><?= htmlspecialchars($s['email']) ?></td>
                        <td><?= htmlspecialchars($s['mobile']) ?></td>
                        <td><?= htmlspecialchars($deptMap[$s['department']] ?? $s['department']) ?></td>
                        <td><?= htmlspecialchars($yrMap[$s['year']] ?? $s['year']) ?></td>
                        <td><?= htmlspecialchars(substr($s['created_at'], 0, 10)) ?></td>
                        <td>
                            <a href="?edit=<?= $s['id'] ?>"><button type="button">Edit</button></a>
                            <form method="POST" action="" style="display:inline;" onsubmit="return confirm('Delete <?= htmlspecialchars(addslashes($s['fullname'])) ?>?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $s['id'] ?>">
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
