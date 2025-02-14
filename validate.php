<?php
function valid_input(): bool
{
    global $errors;
    global $no_errors;
    if (!isset($_POST['brand']) || $_POST["brand"] == ''){
        $errors["brand"] = "Brand is required";
        $no_errors = false;
    }
    if (!isset($_POST['model']) || $_POST["model"] == ''){
        $errors["model"] = "Model is required";
        $no_errors = false;
    }
    if (!isset($_POST['year']) || $_POST["year"] == ''){
        $errors["year"] = "Year is required";
        $no_errors = false;
    } elseif (filter_var($_POST['year'], FILTER_VALIDATE_INT) === false || $_POST['year'] < 1885 || $_POST['year'] > date('Y')) {
        $errors["year"] = "Year must be a valid year";
        $no_errors = false;
    }
    if (!isset($_POST['fuel_type']) || $_POST["fuel_type"] == ''){
        $errors["fuel_type"] = "Fuel type is required";
        $no_errors = false;
    } elseif (!in_array($_POST['fuel_type'], ['Petrol', 'Diesel', 'Electric'])) {
        $errors["fuel_type"] = "Fuel type must be Petrol, Diesel or Electric";
        $no_errors = false;
    }
    if (!isset($_POST['transmission']) || $_POST["transmission"] == ''){
        $errors["transmission"] = "Transmission is required";
        $no_errors = false;
    } elseif (!in_array($_POST['transmission'], ['Manual', 'Automatic'])) {
        $errors["transmission"] = "Transmission must be Manual or Automatic";
        $no_errors = false;
    }
    if (!isset($_POST['passengers']) || $_POST["passengers"] == '') {
        $errors["passengers"] = "Passengers is required";
        $no_errors = false;
    } elseif (filter_var($_POST['passengers'], FILTER_VALIDATE_INT) === false || $_POST['passengers'] < 1) {
        $errors["passengers"] = "Passengers must be a positive integer";
        $no_errors = false;
    }
    if (!isset($_POST['daily_price_huf']) || $_POST["daily_price_huf"] == ''){
        $errors["daily_price_huf"] = "Daily price is required";
        $no_errors = false;
    } elseif (filter_var($_POST['daily_price_huf'], FILTER_VALIDATE_INT) === false || $_POST['daily_price_huf'] < 1) {
        $errors["daily_price_huf"] = "Daily price must be a positive integer";
        $no_errors = false;
    }
    if (!isset($_POST['image']) || $_POST["image"] == ''){
        $errors["image"] = "Image is required";
        $no_errors = false;
    } elseif (!filter_var($_POST['image'], FILTER_VALIDATE_URL)) {
        $errors["image"] = "Image must be a valid URL";
        $no_errors = false;
    }
    return $no_errors;
}
