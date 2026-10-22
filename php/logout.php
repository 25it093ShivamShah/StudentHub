<?php
session_start();
session_unset();
session_destroy();
header('Location: /STUDENTHUB/pages/login.html');
exit;
