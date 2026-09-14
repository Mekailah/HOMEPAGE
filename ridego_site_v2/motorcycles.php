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

    <style>
        .booking-form-error {
            display: none;
            margin: 0 0 18px;
            padding: 11px 14px;
            border-radius: 10px;
            background: #fff1f0;
            color: #b42318;
            font-size: 12px;
            font-weight: 600;
            line-height: 1.5;
        }

        input[name="pickup_date"],
            input[name="return_date"] {
                width: 220px;
                max-width: 100%;
                box-sizing: border-box;
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
            width: 100%;
            max-height: 190px;
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
            padding: 9px 12px;
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
    </style>

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

            <div
                id="bookingFormError"
                class="booking-form-error"
                role="alert"
                aria-live="polite"
            ></div>

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
                        >
                    </div>
                </label>

                <label>
                    RETURN DATE
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
            const hiddenInput = picker.querySelector('.time-picker-value');
            const options = picker.querySelectorAll('.time-option');

            toggle.addEventListener('click', function () {
                document.querySelectorAll('[data-time-picker].open').forEach(function (otherPicker) {
                    if (otherPicker !== picker) {
                        otherPicker.classList.remove('open');

                        const otherToggle =
                            otherPicker.querySelector('.time-picker-toggle');

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

                    hiddenInput.dispatchEvent(
                        new Event('change', { bubbles: true })
                    );
                });
            });
        });

        document.addEventListener('click', function (event) {
            document.querySelectorAll('[data-time-picker].open').forEach(function (picker) {
                if (!picker.contains(event.target)) {
                    picker.classList.remove('open');

                    const toggle =
                        picker.querySelector('.time-picker-toggle');

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