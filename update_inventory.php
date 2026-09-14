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


/* Validate inventory ID */
$inventory_id = filter_input(
    INPUT_POST,
    'inventory_id',
    FILTER_VALIDATE_INT,
    [
        'options' => [
            'min_range' => 1
        ]
    ]
);


/* Validate total units */
$total_units = filter_input(
    INPUT_POST,
    'total_units',
    FILTER_VALIDATE_INT,
    [
        'options' => [
            'min_range' => 0
        ]
    ]
);


/* Validate available units */
$available_units = filter_input(
    INPUT_POST,
    'available_units',
    FILTER_VALIDATE_INT,
    [
        'options' => [
            'min_range' => 0
        ]
    ]
);


/* Stop if any submitted value is invalid */
if (
    $inventory_id === false ||
    $inventory_id === null ||
    $total_units === false ||
    $total_units === null ||
    $available_units === false ||
    $available_units === null
) {
    header("Location: admin.php?inventory=error");
    exit;
}


/* Available units cannot exceed total units */
if ($available_units > $total_units) {
    header("Location: admin.php?inventory=error");
    exit;
}


/* Prepare inventory update */
$stmt = $conn->prepare("
    UPDATE motorcycle_inventory
    SET total_units = ?, available_units = ?
    WHERE id = ?
");


/* Handle database preparation errors safely */
if (!$stmt) {

    error_log(
        "Inventory update prepare error: " .
        $conn->error
    );

    $conn->close();

    header("Location: admin.php?inventory=error");
    exit;
}


/* Bind values */
if (!$stmt->bind_param(
    "iii",
    $total_units,
    $available_units,
    $inventory_id
)) {

    error_log(
        "Inventory update bind error: " .
        $stmt->error
    );

    $stmt->close();
    $conn->close();

    header("Location: admin.php?inventory=error");
    exit;
}


/* Execute update */
if (!$stmt->execute()) {

    error_log(
        "Inventory update execute error: " .
        $stmt->error
    );

    $stmt->close();
    $conn->close();

    header("Location: admin.php?inventory=error");
    exit;
}


$stmt->close();
$conn->close();


/* Return to dashboard */
header("Location: admin.php?inventory=updated");
exit;

?>