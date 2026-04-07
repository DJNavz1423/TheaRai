window.formatPeso = new Intl.NumberFormat('en-PH', { 
    style: 'currency', 
    currency: 'PHP' 
});

// 2. Automatically format anything with the .format-peso class on page load
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.format-peso').forEach(el => {
        const val = parseFloat(el.getAttribute('value')) || 0;
        el.textContent = window.formatPeso.format(val);
    });
});