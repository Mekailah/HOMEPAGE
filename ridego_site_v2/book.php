<?php

session_start();
require_once 'db.php';


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

    $motorcycle_prices = [
    'Honda Click 125 cc' => 629.00,
    'Yamaha Fazzio 125cc' => 819.00,
    'Yamaha AEROX 155 CC' => 799.00,
    'Honda Beat 110' => 449.00,
    'Honda ADV 160' => 899.00,
    'Yamaha PG-1' => 699.00,
    'Honda NAVi' => 549.00,
    'Yamaha NMAX ABS' => 899.00,
    'Honda XRM 125' => 549.00,
    'Yamaha Vino Classic' => 599.00,
    'Kawasaki Ninja 1000SX' => 1499.00,
    'Yamaha Sniper 155' => 799.00
];


if (!isset($motorcycle_prices[$motorcycle_name])) {
    bookingError(
        "Invalid motorcycle selected."
    );
}


$price_per_day =
    $motorcycle_prices[$motorcycle_name];

$pickup_date = trim($_POST['pickup_date'] ?? '');

$pickup_time = trim($_POST['pickup_time'] ?? '');

$return_date = trim($_POST['return_date'] ?? '');

$return_time = trim($_POST['return_time'] ?? '');

$payment_method = trim($_POST['payment_method'] ?? '');

$pickup_branch = trim($_POST['pickup_branch'] ?? '');

$dropoff_branch = trim($_POST['dropoff_branch'] ?? '');


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

$price_per_day =
    $motorcycle_prices[$motorcycle_name];


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
$image_info = @getimagesize($file_tmp);

if ($image_info === false) {

    bookingError(
        "Please upload a valid driver's license image."
    );
}


/* Only allow JPG and PNG */
$allowed_types = [
    'image/jpeg',
    'image/png'
];

if (
    !in_array(
        $image_info['mime'],
        $allowed_types,
        true
    )
) {
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
    $image_info['mime'] === 'image/png'
        ? 'png'
        : 'jpg';

$driver_license =
    uniqid(
        'license_',
        true
    ) .
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


/*
    BOOKING + INVENTORY TRANSACTION

    Both operations must succeed together.
*/

try {

    $conn->begin_transaction();


    /*
        Reserve one motorcycle.

        available_units > 0 prevents the
        value from becoming negative.
    */

    $inventory_stmt =
        $conn->prepare("
            UPDATE motorcycle_inventory
            SET available_units =
                available_units - 1
            WHERE motorcycle_name = ?
              AND available_units > 0
        ");

    $inventory_stmt->bind_param(
        "s",
        $motorcycle_name
    );

    $inventory_stmt->execute();


    /*
        If no row was changed,
        the motorcycle either does not exist
        or has no available units.
    */

    if ($inventory_stmt->affected_rows !== 1) {

        $inventory_stmt->close();

        $conn->rollback();


        /* Remove uploaded license because booking failed */
        if (file_exists($license_path)) {
            unlink($license_path);
        }


        bookingError(
            "Sorry, this motorcycle is currently unavailable."
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
                pickup_branch,
                dropoff_branch
            )
            VALUES
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");


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


    $stmt->execute();

    $stmt->close();


    /* Everything succeeded */
    $conn->commit();

    $conn->close();


    header(
        "Location: index.php?booking=success"
    );

    exit;


} catch (Throwable $e) {

    /* Undo database changes */
    $conn->rollback();


    /* Remove uploaded license if booking failed */
    if (file_exists($license_path)) {
        unlink($license_path);
    }


    $conn->close();


    bookingError(
        "Booking failed. Please try again."
    );
}

?>