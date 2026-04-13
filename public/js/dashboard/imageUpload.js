document.addEventListener('DOMContentLoaded', function() {
    // 1. Grab EVERY upload box on the page (both Add and Edit modals)
    const uploadBoxes = document.querySelectorAll('.image-upload');

    // 2. Loop through each box and apply the logic dynamically
    uploadBoxes.forEach(box => {
        const fileInput = box.querySelector('input[type="file"]');
        const uploadIcon = box.querySelector('.icon-wrapper');

        // Handle clicking the box to trigger the file input
        box.addEventListener('click', function(e) {
            // Prevent click if clicking the remove button, or if a preview figure already exists inside this specific box
            if (e.target.closest('.remove-img-btn') || box.querySelector('.image-preview')) {
                return;
            }
            fileInput.click();
        });

        // Handle the file selection and preview generation
        fileInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const imageUrl = URL.createObjectURL(file);
                uploadIcon.style.display = 'none';

                const figure = document.createElement('figure');
                figure.className = 'image-preview'; // No need for specific IDs anymore
                figure.innerHTML = `
                    <img src="${imageUrl}" alt="Preview">
                    <button type="button" class="remove-img-btn" title="Remove image">✕</button>
                `;

                box.appendChild(figure);

                // Handle the Remove Button Click for this specific preview
                figure.querySelector('.remove-img-btn').addEventListener('click', function(e) {
                    e.stopPropagation(); 
                    
                    fileInput.value = ''; // Clear the input
                    figure.remove(); // Destroy the preview
                    uploadIcon.style.display = 'flex'; // Bring back the SVG
                    
                    URL.revokeObjectURL(imageUrl); // Free memory
                });
            }
        });
    });
});