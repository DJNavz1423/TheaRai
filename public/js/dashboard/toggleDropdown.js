// public/js/dropdown.js

function toggleDropdown(button) {
    // 1. Find the wrapper and the specific menu inside it
    const wrapper = button.closest('.dropdown-wrapper');
    // We look for either class so it works for both the header and the table rows
    const menu = wrapper.querySelector('.dropdown-menu, .table-dropdown-menu');

    // 2. Close all *other* dropdowns on the page before opening this one
    document.querySelectorAll('.dropdown-menu, .table-dropdown-menu').forEach(m => {
        if (m !== menu) {
            m.style.display = 'none';
        }
    });

    // 3. Toggle the display of the target menu
    if (menu) {
        // Toggle between 'flex' (or 'block') and 'none'
        menu.style.display = (menu.style.display === 'none' || menu.style.display === '') ? 'flex' : 'none';
    }
}

// 4. Safety feature: Close dropdowns if the user clicks anywhere outside of them
window.addEventListener('click', function(e) {
    // If the click did NOT happen inside a dropdown wrapper...
    if (!e.target.closest('.dropdown-wrapper')) {
        // Close all of them
        document.querySelectorAll('.dropdown-menu, .table-dropdown-menu').forEach(menu => {
            menu.style.display = 'none';
        });
    }
});