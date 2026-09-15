<?php

session_start();

require_once 'db.php';

date_default_timezone_set('Asia/Manila');


/* Redirect back to homepage with an error message */
function bookingError(string $message): void
{
    header(
        "Location: index.php?booking=error&message=" .
        urlencode($message)
    );
    exit;
}


/* User must be logged in */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}


/* Booking must come from the form */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}


$user_id = (int) $_SESSION['user_id'];


/* Get and clean form values */
$motorcycle_name =
    trim($_POST['motorcycle_name'] ?? '');

$pickup_date =
    trim($_POST['pickup_date'] ?? '');

$pickup_time =
    trim($_POST['pickup_time'] ?? '');

$return_date =
    trim($_POST['return_date'] ?? '');

$return_time =
    trim($_POST['return_time'] ?? '');

$payment_method =
    trim($_POST['payment_method'] ?? '');

$pickup_branch =
    trim($_POST['pickup_branch'] ?? '');

$dropoff_branch =
    trim($_POST['dropoff_branch'] ?? '');


/* Check required fields */
if (
    $motorcycle_name === '' ||
    $pickup_date === '' ||
    $pickup_time === '' ||
    $return_date === '' ||
    $return_time === '' ||
    $payment_method === '' ||
    $pickup_branch === '' ||
    $dropoff_branch === ''
) {
    bookingError(
        "Please complete all required booking fields."
    );
}


/* Validate motorcycle name length */
if (mb_strlen($motorcycle_name) > 100) {
    bookingError(
        "Invalid motorcycle selected."
    );
}


/* Validate payment method */
$allowed_payment_methods = [
    'GCash',
    'BPI'
];

if (
    !in_array(
        $payment_method,
        $allowed_payment_methods,
        true
    )
) {
    bookingError(
        "Please select a valid payment method."
    );
}


/* Validate pickup and drop-off branches */
$allowed_branches = [
    'Hibbard Avenue, Dumaguete City',
    'Leon Kilat Mall, Bacong'
];

if (
    !in_array(
        $pickup_branch,
        $allowed_branches,
        true
    )
) {
    bookingError(
        "Please select a valid pickup branch."
    );
}

if (
    !in_array(
        $dropoff_branch,
        $allowed_branches,
        true
    )
) {
    bookingError(
        "Please select a valid drop-off branch."
    );
}


/* Validate booking time */
function isValidBookingTime(string $time): bool
{
    if (!preg_match('/^\d{2}:\d{2}$/', $time)) {
        return false;
    }

    [$hour, $minute] =
        array_map(
            'intval',
            explode(':', $time)
        );

    /* Only :00 or :30 */
    if (
        $minute !== 0 &&
        $minute !== 30
    ) {
        return false;
    }

    /* Earliest allowed: 6:00 AM */
    if ($hour < 6) {
        return false;
    }

    /* Latest allowed: 10:00 PM */
    if ($hour > 22) {
        return false;
    }

    /* At 10 PM, only 10:00 PM is allowed */
    if (
        $hour === 22 &&
        $minute !== 0
    ) {
        return false;
    }

    return true;
}


if (
    !isValidBookingTime($pickup_time) ||
    !isValidBookingTime($return_time)
) {
    bookingError(
        "Pickup and return times must be between 6:00 AM and 10:00 PM in 30-minute intervals."
    );
}


/* Validate booking date and time */
$pickup_datetime =
    strtotime(
        $pickup_date . ' ' . $pickup_time
    );

$return_datetime =
    strtotime(
        $return_date . ' ' . $return_time
    );

if (
    $pickup_datetime === false ||
    $return_datetime === false
) {
    bookingError(
        "Please enter valid pickup and return dates."
    );
}

$current_datetime = time();

if ($pickup_datetime < $current_datetime) {
    bookingError(
        "Pickup date and time cannot be in the past."
    );
}

if ($return_datetime <= $pickup_datetime) {
    bookingError(
        "Return date and time must be after the pickup date and time."
    );
}


/* DRIVER'S LICENSE UPLOAD */
if (
    !isset($_FILES['driver_license']) ||
    $_FILES['driver_license']['error'] !== UPLOAD_ERR_OK
) {
    bookingError(
        "Please upload a picture of your driver's license."
    );
}

$file_tmp =
    $_FILES['driver_license']['tmp_name'];

$file_size =
    (int) $_FILES['driver_license']['size'];


/* Maximum 5MB */
if ($file_size > 5 * 1024 * 1024) {
    bookingError(
        "Driver's license image must be 5MB or smaller."
    );
}


/* Check if uploaded file is really an image */
$image_info = getimagesize($file_tmp);

if ($image_info === false) {
    bookingError(
        "Please upload a valid driver's license image."
    );
}


/* Only allow JPG and PNG */
$allowed_types = [
    IMAGETYPE_JPEG => 'jpg',
    IMAGETYPE_PNG => 'png'
];

if (!isset($allowed_types[$image_info[2]])) {
    bookingError(
        "Only JPG and PNG images are allowed."
    );
}


/* Create uploads folder if needed */
$upload_dir =
    __DIR__ . '/uploads/licenses/';

if (!is_dir($upload_dir)) {
    if (
        !mkdir(
            $upload_dir,
            0755,
            true
        )
    ) {
        bookingError(
            "Unable to prepare the license upload folder."
        );
    }
}


/* Generate safe unique filename */
$extension =
    $allowed_types[$image_info[2]];

$driver_license =
    'license_' .
    bin2hex(random_bytes(8)) .
    '.' .
    $extension;

$license_path =
    $upload_dir .
    $driver_license;


/* Upload the file */
if (
    !move_uploaded_file(
        $file_tmp,
        $license_path
    )
) {
    bookingError(
        "Failed to upload driver's license. Please try again."
    );
}


/* PAYMENT PROOF UPLOAD */
if (
    !isset($_FILES['payment_proof']) ||
    $_FILES['payment_proof']['error'] !== UPLOAD_ERR_OK
) {
    if (file_exists($license_path)) {
        unlink($license_path);
    }

    bookingError(
        "Please upload a screenshot of your payment."
    );
}

$payment_tmp =
    $_FILES['payment_proof']['tmp_name'];

$payment_size =
    (int) $_FILES['payment_proof']['size'];

/* Maximum 5MB */
if ($payment_size > 5 * 1024 * 1024) {
    if (file_exists($license_path)) {
        unlink($license_path);
    }

    bookingError(
        "Payment proof image must be 5MB or smaller."
    );
}

/* Check if uploaded payment proof is really an image */
$payment_image_info = getimagesize($payment_tmp);

if ($payment_image_info === false) {
    if (file_exists($license_path)) {
        unlink($license_path);
    }

    bookingError(
        "Please upload a valid payment proof image."
    );
}

/* Only allow JPG and PNG */
if (!isset($allowed_types[$payment_image_info[2]])) {
    if (file_exists($license_path)) {
        unlink($license_path);
    }

    bookingError(
        "Payment proof must be a JPG or PNG image."
    );
}

/* Create payment upload folder if needed */
$payment_upload_dir =
    __DIR__ . '/uploads/payments/';

if (!is_dir($payment_upload_dir)) {
    if (
        !mkdir(
            $payment_upload_dir,
            0755,
            true
        )
    ) {
        if (file_exists($license_path)) {
            unlink($license_path);
        }

        bookingError(
            "Unable to prepare the payment upload folder."
        );
    }
}

/* Generate safe unique payment proof filename */
$payment_extension =
    $allowed_types[$payment_image_info[2]];

$payment_proof =
    'payment_' .
    bin2hex(random_bytes(8)) .
    '.' .
    $payment_extension;

$payment_path =
    $payment_upload_dir .
    $payment_proof;

/* Upload payment proof */
if (
    !move_uploaded_file(
        $payment_tmp,
        $payment_path
    )
) {
    if (file_exists($license_path)) {
        unlink($license_path);
    }

    bookingError(
        "Failed to upload payment proof. Please try again."
    );
}


/*
    BOOKING + INVENTORY TRANSACTION

    The motorcycle price and availability are read
    directly from motorcycle_inventory.

    This means motorcycles added by the owner can
    automatically be booked without editing book.php.
*/
try {

    if (!$conn->begin_transaction()) {
        throw new RuntimeException(
            "Unable to start database transaction."
        );
    }


    /*
        Lock the selected motorcycle row while the
        booking is being processed.
    */
    $motorcycle_stmt =
        $conn->prepare("
            SELECT
                price_per_day,
                available_units
            FROM motorcycle_inventory
            WHERE motorcycle_name = ?
              AND is_active = 1
            LIMIT 1
            FOR UPDATE
        ");

    if (!$motorcycle_stmt) {
        throw new RuntimeException(
            "Unable to prepare motorcycle lookup."
        );
    }

    $motorcycle_stmt->bind_param(
        "s",
        $motorcycle_name
    );

    if (!$motorcycle_stmt->execute()) {
        $motorcycle_stmt->close();

        throw new RuntimeException(
            "Unable to check motorcycle."
        );
    }

    $motorcycle_result =
        $motorcycle_stmt->get_result();

    $motorcycle =
        $motorcycle_result->fetch_assoc();

    $motorcycle_stmt->close();


    /* Motorcycle must exist and still be active */
    if (!$motorcycle) {
        throw new RuntimeException(
            "INVALID_MOTORCYCLE"
        );
    }


    /* Get the trusted price directly from the database */
    $price_per_day =
        (float) $motorcycle['price_per_day'];


    /* Check availability */
    if ((int) $motorcycle['available_units'] < 1) {
        throw new RuntimeException(
            "MOTORCYCLE_UNAVAILABLE"
        );
    }


    /* Reserve one motorcycle */
    $inventory_stmt =
        $conn->prepare("
            UPDATE motorcycle_inventory
            SET available_units =
                available_units - 1
            WHERE motorcycle_name = ?
              AND is_active = 1
              AND available_units > 0
        ");

    if (!$inventory_stmt) {
        throw new RuntimeException(
            "Unable to prepare inventory update."
        );
    }

    $inventory_stmt->bind_param(
        "s",
        $motorcycle_name
    );

    if (!$inventory_stmt->execute()) {
        $inventory_stmt->close();

        throw new RuntimeException(
            "Unable to update inventory."
        );
    }

    if ($inventory_stmt->affected_rows !== 1) {
        $inventory_stmt->close();

        throw new RuntimeException(
            "MOTORCYCLE_UNAVAILABLE"
        );
    }

    $inventory_stmt->close();


    /* Insert booking */
    $stmt =
        $conn->prepare("
            INSERT INTO bookings
            (
                user_id,
                driver_license,
                motorcycle_name,
                price_per_day,
                pickup_date,
                pickup_time,
                return_date,
                return_time,
                payment_method,
                payment_proof,
                pickup_branch,
                dropoff_branch
            )
            VALUES
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

    if (!$stmt) {
        throw new RuntimeException(
            "Unable to prepare booking."
        );
    }

    $stmt->bind_param(
        "issdssssssss",
        $user_id,
        $driver_license,
        $motorcycle_name,
        $price_per_day,
        $pickup_date,
        $pickup_time,
        $return_date,
        $return_time,
        $payment_method,
        $payment_proof,
        $pickup_branch,
        $dropoff_branch
    );

    if (!$stmt->execute()) {
        $stmt->close();

        throw new RuntimeException(
            "Unable to save booking."
        );
    }

    $stmt->close();


    /* Everything succeeded */
    if (!$conn->commit()) {
        throw new RuntimeException(
            "Unable to complete booking."
        );
    }

    $conn->close();

    header(
        "Location: index.php?booking=success"
    );
    exit;


} catch (Throwable $e) {

    /* Undo database changes */
    try {
        $conn->rollback();
    } catch (Throwable $rollback_error) {
        error_log(
            "Booking rollback error: " .
            $rollback_error->getMessage()
        );
    }


    /* Remove uploaded files if booking failed */
    if (file_exists($license_path)) {
        unlink($license_path);
    }

    if (isset($payment_path) && file_exists($payment_path)) {
        unlink($payment_path);
    }


    error_log(
        "Booking error: " .
        $e->getMessage()
    );


    $error_message = $e->getMessage();

    $conn->close();


    if ($error_message === 'INVALID_MOTORCYCLE') {
        bookingError(
            "Invalid motorcycle selected."
        );
    }

    if ($error_message === 'MOTORCYCLE_UNAVAILABLE') {
        bookingError(
            "Sorry, this motorcycle is currently unavailable."
        );
    }

    bookingError(
        "Booking failed. Please try again."
    );
}

?>
