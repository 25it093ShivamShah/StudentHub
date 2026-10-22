<?php
require_once __DIR__ . '/auth.php';
if ($_SESSION['user_role'] !== 'admin') { header('Location: /STUDENTHUB/pages/dashboard.php'); exit; }
