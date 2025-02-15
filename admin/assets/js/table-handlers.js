// Function to handle the "Edit" button click
function handleEditClick(event) {
  event.preventDefault();

  const row = this.closest('tr');
  const cells = row.querySelectorAll('td');
  const editButton = this;

  // Store original values and replace with input fields
  cells.forEach((cell, index) => {
    if (index < cells.length - 1) { // Skip the last cell (actions)
      const originalValue = cell.textContent.trim();
      cell.setAttribute('data-original-value', originalValue);

      // Handle price break cell differently
      if (index === 3) {
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
        input.style.width = (index === 0 || index === 2) ? '80px' : '120px' //set width for category and size to be smaller
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

function handleSaveClick(event) {
    event.preventDefault();

    const row = this.closest('tr');
    const cells = row.querySelectorAll('td');
    const saveButton = this;
    const productId = this.closest('form').querySelector('[name="edit_id"]').value;

    // Gather updated values
    const updatedValues = {
        id: productId,
        category: cells[0].querySelector('input').value,
        item: cells[1].querySelector('input').value,
        size: cells[2].querySelector('input').value,
        quantity_min: cells[3].querySelector('.edit-quantity-min').value,
        quantity_max: cells[3].querySelector('.edit-quantity-max').value,
        price: cells[3].querySelector('.edit-price').value,
        discount: cells[3].querySelector('.edit-discount').value
    };

console.log(updatedValues);

  // Send AJAX request to update the product
  fetch('../admin/update-product.php', {
      method: 'POST',
      headers: {
          'Content-Type': 'application/json',
      },
      body: JSON.stringify(updatedValues),
  })
  .then(response => response.json())
  .then(data => {
      if (data.success) {
          // Replace input fields with updated values, handling price break formatting
          cells[0].textContent = updatedValues.category;
          cells[1].textContent = updatedValues.item;
          cells[2].textContent = updatedValues.size;
          
          let priceBreakString = '';
          if (updatedValues.quantity_max !== null && updatedValues.quantity_max !== "") {
              priceBreakString = `${updatedValues.quantity_min}-${updatedValues.quantity_max} = $${updatedValues.price}`;
          } else {
              priceBreakString = `${updatedValues.quantity_min}+ = $${updatedValues.price}`;
          }
          if (parseFloat(updatedValues.discount) > 0 ) {
            const discounted_price = updatedValues.price - (updatedValues.price * updatedValues.discount);
            priceBreakString += ' (Discount: ' + (updatedValues.discount * 100) + '% = $' + discounted_price.toFixed(2) + ')';
          }
          cells[3].textContent = priceBreakString;

          // Change "Save" back to "Edit"
          saveButton.value = 'Edit';
          saveButton.classList.remove('save-product');
          saveButton.classList.add('edit-product');
          saveButton.removeEventListener('click', handleSaveClick);
          saveButton.addEventListener('click', handleEditClick);
      } else {
          // Handle error
          alert('Error updating product: ' + data.message);
      }
  })
  .catch(error => {
      // Handle fetch error
      alert('Error updating product: ' + error);
  });
}

document.addEventListener('DOMContentLoaded', function() {
    // Attach event listeners to all "Edit" buttons
    document.querySelectorAll('.edit-product').forEach(button => {
        button.addEventListener('click', handleEditClick);
    });
});