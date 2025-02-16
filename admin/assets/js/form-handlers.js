// Helper function to display error messages
function displayError(message) {
    const errorMessageDiv = document.getElementById('error-message');
    if (errorMessageDiv) {
        errorMessageDiv.textContent = message;
        errorMessageDiv.style.display = 'block'; // Make sure it's visible
        // Scroll to error message
        errorMessageDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
    } else {
        alert(message); // Fallback to alert if div not found
    }
}

// Function to handle adding a product via AJAX
function handleAddProduct(event) {
    event.preventDefault();

    // Collect form data
    const form = document.getElementById('add-product-form');
    const formData = {
        category: form.querySelector('#category').value.trim(),
        item: form.querySelector('#item').value.trim(),
        size: form.querySelector('#size').value.trim(),
        quantity_min: form.querySelector('#quantity_min').value.trim(),
        quantity_max: form.querySelector('#quantity_max').value.trim(),
        price: form.querySelector('#price').value.trim(),
        discount: form.querySelector('#discount').value.trim() || '0',
        add_product_nonce: form.querySelector('#add_product_nonce').value,
        action: 'add_product' // WordPress AJAX action
    };

    // Basic client-side validation
    if (!formData.category || !formData.item || !formData.quantity_min || !formData.price) {
        displayError('Please fill in all required fields (Category, Item, Quantity Min, Price).');
        return;
    }

    if (isNaN(formData.quantity_min) || isNaN(formData.price)) {
        displayError('Quantity Min and Price must be numeric values.');
        return;
    }
  
    if (formData.quantity_max && isNaN(formData.quantity_max)) {
        displayError('Quantity Max must be a numeric value.');
        return;
    }

    if (formData.discount && isNaN(formData.discount)) {
        displayError('Discount must be a numeric value between 0 and 1.');
        return;
    }

    // Additional validation
    if (parseFloat(formData.price) <= 0) {
        displayError('Price must be greater than 0.');
        return;
    }

    if (parseInt(formData.quantity_min) < 0) {
        displayError('Quantity Min cannot be negative.');
        return;
    }

    if (formData.quantity_max && parseInt(formData.quantity_max) <= parseInt(formData.quantity_min)) {
        displayError('Quantity Max must be greater than Quantity Min.');
        return;
    }

    if (formData.discount && (parseFloat(formData.discount) < 0 || parseFloat(formData.discount) > 1)) {
        displayError('Discount must be between 0 and 1 (e.g., 0.5 for 50% discount).');
        return;
    }

    // Log the form data for debugging
    console.log('Submitting product data:', formData);

    // Send AJAX request
    fetch(ajaxurl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(formData),
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            // Clear the form
            form.reset();
            // Display a success message
            displayError('Product added successfully!');
            // Reload the product list to show the new product
            location.reload();
        } else {
            displayError('Error adding product: ' + (data.data || data.message));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        displayError('Error adding product: ' + error);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Attach event listener to the "Add Product" submit button
    const submitProductBtn = document.getElementById('submit-add-product');
    if (submitProductBtn) {
        submitProductBtn.addEventListener('click', handleAddProduct);
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

    // Dynamic filter handling
    const filterCheckboxes = document.querySelectorAll('input[name="filter_selector[]"]');
    if (filterCheckboxes.length > 0) {
        function updateFilterVisibility() {
            const selectedFilters = Array.from(document.querySelectorAll('input[name="filter_selector[]"]:checked')).map(checkbox => checkbox.value);

            // If no filters are selected, enforce defaults
            if (selectedFilters.length === 0) {
                ['category', 'size', 'search'].forEach(filter => {
                    const checkbox = document.getElementById(`filter_selector_${filter}`);
                    if (checkbox) {
                        checkbox.checked = true;
                    }
                });
                selectedFilters = ['category', 'size', 'search']; // Update selectedFilters array
            }

            // Hide all filter containers
            document.querySelectorAll('[id$=_filter_container]').forEach(container => {
                container.style.display = 'none';
            });

            // Show selected filter containers
            selectedFilters.forEach(filter => {
                const container = document.getElementById(filter + '_filter_container');
                if (container) {
                    container.style.display = 'block';
                }
            });
        }

        // Attach event listeners to filter checkboxes
        filterCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateFilterVisibility);
        });

        // Initial visibility update (for page load with pre-selected filters)
        updateFilterVisibility();

        // Add event listener for "Remove Selected Filters" button
        const removeFiltersButton = document.getElementById('remove_selected_filters');
        if (removeFiltersButton) {
            removeFiltersButton.addEventListener('click', function() {
                // Reset all form inputs
                const filterForm = document.querySelector('.filter-form');
                if (filterForm) {
                    // Clear text and number inputs
                    filterForm.querySelectorAll('input[type="text"], input[type="number"]').forEach(input => {
                        input.value = '';
                    });
                    
                    // Reset selects to first option
                    filterForm.querySelectorAll('select').forEach(select => {
                        select.selectedIndex = 0;
                    });
                    
                    // Uncheck checkboxes
                    filterForm.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                        checkbox.checked = false;
                    });

                    // Reset filter visibility
                    updateFilterVisibility();
                    
                    // Submit the form to refresh results
                    filterForm.submit();
                }
            });
        }
    }
});
