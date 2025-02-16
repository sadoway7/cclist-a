// Helper function to display error messages
function displayError(message) {
  const errorMessageDiv = document.getElementById('error-message');
    if (errorMessageDiv) {
        errorMessageDiv.textContent = message;
        errorMessageDiv.style.display = 'block'; // Make sure it's visible
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
      category: form.querySelector('#category').value,
      item: form.querySelector('#item').value,
      size: form.querySelector('#size').value,
      quantity_min: form.querySelector('#quantity_min').value,
      quantity_max: form.querySelector('#quantity_max').value,
      price: form.querySelector('#price').value,
      discount: form.querySelector('#discount').value,
      add_product_nonce: form.querySelector('#add_product_nonce').value // Include the nonce
  };

    // Basic client-side validation
    if (!formData.category || !formData.item || !formData.quantity_min || !formData.price) {
        displayError('Please fill in all required fields.');
        return;
    }

    if (isNaN(formData.quantity_min) || isNaN(formData.price)) {
        displayError('Quantity Min and Price must be numeric.');
        return;
    }
  
    if (formData.quantity_max && isNaN(formData.quantity_max)) {
      displayError('Quantity Max must be numeric.');
      return;
    }

    if (formData.discount && isNaN(formData.discount)) {
      displayError('Discount must be numeric.');
      return;
    }

  // Send AJAX request
  fetch('../admin/add-product.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify(formData),
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      // Close the modal
      document.getElementById('add-product-modal').style.display = 'none';
      // Display a success message (consider using a more specific element)
      displayError(data.message);
      // Reload the product list to show the new product
      location.reload();
    } else {
      displayError('Error adding product: ' + data.message);
    }
  })
  .catch(error => {
    displayError('Error adding product: ' + error);
  });
}

// Helper function to format the price break string
function formatPriceBreak(quantityMin, quantityMax, price, discount) {
    let priceBreakString = '';
    if (quantityMax !== null && quantityMax !== "") {
        priceBreakString = `${quantityMin}-${quantityMax} = $${price}`;
    } else {
        priceBreakString = `${quantityMin}+ = $${price}`;
    }
    if (parseFloat(discount) > 0) {
        const discounted_price = price - (price * discount);
        priceBreakString += ' (Discount: ' + (discount * 100) + '% = $' + discounted_price.toFixed(2) + ')';
    }
    return priceBreakString;
}

document.addEventListener('DOMContentLoaded', function() {
    // Attach event listener to the "Add Product" button in the modal
    const addProductButton = document.getElementById('submit-add-product');
    if (addProductButton) {
        addProductButton.addEventListener('click', handleAddProduct);
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
  const filterSelector = document.getElementById('filter_selector');
  if (filterSelector) {
    filterSelector.addEventListener('change', function() {
      const selectedFilters = Array.from(this.selectedOptions).map(option => option.value);

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
    });

    // Trigger change on load to handle any pre-selected filters
    filterSelector.dispatchEvent(new Event('change'));
  }

  // Add event listener for "Remove Selected Filters" button
  const removeFiltersButton = document.getElementById('remove_selected_filters');
  if (removeFiltersButton) {
    removeFiltersButton.addEventListener('click', function() {
      const filterSelector = document.getElementById('filter_selector');
      if (filterSelector) {
        Array.from(filterSelector.options).forEach(option => {
          option.selected = false;
        });
        filterSelector.dispatchEvent(new Event('change')); // Trigger change to update display
      }
    });
  }
  location.reload(true);
});
