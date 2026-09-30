/* ==========================================================================
   SICOM - Vanilla JS Application Helpers
   ========================================================================== */

document.addEventListener('DOMContentLoaded', function() {
    console.log('SICOM Frontend App Initialized.');
    
    // Auto fadeout flash alerts after 5s
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.style.display = 'none';
            }, 500);
        }, 5000);
    });
});
