<?php

require 'database/config.php';
require 'validation.php';


/* Only process registration submissions */
if (!isset($_POST['register'])) {
    header('Location: register.php');
    exit;
}


/* Validate submitted registration data */
$result = validateRegisterInput($_POST);
$errors = $result['errors'];


/* Return validation errors to the registration page */
if (!empty($errors)) {

    $message = implode(' ', $errors);

    header(
        'Location: register.php?status=error&message=' .
        urlencode($message)
    );

    exit;
}


try {

    $pdo = getConnection();


    /* Check if the email is already registered */
    $check = $pdo->prepare(
        "SELECT id
         FROM users
         WHERE email = :email
         LIMIT 1"
    );

    $check->bindValue(
        ':email',
        $result['data']['email'],
        PDO::PARAM_STR
    );

    $check->execute();


    if ($check->fetch()) {

        header(
            'Location: register.php?status=error&message=' .
            urlencode(
                'An account with this email already exists.'
            )
        );

        exit;
    }


    /* Hash password securely */
    $hashedPassword = password_hash(
        $result['data']['password'],
        PASSWORD_DEFAULT
    );


    /* Insert new customer account */
    $sql = "
        INSERT INTO users
            (full_name, email, phone, password)
        VALUES
            (:full_name, :email, :phone, :password)
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->bindValue(
        ':full_name',
        $result['data']['full_name'],
        PDO::PARAM_STR
    );

    $stmt->bindValue(
        ':email',
        $result['data']['email'],
        PDO::PARAM_STR
    );

    $stmt->bindValue(
        ':phone',
        $result['data']['phone'],
        PDO::PARAM_STR
    );

    $stmt->bindValue(
        ':password',
        $hashedPassword,
        PDO::PARAM_STR
    );

    $stmt->execute();


    /* Get newly created account ID */
    $newId = $pdo->lastInsertId();


    /* Redirect to success page */
    header(
        'Location: success.php?id=' .
        urlencode($newId)
    );

    exit;


} catch (PDOException $e) {

    /* Record technical details privately */
    error_log(
        'Registration database error: ' .
        $e->getMessage()
    );


    /* Show only a safe message to the user */
    header(
        'Location: register.php?status=error&message=' .
        urlencode(
            'Registration failed. Please try again.'
        )
    );

    exit;
}

?>