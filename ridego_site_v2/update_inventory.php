<?php
session_start();
require_once 'db.php';

/* Only admin can update inventory */
if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'admin'
) {
    header("Location: login.php");
    exit;
}

/* Only allow POST requests */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: admin.php");
    exit;
}

$inventory_id = (int) ($_POST['inventory_id'] ?? 0);
$total_units = (int) ($_POST['total_units'] ?? 0);
$available_units = (int) ($_POST['available_units'] ?? 0);

/* Basic validation */
if (
    $inventory_id <= 0 ||
    $total_units < 0 ||
    $available_units < 0
) {
    header("Location: admin.php");
    exit;
}

/* Available units should not be greater than total units */
if ($available_units > $total_units) {
    header("Location: admin.php?inventory=error");
    exit;
}

$stmt = $conn->prepare("
    UPDATE motorcycle_inventory
    SET total_units = ?, available_units = ?
    WHERE id = ?
");

$stmt->bind_param(
    "iii",
    $total_units,
    $available_units,
    $inventory_id
);

$stmt->execute();

$stmt->close();
$conn->close();

header("Location: admin.php?inventory=updated");
exit;
?>