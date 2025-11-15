// ==============================
// CARNICERÍA LA MORGUE
// Archivo: assets/js/alerts.js
// Sistema de alertas
// ==============================

document.addEventListener('DOMContentLoaded', function() {
    // Auto-ocultar alertas después de 5 segundos
    const alertas = document.querySelectorAll('.alerta');
    alertas.forEach(alerta => {
        if (alerta.classList.contains('alerta-exito')) {
            setTimeout(function() {
                alerta.style.transition = 'opacity 0.5s';
                alerta.style.opacity = '0';
                setTimeout(function() {
                    alerta.remove();
                }, 500);
            }, 5000);
        }
    });

    // Mostrar alertas desde URL
    const urlParams = new URLSearchParams(window.location.search);
    const mensaje = urlParams.get('mensaje');
    if (mensaje) {
        const alerta = document.createElement('div');
        alerta.className = 'alerta alerta-exito';
        alerta.textContent = decodeURIComponent(mensaje);
        const container = document.querySelector('.container') || document.querySelector('main');
        if (container) {
            container.insertBefore(alerta, container.firstChild);
        }
    }
});

