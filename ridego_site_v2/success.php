<?php

require 'database/config.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: index.php');
    exit;
}

$pdo = getConnection();

$sql = "SELECT id, full_name, email, phone
        FROM users
        WHERE id = :id";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: index.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Registration Successful | RIDEGO RENTALS</title>
</head>

<body>

    <h1>Registration Successful!</h1>

    <p>Your RIDEGO RENTALS account has been created.</p>

    <table border="1" cellpadding="8" cellspacing="0">

        <tr>
            <th>ID</th>
            <td><?= htmlspecialchars($user['id']) ?></td>
        </tr>

        <tr>
            <th>Full Name</th>
            <td><?= htmlspecialchars($user['full_name']) ?></td>
        </tr>

        <tr>
            <th>Email</th>
            <td><?= htmlspecialchars($user['email']) ?></td>
        </tr>

        <tr>
            <th>Phone</th>
            <td><?= htmlspecialchars($user['phone']) ?></td>
        </tr>

    </table>

    <p>
        <a href="login.php">LOGIN TO YOUR ACCOUNT</a>
    </p>

    <p>
        <a href="index.php">BACK TO HOME</a>
    </p>

</body>
</html>