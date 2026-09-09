<?php

session_start();
require_once '../db.php';

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {

        $message = 'Please enter your email and password.';
        $message_type = 'error';

    } else {

        $stmt = $conn->prepare(
            "SELECT id, full_name, email, password
             FROM users
             WHERE email = ?"
        );

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['email'] = $user['email'];

                header("Location: ../index.php");
                exit;

            } else {

                $message = 'Incorrect email or password.';
                $message_type = 'error';
            }

        } else {

            $message = 'Incorrect email or password.';
            $message_type = 'error';
        }

        $stmt->close();
    }
}
?>