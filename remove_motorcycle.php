<?php
session_start();
require_once 'db.php';

if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'admin'
) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: admin.php");
    exit;
}

$inventory_id = filter_input(INPUT_POST, 'inventory_id', FILTER_VALIDATE_INT);

if (!$inventory_id || $inventory_id < 1) {
    header("Location: admin.php?motorcycle=remove_error");
    exit;
}

$stmt = $conn->prepare("
    UPDATE motorcycle_inventory
    SET is_active = 0
    WHERE id = ? AND is_active = 1
");

if (!$stmt) {
    error_log("Remove motorcycle prepare error: " . $conn->error);
    header("Location: admin.php?motorcycle=remove_error");
    exit;
}

$stmt->bind_param("i", $inventory_id);

if (!$stmt->execute() || $stmt->affected_rows !== 1) {
    error_log("Remove motorcycle error: " . $stmt->error);
    $stmt->close();
    header("Location: admin.php?motorcycle=remove_error");
    exit;
}

$stmt->close();

header("Location: admin.php?motorcycle=removed");
exit;
?>
