// Function to handle the "Edit" button click
function handleEditClick(event) {
  event.preventDefault();

  const row = this.closest('tr');
  const cells = row.querySelectorAll('td');
  const editButton = this;

  // Store original values and replace with input fields
  cells.forEach((cell, index) => {
    if (index > 0 && index < cells.length - 1) { // Skip the checkbox and actions cell
      const originalValue = cell.textContent.trim();
      cell.setAttribute('data-original-value', originalValue);

      // Handle price break cell differently
      if (index === 4) {
          const match = originalValue.match(/(\d+(?:-\d+)?)\s*=.*?\$([\d.]+)/);

          let quantityValue = '';
          let priceValue = '';

          if (match) {
              quantityValue = match[1];
              priceValue = match[2];
          }
          const quantityMaxMatch = originalValue.match(/-(\d+)/);
          const quantityMaxValue = quantityMaxMatch ? quantityMaxMatch[1] : '';

          const discountMatch = originalValue.match(/Discount:\s*([\d.]+)%/);
          const discountValue = discountMatch ? (parseFloat(discountMatch[1])/100) : '';

          cell.innerHTML = `
              <input type="text" value="${quantityValue.split('-')[0]}" data-original-value="${quantityValue.split('-')[0]}" style="width: 50px;" class="edit-quantity-min"> -
              <input type="text" value="${quantityMaxValue}" data-original-value="${quantityMaxValue}" style="width: 50px;" class="edit-quantity-max"> = 
              $<input type="text" value="${priceValue}" data-original-value="${priceValue}" style="width: 60px;" class="edit-price">
              <br>
              Discount: <input type="text" value="${discountValue}" data-original-value="${discountValue}" style="width: 50px;" class="edit-discount">
          `;
      }
      else {
        const input = document.createElement('input');
        input.type = 'text';
        input.value = originalValue;
        input.style.width = (index === 1 || index === 3) ? '80px' : '120px' //set width for category and size to be smaller
        cell.innerHTML = '';
        cell.appendChild(input);
      }
    }
  });

  // Change "Edit" to "Save"
  editButton.value = 'Save';
  editButton.classList.remove('edit-product');
  editButton.classList.add('save-product');
  editButton.removeEventListener('click', handleEditClick); // Remove edit listener
  editButton.addEventListener('click', handleSaveClick); 
}

/**
 * Handles the click event on the "Save" button in the product table.
 * Gathers updated values from input fields, sends an AJAX request to update the product,
 * and replaces input fields with the new values upon success.
 *
 * @param {Event} event The click event.
 */
function handleSaveClick(event) {
    event.preventDefault();

    const row = this.closest('tr');
    const cells = row.querySelectorAll('td');
    const saveButton = this;
    const productId = this.closest('form').querySelector('[name="edit_id"]').value;

    // Gather updated values from the input fields
    const updatedValues = {
        id: productId,
        category: cells[1].querySelector('input').value, // Get value from input in Category cell
        item: cells[2].querySelector('input').value, // Get value from input in Item cell
        size: cells[3].querySelector('input').value, // Get value from input in Size cell
        quantity_min: cells[4].querySelector('.edit-quantity-min').value, // Get value from .edit-quantity-min
        quantity_max: cells[4].querySelector('.edit-quantity-max').value, // Get value from .edit-quantity-max
        price: cells[4].querySelector('.edit-price').value, // Get value from .edit-price
        discount: cells[4].querySelector('.edit-discount').value // Get value from .edit-discount
    };

    // Get the nonce from the clicked button
    const nonce = this.dataset.nonce;

  // Basic client-side validation
    if (!updatedValues.category || !updatedValues.item || !updatedValues.quantity_min || !updatedValues.price) {
        displayError('Please fill in all required fields.');
        return; // Stop execution if validation fails
    }

    if (isNaN(updatedValues.quantity_min) || isNaN(updatedValues.price) ) {
        displayError('Quantity Min and Price must be numeric.');
        return; // Stop execution if validation fails
    }
  
    if (updatedValues.quantity_max && isNaN(updatedValues.quantity_max)) {
      displayError('Quantity Max must be numeric.');
      return;
    }

    if (updatedValues.discount && isNaN(updatedValues.discount)) {
      displayError('Discount must be numeric.');
      return;
    }

   // Send AJAX request to update the product
    fetch(ajax_object.ajax_url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(Object.assign(updatedValues, { edit_nonce: nonce, action: 'update_product' })),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Replace input fields with updated values, handling price break formatting
            cells[1].textContent = updatedValues.category;
            cells[2].textContent = updatedValues.item;
            cells[3].textContent = updatedValues.size;
            
            //let priceBreakString = '';
            //if (updatedValues.quantity_max !== null && updatedValues.quantity_max !== "") {
            //    priceBreakString = `${updatedValues.quantity_min}-${updatedValues.quantity_max} = $${updatedValues.price}`;
            //} else {
            //    priceBreakString = `${updatedValues.quantity_min}+ = $${updatedValues.price}`;
            //}
            //if (parseFloat(updatedValues.discount) > 0 ) {
            //  const discounted_price = updatedValues.price - (updatedValues.price * updatedValues.discount);
            //  priceBreakString += ' (Discount: ' + (updatedValues.discount * 100) + '% = $' + discounted_price.toFixed(2) + ')';
            //}
			//const priceBreakString = formatPriceBreak(updatedValues.quantity_min, updatedValues.quantity_max, updatedValues.price, updatedValues.discount);
            cells[4].textContent = priceBreakString;

            // Change "Save" back to "Edit"
            saveButton.value = 'Edit';
            saveButton.classList.remove('save-product');
            saveButton.classList.add('edit-product');
            saveButton.removeEventListener('click', handleSaveClick);
            saveButton.addEventListener('click', handleEditClick);
        } else {
            // Handle error: Display error message in the designated area
            const errorMessageDiv = document.getElementById('error-message');
            if (errorMessageDiv) {
                errorMessageDiv.textContent = 'Error updating product: ' + data.message;
            } else {
                alert('Error updating product: ' + data.message); // Fallback to alert if div not found
            }
        }
    })
    .catch(error => {
        // Handle fetch error: Display error message in the designated area
        const errorMessageDiv = document.getElementById('error-message');
        if (errorMessageDiv) {
            errorMessageDiv.textContent = 'Error updating product: ' + error;
        } else {
            alert('Error updating product: ' + error); // Fallback to alert if div not found
        }
    });
}

// Function to handle the "Delete" button click (AJAX)
function handleDeleteClick(event) {
  event.preventDefault();
  const productId = this.dataset.id;
  const nonce = this.dataset.nonce;

  if (confirm('Are you sure you want to delete this product?')) {
    fetch(ajax_object.ajax_url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ id: productId, delete_nonce: nonce, action: 'delete_product' }),
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Remove the row from the table AND refresh to ensure db is updated
        this.closest('tr').remove();
          location.reload();
      } else {
        displayError('Error deleting product: ' + data.message);
      }
    })
    .catch(error => {
      displayError('Error deleting product: ' + error);
    });
  }
}

// Function to handle the "Duplicate" button click (AJAX)
function handleDuplicateClick(event) {
  event.preventDefault();
  const productId = this.dataset.id;
  const nonce = this.dataset.nonce;

  fetch(ajax_object.ajax_url, {
      method: 'POST',
      headers: {
          'Content-Type': 'application/json',
      },
      body: JSON.stringify({ id: productId, duplicate_nonce: nonce, action: 'duplicate_product' }),
  })
  .then(response => response.json())
  .then(data => {
      if (data.success) {
          //Ideally this would insert the new item into the table, but a refresh is easier for now.
          location.reload();
      } else {
          displayError('Error duplicating product: ' + data.message);
      }
  })
   .catch(error => {
      displayError('Error duplicating product: ' + error);
  });
}

document.addEventListener('DOMContentLoaded', function() {
  // Attach event listeners to all "Edit" buttons
  document.querySelectorAll('.edit-product').forEach(button => {
      button.addEventListener('click', handleEditClick);
  });

 // Select all functionality using event delegation
  document.addEventListener('change', function(event) {
      if (event.target.id === 'select-all-products') {
          const checkboxes = document.querySelectorAll('.product-checkbox');
          checkboxes.forEach(checkbox => {
              checkbox.checked = event.target.checked;
          });
      }
  });

  // Bulk actions
  const bulkActionButton = document.getElementById('doaction2'); // Assuming this is the correct button
  if (bulkActionButton) {
      bulkActionButton.addEventListener('click', function(event) {
          event.preventDefault();

          const selectedAction = document.getElementById('bulk-action-selector-bottom').value;
          if (selectedAction === 'delete') {
              const selectedProducts = document.querySelectorAll('.product-checkbox:checked');
              const productIds = Array.from(selectedProducts).map(checkbox => checkbox.value);

              if (confirm('Are you sure you want to delete the selected products?')) {
                  // Construct a form and submit it
                  const form = document.createElement('form');
                  form.method = 'POST';
                  form.action = ''; // Submit to the same page
                 
                  // Add a hidden input for the action
                  const actionInput = document.createElement('input');
                  actionInput.type = 'hidden';
                  actionInput.name = 'bulk_delete'; // A specific action name
                  actionInput.value = '1'; // Indicate we want to perform the action
                  form.appendChild(actionInput);

                  // Add product IDs as hidden inputs
                  productIds.forEach(id => {
                      const input = document.createElement('input');
                      input.type = 'hidden';
                      input.name = 'product_ids[]';
                      input.value = id;
                      form.appendChild(input);
                  });

                  // Append the form to the body and submit it
                  document.body.appendChild(form);
                  form.submit();
              }
          }
      });
  }

  // Row highlighting
  const productCheckboxes = document.querySelectorAll('.product-checkbox');
  productCheckboxes.forEach(checkbox => {
      checkbox.addEventListener('change', function() {
          const row = this.closest('tr');
          if (this.checked) {
              row.classList.add('row-selected');
          } else {
              row.classList.remove('row-selected');
          }
      });
  });

    // Attach event listeners to all "Delete" buttons using event delegation.  Removed individual attachment.
    document.querySelector('.products').addEventListener('click', function(event) {
        if (event.target.closest('.delete-product')) {
            handleDeleteClick.call(event.target.closest('.delete-product'), event);
        }
    });

  // Attach event listeners to all "Duplicate" buttons using event delegation.
  document.querySelector('.products').addEventListener('click', function(event) {
    if (event.target.closest('.duplicate-product')) {
      handleDuplicateClick.call(event.target.closest('.duplicate-product'), event);
    }
  });
});