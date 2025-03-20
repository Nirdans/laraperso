/**
 * Script principal de l'application
 */
document.addEventListener('DOMContentLoaded', function() {
    // Active les tooltips Bootstrap
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
    
    // Ajoute une confirmation pour les formulaires de suppression
    const deleteForms = document.querySelectorAll('form[data-confirm]');
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm(this.dataset.confirm || 'Êtes-vous sûr de vouloir effectuer cette action ?')) {
                e.preventDefault();
            }
        });
    });
    
    // Ajoute l'effet fade-in aux alertes
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        alert.classList.add('fade-in');
        
        // Auto-fermeture des alertes de succès après 5 secondes
        if (alert.classList.contains('alert-success')) {
            setTimeout(() => {
                alert.style.transition = 'opacity 1s';
                alert.style.opacity = 0;
                setTimeout(() => {
                    alert.remove();
                }, 1000);
            }, 5000);
        }
    });
});
