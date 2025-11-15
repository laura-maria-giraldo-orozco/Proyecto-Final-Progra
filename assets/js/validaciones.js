// ==============================
// CARNICERÍA LA MORGUE
// Archivo: assets/js/validaciones.js
// Validaciones de formularios
// ==============================

document.addEventListener('DOMContentLoaded', function() {
    // Validar formularios de registro
    const registroForm = document.querySelector('form[action*="register"]');
    if (registroForm) {
        registroForm.addEventListener('submit', function(e) {
            const password = document.getElementById('password');
            const passwordConfirm = document.getElementById('password_confirm');
            
            if (password && passwordConfirm) {
                if (password.value !== passwordConfirm.value) {
                    e.preventDefault();
                    alert('Las contraseñas no coinciden.');
                    passwordConfirm.focus();
                    return false;
                }
                
                if (password.value.length < 4) {
                    e.preventDefault();
                    alert('La contraseña debe tener al menos 4 caracteres.');
                    password.focus();
                    return false;
                }
            }
        });
    }

    // Validar formularios de productos
    const productoForms = document.querySelectorAll('.product-form');
    productoForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const precio = document.getElementById('precio');
            const stock = document.getElementById('stock');
            
            if (precio) {
                const precioValue = parseFloat(precio.value);
                if (isNaN(precioValue) || precioValue <= 0) {
                    e.preventDefault();
                    alert('El precio debe ser un número válido mayor a 0.');
                    precio.focus();
                    return false;
                }
            }
            
            if (stock) {
                const stockValue = parseInt(stock.value);
                if (isNaN(stockValue) || stockValue < 0) {
                    e.preventDefault();
                    alert('El stock debe ser un número válido mayor o igual a 0.');
                    stock.focus();
                    return false;
                }
            }
        });
    });

    // Validar imágenes
    const imagenInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
    imagenInputs.forEach(input => {
        input.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const maxSize = 5 * 1024 * 1024; // 5MB
                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                
                if (!allowedTypes.includes(file.type)) {
                    alert('El archivo debe ser una imagen (JPEG, PNG, GIF o WebP).');
                    this.value = '';
                    return false;
                }
                
                if (file.size > maxSize) {
                    alert('La imagen no debe superar los 5MB.');
                    this.value = '';
                    return false;
                }
            }
        });
    });

    // Validar email
    const emailInputs = document.querySelectorAll('input[type="email"]');
    emailInputs.forEach(input => {
        input.addEventListener('blur', function() {
            const email = this.value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (email && !emailRegex.test(email)) {
                this.setCustomValidity('Por favor, ingrese un correo electrónico válido.');
            } else {
                this.setCustomValidity('');
            }
        });
    });
});

