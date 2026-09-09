<?php
require_once 'db.php';

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check phone number
    if (!preg_match('/^[0-9]{11}$/', $phone)) {

        $message = 'Phone number must be exactly 11 digits.';
        $message_type = 'error';

    // Check password
    // Check password requirements
} elseif (
    strlen($password) < 8 ||
    !preg_match('/[A-Z]/', $password) ||
    !preg_match('/[a-z]/', $password) ||
    !preg_match('/[0-9]/', $password) ||
    !preg_match('/[^A-Za-z0-9]/', $password)
) {

    $message = 'Password must be at least 8 characters and contain at least 1 uppercase letter, 1 lowercase letter, 1 number, and 1 symbol.';
    $message_type = 'error';

// Check if passwords match
} elseif ($password !== $confirm_password) {

    $message = 'Passwords do not match.';
    $message_type = 'error';

} else {

        // Check if email already exists
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {

            $message = 'An account with this email already exists.';
            $message_type = 'error';

        } else {

            // Securely hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare(
                "INSERT INTO users (full_name, email, phone, password)
                 VALUES (?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "ssss",
                $full_name,
                $email,
                $phone,
                $hashed_password
            );

            if ($stmt->execute()) {
                $message = 'Registration successful! You can now log in.';
                $message_type = 'success';
            } else {
                $message = 'Something went wrong. Please try again.';
                $message_type = 'error';
            }

            $stmt->close();
        }

        $check->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Register | RIDEGO RENTALS</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="assets/style.css">

    <style>
        .auth-page {
            min-height: calc(100vh - 78px);
            background: rgba(14, 27, 41, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .auth-card {
            width: min(450px, 100%);
            background: #FFFFFF;
            padding: 35px;
            border-radius: 28px;
            box-shadow: 0 15px 40px rgba(14, 27, 41, 0.15);
        }

        .auth-card h1 {
            margin: 0 0 8px;
            color: #0E1B29;
            font-size: 30px;
            font-weight: 700;
            text-align: center;
        }

        .auth-card > p {
            margin: 0 0 25px;
            text-align: center;
            font-size: 14px;
        }

        .auth-form {
            display: grid;
            gap: 15px;
        }

        .auth-form label {
            font-size: 12px;
            font-weight: 700;
            color: #0E1B29;
        }

        .auth-form input {
            display: block;
            width: 100%;
            margin-top: 6px;
            padding: 12px;
            border: 1px solid rgba(14, 27, 41, 0.25);
            border-radius: 12px;
            color: #0E1B29;
            outline: none;
        }

        .auth-form input:focus {
            border-color: #3C8D8A;
        }

        .auth-form button {
            width: 100%;
            margin-top: 5px;
            padding: 13px;
        }

        .auth-message {
            margin-bottom: 18px;
            padding: 11px 13px;
            border-radius: 10px;
            font-size: 13px;
            text-align: center;
        }

        .auth-message.error {
            background: rgba(14, 27, 41, 0.10);
            color: #0E1B29;
        }

        .auth-message.success {
            background: rgba(60, 141, 138, 0.15);
            color: #0E1B29;
        }

        .auth-link {
            margin: 20px 0 0;
            text-align: center;
            font-size: 13px;
        }

        .auth-link a {
            color: #3C8D8A;
            font-weight: 700;
        }
    </style>
</head>

<body>

<header class="site-header">

    <a href="index.php#home" class="site-logo">
        <img src="assets/logo.svg" alt="RIDEGO RENTALS">
    </a>

    <nav class="main-nav">
        <a href="index.php#home">HOME</a>
        <a href="index.php#about">ABOUT US</a>
        <a href="motorcycles.php">MOTORCYCLES</a>
        <a href="index.php#how-it-works">HOW IT WORKS</a>
        <a href="index.php#faq">FAQ</a>
        <a href="login.php" class="nav-login">LOGIN</a>
        <a href="register.php" class="nav-register">REGISTER</a>
    </nav>

</header>

<main class="auth-page">

    <div class="auth-card">

        <h1>REGISTER</h1>

        <p>Create your RIDEGO RENTALS account.</p>

        <?php if ($message): ?>
            <div class="auth-message <?= $message_type ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form class="auth-form" method="POST">

            <label>
                FULL NAME
                <input
                    type="text"
                    name="full_name"
                    required
                >
            </label>

            <label>
                EMAIL
                <input
                    type="email"
                    name="email"
                    required
                >
            </label>

            <label>
                PHONE NUMBER
                <input
                type="tel"
                name="phone"
                required
                maxlength="11"
                pattern="[0-9]{11}"
                inputmode="numeric"
                >
            </label>

            <label>
                PASSWORD
                <input
                type="password"
                name="password"
                required
                minlength="8"
                pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{8,}"
                title="Password must be at least 8 characters and contain at least 1 uppercase letter, 1 lowercase letter, 1 number, and 1 symbol."
                >
            </label>

            <label>
                CONFIRM PASSWORD
                <input
                    type="password"
                    name="confirm_password"
                    required
                >
            </label>

            <button type="submit" class="button button-hero">
                REGISTER
            </button>

        </form>

        <p class="auth-link">
            Already have an account?
            <a href="login.php">LOGIN</a>
        </p>

    </div>

</main>

</body>
</html>