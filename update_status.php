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


/* Get and validate form values */
$booking_id =
    filter_input(
        INPUT_POST,
        'booking_id',
        FILTER_VALIDATE_INT
    );

$new_status =
    trim($_POST['status'] ?? '');


$allowed_statuses = [
    'Pending',
    'Confirmed',
    'Completed',
    'Cancelled'
];


if (
    !$booking_id ||
    !in_array(
        $new_status,
        $allowed_statuses,
        true
    )
) {
    header("Location: admin.php?status=invalid");
    exit;
}


try {

    /* Start transaction */
    $conn->begin_transaction();


    /* Get current booking status and motorcycle */
    $stmt = $conn->prepare("
        SELECT status, motorcycle_name
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

        header(
            "Location: admin.php?status=notfound"
        );

        exit;
    }


    $old_status =
        $booking['status'];

    $motorcycle_name =
        $booking['motorcycle_name'];


    /*
        Pending and Confirmed reserve a unit.

        Completed and Cancelled release a unit.
    */

    $old_reserves_unit =
        in_array(
            $old_status,
            ['Pending', 'Confirmed'],
            true
        );


    $new_reserves_unit =
        in_array(
            $new_status,
            ['Pending', 'Confirmed'],
            true
        );


    /*
        Pending -> Cancelled
        Confirmed -> Completed

        Return one motorcycle to inventory.
    */

    if (
        $old_reserves_unit &&
        !$new_reserves_unit
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
                "Unable to prepare inventory release."
            );
        }


        $stmt->bind_param(
            "s",
            $motorcycle_name
        );


        if (!$stmt->execute()) {
            throw new RuntimeException(
                "Unable to release motorcycle."
            );
        }


        $stmt->close();
    }


    /*
        Cancelled -> Pending
        Completed -> Confirmed

        Reserve one motorcycle again.
    */

    if (
        !$old_reserves_unit &&
        $new_reserves_unit
    ) {

        /* Lock inventory row and check availability */

        $stmt = $conn->prepare("
            SELECT available_units
            FROM motorcycle_inventory
            WHERE motorcycle_name = ?
            FOR UPDATE
        ");


        if (!$stmt) {
            throw new RuntimeException(
                "Unable to prepare inventory lookup."
            );
        }


        $stmt->bind_param(
            "s",
            $motorcycle_name
        );


        if (!$stmt->execute()) {
            throw new RuntimeException(
                "Unable to check inventory."
            );
        }


        $inventory_result =
            $stmt->get_result();

        $inventory =
            $inventory_result->fetch_assoc();


        $stmt->close();


        /* No motorcycle available */

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
            SET available_units =
                available_units - 1
            WHERE motorcycle_name = ?
              AND available_units > 0
        ");


        if (!$stmt) {
            throw new RuntimeException(
                "Unable to prepare inventory reservation."
            );
        }


        $stmt->bind_param(
            "s",
            $motorcycle_name
        );


        if (!$stmt->execute()) {
            throw new RuntimeException(
                "Unable to reserve motorcycle."
            );
        }


        if ($stmt->affected_rows !== 1) {

            $stmt->close();

            $conn->rollback();

            header(
                "Location: admin.php?status=unavailable"
            );

            exit;
        }


        $stmt->close();
    }


    /* Update booking status */

    $stmt = $conn->prepare("
        UPDATE bookings
        SET status = ?
        WHERE id = ?
    ");


    if (!$stmt) {
        throw new RuntimeException(
            "Unable to prepare status update."
        );
    }


    $stmt->bind_param(
        "si",
        $new_status,
        $booking_id
    );


    if (!$stmt->execute()) {
        throw new RuntimeException(
            "Unable to update booking status."
        );
    }


    $stmt->close();


    /* Save all changes together */

    $conn->commit();

    $conn->close();


    header(
        "Location: admin.php?status=updated"
    );

    exit;


} catch (Throwable $e) {

    /* Log technical error without showing it to the user */

    error_log(
        "Booking status update error: " .
        $e->getMessage()
    );


    /* Undo database changes */

    try {
        $conn->rollback();
    } catch (Throwable $rollbackError) {

        error_log(
            "Status update rollback error: " .
            $rollbackError->getMessage()
        );
    }


    $conn->close();


    header(
        "Location: admin.php?status=error"
    );

    exit;
}

?>