const menuToggle = document.querySelector('.menu-toggle');
const nav = document.querySelector('.main-nav');
const modal = document.getElementById('bookingModal');

menuToggle?.addEventListener('click', () => {
    const open = nav.classList.toggle('open');
    menuToggle.setAttribute('aria-expanded', String(open));
});

document.querySelectorAll('.main-nav a').forEach(link => {
    link.addEventListener('click', () => {
        nav.classList.remove('open');
        menuToggle?.setAttribute('aria-expanded', 'false');
    });
});


/* =========================
   BOOKING MODAL
========================= */

document.querySelectorAll('.js-book').forEach(button => {

    button.addEventListener('click', () => {

        const motorcycle = button.dataset.motorcycle;
        const price = button.dataset.price;

        const motorcycleField =
            document.getElementById('bookingMotorcycle');

        const priceField =
            document.getElementById('bookingPrice');

        if (motorcycleField) {
            motorcycleField.value = motorcycle || '';
        }

        if (priceField) {
            priceField.value = price || '';
        }

        calculateRentalTotal();

        modal.classList.add('open');
        modal.setAttribute('aria-hidden', 'false');

        document.body.style.overflow = 'hidden';
    });

});


/* =========================
   BOOKING FIELDS
========================= */

const bookingForm =
    document.getElementById('bookingForm');

const pickupDate =
    document.querySelector('input[name="pickup_date"]');

const pickupTime =
    document.querySelector('input[name="pickup_time"]');

const returnDate =
    document.querySelector('input[name="return_date"]');

const returnTime =
    document.querySelector('input[name="return_time"]');

const bookingPrice =
    document.getElementById('bookingPrice');

const rentalDays =
    document.getElementById('rentalDays');

const bookingTotal =
    document.getElementById('bookingTotal');


/* =========================
   PREVENT PAST DATES
========================= */

function getTodayDate() {

    const today = new Date();

    const year = today.getFullYear();

    const month =
        String(today.getMonth() + 1).padStart(2, '0');

    const day =
        String(today.getDate()).padStart(2, '0');

    return `${year}-${month}-${day}`;
}


const todayDate = getTodayDate();

if (pickupDate) {
    pickupDate.min = todayDate;
}

if (returnDate) {
    returnDate.min = todayDate;
}


/* =========================
   RENTAL TOTAL CALCULATION
========================= */

function calculateRentalTotal() {

    if (
        !pickupDate ||
        !returnDate ||
        !bookingPrice ||
        !rentalDays ||
        !bookingTotal
    ) {
        return;
    }

    if (!pickupDate.value || !returnDate.value) {
        rentalDays.textContent = '0';
        bookingTotal.textContent = '0.00';
        return;
    }

    const start =
        new Date(
            pickupDate.value + 'T00:00:00'
        );

    const end =
        new Date(
            returnDate.value + 'T00:00:00'
        );

    const difference =
        end - start;

    let days =
        Math.ceil(
            difference /
            (1000 * 60 * 60 * 24)
        );

    if (difference < 0) {
        rentalDays.textContent = '0';
        bookingTotal.textContent = '0.00';
        return;
    }

    if (days < 1) {
        days = 1;
    }

    const price =
        parseFloat(bookingPrice.value) || 0;

    const total =
        days * price;

    rentalDays.textContent =
        days;

    bookingTotal.textContent =
        total.toFixed(2);
}


pickupDate?.addEventListener(
    'change',
    function () {

        if (returnDate) {
            returnDate.min =
                pickupDate.value || todayDate;
        }

        calculateRentalTotal();
    }
);


returnDate?.addEventListener(
    'change',
    calculateRentalTotal
);

/* =========================
   BOOKING ERROR MESSAGE
========================= */

function showBookingError(message) {

    const errorBox =
        document.getElementById('bookingFormError');

    if (!errorBox) {
        return;
    }

    errorBox.textContent = message;
    errorBox.classList.add('show');

    errorBox.scrollIntoView({
        behavior: 'smooth',
        block: 'nearest'
    });

    setTimeout(function () {
        errorBox.classList.remove('show');
    }, 4000);
}
/* =========================
   BOOKING VALIDATION
========================= */

bookingForm?.addEventListener(
    'submit',
    function (event) {

        if (
            !pickupDate ||
            !pickupTime ||
            !returnDate ||
            !returnTime
        ) {
            return;
        }


        const pickupDateTime =
            new Date(
                pickupDate.value +
                'T' +
                pickupTime.value
            );


        const returnDateTime =
            new Date(
                returnDate.value +
                'T' +
                returnTime.value
            );


        const currentDateTime =
            new Date();


        /* Pickup cannot be in the past */
        if (
            pickupDateTime <
            currentDateTime
        ) {

            event.preventDefault();

            showBookingError(
                'Pickup date and time cannot be in the past.'
            );

            return;
        }


        /* Return must be after pickup */
        if (
            returnDateTime <=
            pickupDateTime
        ) {

            event.preventDefault();

            alert(
                'Return date and time must be after the pickup date and time.'
            );

            return;
        }
    }
);


/* =========================
   CLOSE MODAL
========================= */

document.querySelectorAll('.js-close-modal').forEach(element => {

    element.addEventListener(
        'click',
        closeModal
    );

});


document.addEventListener(
    'keydown',
    event => {

        if (event.key === 'Escape') {
            closeModal();
        }

    }
);


function closeModal() {

    modal.classList.remove('open');

    modal.setAttribute(
        'aria-hidden',
        'true'
    );

    document.body.style.overflow = '';
}