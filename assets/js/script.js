/**
 * LOKASAPA - Main JavaScript
 * Platform Wisata Berkelanjutan Indonesia
 */

// Format number to Indonesian Rupiah
function formatRupiah(angka) {
    return 'Rp ' + angka.toLocaleString('id-ID');
}

// Calculate total price (for booking)
function hitungTotal(hargaPerOrang, jumlahOrang) {
    return hargaPerOrang * jumlahOrang;
}

// Initialize rating stars interaction
function initRatingStars() {
    document.querySelectorAll('.rating-stars label').forEach(label => {
        label.addEventListener('click', function() {
            // Visual feedback
            this.style.transform = 'scale(1.2)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 200);
        });
    });
}

// Initialize payment option selection
function initPaymentOptions() {
    document.querySelectorAll('.payment-option').forEach(option => {
        option.addEventListener('click', function() {
            // Remove selected class from all options
            document.querySelectorAll('.payment-option').forEach(opt => {
                opt.classList.remove('selected');
            });
            
            // Add selected class to clicked option
            this.classList.add('selected');
            
            // Check the radio button
            this.querySelector('input[type="radio"]').checked = true;
        });
    });
}

// Auto-dismiss alerts after 5 seconds
function autoDismissAlerts() {
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
}

// Confirm delete actions
function confirmDelete(message = 'Yakin ingin menghapus data ini?') {
    return confirm(message);
}

// Initialize all interactive elements
document.addEventListener('DOMContentLoaded', function() {
    // Initialize rating stars if exists
    if (document.querySelector('.rating-stars')) {
        initRatingStars();
    }
    
    // Initialize payment options if exists
    if (document.querySelector('.payment-option')) {
        initPaymentOptions();
    }
    
    // Auto dismiss alerts
    autoDismissAlerts();
    
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href !== '#' && document.querySelector(href)) {
                e.preventDefault();
                document.querySelector(href).scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
});
