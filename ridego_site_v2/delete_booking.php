<?php

session_start();
require_once 'db.php';


/* Only admin can delete bookings */
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


/* Validate booking ID */
$booking_id =
    filter_input(
        INPUT_POST,
        'booking_id',
        FILTER_VALIDATE_INT
    );


if (!$booking_id || $booking_id <= 0) {
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


    if (!$stmt) {
        throw new RuntimeException(
            "Unable to prepare booking lookup."
        );
    }


    $stmt->bind_param(
        "i",
        $booking_id
    );


    if (!$stmt->execute()) {
        throw new RuntimeException(
            "Unable to retrieve booking."
        );
    }


    $result =
        $stmt->get_result();

    $booking =
        $result->fetch_assoc();


    $stmt->close();


    /* Booking does not exist */
    if (!$booking) {

        $conn->rollback();
        $conn->close();

        header(
            "Location: admin.php?delete=error"
        );

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
            basename(
                $driver_license
            );

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


        if (!$stmt) {
            throw new RuntimeException(
                "Unable to prepare inventory restoration."
            );
        }


        $stmt->bind_param(
            "s",
            $motorcycle_name
        );


        if (!$stmt->execute()) {
            throw new RuntimeException(
                "Unable to restore motorcycle inventory."
            );
        }


        $stmt->close();
    }


    /* DELETE BOOKING */

    $stmt = $conn->prepare("
        DELETE FROM bookings
        WHERE id = ?
    ");


    if (!$stmt) {
        throw new RuntimeException(
            "Unable to prepare booking deletion."
        );
    }


    $stmt->bind_param(
        "i",
        $booking_id
    );


    if (!$stmt->execute()) {
        throw new RuntimeException(
            "Unable to delete booking."
        );
    }


    if ($stmt->affected_rows !== 1) {

        $stmt->close();

        throw new RuntimeException(
            "Booking deletion did not affect exactly one row."
        );
    }


    $stmt->close();


    /* Commit database changes first */

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


    header(
        "Location: admin.php?delete=success"
    );

    exit;


} catch (Throwable $e) {

    error_log(
        "Delete booking error: " .
        $e->getMessage()
    );


    try {
        $conn->rollback();
    } catch (Throwable $rollbackError) {

        error_log(
            "Delete booking rollback error: " .
            $rollbackError->getMessage()
        );
    }


    $conn->close();


    header(
        "Location: admin.php?delete=error"
    );

    exit;
}

?>