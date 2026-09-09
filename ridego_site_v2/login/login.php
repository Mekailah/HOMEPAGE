<?php
session_start();
require_once 'login_function.php';

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $message = 'Please enter your email and password.';
        $message_type = 'error';
    } else {

        $stmt = $conn->prepare("SELECT id, full_name, email, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['email'] = $user['email'];

                header("Location: index.php");
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | RIDEGO RENTALS</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --dark: #0E1B29;
            --teal: #3C8D8A;
            --white: #FFFFFF;
            --bg: rgba(14, 27, 41, 0.20);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
            background: var(--bg);
            color: var(--white);

            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px;
        }

        .login-container {
            width: 100%;
            max-width: 450px;
        }

        .logo {
            text-align: center;
            margin-bottom: 25px;
        }

        .logo img {
            width: 180px;
            max-width: 100%;
        }

        .login-card {
            background: var(--white);
            color: var(--dark);
            padding: 40px;
            border-radius: 28px;
        }

        .login-card h1 {
            margin: 0 0 8px;
            text-align: center;
            font-size: 30px;
            font-weight: 700;
        }

        .login-card > p {
            margin: 0 0 30px;
            text-align: center;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-size: 13px;
            font-weight: 700;
        }

        input {
            width: 100%;
            padding: 13px 15px;
            border: 2px solid var(--dark);
            border-radius: 10px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            outline: none;
        }

        input:focus {
            border-color: var(--teal);
        }

        .login-button {
            width: 100%;
            margin-top: 8px;
            padding: 14px;
            border: none;
            border-radius: 10px;
            background: var(--teal);
            color: var(--white);
            font-family: 'Poppins', sans-serif;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
        }

        .login-button:hover {
            opacity: 0.9;
        }

        .message {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 10px;
            text-align: center;
            font-size: 13px;
        }

        .message.error {
            background: #f3f3f3;
        }

        .register-link {
            margin-top: 22px;
            text-align: center;
            font-size: 13px;
        }

        .register-link a {
            color: var(--teal);
            font-weight: 700;
            text-decoration: none;
        }

        .back-home {
            display: block;
            margin-top: 20px;
            text-align: center;
            color: var(--white);
            text-decoration: none;
            font-size: 13px;
        }

        .back-home:hover {
            color: var(--teal);
        }

        @media (max-width: 500px) {
            body {
                padding: 20px;
            }

            .login-card {
                padding: 30px 22px;
            }
        }
    </style>
</head>

<body>

<div class="login-container">

    <div class="logo">
        <a href="../index.php">
            <img src="../assets/logo.svg" alt="RIDEGO RENTALS">
        </a>
    </div>

    <div class="login-card">

        <h1>LOGIN</h1>

        <p>Welcome back to RIDEGO RENTALS.</p>

        <?php if ($message): ?>
            <div class="message <?= htmlspecialchars($message_type) ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">

            <div class="form-group">
                <label for="email">EMAIL</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    required
                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                >
            </div>

            <div class="form-group">
                <label for="password">PASSWORD</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                >
            </div>

            <button type="submit" class="login-button">
                LOGIN
            </button>

        </form>

        <div class="register-link">
            Don't have an account?
            <a href="../register.php">REGISTER</a>
        </div>

    </div>

    <a href="index.php" class="back-home">
        ← BACK TO HOME
    </a>

</div>

</body>
</html>