import * as bootstrap from 'bootstrap';

window.bootstrap = bootstrap;

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.toast-autoshow').forEach((el) => new bootstrap.Toast(el).show());
});
