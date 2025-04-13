function toggleMenu() {
    const nav = document.getElementById('navLinks');
    const icon = document.getElementById('menuIcon');
    const body = document.body;

    nav.classList.toggle('active');
    icon.classList.toggle('open');
    body.classList.toggle('no-scroll');
}
