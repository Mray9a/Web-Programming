<?php
session_start();
$logged_in = true;
$is_admin = false;
if (!isset($_SESSION['user'])) {
    $logged_in = false;
} else {
    $user = $_SESSION['user'];
    if ($user['is_admin']) {
        $is_admin = true;
    }
}
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}
include_once "storage.php";
$carStorage = new Storage(new JsonIO('cars.json'));
$cars = $carStorage->findAll();

if (!in_array($_GET["id"], array_column($cars, 'id'))) {
    header('Location: index.php');
    exit;
}
$car = array_find($cars, function ($car) {
    return $car["id"] == $_GET["id"];
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iKar Rental</title>
</head>
<link rel="stylesheet" href="car.css">
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

<br>
<div class="car-card">
    <div class="car-image-container">
        <img src="<?= $car["image"] ?>" class="car-image" alt="Car image">
    </div>
    <div class="car-details">
        <h1 class="car-title"><?= $car["brand"] . ' ' . $car["model"] ?></h1>
        <div class="specs-grid">
            <div class="spec-item">
                <span class="spec-label">Fuel</span>
                <span class="spec-value"><?= $car["fuel_type"] ?></span>
            </div>
            <div class="spec-item">
                <span class="spec-label">Year of manufacture</span>
                <span class="spec-value"><?= $car["year"] ?></span>
            </div>
            <div class="spec-item">
                <span class="spec-label">Transmission</span>
                <span class="spec-value"><?= $car["transmission"] ?></span>
            </div>
            <div class="spec-item">
                <span class="spec-label">Number of seats</span>
                <span class="spec-value"><?= $car["passengers"] ?></span>
            </div>
        </div>
        <div class="price"><?= $car["daily_price_huf"] ?> HUF /day</div>
        <div class="button-group">
            <form method="post" action="book.php?>">
                <label>From
                    <input type="date" name="start-date">
                </label>
                <label>To
                    <input type="date" name="end-date">
                </label>
                <input type="hidden" name="car-id" value="<?=$car["id"]?>">
                <button class="btn btn-book" type="submit">Book it</button>
            </form>
        </div>
    </div>
</div>
</body>

