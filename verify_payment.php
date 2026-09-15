<?php

session_start();

require_once 'db.php';


/* User must be logged in */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}


/* Only the owner/admin can verify payments */
if (
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'admin'
) {
    header("Location: index.php");
    exit;
}


/* Verification must come from the dashboard form */
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

if (!$booking_id || $booking_id < 1) {
    header("Location: admin.php?payment=error");
    exit;
}


try {

    /*
        Mark the payment as Paid only when the booking
        currently has a payment proof and is still waiting
        for verification.
    */
    $stmt = $conn->prepare("
        UPDATE bookings
        SET payment_status = 'Paid'
        WHERE id = ?
          AND payment_proof IS NOT NULL
          AND payment_proof <> ''
          AND payment_status = 'Pending Verification'
    ");

    if (!$stmt) {
        throw new RuntimeException(
            "Unable to prepare payment verification."
        );
    }

    $stmt->bind_param(
        "i",
        $booking_id
    );

    if (!$stmt->execute()) {
        $stmt->close();

        throw new RuntimeException(
            "Unable to verify payment."
        );
    }

    $affected_rows =
        $stmt->affected_rows;

    $stmt->close();


    /*
        If nothing was updated, the booking may not exist,
        may have no proof, or may already be marked Paid.
        Check whether it is already Paid so repeated clicks
        remain safe.
    */
    if ($affected_rows !== 1) {

        $check_stmt = $conn->prepare("
            SELECT payment_status
            FROM bookings
            WHERE id = ?
            LIMIT 1
        ");

        if (!$check_stmt) {
            throw new RuntimeException(
                "Unable to check payment status."
            );
        }

        $check_stmt->bind_param(
            "i",
            $booking_id
        );

        if (!$check_stmt->execute()) {
            $check_stmt->close();

            throw new RuntimeException(
                "Unable to check payment status."
            );
        }

        $result =
            $check_stmt->get_result();

        $booking =
            $result->fetch_assoc();

        $check_stmt->close();

        if (
            !$booking ||
            $booking['payment_status'] !== 'Paid'
        ) {
            throw new RuntimeException(
                "Payment could not be verified."
            );
        }
    }


    $conn->close();

    header(
        "Location: admin.php?payment=verified"
    );
    exit;


} catch (Throwable $e) {

    error_log(
        "Payment verification error: " .
        $e->getMessage()
    );

    $conn->close();

    header(
        "Location: admin.php?payment=error"
    );
    exit;
}

?>
