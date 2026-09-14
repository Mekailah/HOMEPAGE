<?php
session_start();
require_once 'db.php';

$is_logged_in = isset($_SESSION['user_id']);
$motorcycles = [
    ['name' => 'Honda Click 125 cc', 'price' => '₱629.00/Day', 'image' => 'assets/honda-click-125.png'],
    ['name' => 'Yamaha Fazzio 125cc', 'price' => '₱819.00/Day', 'image' => 'assets/yamaha-fazzio-125.png'],
    ['name' => 'Yamaha AEROX 155 CC', 'price' => '₱799.00/Day', 'image' => 'assets/yamaha-aerox-155.png'],
    ['name' => 'Honda Beat 110', 'price' => '₱449.00/Day', 'image' => 'assets/honda-beat-110.png'],
    ['name' => 'Honda ADV 160', 'price' => '₱900.00/Day', 'image' => 'assets/adv.png'],
    ['name' => 'Yamaha PG-1', 'price' => '₱800.00/Day', 'image' => 'assets/pg1.png'],
    ['name' => 'Honda NAVi', 'price' => '₱600.00/Day', 'image' => 'assets/navi.png'],
    ['name' => 'Yamaha NMAX ABS', 'price' => '₱850.00/Day', 'image' => 'assets/nmax.png'],
    ['name' => 'Honda XRM 125', 'price' => '₱600.00/Day', 'image' => 'assets/xrm.png'],
    ['name' => 'Yamaha Vino Classic', 'price' => '₱650.00/Day', 'image' => 'assets/yamaha vino.png'],
    ['name' => 'Kawasaki Ninja 1000SX', 'price' => '₱3,500.00/Day', 'image' => 'assets/kawasaki ninja.png'],
    ['name' => 'Yamaha Sniper 155', 'price' => '₱750.00/Day', 'image' => 'assets/sniper.png']
];

$inventory_result = $conn->query(
    "SELECT motorcycle_name, available_units FROM motorcycle_inventory"
);

$inventory = [];

while ($row = $inventory_result->fetch_assoc()) {
    $inventory[$row['motorcycle_name']] = $row['available_units'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Motorcycles | RIDEGO RENTALS</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="assets/style.css">
</head>

<body>

    <!-- HEADER -->
    <header class="site-header">

        <a href="index.php#home" class="site-logo">
            <img src="assets/logo.svg" alt="RIDEGO RENTALS">
        </a>

        <nav class="main-nav">

            <a href="index.php#home">HOME</a>

            <a href="index.php#about">ABOUT US</a>

            <a href="motorcycles.php" class="active">MOTORCYCLES</a>

            <a href="index.php#how-it-works">HOW IT WORKS</a>

            <a href="index.php#faq">FAQ</a>

        </nav>

    </header>


    <!-- MOTORCYCLES -->
    <main>

        <section class="motorcycles-section">

            <div class="section-label">
                OUR MOTORCYCLES
            </div>

            <h2>
                FIND THE RIDE THAT <span>FITS YOU!</span>
            </h2>

            <p style="margin: -10px 0 24px; font-size: 15px; font-weight: 500;">
                Find the perfect ride for your next adventure.
            </p>

            <div class="vehicle-grid">

                <?php foreach ($motorcycles as $motorcycle): ?>

                    <article class="vehicle-card">

                        <div class="vehicle-image">

                            <img
                                src="<?= htmlspecialchars($motorcycle['image']) ?>"
                                alt="<?= htmlspecialchars($motorcycle['name']) ?>"
                            >

                        </div>

                        <div class="vehicle-info">
                            <p class="availability-count">
                                <?= $inventory[$motorcycle['name']] ?? 0 ?>
                            </p>

                            <h3>
                                <?= htmlspecialchars($motorcycle['name']) ?>
                            </h3>

                            <p>
                                <?= htmlspecialchars($motorcycle['price']) ?>
                            </p>

                            <?php if ($is_logged_in): ?>

                            <button
                                type="button"
                                class="button button-small js-book"
                                data-motorcycle="<?= htmlspecialchars($motorcycle['name']) ?>"
                                data-price="<?= htmlspecialchars(str_replace(['₱', '/Day', ','], '', $motorcycle['price'])) ?>"
                            >
                                RENT NOW
                            </button>
                            <?php else: ?>

                            <a href="login.php" class="button button-small">
                                RENT NOW
                            </a>

                            <?php endif; ?>

                        </div>

                    </article>

                <?php endforeach; ?>

            </div>

            <!-- BACK BUTTON -->
            <a href="index.php" class="back-button">
                ← BACK
            </a>

        </section>

    </main>


    <!-- FOOTER -->
    <footer class="site-footer">

        <div class="footer-brand">

            <img src="assets/logo.svg" alt="RIDEGO RENTALS">

            <p>
                Your trusted motorcycle rental<br>
                service for every adventure<br>
                and everyday journey.
            </p>

        </div>

        <div class="footer-column">

            <h3>QUICK LINKS</h3>

            <a href="index.php#home">Home</a><br>
            <a href="index.php#about">About Us</a><br>
            <a href="motorcycles.php">Motorcycles</a><br>
            <a href="index.php#how-it-works">How It Works</a><br>
            <a href="index.php#faq">FAQ</a>

        </div>

        <div class="footer-column">

            <h3>SUPPORT</h3>

            <a href="#">Terms &amp; Conditions</a><br>
            <a href="#">Privacy Policy</a><br>
            <a href="#">Cancellation Policy</a><br>
            <a href="#">Fees and Charges</a>

        </div>

        <div class="footer-column contact-column">

            <h3>CONTACT US</h3>

            <p><span>☎</span> 63+ 926 465 8301</p>
            <p><span>✉</span> mekailahbangay@gmail.com</p>
             <p><img class="contact-pin-icon" src="assets/contact-pin.svg" alt=""> Dumaguete City</p>

        </div>

    </footer>

    <!-- PASTE YOUR BOOKING MODAL HERE -->

    <div class="modal" id="bookingModal" aria-hidden="true">

        <div class="modal-overlay js-close-modal"></div>

        <div class="modal-card">

            <button type="button" class="modal-close js-close-modal">
                &times;
            </button>

            <h2>BOOK YOUR RIDE</h2>

            <p>Fill in the details below to reserve your motorcycle.</p>

            <form id="bookingForm" method="POST" action="book.php" enctype="multipart/form-data">

                <label>
                    NAME
                    <input
                        type="text"
                        value="<?= htmlspecialchars($_SESSION['full_name'] ?? '') ?>"
                        readonly
                    >
                </label>

                <label>
                    EMAIL
                    <input
                        type="email"
                        value="<?= htmlspecialchars($_SESSION['email'] ?? '') ?>"
                        readonly
                    >
                </label>

                <label>
                    DRIVER'S LICENSE
                    <input type="file" name="driver_license" accept="image/jpeg,image/png" required>
                </label>

                <input type="hidden" name="motorcycle_name" id="bookingMotorcycle">
                <input type="hidden" name="price_per_day" id="bookingPrice">

                <label>
                    SELECT DATE
                    <input type="date" name="pickup_date" required>
                </label>

                <label>
                    SELECT TIME
                    <input type="time" name="pickup_time" required>
                </label>

                <label>
                    RETURN DATE
                    <input type="date" name="return_date" required>
                </label>

                <label>
                    RETURN TIME
                    <input type="time" name="return_time" required>
                </label>

                <div class="booking-total">
                    <p>RENTAL DAYS: <span id="rentalDays">0</span></p>
                    <p>TOTAL: ₱<span id="bookingTotal">0.00</span></p>
                </div>

                <label>
                    MODE OF PAYMENT
                    <select name="payment_method" required>
                        <option value="" disabled selected>Select payment method</option>
                        <option value="GCash">GCash</option>
                        <option value="BPI">BPI</option>
                    </select>
                </label>

                <label>
                    PICK-UP LOCATION
                    <select name="pickup_branch" required>
                        <option value="Hibbard Avenue, Dumaguete City">Hibbard Avenue, Dumaguete City</option>
                        <option value="Leon Kilat Mall, Bacong">Leon Kilat Mall, Bacong</option>
                    </select>
                </label>

                <label>
                    DROP-OFF LOCATION
                    <select name="dropoff_branch" required>
                        <option value="Hibbard Avenue, Dumaguete City">Hibbard Avenue, Dumaguete City</option>
                        <option value="Leon Kilat Mall, Bacong">Leon Kilat Mall, Bacong</option>
                    </select>
                </label>

                <button type="submit" class="button">
                    BOOK &amp; CONFIRM
                </button>

            </form>

        </div>

    </div>

    <script src="assets/app.js"></script>

</body>
</html>