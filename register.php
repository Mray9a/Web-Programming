<?php
session_start();
$logged_in = true;
if (isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}else{
    $logged_in = false;
}

$errors = ["email" => "", "password" => "", "full_name" => ""];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once "storage.php";
    $usersStorage = new Storage(new JsonIO('users.json'));

    $no_errors = true;

    if (!isset($_POST['email'])) {
        $errors["email"] = "Email is required";
        $no_errors = false;
    } elseif ($_POST['email'] == '' || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors["email"] = "Email is invalid";
        $no_errors = false;
    } elseif ($usersStorage->findOne(['email' => $_POST['email']]) !== NULL) {
        $errors["email"] = "Email already exists";
        $no_errors = false;
    }
    if (!isset($_POST['password'])) {
        $errors["password"] = "Password is required";
        $no_errors = false;
    } elseif ($_POST['password'] == '' || strlen($_POST['password']) < 4) {
        $errors["password"] = "Password must be at least 4 characters long";
        $no_errors = false;
    }
    if (!isset($_POST['full_name']) || $_POST['full_name'] == '') {
        $errors["full_name"] = "Full name is required";
        $no_errors = false;
    }
    if ($no_errors) {
        $user = [
            'full_name' => $_POST["full_name"],
            'email' => $_POST["email"],
            'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
            'is_admin' => false,
            'id' => uniqid()
        ];
        $usersStorage->add($user);
        $_SESSION['user'] = $user;
        header('Location: index.php');
        exit;
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
<h1>Register</h1>
<form method="post" novalidate>
    <label>Full name
        <input name="full_name" type="text" value="<?=$_POST["full_name"] ?? ""?>">
        <span class="error"><?=$errors["full_name"]?></span>
    </label>
    <label>Email address
        <input name="email" type="email" value="<?=$_POST["email"] ?? ""?>">
        <span class="error"><?=$errors["email"]?></span>
    </label>
    <label>Password
        <input name="password" type="password" value="<?=$_POST["password"] ?? ""?>">
        <span class="error"><?=$errors["password"]?></span>
    </label>
    <button type="submit">Register</button>
</form>
</body>

