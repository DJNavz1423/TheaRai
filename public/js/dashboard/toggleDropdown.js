// public/js/dropdown.js
function closeAllDropdowns() {
    document.querySelectorAll('.dropdown-menu, .table-dropdown-menu').forEach(menu => {
        if (menu.style.display !== 'none' && menu.style.display !== '') {
            menu.style.display = 'none';
            menu.style.position = '';
            menu.style.top = '';
            menu.style.left = '';
        }
    });
}

function toggleDropdown(button) {
    const wrapper = button.closest('.dropdown-wrapper');
    const menu = wrapper.querySelector('.dropdown-menu, .table-dropdown-menu');

    document.querySelectorAll('.dropdown-menu, .table-dropdown-menu').forEach(m => {
        if (m !== menu) {
            m.style.display = 'none';
            m.style.position = '';
            m.style.top = '';
            m.style.left = '';
        }
    });

    if (menu) {
        const isHidden = menu.style.display === 'none' || menu.style.display === '';

        if(isHidden){
            menu.style.visibility = 'hidden';
            menu.style.display = 'flex';

            menu.style.position = 'fixed';
            menu.style.zIndex = '9999';

            const buttonRect = button.getBoundingClientRect();
            const menuHeight = menu.offsetHeight;
            const menuWidth = menu.offsetWidth;

            const spaceBelow = window.innerHeight - buttonRect.bottom;

            menu.style.left = (buttonRect.right - menuWidth) + 'px';

            if(spaceBelow < (menuHeight + 10)){
                menu.style.top = (buttonRect.top - menuHeight - 5) + 'px';
            } else {
                menu.style.top = (buttonRect.bottom + 5) + 'px';
            }

            menu.style.visibility = 'visible';
            
        } else {
            closeAllDropdowns();
        }
    }
}

window.addEventListener('click', function(e) {
    const clickedOutside = !e.target.closest('.dropdown-wrapper');
    const clickedInsideButton = e.target.closest('.dropdown-item') || e.target.closest('.dropdown-menu button') || e.target.closest('.dropdown-menu a');

    if (clickedOutside || clickedInsideButton) {
        closeAllDropdowns();
    }
});

window.addEventListener('scroll', function(e) {
    closeAllDropdowns();
}, true);

window.addEventListener('resize', function() {
    closeAllDropdowns();
});