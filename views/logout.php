<?php
require_once __DIR__ . '/../config/Auth.php';

$_SESSION = [];
session_destroy();
header('Location: login.php');
exit;
