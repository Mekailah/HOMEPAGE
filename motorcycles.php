<?php
session_start();
require_once 'db.php';

$is_logged_in = isset($_SESSION['user_id']);

$motorcycles = [];

$stmt = $conn->prepare("
    SELECT
        motorcycle_name,
        price_per_day,
        image_path,
        available_units
    FROM motorcycle_inventory
    WHERE is_active = 1
    ORDER BY motorcycle_name ASC
");

$stmt->execute();

$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $motorcycles[] = $row;
}

$stmt->close();
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

        .booking-total p {
            font-size: 13px;
        }

        .booking-total span {
            font-size: 13px;
        }
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
            width: 100%;
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

        /* FOOTER LINK HOVER */
        .site-footer .footer-column a {
            transition: color 0.2s ease, opacity 0.2s ease;
        }

        .site-footer .footer-column a:hover,
        .site-footer .footer-column a:focus {
            text-decoration: underline;
            text-underline-offset: 4px;
            outline: none;
        }

        /* SUPPORT POLICY MODAL */
        .support-modal {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 10000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .support-modal.show {
            display: flex;
        }

        .support-modal-overlay {
            position: absolute;
            inset: 0;
            background: rgba(14, 27, 41, 0.72);
        }

        .support-modal-card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 560px;
            max-height: 80vh;
            overflow-y: auto;
            background: #ffffff;
            border-radius: 18px;
            padding: 28px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.24);
        }

        .support-modal-card h2 {
            margin: 0 35px 12px 0;
            color: #0E1B29;
            font-size: 21px;
        }

        .support-modal-card p {
            margin: 0;
            color: #52606d;
            font-size: 13px;
            line-height: 1.7;
        }

        .support-modal-close {
            position: absolute;
            top: 15px;
            right: 18px;
            border: 0;
            background: transparent;
            color: #0E1B29;
            font-size: 28px;
            line-height: 1;
            cursor: pointer;
        }

        .support-modal-close:hover,
        .support-modal-close:focus {
            color: #3C8D8A;
            outline: none;
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
                                src="<?= htmlspecialchars($motorcycle['image_path']) ?>"
                                alt="<?= htmlspecialchars($motorcycle['motorcycle_name']) ?>"
                            >

                        </div>

                        <div class="vehicle-info">
                            <p class="availability-count">
                                <?= (int) $motorcycle['available_units'] ?>
                            </p>

                            <h3>
                                <?= htmlspecialchars($motorcycle['motorcycle_name']) ?>
                            </h3>

                            <p>
                                ₱<?= htmlspecialchars($motorcycle['price_per_day']) ?>/Day
                            </p>

                            <?php if ($is_logged_in): ?>

                            <button
                                type="button"
                                class="button button-small js-book"
                                data-motorcycle="<?= htmlspecialchars($motorcycle['motorcycle_name']) ?>"
                                data-price="<?= htmlspecialchars(str_replace(['₱', '/Day', ','], '', $motorcycle['price_per_day'])) ?>"
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

            <a href="#" class="js-support-modal" data-support="terms">Terms &amp; Conditions</a><br>
            <a href="#" class="js-support-modal" data-support="privacy">Privacy Policy</a><br>
            <a href="#" class="js-support-modal" data-support="cancellation">Cancellation Policy</a><br>
            <a href="#" class="js-support-modal" data-support="fees">Fees and Charges</a>

        </div>

        <div class="footer-column contact-column">

            <h3>CONTACT US</h3>

            <p><span>☎</span> 63+ 926 465 8301</p>
            <p><span>✉</span> mekailahbangay@gmail.com</p>
             <p><img class="contact-pin-icon" src="assets/contact-pin.svg" alt=""> Dumaguete City</p>

        </div>

    </footer>


    <!-- SUPPORT POLICY MODAL -->
    <div
        class="support-modal"
        id="supportModal"
        aria-hidden="true"
    >
        <div class="support-modal-overlay js-close-support"></div>

        <div
            class="support-modal-card"
            role="dialog"
            aria-modal="true"
            aria-labelledby="supportModalTitle"
        >
            <button
                type="button"
                class="support-modal-close js-close-support"
                aria-label="Close"
            >
                &times;
            </button>

            <h2 id="supportModalTitle"></h2>
            <p id="supportModalText"></p>
        </div>
    </div>


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
                            required
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

                    <p style="font-size: 12px; margin-top: 8px; color: #555;">
                        Scan the QR code and pay the total amount.
                        Your booking will remain pending until payment is verified.
                    </p>

                    <label style="display: block; margin-top: 18px; text-align: left;">
                        PAYMENT PROOF
                        <input
                            type="file"
                            name="payment_proof"
                            id="paymentProof"
                            accept="image/jpeg,image/png"
                        >
                    </label>

                    <p style="
                        font-size: 11px;
                        margin-top: 5px;
                        color: #777;
                        text-align: left;
                    ">
                        Upload a screenshot of your successful GCash or BPI payment.
                        JPG or PNG only.
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

    <script>
    (function () {

        const paymentMethod = document.getElementById('paymentMethod');
        const paymentDetails = document.getElementById('paymentDetails');
        const gcashQR = document.getElementById('gcashQR');
        const bpiQR = document.getElementById('bpiQR');
        const paymentAmount = document.getElementById('paymentAmount');
        const paymentBookingTotal = document.getElementById('bookingTotal');
        const paymentProof = document.getElementById('paymentProof');

        if (!paymentMethod) {
            return;
        }

        paymentMethod.addEventListener('change', function () {
            paymentDetails.style.display = 'block';

            if (paymentProof) {
                paymentProof.required = true;
            }

            if (paymentBookingTotal) {
                paymentAmount.textContent = paymentBookingTotal.textContent;
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
            const paymentObserver = new MutationObserver(function () {
                paymentAmount.textContent = paymentBookingTotal.textContent;
            });

            paymentObserver.observe(paymentBookingTotal, {
                childList: true,
                characterData: true,
                subtree: true
            });
        }

    })();
    </script>

    <script>
        (function () {
            const supportModal =
                document.getElementById('supportModal');

            const supportTitle =
                document.getElementById('supportModalTitle');

            const supportText =
                document.getElementById('supportModalText');

            const supportContent = {
                terms: {
                    title: 'Terms & Conditions',
                    text: 'Renters must provide valid information and a valid driver’s license. The motorcycle must be used responsibly and returned on the agreed date, time, and location. The renter is responsible for following traffic laws and taking reasonable care of the motorcycle during the rental period.'
                },
                privacy: {
                    title: 'Privacy Policy',
                    text: 'RIDEGO RENTALS collects only the information needed to process bookings, verify identity and payment, and manage rental records. Personal information, driver’s license images, and payment proof are used only for rental-related purposes and should not be shared unnecessarily.'
                },
                cancellation: {
                    title: 'Cancellation Policy',
                    text: 'Customers who need to cancel a booking should contact RIDEGO RENTALS as soon as possible. Cancellation requests are subject to booking status and rental arrangements. Any applicable refund or adjustment will be reviewed by the owner based on the circumstances of the booking.'
                },
                fees: {
                    title: 'Fees and Charges',
                    text: 'The rental price is based on the selected motorcycle and rental duration. Additional charges may apply for late returns, damage, loss, or other costs caused during the rental period. Any additional charge should be explained to the customer by RIDEGO RENTALS.'
                }
            };

            function openSupportModal(type) {
                const content = supportContent[type];

                if (!content) {
                    return;
                }

                supportTitle.textContent = content.title;
                supportText.textContent = content.text;

                supportModal.classList.add('show');
                supportModal.setAttribute('aria-hidden', 'false');
            }

            function closeSupportModal() {
                supportModal.classList.remove('show');
                supportModal.setAttribute('aria-hidden', 'true');
            }

            document
                .querySelectorAll('.js-support-modal')
                .forEach(function (link) {
                    link.addEventListener('click', function (event) {
                        event.preventDefault();
                        openSupportModal(link.dataset.support);
                    });
                });

            document
                .querySelectorAll('.js-close-support')
                .forEach(function (element) {
                    element.addEventListener(
                        'click',
                        closeSupportModal
                    );
                });

            document.addEventListener('keydown', function (event) {
                if (
                    event.key === 'Escape' &&
                    supportModal.classList.contains('show')
                ) {
                    closeSupportModal();
                }
            });
        })();
    </script>

    <script src="assets/app.js"></script>

</body>
</html>