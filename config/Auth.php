<?php
session_start();

const LOGIN_USERNAME = 'admin';
const LOGIN_PASSWORD = 'admin123';

function isLoggedIn(): bool
{
    return isset($_SESSION['user']);
}

function requireLogin(): void
{
    if (!isLoggedIn()){
        header('Location: login.php');
        exit;
    }
}
