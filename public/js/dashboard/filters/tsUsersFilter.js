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

  function applyUserFilters(){
    const searchQuery = document.getElementById('userSearch').value.toLowerCase();
    const roleVal = document.getElementById('filter-role').value;
    const sortType = document.getElementById('sort-users').value;

    let rows = Array.from(document.querySelectorAll('.user-row'));
    const tbody = document.querySelector('tbody[role="rowgroup"]');


    //filtering
    rows.forEach(row => {
      const name = row.getAttribute('data-name');
      const rowVal = row.getAttribute('data-role');

      const matchesSearch = name.includes(searchQuery);
      const matchesRole = (roleVal === 'all' || rowVal === roleVal);

      if(matchesSearch && matchesRole){
        row.style.display = '';
      } else{
        row.style.display = 'none';
      }
    });

    //sorting
    let visibleRows = rows.filter(row => row.style.display !== 'none');

    visibleRows.sort ((a, b) => {
      const nameA = a.getAttribute('data-name');
      const nameB = b.getAttribute('data-name');
      const createdA = parseInt(a.getAttribute('data-created')) || 0;
      const createdB = parseInt(b.getAttribute('data-created')) || 0;

      switch(sortType){
        case 'latest' : return createdB - createdA;
        case 'name_asc' : return nameA.localeCompare(nameB);
        case 'name_desc' : return nameB.localeCompare(nameA);
        default: return 0;
      }
    });

    visibleRows.forEach(row => tbody.appendChild(row));
  }

  const searchBar = document.getElementById('userSearch');
  if(searchBar){
    searchBar.addEventListener('input', applyUserFilters);
  }

  document.querySelectorAll('.ts-filter').forEach(select => {
    select.addEventListener('change', applyUserFilters);
  });
});