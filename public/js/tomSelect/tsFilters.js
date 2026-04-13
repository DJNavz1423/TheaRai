document.addEventListener('DOMContentLoaded', function() {
    // 1. Initialize TomSelect
    document.querySelectorAll('.ts-filter').forEach(select => {
        new TomSelect(select, {
            create: false,
            controlInput: null, 
            maxOptions: 50,
            maxItems: 1,
            dropdownParent: 'body',
            closeAfterSelect: true,
        });
    });

    // 2. The Master Filtering & Sorting Function
    function applyInventoryFilters() {
        const searchQuery = document.getElementById('ingredientSearch').value.toLowerCase();
        const catId = document.getElementById('filter-category').value;
        const stockStatus = document.getElementById('filter-stock').value;
        const unitToggle = document.getElementById('filter-unit').value; // 'primary' or 'secondary'
        const sortType = document.getElementById('sort-items').value;

        let rows = Array.from(document.querySelectorAll('.inventory-row'));
        const tbody = document.querySelector('tbody[role="rowgroup"]');

        rows.forEach(row => {
            // Grab all the math variables from the row
            const name = row.getAttribute('data-name');
            const rowCat = row.getAttribute('data-category');
            const baseQty = parseFloat(row.getAttribute('data-qty'));
            const threshold = parseFloat(row.getAttribute('data-threshold'));
            const basePrice = parseFloat(row.getAttribute('data-base-price'));
            const conv = parseFloat(row.getAttribute('data-conv'));
            const pAbbr = row.getAttribute('data-p-abbr');
            const sAbbr = row.getAttribute('data-s-abbr');

            // --- DYNAMIC MATH RECALCULATION ---
            let currentQty, currentPrice, currentAbbr;

            if (unitToggle === 'secondary') {
                currentQty = baseQty * conv;
                currentPrice = basePrice / conv;
                currentAbbr = sAbbr;
            } else {
                currentQty = baseQty;
                currentPrice = basePrice;
                currentAbbr = pAbbr;
            }

            // Inject the new math into the HTML
            row.querySelector('.display-price').innerHTML = `&#8369;${currentPrice.toFixed(2)}`;
            row.querySelector('.display-unit').innerText = currentAbbr;
            row.querySelector('.display-qty').innerText = `${currentQty.toFixed(2)} ${currentAbbr}`;

            // --- FILTERING LOGIC ---
            const matchesSearch = name.includes(searchQuery);
            const matchesCat = (catId === 'all' || rowCat === catId);
            
            let matchesStock = true;
            if (stockStatus === 'in_stock') matchesStock = (baseQty > threshold);
            if (stockStatus === 'low_stock') matchesStock = (baseQty <= threshold && baseQty > 0);
            if (stockStatus === 'out_of_stock') matchesStock = (baseQty <= 0);

            if (matchesSearch && matchesCat && matchesStock) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        // --- SORTING LOGIC ---
        let visibleRows = rows.filter(row => row.style.display !== 'none');

        visibleRows.sort((a, b) => {
            const qtyA = parseFloat(a.getAttribute('data-qty'));
            const qtyB = parseFloat(b.getAttribute('data-qty'));
            const nameA = a.getAttribute('data-name');
            const nameB = b.getAttribute('data-name');
            const createdA = parseInt(a.getAttribute('data-created'));
            const createdB = parseInt(b.getAttribute('data-created'));

            switch(sortType) {
                case 'latest': return createdB - createdA;
                case 'qty_desc': return qtyB - qtyA;
                case 'qty_asc': return qtyA - qtyB;
                case 'name_asc': return nameA.localeCompare(nameB);
                case 'name_desc': return nameB.localeCompare(nameA);
            }
        });

        visibleRows.forEach(row => tbody.appendChild(row));
    }

    // 3. ATTACH THE EVENT LISTENERS (This is what was missing!)
    
    // Listen for typing in the search bar
    const searchBar = document.getElementById('ingredientSearch');
    if(searchBar) {
        searchBar.addEventListener('input', applyInventoryFilters);
    }
    
    // Listen for changes in ANY of the TomSelect dropdowns
    document.querySelectorAll('.ts-filter').forEach(select => {
        select.addEventListener('change', applyInventoryFilters);
    });
});