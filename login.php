<?php
session_start();
$logged_in = true;
if (isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}else{
    $logged_in = false;
}

$errors = ["email" => "", "password" => "", "credentials" => ""];
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    include_once "storage.php";
    $usersStorage = new Storage(new JsonIO('users.json'));
    if (!isset($_POST['email'])) {
        $errors["email"] = "Email is required";
    } elseif ($_POST['email'] == '' || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors["email"] = "Email is invalid";
    }
    if (!isset($_POST['password']) || $_POST['password'] == '') {
        $errors["password"] = "Password is required";
    }
    if ($errors["email"] === "" && $errors["password"] === "") {
        $user = $usersStorage->findOne(['email' => $_POST['email']]);
        if ($user !== NULL && password_verify($_POST['password'], $user['password'])) {
            $_SESSION['user'] = $user;
            header('Location: index.php');
            exit;
        } else{
            $errors["credentials"] = "Invalid email or password";
        }
    }
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
<h1>Login</h1>
<form method="post" novalidate>
    <label>Email address
        <input name="email" type="email" value="<?=$_POST["email"] ?? ""?>">
        <span class="error"><?= $errors["email"] ?></span>
    </label>
    <label>Password
        <input name="password" type="password">
        <span class="error"><?= $errors["password"] ?></span>
    </label>
    <span class="error"><?= $errors["credentials"] ?></span>
    <button type="submit">Login</button>
</form>
</body>

