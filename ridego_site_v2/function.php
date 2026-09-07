<?php

require 'database/config.php';
require 'validation.php';

if (!isset($_POST['register'])) {
    header('Location: register.php');
    exit;
}

$result = validateRegisterInput($_POST);
$errors = $result['errors'];

if (!empty($errors)) {
    $message = implode(' ', $errors);
    header('Location: register.php?status=error&message=' . urlencode($message));
    exit;
}

try {
    $pdo = getConnection();

    $sql = "INSERT INTO users (full_name, email, phone, password)
            VALUES (:full_name, :email, :phone, :password)";

    $stmt = $pdo->prepare($sql);

    $stmt->bindValue(':full_name', $result['data']['full_name']);
    $stmt->bindValue(':email', $result['data']['email']);
    $stmt->bindValue(':phone', $result['data']['phone']);
    $stmt->bindValue(
        ':password',
        password_hash($result['data']['password'], PASSWORD_DEFAULT)
    );

    $stmt->execute();

    $newId = $pdo->lastInsertId();

    header('Location: success.php?id=' . $newId);
    exit;

} catch (PDOException $e) {
    header(
        'Location: register.php?status=error&message=' .
        urlencode($e->getMessage())
    );
    exit;
}