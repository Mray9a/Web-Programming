<?php
session_start();
$logged_in = true;
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
include_once 'storage.php';
$user = $_SESSION['user'];
$is_admin = $user["is_admin"];
$bookingStorage = new Storage(new JsonIO('bookings.json'));
$bookings = $bookingStorage->findAll();
$carStorage = new Storage(new JsonIO('cars.json'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iKar Rental</title>
</head>
<link rel="stylesheet" href="index.css">
<body>
<header>
    <h2><a href="index.php">iKar Rental</a></h2>
    <?php if ($logged_in){?>
        <h2>Welcome <?=$_SESSION['user']['full_name']?></h2>
    <?php } ?>
    <nav>
        <ul>
            <?php if ($logged_in) : ?>
                <li><a href="logout.php">Logout</a></li>
                <li><a href="account.php">Account</a></li>
            <?php else: ?>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
<h1>Logged in as <?=$user["full_name"]?></h1>
<h2><?=$is_admin? "All" : "My"?> bookings:</h2>
<div class="car-grid" id="carGrid">
    <?php foreach ($bookings as $booking) {
        $car = $carStorage->findOne(["id"=>$booking["car_id"]]);
        if ($booking["user_email"] !== $user["email"] && !$is_admin) {
            continue;
        }?>
    <div class="car-card">
        <?php if ($is_admin) { ?>
            <div class="button-group">
                <form method="post" action="delete_booking.php">
                    <input type="hidden" name="id" value="<?= $booking['id'] ?>">
                    <button class="btn btn-delete">Delete</button>
                </form>
            </div>
        <?php } ?>
        <div class="car-price"><?= 'from '.$booking["start_date"].' to '.$booking["end_date"]?></div>
        <img src="<?= $car["image"] ?>" alt="<?= $car["model"] ?>" class="car-image">
        <div class="car-info">
            <h3 class="car-name"><?= $car["brand"] ?> <b><?= $car["model"] ?></b></h3>
            <div class="details-and-book-btn">
                <p class="car-details"><?= $car["passengers"] . ' seats' . ' - ' . $car["transmission"] ?></p>
            </div>
        </div>
    </div>
    <?php } ?>
</div>
</body>
</html>

