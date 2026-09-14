<?php

$id = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

if (!$id) {
    header('Location: index.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Registration Successful | RIDEGO RENTALS
    </title>

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 30px 20px;

            font-family: 'Poppins';

            background:
                linear-gradient(
                    135deg,
                    rgba(60, 141, 138, 0.15)
                );

            color: #0E1B29;
        }


        .success-card {
            width: 100%;
            max-width: 520px;

            background: #FFFFFF;

            border-radius: 24px;

            padding: 42px 38px;

            box-shadow:
                0 20px 60px
                rgba(0, 0, 0, 0.25);

            text-align: center;
        }


        .brand {
            margin-bottom: 25px;

            font-size: 14px;
            font-weight: 700;

            letter-spacing: 2px;

            color: #3C8D8A;
        }


        .success-icon {
            width: 72px;
            height: 72px;

            margin: 0 auto 20px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 50%;

            background: #E7F5F3;

            color: #3C8D8A;

            font-size: 38px;
            font-weight: 700;
        }


        h1 {
            margin: 0 0 12px;

            font-size: 30px;

            color: #0E1B29;
        }


        .subtitle {
            margin: 0 0 30px;

            font-size: 14px;
            line-height: 1.6;

            color: #0E1B29;
        }


        .login-button {
            display: block;

            width: 100%;

            padding: 14px 18px;

            margin-bottom: 14px;

            border-radius: 10px;

            background: #3C8D8A;

            color: #FFFFFF;

            text-decoration: none;

            font-size: 13px;
            font-weight: 700;

            transition:
                opacity 0.2s ease,
                transform 0.2s ease;
        }


        .login-button:hover {
            opacity: 0.92;
            transform: translateY(-1px);
        }


        .home-link {
            display: inline-block;

            color: #0E1B29;

            font-size: 12px;
            font-weight: 600;

            text-decoration: none;
        }


        .home-link:hover {
            color: #3C8D8A;
        }


        @media (max-width: 520px) {

            .success-card {
                padding: 32px 22px;
            }

            h1 {
                font-size: 24px;
            }

        }

    </style>

</head>


<body>

    <main class="success-card">

        <div class="success-icon">
            ✓
        </div>


        <h1>
            Registration Successful!
        </h1>


        <p class="subtitle">
            Your RIDEGO RENTALS account has been created successfully.
        </p>


        <a
            href="login.php"
            class="login-button"
        >
            LOGIN TO YOUR ACCOUNT
        </a>


        <a
            href="index.php"
            class="home-link"
        >
            BACK TO HOME
        </a>

    </main>

</body>

</html>