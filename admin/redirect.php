<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$action = $_GET['action'] ?? '';
$target = $_GET['target'] ?? '';

if (!in_array($action, ['create', 'update', 'delete']) || !in_array($target, ['domain', 'level', 'exercise'])) {
    header("Location: index.php");
    exit();
}

// Redirection
$page = "{$action}/{$action}_{$target}.php";
header("Location: $page");
exit();
