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

document.querySelectorAll('.js-book').forEach(button => {
    button.addEventListener('click', () => {
        modal.classList.add('open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    });
});

document.querySelectorAll('.js-close-modal').forEach(element => {
    element.addEventListener('click', closeModal);
});

document.addEventListener('keydown', event => {
    if (event.key === 'Escape') closeModal();
});

function closeModal() {
    modal.classList.remove('open');
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
}

document.getElementById('bookingForm')?.addEventListener('submit', event => {
    event.preventDefault();
    alert('BOOK & CONFIRM');
    closeModal();
});

