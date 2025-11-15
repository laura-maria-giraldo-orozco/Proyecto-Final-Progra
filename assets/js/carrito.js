// ==============================
// CARNICERÍA LA MORGUE
// Archivo: assets/js/carrito.js
// Funcionalidades del carrito
// ==============================

document.addEventListener('DOMContentLoaded', function() {
    // Actualizar cantidad en carrito
    const cantidadInputs = document.querySelectorAll('input[name="cantidad"]');
    cantidadInputs.forEach(input => {
        input.addEventListener('change', function() {
            const max = parseInt(this.getAttribute('max'));
            const value = parseInt(this.value);
            
            if (value > max) {
                this.value = max;
                alert('La cantidad no puede ser mayor al stock disponible: ' + max);
            } else if (value < 1) {
                this.value = 1;
            }
        });
    });

    // Confirmar eliminación de productos
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('¿Está seguro de eliminar este producto del carrito?')) {
                e.preventDefault();
            }
        });
    });

    // Confirmar vaciar carrito
    const vaciarButtons = document.querySelectorAll('a[href*="vaciar"]');
    vaciarButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('¿Está seguro de vaciar todo el carrito?')) {
                e.preventDefault();
            }
        });
    });
});

