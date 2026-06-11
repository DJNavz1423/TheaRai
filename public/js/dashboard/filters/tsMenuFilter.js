document.addEventListener('DOMContentLoaded', function(){
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

  function applyMenuFilters(){
    const searchQuery = document.getElementById('menuSearch').value.toLowerCase();
    const catId = document.getElementById('filter-category').value;
    const branchId = document.getElementById('filter-branch').value;
    const sortType = document.getElementById('sort-items').value;

    let rows = Array.from(document.querySelectorAll('.menu-row'));
    const tbody = document.querySelector('tbody[role="rowgroup"]');

    // =====================
    // FILTERING
    // =====================
    rows.forEach(row => {

        const name = row.dataset.name;
        const rowCat = row.dataset.category;

        let matchesBranch = true;

        const branches = JSON.parse(row.dataset.branches || '{}');

        if(branchId !== 'all'){
            matchesBranch = branches[branchId] === true;
        }

        const matchesSearch = name.includes(searchQuery);
        const matchesCat = catId === 'all' || rowCat === catId;

        row.style.display =
            matchesSearch &&
            matchesCat &&
            matchesBranch
                ? ''
                : 'none';
    });

    // =====================
    // UPDATE STATUS TEXT
    // =====================
    rows.forEach(row => {

        const statusCell = row.querySelector('.status-display');

        if(!statusCell) return;

        if(branchId === 'all') {

            const count = row.dataset.availableCount;

            statusCell.textContent =
                `Avail in ${count} branch${count != 1 ? 'es' : ''}`;

        } else {

            const branches = JSON.parse(row.dataset.branches || '{}');

            statusCell.textContent =
                branches[branchId] === true
                    ? 'Available'
                    : 'Unavailable';
        }
    });

    // =====================
    // SORTING
    // =====================
    let visibleRows = rows.filter(
        row => row.style.display !== 'none'
    );

    visibleRows.sort((a, b) => {

        const priceA = parseFloat(a.dataset.price);
        const priceB = parseFloat(b.dataset.price);

        const nameA = a.dataset.name;
        const nameB = b.dataset.name;

        const createdA = parseInt(a.dataset.created) || 0;
        const createdB = parseInt(b.dataset.created) || 0;

        switch(sortType) {
            case 'latest':
                return createdB - createdA;

            case 'price_desc':
                return priceB - priceA;

            case 'price_asc':
                return priceA - priceB;

            case 'name_asc':
                return nameA.localeCompare(nameB);

            case 'name_desc':
                return nameB.localeCompare(nameA);

            default:
                return 0;
        }
    });

    visibleRows.forEach(row => tbody.appendChild(row));
  }

  const searchBar = document.getElementById('menuSearch');
  if(searchBar){
    searchBar.addEventListener('input', applyMenuFilters);
  }

  document.querySelectorAll('.ts-filter').forEach(select => {
    if (select.tomselect) {
        select.tomselect.on('change', applyMenuFilters);
    } else {
        select.addEventListener('change', applyMenuFilters);
    }
  });
})