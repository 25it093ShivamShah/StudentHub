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
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['user_id'])) { http_response_code(401); echo json_encode(['error'=>'Unauthorized']); exit; }

    if (isset($_GET['id'])) {
        $id   = (int)$_GET['id'];
        $stmt = $conn->prepare('SELECT id,fullname,email,mobile,gender,studentid,department,year,interests,created_at FROM users WHERE id=? AND role="student"');
        $stmt->bind_param('i',$id);
        $stmt->execute();
        $row  = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        echo $row ? json_encode(['success'=>true,'data'=>$row]) : json_encode(['success'=>false,'error'=>'Not found']);
    } else {
        $search = trim($_GET['search'] ?? '');
        $dept   = trim($_GET['dept'] ?? '');
        $yr     = trim($_GET['year'] ?? '');
        $where  = ['role="student"'];
        $params = [];
        $types  = '';
        if ($search) { $where[] = '(fullname LIKE ? OR email LIKE ? OR studentid LIKE ?)'; $s="%$search%"; $params=array_merge($params,[$s,$s,$s]); $types.='sss'; }
        if ($dept)   { $where[] = 'department=?'; $params[]=$dept; $types.='s'; }
        if ($yr)     { $where[] = 'year=?'; $params[]=$yr; $types.='s'; }
        $sql  = 'SELECT id,fullname,email,mobile,gender,studentid,department,year,interests,created_at FROM users WHERE ' . implode(' AND ',$where) . ' ORDER BY created_at DESC';
        $stmt = $conn->prepare($sql);
        if ($types) $stmt->bind_param($types,...$params);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        echo json_encode(['success'=>true,'data'=>$rows,'total'=>count($rows)]);
    }
    exit;
}

if ($method === 'POST') {
    $requireAdmin();
    $fn  = trim(strip_tags($_POST['fullname'] ?? ''));
    $em  = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));
    $mb  = trim(strip_tags($_POST['mobile'] ?? ''));
    $gn  = trim(strip_tags($_POST['gender'] ?? ''));
    $pw  = $_POST['password'] ?? '';
    $sid = trim(strip_tags($_POST['studentid'] ?? ''));
    $dep = trim(strip_tags($_POST['department'] ?? ''));
    $yr  = trim(strip_tags($_POST['year'] ?? ''));

    $errors=[];
    if (!$fn) $errors[]='Full name required.';
    if (!filter_var($em,FILTER_VALIDATE_EMAIL)) $errors[]='Valid email required.';
    if (!preg_match('/^[6-9]\d{9}$/',$mb)) $errors[]='Valid mobile required.';
    if (!in_array($gn,['Male','Female','Other'])) $errors[]='Gender required.';
    if (!$sid) $errors[]='Student ID required.';
    if (!in_array($dep,['cs','it','ece','mech','business','arts'])) $errors[]='Department required.';
    if (!in_array($yr,['1','2','3','4','graduate'])) $errors[]='Year required.';
    if (!$pw||!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/',$pw)) $errors[]='Strong password required.';
    if ($errors) { http_response_code(400); echo json_encode(['success'=>false,'errors'=>$errors]); exit; }

    $chk=$conn->prepare('SELECT id FROM users WHERE email=? OR studentid=?');
    $chk->bind_param('ss',$em,$sid);
    $chk->execute();
    $chk->store_result();
    if ($chk->num_rows>0) { http_response_code(409); echo json_encode(['success'=>false,'error'=>'Email or Student ID already exists.']); exit; }
    $chk->close();

    $hash=$conn->real_escape_string(password_hash($pw,PASSWORD_DEFAULT));
    $ins=$conn->prepare('INSERT INTO users (fullname,email,mobile,gender,password,studentid,department,year) VALUES (?,?,?,?,?,?,?,?)');
    $ins->bind_param('ssssssss',$fn,$em,$mb,$gn,$hash,$sid,$dep,$yr);
    $ins->execute();
    $newId=$ins->insert_id;
    $ins->close();
    echo json_encode(['success'=>true,'message'=>'Student added.','id'=>$newId]);
    exit;
}

if ($method === 'PUT') {
    $requireAdmin();
    $id   = (int)($_GET['id'] ?? 0);
    if (!$id) { http_response_code(400); echo json_encode(['error'=>'ID required']); exit; }
    $body = json_decode(file_get_contents('php://input'),true) ?? [];
    $allowed = ['fullname','email','mobile','gender','studentid','department','year'];
    $sets=[]; $params=[]; $types='';
    foreach ($allowed as $f) {
        if (isset($body[$f])) { $sets[]="$f=?"; $params[]=trim(strip_tags($body[$f])); $types.='s'; }
    }
    if (isset($body['password'])&&$body['password']) {
        $sets[]='password=?'; $params[]=password_hash($body['password'],PASSWORD_DEFAULT); $types.='s';
    }
    if (!$sets) { http_response_code(400); echo json_encode(['error'=>'Nothing to update.']); exit; }
    $params[]=$id; $types.='i';
    $stmt=$conn->prepare('UPDATE users SET '.implode(',',$sets).' WHERE id=? AND role="student"');
    $stmt->bind_param($types,...$params);
    $stmt->execute();
    $stmt->close();
    echo json_encode(['success'=>true,'message'=>'Student updated.']);
    exit;
}

if ($method === 'DELETE') {
    $requireAdmin();
    $id=(int)($_GET['id'] ?? 0);
    if (!$id) { http_response_code(400); echo json_encode(['error'=>'ID required']); exit; }
    $del=$conn->prepare('DELETE FROM users WHERE id=? AND role="student"');
    $del->bind_param('i',$id);
    $del->execute();
    $del->close();
    echo json_encode(['success'=>true,'message'=>'Student deleted.']);
    exit;
}

http_response_code(405);
echo json_encode(['error'=>'Method not allowed']);
