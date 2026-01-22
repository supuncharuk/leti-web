<?php
session_start();

function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

function checkLogin()
{
    if (!isLoggedIn()) {
        header("Location: index.php");
        exit();
    }
}

function isAdmin()
{
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function checkAdmin()
{
    checkLogin();
    if (!isAdmin()) {
        header("Location: dashboard.php");
        exit();
    }
}
?>