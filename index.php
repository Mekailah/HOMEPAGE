<?php

require_once 'db.php';
session_start();

$is_logged_in = isset($_SESSION['user_id']);

$booking_success =
    isset($_GET['booking']) &&
    $_GET['booking'] === 'success';

$booking_error =
    isset($_GET['booking']) &&
    $_GET['booking'] === 'error';

$booking_message = $_GET['message'] ?? '';

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

    <style>

        .booking-total p {
            font-size: 13px;
        }

        .booking-total span {
            font-size: 13px;
        }

        .time-picker {
            position: relative;
            margin-top: 6px;
            width: 220px;
            max-width: 100%;
        }

        .time-picker-toggle {
            width: 100%;
            min-height: 42px;
            padding: 9px 36px 9px 12px;
            border: 1px solid #cfd6dc;
            border-radius: 8px;
            background: #ffffff;
            color: #0E1B29;
            font: inherit;
            font-weight: 400;
            text-align: left;
            cursor: pointer;
            position: relative;
        }

        .time-picker-toggle::after {
            content: "⌄";
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-55%);
            font-size: 18px;
        }

        .time-picker.open .time-picker-toggle::after {
            transform: translateY(-45%) rotate(180deg);
        }

        .time-picker-menu {
            display: none;
            position: absolute;
            z-index: 50;
            top: calc(100% + 4px);
            left: 0;
            right: 0;
            max-height: 220px;
            overflow-y: auto;
            background: #ffffff;
            border: 1px solid #cfd6dc;
            border-radius: 8px;
            box-shadow: 0 10px 24px rgba(14, 27, 41, 0.14);
        }

        .time-picker.open .time-picker-menu {
            display: block;
        }

        .time-option {
            width: 100%;
            padding: 10px 12px;
            border: 0;
            background: #ffffff;
            color: #0E1B29;
            font: inherit;
            font-weight: 400;
            text-align: left;
            cursor: pointer;
        }

        .time-option:hover,
        .time-option:focus {
            background: #eef7f6;
            outline: none;
        }

        .time-option.selected {
            background: #3C8D8A;
            color: #ffffff;
        }

        /* Hover the quick links and contact us */
        .site-footer a:hover,
        .policy-link:hover {
            text-decoration: underline;
        }

        /* FAQ SECTION */
        .faq-section { padding: 70px 20px; background: #f7f9fa; }
        .faq-section h2 { margin: 0 0 30px; text-align: center; color: #0E1B29; }
        .faq-section h2 span { color: #3C8D8A; }
        .faq-list { width: 760px; max-width: 100%; margin: 0 auto; }
        .faq-item { margin-bottom: 12px; border: 1px solid #dce3e7; border-radius: 10px; background: #fff; overflow: hidden; }
        .faq-question { width: 100%; padding: 16px 18px; border: 0; background: #fff; color: #0E1B29; font: inherit; font-size: 14px; font-weight: 700; text-align: left; cursor: pointer; display: flex; align-items: center; justify-content: space-between; gap: 15px; }
        .faq-question::after { content: "+"; color: #3C8D8A; font-size: 22px; font-weight: 500; }
        .faq-item.open .faq-question::after { content: "−"; }
        .faq-answer { display: none; padding: 0 18px 16px; color: #555; font-size: 13px; line-height: 1.7; }
        .faq-answer p { margin: 0; }
        .faq-item.open .faq-answer { display: block; }


        /* SUPPORT POLICY POPUPS */
        .policy-link {
            background: none;
            border: 0;
            padding: 0;
            color: inherit;
            font: inherit;
            cursor: pointer;
            text-align: left;
        }

        .policy-link:hover { text-decoration: underline; }

        .policy-modal {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .policy-modal.open { display: flex; }

        .policy-overlay {
            position: absolute;
            inset: 0;
            background: rgba(14, 27, 41, 0.65);
        }

        .policy-card {
            position: relative;
            z-index: 1;
            width: 620px;
            max-width: 100%;
            max-height: 85vh;
            overflow-y: auto;
            padding: 30px;
            border-radius: 14px;
            background: #fff;
            color: #0E1B29;
            box-shadow: 0 18px 50px rgba(0,0,0,.22);
        }

        .policy-card h2 {
            margin: 0 35px 16px 0;
            font-size: 22px;
        }

        .policy-card p {
            margin: 0;
            color: #4b5560;
            font-size: 13px;
            font-weight: 400;
            line-height: 1.8;
        }

        .policy-close {
            position: absolute;
            top: 14px;
            right: 16px;
            border: 0;
            background: transparent;
            color: #0E1B29;
            font-size: 28px;
            cursor: pointer;
        }

    </style>

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
    <a href="login.php" class="nav-login">LOGIN</a>
    <a href="register.php" class="nav-register">REGISTER</a>
       <?php endif; ?>
           </nav>
</header>

<main>
        <!-- BOOKING SUCCESS MESSAGE -->
    <?php if ($booking_success): ?>

        <div
            id="bookingSuccess"
            style="
                background:#3C8D8A;
                color:#FFFFFF;
                text-align:center;
                padding:10px 15px;
                font-size:12px;
                font-weight:700;
            "
        >
            Booking submitted successfully!
        </div>

    <?php endif; ?>


    <!-- BOOKING ERROR MESSAGE -->
    <?php if ($booking_error): ?>

        <div
            id="bookingError"
            style="
                background:#b42318;
                color:#FFFFFF;
                text-align:center;
                padding:10px 15px;
                font-size:12px;
                font-weight:700;
            "
        >
            <?= htmlspecialchars($booking_message) ?>
        </div>

    <?php endif; ?>


    <!-- HIDE MESSAGE AFTER 3 SECONDS -->
    <script>
        setTimeout(function () {

            const successMessage =
                document.getElementById('bookingSuccess');

            const errorMessage =
                document.getElementById('bookingError');

            if (successMessage) {
                successMessage.style.display = 'none';
            }

            if (errorMessage) {
                errorMessage.style.display = 'none';
            }

        }, 3000);


        if (
            window.history.replaceState &&
            (
                document.getElementById('bookingSuccess') ||
                document.getElementById('bookingError')
            )
        ) {
            window.history.replaceState(
                null,
                '',
                'index.php'
            );
        }
    </script>


    <section class="hero" id="home">
    
        <div class="hero-content">
        <h1 style="color: #0E1B29 !important;">RIDE MORE.<br>WORRY <span style="color: #3C8D8A !important;">LESS.</span></h1>
            <p>Your ride, your schedule. Explore the City<br>and beyond with RIDEGO RENTALS</p>
            <?php if ($is_logged_in): ?>

            <a class="button button-hero" href="motorcycles.php">
            BOOK YOUR RIDE <span>›</span>
            </a>
            <?php else: ?>

            <a class="button button-hero" href="login.php">
                BOOK YOUR RIDE <span>›</span>
            </a>

            <?php endif; ?>
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
            <?php
                $inventory_result = $conn->query("SELECT motorcycle_name, available_units FROM motorcycle_inventory");

                $inventory = [];

                while ($row = $inventory_result->fetch_assoc()) {
                    $inventory[$row['motorcycle_name']] = $row['available_units'];
                }
            ?>
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

                            <p class="availability-count">
                                <?= $inventory[$vehicle['name']] ?? 0 ?>
                            </p>

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
    <section class="faq-section" id="faq">
        <h2>FREQUENTLY ASKED <span>QUESTIONS</span></h2>
        <div class="faq-list">
            <div class="faq-item"><button type="button" class="faq-question" aria-expanded="false">What do I need to rent a motorcycle?</button><div class="faq-answer"><p>You need a valid driver's license and the required booking information to reserve a motorcycle.</p></div></div>
            <div class="faq-item"><button type="button" class="faq-question" aria-expanded="false">How do I book a motorcycle?</button><div class="faq-answer"><p>Choose your preferred motorcycle, click RENT NOW, complete the booking form, and submit your reservation.</p></div></div>
            <div class="faq-item"><button type="button" class="faq-question" aria-expanded="false">What payment methods do you accept?</button><div class="faq-answer"><p>RIDEGO Rentals currently accepts GCash and BPI payments.</p></div></div>
            <div class="faq-item"><button type="button" class="faq-question" aria-expanded="false">Where can I pick up and return the motorcycle?</button><div class="faq-answer"><p>You can choose Hibbard Avenue, Dumaguete City or Leon Kilat Mall, Bacong as your pick-up and drop-off location.</p></div></div>
            <div class="faq-item"><button type="button" class="faq-question" aria-expanded="false">When is my booking confirmed?</button><div class="faq-answer"><p>Your booking remains Pending until your payment and reservation are verified.</p></div></div>
        </div>
    </section>
</main>

<footer class="site-footer">
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
        <button type="button" class="policy-link" data-policy="termsPolicy">Terms &amp; Conditions</button><br>
        <button type="button" class="policy-link" data-policy="privacyPolicy">Privacy Policy</button><br>
        <button type="button" class="policy-link" data-policy="cancellationPolicy">Cancellation Policy</button><br>
        <button type="button" class="policy-link" data-policy="feesPolicy">Fees and Charges</button>
    </div>
    <div class="footer-column contact-column">
        <h3>CONTACT US</h3>
        <p><span>☎</span> 63+ 926 465 8301</p>
        <p><span>✉</span> mekailahbangay@gmail.com</p>
        <p><img class="contact-pin-icon" src="assets/contact-pin.svg" alt=""> Dumaguete City</p>
    </div>
</footer>


<div class="policy-modal" id="termsPolicy" aria-hidden="true">
    <div class="policy-overlay" data-policy-close></div>
    <div class="policy-card" role="dialog" aria-modal="true">
        <button type="button" class="policy-close" data-policy-close aria-label="Close">&times;</button>
        <h2>Terms &amp; Conditions</h2>
        <p>By renting a motorcycle from RIDEGO Rentals, customers agree to provide accurate personal information and a valid driver's license during the booking process. The rented motorcycle must be used responsibly and only for lawful purposes. Customers are expected to take proper care of the motorcycle while it is under their possession and return it at the agreed location, date, and time. Any damage, loss, or violation that occurs during the rental period may be subject to review and applicable charges. RIDEGO Rentals reserves the right to refuse or cancel a booking when the provided information is incomplete, invalid, or does not meet the rental requirements.</p>
    </div>
</div>

<div class="policy-modal" id="privacyPolicy" aria-hidden="true">
    <div class="policy-overlay" data-policy-close></div>
    <div class="policy-card" role="dialog" aria-modal="true">
        <button type="button" class="policy-close" data-policy-close aria-label="Close">&times;</button>
        <h2>Privacy Policy</h2>
        <p>RIDEGO Rentals collects customer information such as name, email address, booking details, and driver's license images only for purposes related to motorcycle rental and reservation processing. This information is used to identify customers, manage bookings, verify rental requirements, and provide rental services. Personal information should not be shared with unrelated parties unless necessary for the rental process or required by law. Customers are responsible for providing accurate information when creating an account or making a reservation. RIDEGO Rentals aims to handle all submitted customer information responsibly and securely.</p>
    </div>
</div>

<div class="policy-modal" id="cancellationPolicy" aria-hidden="true">
    <div class="policy-overlay" data-policy-close></div>
    <div class="policy-card" role="dialog" aria-modal="true">
        <button type="button" class="policy-close" data-policy-close aria-label="Close">&times;</button>
        <h2>Cancellation Policy</h2>
        <p>Customers who are unable to continue with their reservation should request cancellation as early as possible before the scheduled pickup date and time. Cancellation requests may be reviewed based on the current status of the booking and whether payment has already been processed or verified. Once a motorcycle has already been released to the customer, the reservation may no longer be treated as a normal cancellation. Any refund, if applicable, may depend on the circumstances of the cancellation and the payment status. Customers are encouraged to contact RIDEGO Rentals immediately if their travel plans or rental schedule change.</p>
    </div>
</div>

<div class="policy-modal" id="feesPolicy" aria-hidden="true">
    <div class="policy-overlay" data-policy-close></div>
    <div class="policy-card" role="dialog" aria-modal="true">
        <button type="button" class="policy-close" data-policy-close aria-label="Close">&times;</button>
        <h2>Fees and Charges</h2>
        <p>The total rental cost is calculated based on the selected motorcycle, its daily rental rate, and the number of rental days indicated in the reservation. Additional charges may apply when a motorcycle is returned later than the agreed return schedule or when damage, loss, or other issues occur during the rental period. Customers may also be responsible for costs resulting from improper use of the motorcycle while it is under their possession. Any additional fee should be reviewed based on the circumstances before it is charged. Customers are encouraged to check their booking information and total rental amount carefully before confirming their reservation.</p>
    </div>
</div>

<div class="modal" id="bookingModal" aria-hidden="true">

        <div class="modal-overlay js-close-modal"></div>

        <div class="modal-card">

            <button type="button" class="modal-close js-close-modal">
                &times;
            </button>

            <h2>BOOK YOUR RIDE</h2>

            <p>Fill in the details below to reserve your motorcycle.</p>

            <form id="bookingForm" method="POST" action="book.php" enctype="multipart/form-data">
                <div id="bookingFormError" class="booking-form-error" role="alert"></div>
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

                <label>SELECT DATE
                    <input type="date" name="pickup_date" required>
                </label>

                <label>
                    PICKUP TIME
                    <div class="time-picker" data-time-picker>
                        <button
                            type="button"
                            class="time-picker-toggle"
                            aria-haspopup="listbox"
                            aria-expanded="false"
                        >
                            Select time
                        </button>

                        <div class="time-picker-menu" role="listbox">
                            <button type="button" class="time-option" data-value="06:00">6:00 AM</button>
                            <button type="button" class="time-option" data-value="06:30">6:30 AM</button>
                            <button type="button" class="time-option" data-value="07:00">7:00 AM</button>
                            <button type="button" class="time-option" data-value="07:30">7:30 AM</button>
                            <button type="button" class="time-option" data-value="08:00">8:00 AM</button>
                            <button type="button" class="time-option" data-value="08:30">8:30 AM</button>
                            <button type="button" class="time-option" data-value="09:00">9:00 AM</button>
                            <button type="button" class="time-option" data-value="09:30">9:30 AM</button>
                            <button type="button" class="time-option" data-value="10:00">10:00 AM</button>
                            <button type="button" class="time-option" data-value="10:30">10:30 AM</button>
                            <button type="button" class="time-option" data-value="11:00">11:00 AM</button>
                            <button type="button" class="time-option" data-value="11:30">11:30 AM</button>
                            <button type="button" class="time-option" data-value="12:00">12:00 PM</button>
                            <button type="button" class="time-option" data-value="12:30">12:30 PM</button>
                            <button type="button" class="time-option" data-value="13:00">1:00 PM</button>
                            <button type="button" class="time-option" data-value="13:30">1:30 PM</button>
                            <button type="button" class="time-option" data-value="14:00">2:00 PM</button>
                            <button type="button" class="time-option" data-value="14:30">2:30 PM</button>
                            <button type="button" class="time-option" data-value="15:00">3:00 PM</button>
                            <button type="button" class="time-option" data-value="15:30">3:30 PM</button>
                            <button type="button" class="time-option" data-value="16:00">4:00 PM</button>
                            <button type="button" class="time-option" data-value="16:30">4:30 PM</button>
                            <button type="button" class="time-option" data-value="17:00">5:00 PM</button>
                            <button type="button" class="time-option" data-value="17:30">5:30 PM</button>
                            <button type="button" class="time-option" data-value="18:00">6:00 PM</button>
                            <button type="button" class="time-option" data-value="18:30">6:30 PM</button>
                            <button type="button" class="time-option" data-value="19:00">7:00 PM</button>
                            <button type="button" class="time-option" data-value="19:30">7:30 PM</button>
                            <button type="button" class="time-option" data-value="20:00">8:00 PM</button>
                            <button type="button" class="time-option" data-value="20:30">8:30 PM</button>
                            <button type="button" class="time-option" data-value="21:00">9:00 PM</button>
                            <button type="button" class="time-option" data-value="21:30">9:30 PM</button>
                            <button type="button" class="time-option" data-value="22:00">10:00 PM</button>
                        </div>

                        <input
                            type="hidden"
                            name="pickup_time"
                            class="time-picker-value"
                            required
                        >
                    </div>
                </label>

                <label>RETURN DATE
                    <input type="date" name="return_date" required>
                </label>

                <label>
                    RETURN TIME
                    <div class="time-picker" data-time-picker>
                        <button
                            type="button"
                            class="time-picker-toggle"
                            aria-haspopup="listbox"
                            aria-expanded="false"
                        >
                            Select time
                        </button>

                        <div class="time-picker-menu" role="listbox">
                            <button type="button" class="time-option" data-value="06:00">6:00 AM</button>
                            <button type="button" class="time-option" data-value="06:30">6:30 AM</button>
                            <button type="button" class="time-option" data-value="07:00">7:00 AM</button>
                            <button type="button" class="time-option" data-value="07:30">7:30 AM</button>
                            <button type="button" class="time-option" data-value="08:00">8:00 AM</button>
                            <button type="button" class="time-option" data-value="08:30">8:30 AM</button>
                            <button type="button" class="time-option" data-value="09:00">9:00 AM</button>
                            <button type="button" class="time-option" data-value="09:30">9:30 AM</button>
                            <button type="button" class="time-option" data-value="10:00">10:00 AM</button>
                            <button type="button" class="time-option" data-value="10:30">10:30 AM</button>
                            <button type="button" class="time-option" data-value="11:00">11:00 AM</button>
                            <button type="button" class="time-option" data-value="11:30">11:30 AM</button>
                            <button type="button" class="time-option" data-value="12:00">12:00 PM</button>
                            <button type="button" class="time-option" data-value="12:30">12:30 PM</button>
                            <button type="button" class="time-option" data-value="13:00">1:00 PM</button>
                            <button type="button" class="time-option" data-value="13:30">1:30 PM</button>
                            <button type="button" class="time-option" data-value="14:00">2:00 PM</button>
                            <button type="button" class="time-option" data-value="14:30">2:30 PM</button>
                            <button type="button" class="time-option" data-value="15:00">3:00 PM</button>
                            <button type="button" class="time-option" data-value="15:30">3:30 PM</button>
                            <button type="button" class="time-option" data-value="16:00">4:00 PM</button>
                            <button type="button" class="time-option" data-value="16:30">4:30 PM</button>
                            <button type="button" class="time-option" data-value="17:00">5:00 PM</button>
                            <button type="button" class="time-option" data-value="17:30">5:30 PM</button>
                            <button type="button" class="time-option" data-value="18:00">6:00 PM</button>
                            <button type="button" class="time-option" data-value="18:30">6:30 PM</button>
                            <button type="button" class="time-option" data-value="19:00">7:00 PM</button>
                            <button type="button" class="time-option" data-value="19:30">7:30 PM</button>
                            <button type="button" class="time-option" data-value="20:00">8:00 PM</button>
                            <button type="button" class="time-option" data-value="20:30">8:30 PM</button>
                            <button type="button" class="time-option" data-value="21:00">9:00 PM</button>
                            <button type="button" class="time-option" data-value="21:30">9:30 PM</button>
                            <button type="button" class="time-option" data-value="22:00">10:00 PM</button>
                        </div>

                        <input
                            type="hidden"
                            name="return_time"
                            class="time-picker-value"
                            required
                        >
                    </div>
                </label>

                <div class="booking-total">
                        <p>RENTAL DAYS: <span id="rentalDays">0</span></p>
                        <p>TOTAL: ₱<span id="bookingTotal">0.00</span></p>
                    </div>

                <label>
                        MODE OF PAYMENT
                        <select name="payment_method" id="paymentMethod" required>
                            <option value="" disabled selected>Select payment method</option>
                            <option value="GCash">GCash</option>
                            <option value="BPI">BPI</option>
                        </select>
                    </label>

                    <div id="paymentDetails" style="display: none; margin-top: 15px; text-align: center;">

                        <p style="margin-bottom: 8px;">
                            AMOUNT TO PAY:
                            ₱<span id="paymentAmount">0.00</span>
                        </p>

                        <img
                            id="gcashQR"
                            src="assets/gcash-qr.jpg"
                            alt="GCash payment QR"
                            style="
                                display: none;
                                width: 260px;
                                max-width: 100%;
                                margin: 10px auto;
                                border-radius: 10px;
                            "
                        >

                        <img
                            id="bpiQR"
                            src="assets/bpi-qr.jpg"
                            alt="BPI payment QR"
                            style="
                                display: none;
                                width: 260px;
                                max-width: 100%;
                                margin: 10px auto;
                                border-radius: 10px;
                            "
                        >

                        <p style="
                            font-size: 12px;
                            margin-top: 8px;
                            color: #555;
                        ">
                            Scan the QR code and pay the total amount.
                            Your booking will remain pending until payment is verified.
                        </p>

                    </div>
                                    
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


<script>
document.querySelectorAll('[data-time-picker]').forEach(function (picker) {
    const toggle = picker.querySelector('.time-picker-toggle');
    const menu = picker.querySelector('.time-picker-menu');
    const hiddenInput = picker.querySelector('.time-picker-value');
    const options = picker.querySelectorAll('.time-option');

    toggle.addEventListener('click', function () {
        document.querySelectorAll('[data-time-picker].open').forEach(function (otherPicker) {
            if (otherPicker !== picker) {
                otherPicker.classList.remove('open');
                const otherToggle = otherPicker.querySelector('.time-picker-toggle');
                if (otherToggle) {
                    otherToggle.setAttribute('aria-expanded', 'false');
                }
            }
        });

        const isOpen = picker.classList.toggle('open');
        toggle.setAttribute('aria-expanded', String(isOpen));
    });

    options.forEach(function (option) {
        option.addEventListener('click', function () {
            hiddenInput.value = option.dataset.value;
            toggle.textContent = option.textContent.trim();

            options.forEach(function (item) {
                item.classList.remove('selected');
            });

            option.classList.add('selected');
            picker.classList.remove('open');
            toggle.setAttribute('aria-expanded', 'false');

            hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
        });
    });
});

document.addEventListener('click', function (event) {
    document.querySelectorAll('[data-time-picker].open').forEach(function (picker) {
        if (!picker.contains(event.target)) {
            picker.classList.remove('open');
            const toggle = picker.querySelector('.time-picker-toggle');
            if (toggle) {
                toggle.setAttribute('aria-expanded', 'false');
            }
        }
    });
});
</script>

<script>
(function () {

    const paymentMethod =
        document.getElementById('paymentMethod');

    const paymentDetails =
        document.getElementById('paymentDetails');

    const gcashQR =
        document.getElementById('gcashQR');

    const bpiQR =
        document.getElementById('bpiQR');

    const paymentAmount =
        document.getElementById('paymentAmount');

    const paymentBookingTotal =
        document.getElementById('bookingTotal');


    if (!paymentMethod) {
        return;
    }


    paymentMethod.addEventListener('change', function () {

        paymentDetails.style.display = 'block';

        if (paymentBookingTotal) {
            paymentAmount.textContent =
                paymentBookingTotal.textContent;
        }


        if (this.value === 'GCash') {

            gcashQR.style.display = 'block';
            bpiQR.style.display = 'none';

        } else if (this.value === 'BPI') {

            gcashQR.style.display = 'none';
            bpiQR.style.display = 'block';

        }

    });


    if (paymentBookingTotal) {

        const paymentObserver =
            new MutationObserver(function () {

                paymentAmount.textContent =
                    paymentBookingTotal.textContent;

            });


        paymentObserver.observe(
            paymentBookingTotal,
            {
                childList: true,
                characterData: true,
                subtree: true
            }
        );

    }

})();
</script>

<script>
document.querySelectorAll('.faq-question').forEach(function (question) {
    question.addEventListener('click', function () {
        const item = question.closest('.faq-item');
        const isOpen = item.classList.toggle('open');
        question.setAttribute('aria-expanded', String(isOpen));
    });
});
</script>

<script>
(function () {
    document.querySelectorAll('[data-policy]').forEach(function (link) {
        link.addEventListener('click', function () {
            const modal = document.getElementById(link.dataset.policy);
            if (modal) {
                modal.classList.add('open');
                modal.setAttribute('aria-hidden', 'false');
            }
        });
    });

    function closePolicy(modal) {
        if (!modal) return;
        modal.classList.remove('open');
        modal.setAttribute('aria-hidden', 'true');
    }

    document.querySelectorAll('[data-policy-close]').forEach(function (button) {
        button.addEventListener('click', function () {
            closePolicy(button.closest('.policy-modal'));
        });
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            document.querySelectorAll('.policy-modal.open').forEach(closePolicy);
        }
    });
})();
</script>

<script src="assets/app.js"></script>
</body>
</html>
