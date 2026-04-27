document.querySelectorAll('.unit-selector').forEach((el) => {
        new TomSelect(el, {
            create: false,
            maxItems: 1,
            placeholder: el.getAttribute('placeholder'),
            closeAfterSelect: true, 
            
            onItemAdd: function() {
                this.blur(); 
                this.wrapper.classList.remove('is-typing'); 
            },

            onChange: function(value) {
                if (!value || value === "") {
                    this.clear(); 
                    const emptyItem = this.control.querySelector('.item');
                    if (emptyItem) emptyItem.remove();
                    this.wrapper.classList.remove('is-typing');
                }
            },

            onBlur: function() {
                this.wrapper.classList.remove('is-typing');
                this.control_input.value = ''; // Instantly clears leftover text
            },

            onInitialize: function() {
                // 1. NATIVE INSTANT TYPING LISTENER (Bypasses Tom Select's lag)
                this.control_input.addEventListener('input', () => {
                    if (this.control_input.value.length > 0) {
                        this.wrapper.classList.add('is-typing');
                    } else {
                        this.wrapper.classList.remove('is-typing');
                    }
                });

                // 2. FORCE CLOSE if they click the already-selected option
                this.dropdown.addEventListener('click', (e) => {
                    const option = e.target.closest('.option');
                    if (option && option.classList.contains('selected')) {
                        this.blur();
                    }
                });
            },

            render: {
                no_results: function(data, escape) {
                    return '<div class="no-results">No units found for "' + escape(data.input) + '"</div>';
                },
            }
        });
    });

document.querySelectorAll('.unit-toggle').forEach(function(selectEl) {
    new TomSelect(selectEl, {
        controlInput: null,
        maxOptions: null,
        placeholder: "Select Unit",
        dropdownParent: 'body'
    });
});

