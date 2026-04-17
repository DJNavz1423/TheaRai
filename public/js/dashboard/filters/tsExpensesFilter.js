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

  function applyExpenseFilters(){
    const searchQuery = document.getElementById('expenseSearch').value.toLowerCase();
    const typeVal = document.getElementById('filter-type').value;
    const sourceVal = document.getElementById('filter-source').value;
    const sortType = document.getElementById('sort-items').value;
    
    let rows = Array.from(document.querySelectorAll('.expense-row'));
    const tbody = document.querySelector('tbody[role="rowgroup"]');

    //filtering
    rows.forEach(row => {
      const desc = row.getAttribute('data-desc');
      const type = row.getAttribute('data-type');
      const source = row.getAttribute('data-source');

      const matchesSearch = desc.includes(searchQuery) || type.includes(searchQuery) || source.includes(searchQuery);
      const matchesType = (typeVal === 'all' || type === typeVal);
      const matchesSource = (sourceVal === 'all' || source === sourceVal);
      
      if (matchesSearch && matchesType && matchesSource) {
          row.style.display = '';
      } else {
          row.style.display = 'none';
      }
    });

    //sorting
    let visibleRows = rows.filter(row => row.style.display !== 'none');

    visibleRows.sort((a, b) => {
      const amountA = parseFloat(a.getAttribute('data-amount'));
      const amountB = parseFloat(b.getAttribute('data-amount'));
      const descA = a.getAttribute('data-desc');
      const descB = b.getAttribute('data-desc');
      const createdA = parseInt(a.getAttribute('data-created')) || 0;
      const createdB = parseInt(b.getAttribute('data-created')) || 0;

      switch(sortType){
        case 'latest': return createdB - createdA;
        case 'amount_desc': return amountB - amountA;
        case 'amount_asc': return amountA - amountB;
        case 'desc_asc': return descA.localeCompare(descB);
        case 'desc_desc': return descB.localeCompare(descA);
        default: return 0;
      }
    });

    visibleRows.forEach(row => tbody.appendChild(row));
  }

  const searchBar = document.getElementById('expenseSearch');
  if(searchBar) {
      searchBar.addEventListener('input', applyExpenseFilters);
  }
  
  document.querySelectorAll('.ts-filter').forEach(select => {
      select.addEventListener('change', applyExpenseFilters);
  });
})