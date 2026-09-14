<?php
session_start();
require_once 'db.php';

$status_updated =
    isset($_GET['status']) &&
    $_GET['status'] === 'updated';

/* User must be logged in */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

/* Only the owner/admin can access this page */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

/* Get dashboard totals */
$total_users_result = $conn->query("
    SELECT COUNT(*) AS total
    FROM users
    WHERE role = 'customer'
");
$total_users = $total_users_result->fetch_assoc()['total'];

$total_bookings_result = $conn->query("
    SELECT COUNT(*) AS total
    FROM bookings
");
$total_bookings = $total_bookings_result->fetch_assoc()['total'];

$total_available_result = $conn->query("
    SELECT SUM(available_units) AS total
    FROM motorcycle_inventory
");
$total_available = $total_available_result->fetch_assoc()['total'] ?? 0;

/* Get bookings */
$bookings = $conn->query("
    SELECT
        bookings.id,
        users.full_name,
        users.email,
        bookings.motorcycle_name,
        bookings.pickup_date,
        bookings.pickup_time,
        bookings.return_date,
        bookings.return_time,
        bookings.payment_method,
        bookings.pickup_branch,
        bookings.dropoff_branch,
        bookings.status
    FROM bookings
    INNER JOIN users
        ON bookings.user_id = users.id
    ORDER BY bookings.id DESC
");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Owner Dashboard | RIDEGO RENTALS</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;700&display=swap"
        rel="stylesheet"
    >

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: #f4f6f7;
            color: #0E1B29;
        }

        .admin-header {
            background: #0E1B29;
            color: #FFFFFF;
            padding: 18px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .admin-header h1 {
            margin: 0;
            font-size: 20px;
        }

        .admin-header a {
            color: #FFFFFF;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
        }

        .dashboard {
            width: 90%;
            max-width: 1200px;
            margin: 35px auto;
        }

        .welcome {
            margin-bottom: 25px;
        }

        .welcome h2 {
            margin-bottom: 5px;
        }

        .welcome p {
            margin-top: 0;
            font-size: 14px;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 35px;
        }

        .stat-card {
            background: #FFFFFF;
            padding: 25px;
            border-radius: 18px;
        }

        .stat-card p {
            margin: 0;
            font-size: 13px;
        }

        .stat-card h3 {
            margin: 8px 0 0;
            font-size: 30px;
            color: #3C8D8A;
        }

        .bookings-section {
            background: #FFFFFF;
            padding: 25px;
            border-radius: 18px;
            overflow-x: auto;
        }

        .bookings-section h2 {
            margin-top: 0;
            font-size: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #dddddd;
            white-space: nowrap;
        }

        th {
            background: #0E1B29;
            color: #FFFFFF;
        }

        .status {
            color: #3C8D8A;
            font-weight: 700;
        }

        .status form {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .status select {
            padding: 7px 10px;
            border: 1px solid #cccccc;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 12px;
        }

        .status button {
            padding: 7px 12px;
            border: none;
            border-radius: 8px;
            background: #3C8D8A;
            color: #FFFFFF;
            font-family: 'Poppins', sans-serif;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
        }

        .status button:hover {
            opacity: 0.9;
        }

        @media (max-width: 700px) {
            .stats {
                grid-template-columns: 1fr;
            }

            .admin-header {
                padding: 15px 20px;
            }

            .dashboard {
                width: 92%;
            }
        }
    </style>
</head>

<body>

<header class="admin-header">

    <h1>RIDEGO OWNER DASHBOARD</h1>

    <a href="logout.php">LOGOUT</a>

</header>

<main class="dashboard">

        <?php if ($status_updated): ?>

            <div
                id="statusMessage"
                style="
                    background:#3C8D8A;
                    color:#FFFFFF;
                    padding:12px 16px;
                    border-radius:10px;
                    margin-bottom:20px;
                    text-align:center;
                    font-size:13px;
                    font-weight:700;
                "
            >
                Booking status updated successfully!
            </div>

            <script>
                setTimeout(function () {
                    const message = document.getElementById('statusMessage');

                    if (message) {
                        message.style.display = 'none';
                    }
                }, 3000);

                if (window.history.replaceState) {
                    window.history.replaceState(null, '', 'admin.php');
                }
            </script>

        <?php endif; ?>

    <div class="welcome">
        <h2>
            Welcome,
            <?= htmlspecialchars($_SESSION['full_name']) ?>
        </h2>

        <p>Manage and monitor RIDEGO RENTALS.</p>
    </div>

    <div class="stats">

        <div class="stat-card">
            <p>TOTAL CUSTOMERS</p>
            <h3><?= $total_users ?></h3>
        </div>

        <div class="stat-card">
            <p>TOTAL BOOKINGS</p>
            <h3><?= $total_bookings ?></h3>
        </div>

        <div class="stat-card">
            <p>AVAILABLE MOTORCYCLES</p>
            <h3><?= $total_available ?></h3>
        </div>

    </div>

    <section class="bookings-section">

        <h2>BOOKINGS</h2>

        <table>

            <thead>
                <tr>
                    <th>ID</th>
                    <th>CUSTOMER</th>
                    <th>EMAIL</th>
                    <th>MOTORCYCLE</th>
                    <th>PICK-UP</th>
                    <th>RETURN</th>
                    <th>PAYMENT</th>
                    <th>PICK-UP LOCATION</th>
                    <th>DROP-OFF LOCATION</th>
                    <th>STATUS</th>
                </tr>
            </thead>

            <tbody>

                <?php if ($bookings && $bookings->num_rows > 0): ?>

                    <?php while ($booking = $bookings->fetch_assoc()): ?>

                        <tr>
                            <td>
                                <?= htmlspecialchars($booking['id']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($booking['full_name']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($booking['email']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($booking['motorcycle_name']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($booking['pickup_date']) ?>
                                <?= htmlspecialchars($booking['pickup_time']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($booking['return_date']) ?>
                                <?= htmlspecialchars($booking['return_time']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($booking['payment_method']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($booking['pickup_branch']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($booking['dropoff_branch']) ?>
                            </td>

                            <td class="status">
                                <form method="POST" action="update_status.php">

                                    <input
                                        type="hidden"
                                        name="booking_id"
                                        value="<?= htmlspecialchars($booking['id']) ?>"
                                    >

                                    <select name="status">
                                        <option value="Pending"
                                            <?= $booking['status'] === 'Pending' ? 'selected' : '' ?>>
                                            Pending
                                        </option>

                                        <option value="Confirmed"
                                            <?= $booking['status'] === 'Confirmed' ? 'selected' : '' ?>>
                                            Confirmed
                                        </option>

                                        <option value="Completed"
                                            <?= $booking['status'] === 'Completed' ? 'selected' : '' ?>>
                                            Completed
                                        </option>

                                        <option value="Cancelled"
                                            <?= $booking['status'] === 'Cancelled' ? 'selected' : '' ?>>
                                            Cancelled
                                        </option>
                                    </select>

                                    <button type="submit">
                                        SAVE
                                    </button>

                                </form>
                            </td>
                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="10">
                            No bookings found.
                        </td>
                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </section>

</main>

</body>
</html>