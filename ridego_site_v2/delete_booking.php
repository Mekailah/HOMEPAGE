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

$booking_id = (int) ($_POST['booking_id'] ?? 0);

if ($booking_id <= 0) {
    header("Location: admin.php?delete=error");
    exit;
}

$license_path = null;

try {

    $conn->begin_transaction();


    /* GET BOOKING DETAILS */

    $stmt = $conn->prepare("
        SELECT
            motorcycle_name,
            status,
            driver_license
        FROM bookings
        WHERE id = ?
        FOR UPDATE
    ");

    $stmt->bind_param(
        "i",
        $booking_id
    );

    $stmt->execute();

    $result = $stmt->get_result();
    $booking = $result->fetch_assoc();

    $stmt->close();


    if (!$booking) {

        $conn->rollback();
        $conn->close();

        header("Location: admin.php?delete=error");
        exit;
    }


    $motorcycle_name =
        $booking['motorcycle_name'];

    $status =
        $booking['status'];

    $driver_license =
        $booking['driver_license'];


    /*
        Prepare license path.

        basename() prevents directory traversal.
    */

    if ($driver_license !== '') {

        $safe_license_name =
            basename($driver_license);

        $license_path =
            __DIR__ .
            '/uploads/licenses/' .
            $safe_license_name;
    }


    /*
        RESTORE INVENTORY IF BOOKING
        WAS PENDING OR CONFIRMED
    */

    if (
        $status === 'Pending' ||
        $status === 'Confirmed'
    ) {

        $stmt = $conn->prepare("
            UPDATE motorcycle_inventory
            SET available_units =
                LEAST(
                    total_units,
                    available_units + 1
                )
            WHERE motorcycle_name = ?
        ");

        $stmt->bind_param(
            "s",
            $motorcycle_name
        );

        $stmt->execute();
        $stmt->close();
    }


    /* DELETE BOOKING */

    $stmt = $conn->prepare("
        DELETE FROM bookings
        WHERE id = ?
    ");

    $stmt->bind_param(
        "i",
        $booking_id
    );

    $stmt->execute();


    if ($stmt->affected_rows !== 1) {

        $stmt->close();

        $conn->rollback();
        $conn->close();

        header("Location: admin.php?delete=error");
        exit;
    }


    $stmt->close();


    /*
        Commit database changes first.
    */

    $conn->commit();
    $conn->close();


    /*
        Remove uploaded driver's license
        only after successful database commit.
    */

    if (
        $license_path !== null &&
        is_file($license_path)
    ) {

        if (!unlink($license_path)) {

            error_log(
                "Unable to delete driver's license file: " .
                $license_path
            );
        }
    }


    header("Location: admin.php?delete=success");
    exit;


} catch (Throwable $e) {

    $conn->rollback();
    $conn->close();

    error_log(
        "Delete booking error: " .
        $e->getMessage()
    );

    header("Location: admin.php?delete=error");
    exit;
}

?>