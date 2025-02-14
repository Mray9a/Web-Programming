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
$bookingStorage = new Storage(new JsonIO('bookings.json'));
$bookingStorage->delete($_POST['id']);
header('Location: account.php');
exit;
