const menuToggle =
    document.querySelector('.menu-toggle');

const nav =
    document.querySelector('.main-nav');

const modal =
    document.getElementById('bookingModal');


/* =========================
   MOBILE NAVIGATION
========================= */

menuToggle?.addEventListener(
    'click',
    () => {

        const open =
            nav?.classList.toggle('open');

        menuToggle.setAttribute(
            'aria-expanded',
            String(open)
        );
    }
);


document
    .querySelectorAll('.main-nav a')
    .forEach(link => {

        link.addEventListener(
            'click',
            () => {

                nav?.classList.remove('open');

                menuToggle?.setAttribute(
                    'aria-expanded',
                    'false'
                );
            }
        );
    });


/* =========================
   BOOKING MODAL
========================= */

document
    .querySelectorAll('.js-book')
    .forEach(button => {

        button.addEventListener(
            'click',
            () => {

                const motorcycle =
                    button.dataset.motorcycle || '';

                const price =
                    button.dataset.price || '';


                const motorcycleField =
                    document.getElementById(
                        'bookingMotorcycle'
                    );

                const priceField =
                    document.getElementById(
                        'bookingPrice'
                    );


                if (motorcycleField) {
                    motorcycleField.value =
                        motorcycle;
                }


                if (priceField) {
                    priceField.value =
                        price;
                }


                clearBookingError();

                calculateRentalTotal();


                if (modal) {

                    modal.classList.add('open');

                    modal.setAttribute(
                        'aria-hidden',
                        'false'
                    );

                    document.body.style.overflow =
                        'hidden';
                }
            }
        );
    });


/* =========================
   BOOKING FIELDS
========================= */

const bookingForm =
    document.getElementById('bookingForm');

const pickupDate =
    document.querySelector(
        'input[name="pickup_date"]'
    );

const pickupTime =
    document.querySelector(
        'input[name="pickup_time"]'
    );

const returnDate =
    document.querySelector(
        'input[name="return_date"]'
    );

const returnTime =
    document.querySelector(
        'input[name="return_time"]'
    );

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

    const today =
        new Date();

    const year =
        today.getFullYear();

    const month =
        String(
            today.getMonth() + 1
        ).padStart(
            2,
            '0'
        );

    const day =
        String(
            today.getDate()
        ).padStart(
            2,
            '0'
        );

    return `${year}-${month}-${day}`;
}


const todayDate =
    getTodayDate();


if (pickupDate) {
    pickupDate.min =
        todayDate;
}


if (returnDate) {
    returnDate.min =
        todayDate;
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


    if (
        !pickupDate.value ||
        !returnDate.value
    ) {

        rentalDays.textContent =
            '0';

        bookingTotal.textContent =
            '0.00';

        return;
    }


    const start =
        new Date(
            pickupDate.value +
            'T00:00:00'
        );


    const end =
        new Date(
            returnDate.value +
            'T00:00:00'
        );


    const difference =
        end - start;


    if (
        !Number.isFinite(difference) ||
        difference < 0
    ) {

        rentalDays.textContent =
            '0';

        bookingTotal.textContent =
            '0.00';

        return;
    }


    let days =
        Math.ceil(
            difference /
            (
                1000 *
                60 *
                60 *
                24
            )
        );


    if (days < 1) {
        days = 1;
    }


    const price =
        parseFloat(
            bookingPrice.value
        ) || 0;


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
                pickupDate.value ||
                todayDate;


            if (
                returnDate.value &&
                pickupDate.value &&
                returnDate.value <
                pickupDate.value
            ) {

                returnDate.value =
                    '';
            }
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

let bookingErrorTimer = null;


function clearBookingError() {

    const errorBox =
        document.getElementById(
            'bookingFormError'
        );


    if (!errorBox) {
        return;
    }


    if (bookingErrorTimer) {

        clearTimeout(
            bookingErrorTimer
        );

        bookingErrorTimer =
            null;
    }


    errorBox.textContent =
        '';

    errorBox.classList.remove(
        'show'
    );

    errorBox.style.display =
        'none';
}


function showBookingError(message) {

    const errorBox =
        document.getElementById(
            'bookingFormError'
        );


    if (!errorBox) {
        return;
    }


    if (bookingErrorTimer) {

        clearTimeout(
            bookingErrorTimer
        );
    }


    errorBox.textContent =
        message;

    errorBox.classList.add(
        'show'
    );

    errorBox.style.display =
        'block';


    errorBox.scrollIntoView({
        behavior: 'smooth',
        block: 'nearest'
    });


    bookingErrorTimer =
        setTimeout(
            function () {

                errorBox.classList.remove(
                    'show'
                );

                errorBox.style.display =
                    'none';

                bookingErrorTimer =
                    null;
            },
            4000
        );
}


/* =========================
   TIME VALIDATION
========================= */

function isValidBookingTime(
    timeValue
) {

    if (
        typeof timeValue !== 'string' ||
        !timeValue
    ) {
        return false;
    }


    if (
        !/^\d{2}:\d{2}$/.test(
            timeValue
        )
    ) {
        return false;
    }


    const parts =
        timeValue.split(':');


    if (parts.length !== 2) {
        return false;
    }


    const hour =
        Number(parts[0]);

    const minute =
        Number(parts[1]);


    if (
        !Number.isInteger(hour) ||
        !Number.isInteger(minute)
    ) {
        return false;
    }


    /* Only :00 or :30 */
    if (
        minute !== 0 &&
        minute !== 30
    ) {
        return false;
    }


    /* Earliest: 6:00 AM */
    if (hour < 6) {
        return false;
    }


    /* Latest: 10:00 PM */
    if (hour > 22) {
        return false;
    }


    /* At 10 PM, only 10:00 PM */
    if (
        hour === 22 &&
        minute !== 0
    ) {
        return false;
    }


    return true;
}


/* =========================
   BOOKING VALIDATION
========================= */

bookingForm?.addEventListener(
    'submit',
    function (event) {

        clearBookingError();


        if (
            !pickupDate ||
            !pickupTime ||
            !returnDate ||
            !returnTime
        ) {
            return;
        }


        /* Date fields */
        if (
            !pickupDate.value ||
            !returnDate.value
        ) {

            event.preventDefault();

            showBookingError(
                'Please select both the pickup date and return date.'
            );

            return;
        }


        /* Pickup time */
        if (
            !isValidBookingTime(
                pickupTime.value
            )
        ) {

            event.preventDefault();


            showBookingError(
                'Please select a valid pickup time between 6:00 AM and 10:00 PM.'
            );


            return;
        }


        /* Return time */
        if (
            !isValidBookingTime(
                returnTime.value
            )
        ) {

            event.preventDefault();


            showBookingError(
                'Please select a valid return time between 6:00 AM and 10:00 PM.'
            );


            return;
        }


        const pickupDateTime =
            new Date(
                pickupDate.value +
                'T' +
                pickupTime.value +
                ':00'
            );


        const returnDateTime =
            new Date(
                returnDate.value +
                'T' +
                returnTime.value +
                ':00'
            );


        const currentDateTime =
            new Date();


        if (
            Number.isNaN(
                pickupDateTime.getTime()
            ) ||
            Number.isNaN(
                returnDateTime.getTime()
            )
        ) {

            event.preventDefault();


            showBookingError(
                'Please enter a valid booking date and time.'
            );


            return;
        }


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


            showBookingError(
                'Return date and time must be after the pickup date and time.'
            );


            return;
        }
    }
);


/* =========================
   CLOSE MODAL
========================= */

document
    .querySelectorAll(
        '.js-close-modal'
    )
    .forEach(element => {

        element.addEventListener(
            'click',
            closeModal
        );
    });


document.addEventListener(
    'keydown',
    event => {

        if (
            event.key ===
            'Escape'
        ) {

            closeModal();
        }
    }
);


function closeModal() {

    if (!modal) {
        return;
    }


    modal.classList.remove(
        'open'
    );


    modal.setAttribute(
        'aria-hidden',
        'true'
    );


    document.body.style.overflow =
        '';


    clearBookingError();
}