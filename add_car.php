<?php

session_start();
$logged_in = true;
$is_admin = false;
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
} else {
    $user = $_SESSION['user'];
    if ($user['is_admin']) {
        $is_admin = true;
    }else{
        header('Location: index.php');
        exit;
    }
}
include_once "storage.php";
include_once "validate.php";
$carStorage = new Storage(new JsonIO('cars.json'));
$cars = $carStorage->findAll();
$errors = ["brand" => "", "model" => "", "year" => "", "fuel_type" => "", "transmission" => "", "passengers" => "", "daily_price_huf" => "", "image" => ""];
$no_errors = true;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && valid_input()) {
    $car = [
        'id' => uniqid(),
        'brand' => $_POST['brand'],
        'model' => $_POST['model'],
        'year' => (int)$_POST['year'],
        'fuel_type' => $_POST['fuel_type'],
        'transmission' => $_POST['transmission'],
        'passengers' => (int)$_POST['passengers'],
        'daily_price_huf' => (int)$_POST['daily_price_huf'],
        'image' => $_POST['image']
    ];
    $carStorage->add($car);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iKar Rental</title>
</head>
<link rel="stylesheet" href="add_car.css">
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
<h1>Add a car</h1>
<?php if ($_SERVER["REQUEST_METHOD"]=="POST" && $no_errors){?>
    <h2>Car added successfully</h2>
<?php } ?>
<form method="post" novalidate>
    <label>
        Brand
        <input name="brand" type="text" value="<?=$_POST["brand"] ?? ""?>">
        <span class="error"><?=$errors["brand"] ?></span>
    </label>
    <label>
        Model
        <input name="model" type="text" value="<?=$_POST["model"] ?? ""?>">
        <span class="error"><?=$errors["model"] ?></span>
    </label>
    <label>
        Year
        <input name="year" type="number" value="<?=$_POST["year"] ?? ""?>">
        <span class="error"><?=$errors["year"] ?></span>
    </label>
    <label>
        Fuel type
        <select name="fuel_type">
            <option value="Petrol" <?=(!isset($_POST["fuel_type"]) || $_POST["fuel_type"]=="Petrol") ? "selected" : ""?>>Petrol</option>
            <option value="Diesel" <?=(isset($_POST["fuel_type"]) && $_POST["fuel_type"]=="Diesel") ? "selected" : ""?>>Diesel</option>
            <option value="Electric" <?=(isset($_POST["fuel_type"]) && $_POST["fuel_type"]=="Electric") ? "selected" : ""?>>Electric</option>
        </select>
        <span class="error"><?=$errors["fuel_type"] ?></span>
    </label>
    <label>
        Transmission
        <select name="transmission" >
            <option value="Manual" <?=(isset($_POST["transmission"]) && $_POST["transmission"]=="Manual") ? "selected" : ""?>>Manual</option>
            <option value="Automatic" <?=(isset($_POST["transmission"]) && $_POST["transmission"]=="Automatic") ? "selected" : ""?>>Automatic</option>
        </select>
        <span class="error"><?=$errors["transmission"] ?></span>
    </label>
    <label>
        Passengers
        <input name="passengers" type="number"  value="<?=$_POST["passengers"] ?? ""?>">
        <span class="error"><?=$errors["passengers"] ?></span>
    </label>
    <label>
        Daily price
        <input name="daily_price_huf" type="number"  value="<?=$_POST["daily_price_huf"] ?? ""?>">
        <span class="error"><?=$errors["daily_price_huf"] ?></span>
    </label>
    <label>
        Image URL
        <input name="image" type="url" value="<?=$_POST["image"] ?? ""?>">
        <span class="error"><?=$errors["image"] ?></span>
    </label>
    <button type="submit">Add car</button>
</form>
</body>

