<?php
session_start();

$is_logged_in = isset($_SESSION['user_id']);
//http://localhost/trial_app/HOMEPAGE/ridego_site_v2/
$vehicles = [
    ['name' => 'Honda Click 125 cc', 'price' => '₱629.00/Day', 'image' => 'assets/honda-click-125.png'],
    ['name' => 'Yamaha Fazzio 125cc', 'price' => '₱819.00/Day', 'image' => 'assets/yamaha-fazzio-125.png'],
    ['name' => 'Yamaha AEROX 155 CC', 'price' => '₱799.00/Day', 'image' => 'assets/yamaha-aerox-155.png'],
    ['name' => 'Honda Beat 110', 'price' => '₱449.00/Day', 'image' => 'assets/honda-beat-110.png'],
];

$features = [
    ['icon' => 'assets/easy-booking.svg', 'title' => 'Easy Booking', 'text' => "Reserve your ride\nin just a few clicks."],
    ['icon' => 'assets/reliable-units.svg', 'title' => 'Reliable Units', 'text' => "Clean, safe, and\nready for the road."],
    ['icon' => 'assets/convenient-locations.svg', 'title' => 'Convenient Locations', 'text' => "Pick up and drop off\nat accessible areas."],
    ['icon' => 'assets/friendly-support.svg', 'title' => 'Friendly Support', 'text' => "We are to help\nwhenever you need."],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0E1B29">
    <title>RIDEGO RENTALS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="site-header">
    <a href="#home" class="site-logo" aria-label="RIDEGO RENTALS">
        <img src="assets/logo.svg" alt="RIDEGO RENTALS">
    </a>
    <button class="menu-toggle" type="button" aria-label="Open navigation" aria-expanded="false">☰</button>
    <nav class="main-nav" aria-label="Main navigation">
        <a class="active" href="#home">HOME</a>
        <a href="#about">ABOUT US</a>
        <a href="#motorcycles">MOTORCYCLES</a>
        <a href="#how-it-works">HOW IT WORKS</a>
        <a href="#faq">FAQ</a>

       <?php if (isset($_SESSION['user_id'])): ?>
    <span class="nav-user">
    <span>Welcome,</span>
    <strong><?= htmlspecialchars(explode(' ', trim($_SESSION['full_name']))[0]) ?></strong>
    </span>
    <a href="logout.php" class="nav-register">LOGOUT</a>
       <?php else: ?>
    <a href="../login.php" class="nav-login">LOGIN</a>
    <a href="register.php" class="nav-register">REGISTER</a>
       <?php endif; ?>
           </nav>
</header>

<main>
    <section class="hero" id="home">
        <div class="hero-content">
        <h1 style="color: #0E1B29 !important;">RIDE MORE.<br>WORRY <span style="color: #3C8D8A !important;">LESS.</span></h1>
            <p>Your ride, your schedule. Explore the City<br>and beyond with RIDEGO RENTALS</p>
            <a class="button button-hero" href="motorcycles.php">
            BOOK YOUR RIDE <span>›</span>
            </a>
        </div>
    </section>

    <section class="why-section" id="about">
        <h2>WHY CHOOSE RIDE<span>GO</span> RENTALS?</h2>
        <div class="feature-grid">
            <?php foreach ($features as $feature): ?>
                <article class="feature-card">
                    <img src="<?= htmlspecialchars($feature['icon']) ?>" alt="">
                    <h3><?= htmlspecialchars($feature['title']) ?></h3>
                    <p><?= nl2br(htmlspecialchars($feature['text'])) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="motorcycles-section" id="motorcycles">
        <div class="section-label">OUR MOTORCYCLES</div>
        <h2>FIND THE RIDE THAT <span>FITS YOU!</span></h2>
        <div class="vehicle-grid">
            <?php foreach ($vehicles as $vehicle): ?>
                <article class="vehicle-card">
                    <div class="vehicle-image">
                        <img src="<?= htmlspecialchars($vehicle['image']) ?>" alt="<?= htmlspecialchars($vehicle['name']) ?>">
                    </div>
                    <div class="vehicle-info">
                        <h3><?= htmlspecialchars($vehicle['name']) ?></h3>
                        <p><?= htmlspecialchars($vehicle['price']) ?></p>
                        <?php if ($is_logged_in): ?>

                        <button class="button button-small js-book"
                            type="button"
                            data-motorcycle="<?= htmlspecialchars($vehicle['name']) ?>"
                            data-price="<?= htmlspecialchars(str_replace(['₱', '/Day', ','], '', $vehicle['price'])) ?>"
                        >
                            RENT NOW
                        </button>

                        <?php else: ?>

                            <a class="button button-small" href="login.php">
                                RENT NOW
                            </a>

<?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <a class="view-all" href="motorcycles.php">View All Motorcycles <span>⟶</span></a>
    </section>

    <section class="how-section" id="how-it-works">
        <h2>HOW IT <span>WORKS</span></h2>
        <div class="steps-grid">
            <article class="step-card">
                <img src="assets/choose-your-ride.svg" alt="">
                <h3>CHOOSE YOUR RIDE</h3>
                <p>Browse our motorcycles<br>and choose the one that<br>fits your journey.</p>
            </article>
            <div class="step-arrow" aria-hidden="true">⟶</div>
            <article class="step-card">
                <img src="assets/select-date-time.svg" alt="">
                <h3>SELECT DATE &amp; TIME</h3>
                <p>Choose your preferred<br>rental date and time.</p>
            </article>
            <div class="step-arrow" aria-hidden="true">⟶</div>
            <article class="step-card">
                <img src="assets/book-confirm.svg" alt="">
                <h3>BOOK &amp; CONFIRM</h3>
                <p>Enter your details and<br>confirm your reservations.</p>
            </article>
        </div>

        <div class="adventure-banner">
            <img src="assets/banner-scooter.svg" alt="">
            <div class="banner-main">
                <h3>PLAN YOUR NEXT <span>ADVENTURE</span><br>WITH THE RIGHT RIDE.</h3>
                <p>Safe, reliable, and ready when you are.</p>
            </div>
            <div class="banner-divider"></div>
            <p class="banner-side">We are here to make<br>every ride simple<br>and enjoyable</p>
        </div>
    </section>
</main>

<footer id="faq" class="site-footer">
    <div class="footer-brand">
        <img src="assets/logo.svg" alt="RIDEGO RENTALS">
        <p>Your trusted motorcycle rental<br>service for every adventure<br>and everyday journey.</p>
    </div>
    <div class="footer-column">
        <h3>QUICK LINKS</h3>
        <a href="#home">Home</a><br>
        <a href="#about">About Us</a><br>
        <a href="#motorcycles">Motorcycles</a><br>
        <a href="#how-it-works">How It Works</a><br>
        <a href="#faq">FAQ</a>
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

<div class="modal" id="bookingModal" aria-hidden="true">

        <div class="modal-overlay js-close-modal"></div>

        <div class="modal-card">

            <button type="button" class="modal-close js-close-modal">
                &times;
            </button>

            <h2>BOOK YOUR RIDE</h2>

            <p>Fill in the details below to reserve your motorcycle.</p>

            <form id="bookingForm" method="POST" action="book.php">

                <label>
                    NAME
                    <input type="text" name="name" required>
                </label>

                <label>
                    EMAIL
                    <input type="email" name="email" required>
                </label>

                    <input type="hidden" name="motorcycle_name" id="bookingMotorcycle">
                    <input type="hidden" name="price_per_day" id="bookingPrice">

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

<script src="assets/app.js"></script>
</body>
</html>
