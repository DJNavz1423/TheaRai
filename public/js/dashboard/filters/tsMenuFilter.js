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
    const sortType = document.getElementById('sort-items').value;
    const statusVal = document.getElementById('filter-status').value;

    let rows = Array.from(document.querySelectorAll('.menu-row'));
    const tbody = document.querySelector('tbody[role="rowgroup"]');

    //filtering

    rows.forEach(row => {
      const name = row.getAttribute('data-name');
      const rowCat = row.getAttribute('data-category');
      const rowStatus = row.getAttribute('data-status');
      
      const matchesSearch = name.includes(searchQuery);
      const matchesCat = (catId === 'all' || rowCat === catId);
      
      const matchesStatus = (statusVal === 'all' || rowStatus === statusVal);

      if(matchesSearch && matchesCat && matchesStatus) {
         row.style.display = '';
      } else {
        row.style.display = 'none';
      }
    });

    //sorting
    let visibleRows = rows.filter(row => row.style.display !== 'none');

    visibleRows.sort((a, b) => {
      const priceA = parseFloat(a.getAttribute('data-price'));
      const priceB = parseFloat(b.getAttribute('data-price'));
      const nameA = a.getAttribute('data-name');
      const nameB = b.getAttribute('data-name');
      const createdA = parseInt(a.getAttribute('data-created')) || 0;
      const createdB = parseInt(b.getAttribute('data-created')) || 0;

      switch(sortType) {
        case 'latest' : return createdB - createdA;
        case 'price_desc' : return priceB - priceA;
        case 'price_asc' : return priceA - priceB;
        case 'name_asc' : return nameA.localeCompare(nameB);
        case 'name_desc' : return nameB.localeCompare(nameA);
        default: return 0;
      }
    });

    visibleRows.forEach(row => tbody.appendChild(row));
  }

  const searchBar = document.getElementById('menuSearch');
  if(searchBar){
    searchBar.addEventListener('input', applyMenuFilters);
  }

  document.querySelectorAll('.ts-filter').forEach(select => {
    select.addEventListener('change', applyMenuFilters);
  });
})