<?php
session_start();
require_once 'db.php';

/* Only admin can update booking status */
if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'admin'
) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: admin.php?status=updated");
    exit;
}

$booking_id = (int) ($_POST['booking_id'] ?? 0);
$status = $_POST['status'] ?? '';

$allowed_statuses = [
    'Pending',
    'Confirmed',
    'Completed',
    'Cancelled'
];

if (
    $booking_id <= 0 ||
    !in_array($status, $allowed_statuses, true)
) {
    header("Location: admin.php");
    exit;
}

$stmt = $conn->prepare("
    UPDATE bookings
    SET status = ?
    WHERE id = ?
");

$stmt->bind_param("si", $status, $booking_id);
$stmt->execute();

$stmt->close();
$conn->close();

header("Location: admin.php");
exit;
?>