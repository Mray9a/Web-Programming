<?php
session_start();
$logged_in = true;
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
} elseif (!$_SESSION['user']['is_admin']) {
    header('Location: index.php');
    exit;
}
if (!isset($_POST['id'])) {
    header('Location: index.php');
    exit;
}
include_once "storage.php";
$carStorage = new Storage(new JsonIO('cars.json'));
$carStorage->delete($_POST['id']);
header('Location: index.php');
exit;