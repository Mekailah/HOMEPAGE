<?php

function getConnection(): PDO
{
    $host = 'localhost';
    $db = 'ridego_rentals';
    $user = 'root';
    $pass = '';

    try {

        $pdo = new PDO(
            "mysql:host=$host;dbname=$db;charset=utf8mb4",
            $user,
            $pass,
            [
                PDO::ATTR_ERRMODE =>
                    PDO::ERRMODE_EXCEPTION,

                PDO::ATTR_DEFAULT_FETCH_MODE =>
                    PDO::FETCH_ASSOC,

                PDO::ATTR_EMULATE_PREPARES =>
                    false
            ]
        );

        return $pdo;

    } catch (PDOException $e) {

        error_log(
            "PDO database connection error: " .
            $e->getMessage()
        );

        die(
            "We're having trouble connecting to the database. " .
            "Please try again later."
        );
    }
}