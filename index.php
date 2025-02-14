<?php
require_once 'storage.php';

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

$bookingStorage = new Storage(new JsonIO('bookings.json'));
$carStorage = new Storage(new JsonIO('cars.json'));
$cars = $carStorage->findAll();

function filter_valid($car) {
    global $bookingStorage;
    if (isset($_GET['seats']) && $_GET["seats"] != '' && $car['passengers'] != $_GET['seats']) {
        return false;
    }
    if (isset($_GET['transmission']) && $_GET["transmission"] != '' && $car['transmission'] != $_GET['transmission']) {
        return false;
    }
    if (isset($_GET['min-price']) && $_GET["min-price"] != '' && $car['daily_price_huf'] < $_GET['min-price']) {
        return false;
    }
    if (isset($_GET['max-price']) && $_GET["max-price"] != '' && $car['daily_price_huf'] > $_GET['max-price']) {
        return false;
    }
    if (isset($_GET['from-date']) && isset($_GET['to-date']) &&
        filter_var($_GET["to-date"], FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^\d{4}-\d{2}-\d{2}$/"))) &&
        filter_var($_GET["from-date"], FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^\d{4}-\d{2}-\d{2}$/")))
    ) {
        $clashing_bookings = $bookingStorage->findMany(function ($booking) use ($car) {
            return $booking["car_id"] == $car["id"] && !(strtotime($_GET["from-date"]) > strtotime($booking["end_date"])
                    || strtotime($_GET["to-date"]) < strtotime($booking["start_date"]));
        });
        if (count($clashing_bookings) > 0) {
            return false;
        }
    }
    return true;
}

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
<h1>Rent cars easily!</h1>
<div class="search-bar">
    <form method="get" novalidate>
        <label>
            <input name="seats" type="number" placeholder="5" min="1" value="<?= $_GET["seats"] ?? "" ?>">seats
        </label>
        <label>From
            <input name="from-date" type="date" value="<?= $_GET["from-date"] ?? "" ?>">
        </label>
        <label>To
            <input name="to-date" type="date" value="<?= $_GET["to-date"] ?? "" ?>">
        </label>
        <label>Transmission
            <select name="transmission">
                <option value="" selected>Any</option>
                <option value="Manual" <?= (isset($_GET["transmission"]) && $_GET["transmission"] == "Manual") ? "selected" : "" ?>>
                    Manual
                </option>
                <option value="Automatic" <?= (isset($_GET["transmission"]) && $_GET["transmission"] == "Automatic") ? "selected" : "" ?>>
                    Automatic
                </option>
            </select>
        </label>
        <label>Price
            <input name="min-price" type="number" placeholder="15000" step="100" min="0"
                   value="<?= $_GET["min-price"] ?? "" ?>"> -
            <input name="max-price" type="number" placeholder="50000" step="100" min="0"
                   value="<?= $_GET["max-price"] ?? "" ?>">Ft
        </label>
        <button class="btn btn-filter">Filter</button>
    </form>
</div>
<div class="car-grid" id="carGrid">
    <?php if ($is_admin) { ?>
    <div class="car-card">
        <a href="add_car.php">
            <button class="btn btn-add">+</button>
        </a>
    </div>
    <?php } ?>
    <?php foreach ($cars as $car) {
        if (!filter_valid($car)) {
            continue;
        } ?>
        <div class="car-card">
            <?php if ($is_admin) { ?>
            <div class="button-group">
                <form method="post" action="delete_car.php">
                    <input type="hidden" name="id" value="<?= $car['id'] ?>">
                    <button class="btn btn-delete">Delete</button>
                </form>
                <a href="edit_car.php?id=<?=$car['id']?>"><button class="btn btn-edit">Edit</button></a>
            </div>
            <?php } ?>
            <div class="car-price"><?= $car["daily_price_huf"] ?> HUF</div>
            <img src="<?= $car["image"] ?>" alt="<?= $car["model"] ?>" class="car-image">
            <div class="car-info">
                <h3 class="car-name"><?= $car["brand"] ?> <b><?= $car["model"] ?></b></h3>
                <div class="details-and-book-btn">
                    <p class="car-details"><?= $car["passengers"] . ' seats' . ' - ' . $car["transmission"] ?></p>
                    <a href="car.php?id=<?= $car["id"] ?>">
                        <button class="btn btn-book">Book</button>
                    </a>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
</body>
</html>