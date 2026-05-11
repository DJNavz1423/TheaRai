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

        const selectedBranch = document.getElementById('filter-branch').value;

        let rows = Array.from(document.querySelectorAll('.inventory-row'));
        const tbody = document.querySelector('tbody[role="rowgroup"]');

        rows.forEach(row => {
            const name = row.getAttribute('data-name');
            const rowCat = row.getAttribute('data-category');
        
            // 2. Get the branch data we tucked away in the HTML
            const branchStocks = JSON.parse(row.getAttribute('data-branch-stocks') || '{}');
            
            // 3. CONTEXT SWITCHING LOGIC
            let baseQty, basePrice, threshold;
            let matchesBranch = true;

            if (selectedBranch === 'all') {
                // Use the global totals from the view
                baseQty = parseFloat(row.getAttribute('data-qty'));
                basePrice = parseFloat(row.getAttribute('data-base-price'));
                threshold = parseFloat(row.getAttribute('data-threshold'));
            } else {
                // Use branch-specific numbers from the branch_inventory table
                const branchData = branchStocks[selectedBranch];
                if (branchData) {
                    baseQty = parseFloat(branchData.stock_quantity);
                    basePrice = parseFloat(branchData.purchase_price);
                    threshold = parseFloat(branchData.alert_threshold);
                } else {
                    // This branch doesn't have this ingredient
                    baseQty = 0;
                    basePrice = 0;
                    threshold = 0;
                    matchesBranch = false; // Ingredient doesn't exist in this branch's inventory
                }
            }

            const conv = parseFloat(row.getAttribute('data-conv'));
            const pAbbr = row.getAttribute('data-p-abbr');
            const sAbbr = row.getAttribute('data-s-abbr');

            // --- DYNAMIC MATH RECALCULATION (Uses the swapped values) ---
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

            // Update the UI
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

            // Only show the row if it matches everything AND exists in the selected branch
            if (matchesSearch && matchesCat && matchesStock && matchesBranch) {
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