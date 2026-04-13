// public/js/dropdown.js

function toggleDropdown(button) {
    const wrapper = button.closest('.dropdown-wrapper');
    const menu = wrapper.querySelector('.dropdown-menu, .table-dropdown-menu');

    document.querySelectorAll('.dropdown-menu, .table-dropdown-menu').forEach(m => {
        if (m !== menu) {
            m.style.display = 'none';
            m.classList.remove('drop-up');
        }
    });

    if (menu) {
        const isHidden = menu.style.display === 'none' || menu.style.display === '';

        if(isHidden){
            menu.style.visibility = 'hidden';
            menu.style.display = 'flex';
            menu.classList.remove('drop-up');

            const buttonRect = button.getBoundingClientRect();
            const menuHeight = menu.offsetHeight;
            const container = button.closest('.table-container');

            let spaceBelow = 0;

            if(container){
                const containerRect = container.getBoundingClientRect();

                spaceBelow = containerRect.bottom - buttonRect.bottom;
            } else{
                spaceBelow = window.innerHeight - buttonRect.bottom;
            }

            if(spaceBelow < (menuHeight + 6)){
                menu.classList.add('drop-up');
            }

            menu.style.visibility = 'visible';
        } else{
            menu.style.display = 'none';
            menu.classList.remove('drop-up');
        }
    
    }
}

window.addEventListener('click', function(e) {

    if (!e.target.closest('.dropdown-wrapper')) {

        document.querySelectorAll('.dropdown-menu, .table-dropdown-menu').forEach(menu => {
            menu.style.display = 'none';
            menu.classList.remove('drop-up');
        });
    }
});