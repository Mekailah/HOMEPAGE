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

/* Only allow POST requests */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: admin.php");
    exit;
}

$booking_id = (int) ($_POST['booking_id'] ?? 0);
$new_status = $_POST['status'] ?? '';

$allowed_statuses = [
    'Pending',
    'Confirmed',
    'Completed',
    'Cancelled'
];

/* Validate input */
if (
    $booking_id <= 0 ||
    !in_array($new_status, $allowed_statuses, true)
) {
    header("Location: admin.php");
    exit;
}

try {

    /* Start transaction */
    $conn->begin_transaction();


    /* Get the booking's current status and motorcycle */
    $stmt = $conn->prepare("
        SELECT status, motorcycle_name
        FROM bookings
        WHERE id = ?
        FOR UPDATE
    ");

    $stmt->bind_param("i", $booking_id);
    $stmt->execute();

    $result = $stmt->get_result();
    $booking = $result->fetch_assoc();

    $stmt->close();


    /* Booking does not exist */
    if (!$booking) {
        $conn->rollback();

        header("Location: admin.php");
        exit;
    }


    $old_status = $booking['status'];
    $motorcycle_name = $booking['motorcycle_name'];


    /*
        Pending and Confirmed = motorcycle is reserved.

        Completed and Cancelled = motorcycle becomes
        available again.
    */

    $old_reserves_unit = in_array(
        $old_status,
        ['Pending', 'Confirmed'],
        true
    );

    $new_reserves_unit = in_array(
        $new_status,
        ['Pending', 'Confirmed'],
        true
    );


    /*
        Example:
        Pending -> Cancelled
        Confirmed -> Completed

        Return one motorcycle to inventory.
    */
    if ($old_reserves_unit && !$new_reserves_unit) {

        $stmt = $conn->prepare("
            UPDATE motorcycle_inventory
            SET available_units =
                LEAST(total_units, available_units + 1)
            WHERE motorcycle_name = ?
        ");

        $stmt->bind_param(
            "s",
            $motorcycle_name
        );

        $stmt->execute();
        $stmt->close();
    }


    /*
        Example:
        Cancelled -> Pending
        Completed -> Confirmed

        Reserve one motorcycle again.
    */
    if (!$old_reserves_unit && $new_reserves_unit) {

        /* Check if a unit is available */
        $stmt = $conn->prepare("
            SELECT available_units
            FROM motorcycle_inventory
            WHERE motorcycle_name = ?
            FOR UPDATE
        ");

        $stmt->bind_param(
            "s",
            $motorcycle_name
        );

        $stmt->execute();

        $inventory_result = $stmt->get_result();
        $inventory = $inventory_result->fetch_assoc();

        $stmt->close();


        if (
            !$inventory ||
            (int) $inventory['available_units'] <= 0
        ) {
            $conn->rollback();

            header(
                "Location: admin.php?status=unavailable"
            );
            exit;
        }


        /* Reserve one unit */
        $stmt = $conn->prepare("
            UPDATE motorcycle_inventory
            SET available_units = available_units - 1
            WHERE motorcycle_name = ?
        ");

        $stmt->bind_param(
            "s",
            $motorcycle_name
        );

        $stmt->execute();
        $stmt->close();
    }


    /* Update booking status */
    $stmt = $conn->prepare("
        UPDATE bookings
        SET status = ?
        WHERE id = ?
    ");

    $stmt->bind_param(
        "si",
        $new_status,
        $booking_id
    );

    $stmt->execute();
    $stmt->close();


    /* Save everything together */
    $conn->commit();

    $conn->close();


    header("Location: admin.php?status=updated");
    exit;


} catch (Throwable $e) {

    $conn->rollback();
    $conn->close();

    header("Location: admin.php?status=error");
    exit;
}
?>