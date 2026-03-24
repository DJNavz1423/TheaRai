document.querySelectorAll('.searchable-input').forEach(input => {
    input.addEventListener('input', function(e) {
        const listId = this.getAttribute('list');
        const options = document.querySelectorAll(`#${listId} option`);
        const targetId = this.getAttribute('data-target');
        const hiddenInput = document.getElementById(targetId);
        const inputValue = this.value;

        hiddenInput.value = ""; // Reset hidden ID if no match is found

        for (let option of options) {
            if (option.value === inputValue) {
                hiddenInput.value = option.getAttribute('data-id');
                break;
            }
        }
        
        // Debugging: Log the ID being sent to the server
        console.log(`Input: ${this.id} | Selected ID: ${hiddenInput.value}`);
    });
});