<?php

$host = "localhost";
$username = "root";
$password = "";
$database = "ridego_rentals";

/*
 * Prevent MySQLi from displaying technical
 * database errors directly to users.
 */
mysqli_report(MYSQLI_REPORT_OFF);

$conn = new mysqli(
    $host,
    $username,
    $password,
    $database
);

if ($conn->connect_error) {

    error_log(
        "Database connection error: " .
        $conn->connect_error
    );

    die(
        "We're having trouble connecting to the database. " .
        "Please try again later."
    );
}

if (!$conn->set_charset("utf8mb4")) {

    error_log(
        "Database charset error: " .
        $conn->error
    );

    die(
        "Something went wrong while preparing the database connection."
    );
}

?>