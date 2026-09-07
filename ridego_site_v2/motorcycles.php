<?php
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

                            <h3>
                                <?= htmlspecialchars($motorcycle['name']) ?>
                            </h3>

                            <p>
                                <?= htmlspecialchars($motorcycle['price']) ?>
                            </p>

                            <a href="#" class="button button-small js-book">
                                RENT NOW
                            </a>

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


    <!-- ================================================== -->
    <!-- PASTE YOUR BOOKING MODAL HERE -->
    <!-- ================================================== -->

    <div class="modal" id="bookingModal" aria-hidden="true">

        <div class="modal-overlay js-close-modal"></div>

        <div class="modal-card">

            <button type="button" class="modal-close js-close-modal">
                &times;
            </button>

            <h2>BOOK YOUR RIDE</h2>

            <p>Fill in the details below to reserve your motorcycle.</p>

            <form id="bookingForm">

                <label>
                    NAME
                    <input type="text" name="name" required>
                </label>

                <label>
                    EMAIL
                    <input type="email" name="email" required>
                </label>

                <label>SELECT DATE
                    <input type="date" name="pickup_date" required>
                </label>

                <label>RETURN DATE
                    <input type="date" name="return_date" required>
                </label>

                <button type="submit" class="button">
                    BOOK &amp; CONFIRM
                </button>

            </form>

        </div>

    </div>


    <!-- ================================================== -->
    <!-- YOUR JAVASCRIPT GOES HERE -->
    <!-- ================================================== -->

    <script src="assets/app.js"></script>

</body>
</html>