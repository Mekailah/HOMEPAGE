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
   RENTAL TOTAL CALCULATION
========================= */

const pickupDate =
    document.querySelector('input[name="pickup_date"]');

const returnDate =
    document.querySelector('input[name="return_date"]');

const bookingPrice =
    document.getElementById('bookingPrice');

const rentalDays =
    document.getElementById('rentalDays');

const bookingTotal =
    document.getElementById('bookingTotal');


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

    const start = new Date(pickupDate.value + 'T00:00:00');
    const end = new Date(returnDate.value + 'T00:00:00');

    const difference = end - start;

    let days = Math.ceil(
        difference / (1000 * 60 * 60 * 24)
    );

    if (difference < 0) {
        rentalDays.textContent = '0';
        bookingTotal.textContent = '0.00';
        return;
    }

if (days < 1) {
    days = 1;
}

    const price = parseFloat(bookingPrice.value) || 0;

    const total = days * price;

    rentalDays.textContent = days;
    bookingTotal.textContent = total.toFixed(2);
}


pickupDate?.addEventListener(
    'change',
    calculateRentalTotal
);

returnDate?.addEventListener(
    'change',
    calculateRentalTotal
);


/* =========================
   CLOSE MODAL
========================= */

document.querySelectorAll('.js-close-modal').forEach(element => {
    element.addEventListener('click', closeModal);
});

document.addEventListener('keydown', event => {
    if (event.key === 'Escape') {
        closeModal();
    }
});

function closeModal() {

    modal.classList.remove('open');

    modal.setAttribute('aria-hidden', 'true');

    document.body.style.overflow = '';
}