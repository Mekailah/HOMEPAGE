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
$driver_license = '';

if (isset($_FILES['driver_license']) && $_FILES['driver_license']['error'] === UPLOAD_ERR_OK) {

    $upload_dir = __DIR__ . '/uploads/licenses/';

    $file_tmp = $_FILES['driver_license']['tmp_name'];
    $file_size = $_FILES['driver_license']['size'];

    if ($file_size > 5 * 1024 * 1024) {
        echo "Driver's license image must be 5MB or smaller.";
        exit;
    }

    $image_info = getimagesize($file_tmp);

    if ($image_info === false) {
        echo "Please upload a valid driver's license image.";
        exit;
    }

    $allowed_types = ['image/jpeg', 'image/png'];

    if (!in_array($image_info['mime'], $allowed_types, true)) {
        echo "Only JPG and PNG images are allowed.";
        exit;
    }

    $extension = ($image_info['mime'] === 'image/png') ? 'png' : 'jpg';

    $driver_license = uniqid('license_', true) . '.' . $extension;

    if (!move_uploaded_file(
        $file_tmp,
        $upload_dir . $driver_license
    )) {
        echo "Failed to upload driver's license.";
        exit;
    }
} else {
    echo "Please upload a picture of your driver's license.";
    exit;
}
$price_per_day = $_POST['price_per_day'];
$pickup_date = $_POST['pickup_date'];
$pickup_time = $_POST['pickup_time'];
$return_date = $_POST['return_date'];
$return_time = $_POST['return_time'];
$payment_method = $_POST['payment_method'];
$pickup_branch = $_POST['pickup_branch'];
$dropoff_branch = $_POST['dropoff_branch'];
$pickup_datetime = strtotime($pickup_date . ' ' . $pickup_time);
$return_datetime = strtotime($return_date . ' ' . $return_time);

if ($return_datetime <= $pickup_datetime) {
    header("Location: index.php?booking=error&message=" . urlencode(
        "Return date and time must be after the pickup date and time."
    ));
    exit;
}

$inventory = $conn->prepare("
    SELECT available_units
    FROM motorcycle_inventory
    WHERE motorcycle_name = ?
");

$inventory->bind_param("s", $motorcycle_name);
$inventory->execute();

$inventory_result = $inventory->get_result();
$inventory_data = $inventory_result->fetch_assoc();

if (!$inventory_data || $inventory_data['available_units'] <= 0) {
    echo "Sorry, this motorcycle is currently unavailable.";
    exit;
}

$inventory->close();

$stmt = $conn->prepare(

    "INSERT INTO bookings
    (user_id, driver_license, motorcycle_name, price_per_day, pickup_date, pickup_time, return_date, return_time, payment_method, pickup_branch, dropoff_branch)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
);

$stmt->bind_param(
    "issdsssssss",
    $user_id,
    $driver_license,
    $motorcycle_name,
    $price_per_day,
    $pickup_date,
    $pickup_time,
    $return_date,
    $return_time,
    $payment_method,
    $pickup_branch,
    $dropoff_branch
);

if ($stmt->execute()) {

    $update_inventory = $conn->prepare("
    UPDATE motorcycle_inventory
    SET available_units = available_units - 1
    WHERE motorcycle_name = ?
      AND available_units > 0
");

$update_inventory->bind_param("s", $motorcycle_name);
$update_inventory->execute();
$update_inventory->close();

    header("Location: index.php?booking=success");
exit;

} else {
    echo "Booking failed. Please try again.";
}

$stmt->close();
$conn->close();

?>