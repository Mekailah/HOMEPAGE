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

<script src="assets/app.js"></script>
</body>
</html>
