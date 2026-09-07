<?php

$status  = $_GET['status'] ?? null;
$message = $_GET['message'] ?? null;
$id      = $_GET['id'] ?? null;

require 'database/config.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: index.php');
    exit;
}

$pdo = getConnection();

$sql = "SELECT id, full_name, email, phone FROM users WHERE id = :id";

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
    <title>RIDEGO Registration Successful</title>
</head>

<body>

    <h1 style="color: green;">Registration successful!</h1>

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
        <a href="index.php">Back to RIDEGO</a>
    </p>

</body>

</html>