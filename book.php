<?php
session_start();
$logged_in = true;
if (!isset($_POST['car-id'])){
    header('Location: index.php');
    exit;
}
if (!isset($_SESSION['user'])) {
    header('Location: login.php?id=');
    exit;
}

if (!isset($_POST["start-date"]) || !isset($_POST["end-date"]) || $_POST["car-id"] == "" ||
    !filter_var($_POST["start-date"], FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^\d{4}-\d{2}-\d{2}$/"))) ||
    !filter_var($_POST["end-date"], FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^\d{4}-\d{2}-\d{2}$/")))) {
    header('Location: car.php?id=' . $_POST['car-id']);
    exit;
}
if (strtotime($_POST["start-date"]) > strtotime($_POST["end-date"])) {
    header('Location: car.php?id=' . $_POST['car-id']);
    exit;
}
include_once "storage.php";
$bookingStorage = new Storage(new JsonIO('bookings.json'));
$clashing_bookings = $bookingStorage->findMany(function ($booking) {
    return $booking["car_id"] == $_POST["car-id"] && !(strtotime($_POST["start-date"]) > strtotime($booking["end_date"])
            || strtotime($_POST["end-date"]) < strtotime($booking["start_date"]));
});
$carStorage = new Storage(new JsonIO('cars.json'));
$successful_booking = false;
$car = $carStorage->findById($_POST["car-id"]);
if (count($clashing_bookings) == 0) {
    $successful_booking = true;
    $bookingStorage->add([
        "id" => uniqid(),
        "car_id" => $_POST["car-id"],
        "user_email" => $_SESSION["user"]["email"],
        "start_date" => $_POST["start-date"],
        "end_date" => $_POST["end-date"]
    ]);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iKar Rental</title>
</head>
<link rel="stylesheet" href="book.css">
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
<?php if ($successful_booking){?>
<div class="status-container success">
    <div class="status-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"></polyline>
        </svg>
    </div>
    <h1>Successful booking!</h1>
    <p>The <?=$car["brand"].' '.$car["model"]?> has been successfully booked for the interval <?=$_POST["start-date"]?> to <?=$_POST["end-date"]?>.<br>
        You can track the status of your reservation on your profile page.</p>
    <a href="account.php" class="btn">My profile</a>
</div>
<?php }else{ ?>
<div class="status-container failure">
    <div class="status-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
    </div>
    <h1>Booking failed!</h1>
    <p>The <?=$car["brand"].' '.$car["model"]?> is not available in the specified interval from <?=$_POST["start-date"]?> to <?=$_POST["end-date"]?>.<br>
        Try entering a different interval or search for another vehicle.</p>
    <a href="car.php?id=<?=$_POST["car-id"]?>" class="btn">Back to the vehicle side</a>
</div>
<?php } ?>
</body>
</html>

