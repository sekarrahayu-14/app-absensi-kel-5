<?php
require_once __DIR__ . '/config/Auth.php';
header('Location: ' . (isLoggedIn() ? 'app/views/index.php' : 'app/views/login.php'));
exit;