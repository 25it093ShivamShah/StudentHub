<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../db.php';

$method = $_SERVER['REQUEST_METHOD'];

$requireAdmin = function() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['user_id'])) { http_response_code(401); echo json_encode(['error'=>'Unauthorized']); exit; }
    if ($_SESSION['user_role'] !== 'admin') { http_response_code(403); echo json_encode(['error'=>'Forbidden']); exit; }
};

if ($method === 'GET') {
    if (isset($_GET['id'])) {
        $id   = (int)$_GET['id'];
        $stmt = $conn->prepare('SELECT * FROM events WHERE id=?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $row  = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        echo $row ? json_encode(['success'=>true,'data'=>$row]) : json_encode(['success'=>false,'error'=>'Not found']);
    } else {
        $search = trim($_GET['search'] ?? '');
        $cat    = trim($_GET['category'] ?? '');
        $where  = ['1=1'];
        $params = [];
        $types  = '';
        if ($search) { $where[] = '(title LIKE ? OR description LIKE ? OR venue LIKE ?)'; $s = "%$search%"; $params = array_merge($params,[$s,$s,$s]); $types .= 'sss'; }
        if ($cat && $cat !== 'all') { $where[] = 'category=?'; $params[] = $cat; $types .= 's'; }
        $sql  = 'SELECT * FROM events WHERE ' . implode(' AND ', $where) . ' ORDER BY date ASC';
        $stmt = $conn->prepare($sql);
        if ($types) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        echo json_encode(['success'=>true,'data'=>$rows,'total'=>count($rows)]);
    }
    exit;
}

if ($method === 'POST') {
    $requireAdmin();
    $id     = (int)($_POST['id'] ?? 0);
    $title  = trim(strip_tags($_POST['title'] ?? ''));
    $cat    = trim(strip_tags($_POST['category'] ?? ''));
    $desc   = trim(strip_tags($_POST['description'] ?? ''));
    $date   = trim(strip_tags($_POST['date'] ?? ''));
    $time   = trim(strip_tags($_POST['time'] ?? ''));
    $venue  = trim(strip_tags($_POST['venue'] ?? ''));
    $speaker= trim(strip_tags($_POST['speaker'] ?? ''));
    $seats  = max(1,(int)($_POST['seats'] ?? 50));
    $fee    = trim(strip_tags($_POST['fee'] ?? 'Free'));

    if (!$title||!$cat||!$desc||!$date||!$time||!$venue) {
        http_response_code(400); echo json_encode(['success'=>false,'error'=>'Required fields missing.']); exit;
    }

    $posterName = null;
    if (!empty($_FILES['poster']['name'])) {
        $allowed   = ['image/jpeg','image/png','image/gif','image/webp'];
        $ftype     = mime_content_type($_FILES['poster']['tmp_name']);
        $fext      = strtolower(pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION));
        $allowedExt= ['jpg','jpeg','png','gif','webp'];
        if (!in_array($ftype,$allowed)||!in_array($fext,$allowedExt)) { http_response_code(400); echo json_encode(['success'=>false,'error'=>'Invalid image type.']); exit; }
        if ($_FILES['poster']['size'] > 2097152) { http_response_code(400); echo json_encode(['success'=>false,'error'=>'Image exceeds 2MB.']); exit; }
        $posterName = uniqid('ev_') . '.' . $fext;
        move_uploaded_file($_FILES['poster']['tmp_name'], __DIR__ . '/../../uploads/events/' . $posterName);
    }

    if ($id) {
        if ($posterName) {
            $upd = $conn->prepare('UPDATE events SET title=?,category=?,description=?,date=?,time=?,venue=?,speaker=?,seats=?,fee=?,poster=? WHERE id=?');
            $upd->bind_param('sssssssissi',$title,$cat,$desc,$date,$time,$venue,$speaker,$seats,$fee,$posterName,$id);
        } else {
            $upd = $conn->prepare('UPDATE events SET title=?,category=?,description=?,date=?,time=?,venue=?,speaker=?,seats=?,fee=? WHERE id=?');
            $upd->bind_param('sssssssisi',$title,$cat,$desc,$date,$time,$venue,$speaker,$seats,$fee,$id);
        }
        $upd->execute();
        $upd->close();
        echo json_encode(['success'=>true,'message'=>'Event updated.']);
    } else {
        $ins = $conn->prepare('INSERT INTO events (title,category,description,date,time,venue,speaker,seats,fee,poster) VALUES (?,?,?,?,?,?,?,?,?,?)');
        $ins->bind_param('sssssssiss',$title,$cat,$desc,$date,$time,$venue,$speaker,$seats,$fee,$posterName);
        $ins->execute();
        $newId = $ins->insert_id;
        $ins->close();
        echo json_encode(['success'=>true,'message'=>'Event added.','id'=>$newId]);
    }
    exit;
}

if ($method === 'PUT') {
    $requireAdmin();
    $id   = (int)($_GET['id'] ?? 0);
    if (!$id) { http_response_code(400); echo json_encode(['error'=>'ID required']); exit; }
    $body = json_decode(file_get_contents('php://input'), true) ?? [];
    $fields = ['title','category','description','date','time','venue','speaker','fee'];
    $sets   = [];
    $params = [];
    $types  = '';
    foreach ($fields as $f) {
        if (isset($body[$f])) { $sets[] = "$f=?"; $params[] = trim(strip_tags($body[$f])); $types .= 's'; }
    }
    if (isset($body['seats'])) { $sets[] = 'seats=?'; $params[] = max(1,(int)$body['seats']); $types .= 'i'; }
    if (!$sets) { http_response_code(400); echo json_encode(['error'=>'Nothing to update.']); exit; }
    $params[] = $id; $types .= 'i';
    $stmt = $conn->prepare('UPDATE events SET ' . implode(',',$sets) . ' WHERE id=?');
    $stmt->bind_param($types,...$params);
    $stmt->execute();
    $stmt->close();
    echo json_encode(['success'=>true,'message'=>'Event updated.']);
    exit;
}

if ($method === 'DELETE') {
    $requireAdmin();
    $id = (int)($_GET['id'] ?? 0);
    if (!$id) { http_response_code(400); echo json_encode(['error'=>'ID required']); exit; }
    $row = $conn->prepare('SELECT poster FROM events WHERE id=?');
    $row->bind_param('i',$id);
    $row->execute();
    $old = $row->get_result()->fetch_assoc();
    $row->close();
    if ($old && $old['poster']) { $f = __DIR__ . '/../../uploads/events/' . $old['poster']; if (file_exists($f)) unlink($f); }
    $del = $conn->prepare('DELETE FROM events WHERE id=?');
    $del->bind_param('i',$id);
    $del->execute();
    $del->close();
    echo json_encode(['success'=>true,'message'=>'Event deleted.']);
    exit;
}

http_response_code(405);
echo json_encode(['error'=>'Method not allowed']);
