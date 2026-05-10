// admin/js/admin_scripts.js

document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.querySelector('.menu-toggle');
    const sidebar = document.querySelector('.admin-sidebar');
    const adminContent = document.querySelector('.admin-content'); // Get the main content area

    if (menuToggle && sidebar && adminContent) {
        menuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            // Optional: Add a class to the content to shift it, or just let sidebar overlay
             adminContent.classList.toggle('sidebar-active'); 
        });

        // Optional: Close sidebar if clicked outside (for overlaying sidebar)
         document.addEventListener('click', function(event) {
            if (!sidebar.contains(event.target) && !menuToggle.contains(event.target) && sidebar.classList.contains('active') && window.innerWidth <= 992) {
                 sidebar.classList.remove('active');
             }
         });
    }

    // Add any other admin-specific JavaScript here,
    // e.g., for modal windows, form validations, dynamic updates, charts.
});
