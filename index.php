<?php
require_once __DIR__ . '/config/Auth.php';
header('Location: ' . (isLoggedIn() ? 'views/index.php' : 'views/Login.php'));
exit;