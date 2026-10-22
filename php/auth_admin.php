<?php
require_once __DIR__ . '/auth.php';
if ($_SESSION['user_role'] !== 'admin') { header('Location: /pages/dashboard.php'); exit; }
