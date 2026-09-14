<?php
session_start();
require_once 'db.php';

/* Message states */
$status_updated =
    isset($_GET['status']) &&
    $_GET['status'] === 'updated';

$status_unavailable =
    isset($_GET['status']) &&
    $_GET['status'] === 'unavailable';

$status_error =
    isset($_GET['status']) &&
    $_GET['status'] === 'error';

$inventory_updated =
    isset($_GET['inventory']) &&
    $_GET['inventory'] === 'updated';

$inventory_error =
    isset($_GET['inventory']) &&
    $_GET['inventory'] === 'error';


$delete_success =
    isset($_GET['delete']) &&
    $_GET['delete'] === 'success';

$delete_error =
    isset($_GET['delete']) &&
    $_GET['delete'] === 'error';


/* User must be logged in */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}


/* Only the owner/admin can access this page */
if (
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'admin'
) {
    header("Location: index.php");
    exit;
}


/* Get total customers */
$total_users_result = $conn->query("
    SELECT COUNT(*) AS total
    FROM users
    WHERE role = 'customer'
");

$total_users =
    $total_users_result->fetch_assoc()['total'];


/* Get total bookings */
$total_bookings_result = $conn->query("
    SELECT COUNT(*) AS total
    FROM bookings
");

$total_bookings =
    $total_bookings_result->fetch_assoc()['total'];


/* Get total available motorcycles */
$total_available_result = $conn->query("
    SELECT SUM(available_units) AS total
    FROM motorcycle_inventory
");

$total_available =
    $total_available_result->fetch_assoc()['total'] ?? 0;


/* Get bookings */
$bookings = $conn->query("
    SELECT
        bookings.id,
        users.full_name,
        users.email,
        users.phone,
        bookings.driver_license,
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


/* Get motorcycle inventory */
$inventory_list = $conn->query("
    SELECT
        id,
        motorcycle_name,
        total_units,
        available_units
    FROM motorcycle_inventory
    ORDER BY motorcycle_name ASC
");
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
        Owner Dashboard | RIDEGO RENTALS
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


        /* HEADER */

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


        /* DASHBOARD */

        .dashboard {
            width: 90%;
            max-width: 1200px;
            margin: 35px auto;
        }


        /* WELCOME */

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


        /* STATISTICS */

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


        /* TABLE SECTIONS */

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

        .bookings-section input[type="number"] {
            width: 90px;
            padding: 7px 10px;
            border: 1px solid #cccccc;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 12px;
        }

        .bookings-section button {
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

        .bookings-section button:hover {
            opacity: 0.9;
        }


        /* TABLE */

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


        /* STATUS */

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



        /* LICENSE BUTTON */
        .bookings-section .license-button {
            display:inline-block; padding:7px 12px; border-radius:8px;
            background:#0E1B29; color:#FFFFFF; text-decoration:none;
            font-size:11px; font-weight:700;
        }
        .bookings-section .license-button:hover { background:#3C8D8A; }

        /* DELETE BUTTON */
        .bookings-section .delete-button {
            background: #b42318;
            color: #FFFFFF;
        }

        .bookings-section .delete-button:hover {
            background: #8f1c14;
        }



        /* DELETE CONFIRMATION MODAL */

        .delete-modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: rgba(14, 27, 41, 0.72);
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .delete-modal-overlay.show {
            display: flex;
        }

        .delete-modal {
            width: 100%;
            max-width: 430px;
            background: #FFFFFF;
            border-radius: 18px;
            padding: 28px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.24);
            text-align: center;
        }

        .delete-modal-icon {
            width: 58px;
            height: 58px;
            margin: 0 auto 16px;
            border-radius: 50%;
            background: #fff1f0;
            color: #b42318;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            font-weight: 700;
        }

        .delete-modal h3 {
            margin: 0 0 10px;
            font-size: 20px;
            color: #0E1B29;
        }

        .delete-modal p {
            margin: 0 0 22px;
            color: #52606d;
            font-size: 13px;
            line-height: 1.6;
        }

        .delete-modal-actions {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .delete-modal-actions button {
            min-width: 120px;
            padding: 10px 16px;
            border: none;
            border-radius: 9px;
            font-family: 'Poppins', sans-serif;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
        }

        .delete-cancel-button {
            background: #e9eef2;
            color: #0E1B29;
        }

        .delete-confirm-button {
            background: #b42318;
            color: #FFFFFF;
        }

        .delete-cancel-button:hover,
        .delete-confirm-button:hover {
            opacity: 0.9;
        }

        /* MOBILE */

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

    <h1>
        RIDEGO OWNER DASHBOARD
    </h1>

    <a href="logout.php">
        LOGOUT
    </a>

</header>


<main class="dashboard">


    <!-- BOOKING STATUS SUCCESS MESSAGE -->

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

    <?php endif; ?>


    <!-- BOOKING STATUS UNAVAILABLE MESSAGE -->

    <?php if ($status_unavailable): ?>

        <div
            id="statusUnavailable"
            style="
                background:#b42318;
                color:#FFFFFF;
                padding:12px 16px;
                border-radius:10px;
                margin-bottom:20px;
                text-align:center;
                font-size:13px;
                font-weight:700;
            "
        >
            Cannot update booking status because no motorcycle unit is available.
        </div>

    <?php endif; ?>


    <!-- BOOKING STATUS ERROR MESSAGE -->

    <?php if ($status_error): ?>

        <div
            id="statusError"
            style="
                background:#b42318;
                color:#FFFFFF;
                padding:12px 16px;
                border-radius:10px;
                margin-bottom:20px;
                text-align:center;
                font-size:13px;
                font-weight:700;
            "
        >
            Something went wrong while updating the booking status.
        </div>

    <?php endif; ?>


    <!-- INVENTORY SUCCESS MESSAGE -->

    <?php if ($inventory_updated): ?>

        <div
            id="inventoryMessage"
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
            Inventory updated successfully!
        </div>

    <?php endif; ?>


    <!-- INVENTORY ERROR MESSAGE -->

    <?php if ($inventory_error): ?>

        <div
            id="inventoryError"
            style="
                background:#b42318;
                color:#FFFFFF;
                padding:12px 16px;
                border-radius:10px;
                margin-bottom:20px;
                text-align:center;
                font-size:13px;
                font-weight:700;
            "
        >
            Available units cannot be greater than total units.
        </div>

    <?php endif; ?>



    <!-- DELETE SUCCESS MESSAGE -->

    <?php if ($delete_success): ?>

        <div
            id="deleteSuccess"
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
            Booking deleted successfully!
        </div>

    <?php endif; ?>


    <!-- DELETE ERROR MESSAGE -->

    <?php if ($delete_error): ?>

        <div
            id="deleteError"
            style="
                background:#b42318;
                color:#FFFFFF;
                padding:12px 16px;
                border-radius:10px;
                margin-bottom:20px;
                text-align:center;
                font-size:13px;
                font-weight:700;
            "
        >
            Something went wrong while deleting the booking.
        </div>

    <?php endif; ?>


    <!-- HIDE MESSAGES AFTER 3 SECONDS -->

    <script>

        setTimeout(function () {

            const statusMessage =
                document.getElementById('statusMessage');

            const statusUnavailable =
                document.getElementById('statusUnavailable');

            const statusError =
                document.getElementById('statusError');

            const inventoryMessage =
                document.getElementById('inventoryMessage');

            const inventoryError =
                document.getElementById('inventoryError');


            const deleteSuccess =
                document.getElementById('deleteSuccess');

            const deleteError =
                document.getElementById('deleteError');


            if (statusMessage) {
                statusMessage.style.display = 'none';
            }

            if (statusUnavailable) {
                statusUnavailable.style.display = 'none';
            }

            if (statusError) {
                statusError.style.display = 'none';
            }

            if (inventoryMessage) {
                inventoryMessage.style.display = 'none';
            }

            if (inventoryError) {
                inventoryError.style.display = 'none';
            }


            if (deleteSuccess) {
                deleteSuccess.style.display = 'none';
            }

            if (deleteError) {
                deleteError.style.display = 'none';
            }

        }, 3000);


        if (window.history.replaceState) {

            window.history.replaceState(
                null,
                '',
                'admin.php'
            );

        }

    </script>


    <!-- WELCOME -->

    <div class="welcome">

        <h2>

            Welcome,

            <?= htmlspecialchars($_SESSION['full_name']) ?>

        </h2>

        <p>
            Manage and monitor RIDEGO RENTALS.
        </p>

    </div>


    <!-- DASHBOARD TOTALS -->

    <div class="stats">

        <div class="stat-card">

            <p>
                TOTAL CUSTOMERS
            </p>

            <h3>
                <?= $total_users ?>
            </h3>

        </div>


        <div class="stat-card">

            <p>
                TOTAL BOOKINGS
            </p>

            <h3>
                <?= $total_bookings ?>
            </h3>

        </div>


        <div class="stat-card">

            <p>
                AVAILABLE MOTORCYCLES
            </p>

            <h3>
                <?= $total_available ?>
            </h3>

        </div>

    </div>


    <!-- BOOKINGS -->
    <section class="bookings-section">
        <h2>BOOKINGS</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th><th>CUSTOMER</th><th>EMAIL</th><th>PHONE</th>
                    <th>DRIVER'S LICENSE</th><th>MOTORCYCLE</th>
                    <th>PICK-UP</th><th>RETURN</th><th>PAYMENT</th>
                    <th>PICK-UP LOCATION</th><th>DROP-OFF LOCATION</th>
                    <th>STATUS</th><th>ACTION</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($bookings && $bookings->num_rows > 0): ?>
                <?php while ($booking = $bookings->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($booking['id']) ?></td>
                        <td><?= htmlspecialchars($booking['full_name']) ?></td>
                        <td><?= htmlspecialchars($booking['email']) ?></td>
                        <td><?= htmlspecialchars($booking['phone']) ?></td>
                        <td>
                            <?php if (!empty($booking['driver_license'])): ?>
                                <a class="license-button"
                                   href="<?= htmlspecialchars('uploads/licenses/' . rawurlencode(basename($booking['driver_license']))) ?>"
                                   target="_blank" rel="noopener noreferrer">
                                    VIEW LICENSE
                                </a>
                            <?php else: ?>
                                No license
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($booking['motorcycle_name']) ?></td>
                        <td>
                            <?= htmlspecialchars($booking['pickup_date']) ?>
                            <?= htmlspecialchars($booking['pickup_time']) ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($booking['return_date']) ?>
                            <?= htmlspecialchars($booking['return_time']) ?>
                        </td>
                        <td><?= htmlspecialchars($booking['payment_method']) ?></td>
                        <td><?= htmlspecialchars($booking['pickup_branch']) ?></td>
                        <td><?= htmlspecialchars($booking['dropoff_branch']) ?></td>
                        <td class="status">
                            <form method="POST" action="update_status.php">
                                <input type="hidden" name="booking_id"
                                    value="<?= htmlspecialchars($booking['id']) ?>">
                                <select name="status">
                                    <option value="Pending" <?= $booking['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="Confirmed" <?= $booking['status'] === 'Confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                    <option value="Completed" <?= $booking['status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                                    <option value="Cancelled" <?= $booking['status'] === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                </select>
                                <button type="submit">SAVE</button>
                            </form>
                        </td>
                        <td>
                            <button
                                type="button"
                                class="delete-button js-delete-booking"
                                data-booking-id="<?= htmlspecialchars($booking['id']) ?>"
                                data-customer="<?= htmlspecialchars($booking['full_name']) ?>"
                                data-motorcycle="<?= htmlspecialchars($booking['motorcycle_name']) ?>"
                            >
                                DELETE
                            </button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="13">No bookings found.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </section>


    <!-- MOTORCYCLE INVENTORY -->

<section
    class="bookings-section"
    style="margin-top:30px;"
>

    <h2>
        MOTORCYCLE INVENTORY
    </h2>

    <table>

        <thead>
            <tr>
                <th>MOTORCYCLE</th>
                <th>TOTAL UNITS</th>
                <th>AVAILABLE UNITS</th>
                <th>ACTION</th>
            </tr>
        </thead>

        <tbody>

        <?php if (
            $inventory_list &&
            $inventory_list->num_rows > 0
        ): ?>

            <?php while (
                $motorcycle =
                    $inventory_list->fetch_assoc()
            ): ?>

                <tr>

                    <form
                        method="POST"
                        action="update_inventory.php"
                    >

                        <input
                            type="hidden"
                            name="inventory_id"
                            value="<?= htmlspecialchars(
                                $motorcycle['id']
                            ) ?>"
                        >

                        <td>
                            <?= htmlspecialchars(
                                $motorcycle['motorcycle_name']
                            ) ?>
                        </td>

                        <td>
                            <input
                                type="number"
                                name="total_units"
                                min="0"
                                value="<?= htmlspecialchars(
                                    $motorcycle['total_units']
                                ) ?>"
                                required
                            >
                        </td>

                        <td>
                            <input
                                type="number"
                                name="available_units"
                                min="0"
                                value="<?= htmlspecialchars(
                                    $motorcycle['available_units']
                                ) ?>"
                                required
                            >
                        </td>

                        <td>
                            <button type="submit">
                                SAVE
                            </button>
                        </td>

                    </form>

                </tr>

            <?php endwhile; ?>

        <?php else: ?>

            <tr>
                <td colspan="4">
                    No motorcycles found.
                </td>
            </tr>

        <?php endif; ?>

        </tbody>

    </table>

</section>


</main>




<div id="deleteModal" class="delete-modal-overlay" aria-hidden="true">
    <div class="delete-modal" role="dialog" aria-modal="true" aria-labelledby="deleteModalTitle">
        <div class="delete-modal-icon">!</div>

        <h3 id="deleteModalTitle">Delete Booking?</h3>

        <p id="deleteModalText">
            This action cannot be undone.
        </p>

        <div class="delete-modal-actions">
            <button
                type="button"
                id="cancelDeleteButton"
                class="delete-cancel-button"
            >
                CANCEL
            </button>

            <form
                id="deleteBookingForm"
                method="POST"
                action="delete_booking.php"
            >
                <input
                    type="hidden"
                    id="deleteBookingId"
                    name="booking_id"
                    value=""
                >

                <button
                    type="submit"
                    class="delete-confirm-button"
                >
                    YES, DELETE
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    const deleteModal =
        document.getElementById('deleteModal');

    const deleteBookingId =
        document.getElementById('deleteBookingId');

    const deleteModalText =
        document.getElementById('deleteModalText');

    const cancelDeleteButton =
        document.getElementById('cancelDeleteButton');

    document
        .querySelectorAll('.js-delete-booking')
        .forEach(function (button) {
            button.addEventListener('click', function () {
                const bookingId =
                    button.dataset.bookingId || '';

                const customer =
                    button.dataset.customer || 'this customer';

                const motorcycle =
                    button.dataset.motorcycle || 'this motorcycle';

                deleteBookingId.value = bookingId;

                deleteModalText.textContent =
                    'Delete the booking for ' +
                    customer +
                    ' (' +
                    motorcycle +
                    ')? This action cannot be undone.';

                deleteModal.classList.add('show');
                deleteModal.setAttribute(
                    'aria-hidden',
                    'false'
                );
            });
        });

    function closeDeleteModal() {
        deleteModal.classList.remove('show');
        deleteModal.setAttribute(
            'aria-hidden',
            'true'
        );
        deleteBookingId.value = '';
    }

    cancelDeleteButton.addEventListener(
        'click',
        closeDeleteModal
    );

    deleteModal.addEventListener(
        'click',
        function (event) {
            if (event.target === deleteModal) {
                closeDeleteModal();
            }
        }
    );

    document.addEventListener(
        'keydown',
        function (event) {
            if (
                event.key === 'Escape' &&
                deleteModal.classList.contains('show')
            ) {
                closeDeleteModal();
            }
        }
    );
</script>

</body>

</html>