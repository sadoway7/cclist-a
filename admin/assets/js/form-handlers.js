document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.duplicate-form').forEach(function(form) {
        form.addEventListener('submit', function(event) {
            event.preventDefault();

            // Get values from the duplicate form's hidden inputs
            var category = this.querySelector('[name="duplicate_category"]').value;
            var item = this.querySelector('[name="duplicate_item"]').value;
            var size = this.querySelector('[name="duplicate_size"]').value;

            // Populate the "Add Product" form
            document.getElementById('category').value = category;
            document.getElementById('item').value = item;
            document.getElementById('size').value = size;
            
            //Clear other inputs
            document.getElementById('quantity_min').value = '';
            document.getElementById('quantity_max').value = '';
            document.getElementById('price').value = '';
            document.getElementById('discount').value = '';

            // Scroll to the "Add Product" form
            document.getElementById('add-product-form').scrollIntoView({ behavior: 'smooth' });
        });
    });

    // Modal functionality
    var modal = document.getElementById('add-product-modal');
    var openButton = document.getElementById('add-product-button');
    var closeButton = document.querySelector('.close-button');

    if (modal && openButton && closeButton) {
        openButton.addEventListener('click', function() {
            modal.style.display = 'flex';
        });

        closeButton.addEventListener('click', function() {
            modal.style.display = 'none';
        });

        window.addEventListener('click', function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        });
    }

    // JSON validation for import form
    var importTextarea = document.getElementById('import_data');
    var jsonPreview = document.getElementById('json-preview');
    var isValidInput = document.getElementById('is_json_valid');

    if (importTextarea && jsonPreview && isValidInput) {
        importTextarea.addEventListener('input', function() {
            try {
                var parsed = JSON.parse(importTextarea.value);
                jsonPreview.textContent = JSON.stringify(parsed, null, 2); // Pretty print JSON
                jsonPreview.style.color = 'green';
                isValidInput.value = '1';
            } catch (error) {
                jsonPreview.textContent = 'Invalid JSON: ' + error.message;
                jsonPreview.style.color = 'red';
                isValidInput.value = '0';
            }
        });
    }
});