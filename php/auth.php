<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: /STUDENTHUB/pages/login.html'); exit; }
if (time() - ($_SESSION['last_activity'] ?? 0) > 1800) {
    session_destroy();
    header('Location: /STUDENTHUB/pages/login.html?timeout=1');
    exit;
}
$_SESSION['last_activity'] = time();
