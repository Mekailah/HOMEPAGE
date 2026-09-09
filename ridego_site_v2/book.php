<?php

session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$motorcycle_name = trim($_POST['motorcycle_name']);
$price_per_day = $_POST['price_per_day'];
$pickup_date = $_POST['pickup_date'];
$return_date = $_POST['return_date'];

$stmt = $conn->prepare(
    "INSERT INTO bookings
    (user_id, motorcycle_name, price_per_day, pickup_date, return_date)
    VALUES (?, ?, ?, ?, ?)"
);

$stmt->bind_param(
    "isdss",
    $user_id,
    $motorcycle_name,
    $price_per_day,
    $pickup_date,
    $return_date
);

if ($stmt->execute()) {
    echo "Booking submitted successfully!";
} else {
    echo "Booking failed. Please try again.";
}

$stmt->close();
$conn->close();

?>