<?php
/**
 * Displays the "Add Product" form within a modal.
 *
 * @param array $available_categories An array of available product categories.
 * @param array $available_sizes An array of available product sizes.
 */
function display_product_form( $available_categories, $available_sizes ) {
    // Button to trigger the modal
    echo '<button type="button" class="button button-primary" id="add-product-button">Add Product</button>';

    // Modal structure (initially hidden)
    echo '<div id="add-product-modal" class="modal-container" style="display: none;">';
    echo '<div class="modal-content">';
    echo '<span class="close-button">&times;</span>'; // Close button (X)
    echo '<h2>Add Product</h2>';
    echo '<form method="post" id="add-product-form" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px; margin-bottom: 20px;">';

	// Category Input with Datalist
	echo '<div style="grid-column: span 1;">'; // Each input takes one column
	echo '<label for="category">Category:</label>';
	echo '<input type="text" name="category" id="category" list="category_list" required style="width: 100%;">';
	echo '<datalist id="category_list">';
	foreach ( $available_categories as $category ) {
		echo '<option value="' . esc_attr( $category ) . '">';
	}
	echo '</datalist>';
	echo '</div>';

	// Item Input
	echo '<div style="grid-column: span 1;">';
	echo '<label for="item">Item:</label>';
	echo '<input type="text" name="item" id="item" required style="width: 100%;">';
	echo '</div>';

	// Size Input with Datalist
	echo '<div style="grid-column: span 1;">';
	echo '<label for="size">Size:</label>';
	echo '<input type="text" name="size" id="size" list="size_list" style="width: 100%;">';
	echo '<datalist id="size_list">';
	foreach ( $available_sizes as $size ) {
		echo '<option value="' . esc_attr( $size ) . '">';
	}
	echo '</datalist>';
	echo '</div>';

	// Quantity Min Input
	echo '<div style="grid-column: span 1;">';
	echo '<label for="quantity_min">Quantity Min:</label>';
	echo '<input type="number" name="quantity_min" id="quantity_min" required style="width: 100%;">';
	echo '</div>';

	// Quantity Max Input
	echo '<div style="grid-column: span 1;">';
	echo '<label for="quantity_max">Quantity Max:</label>';
	echo '<input type="number" name="quantity_max" id="quantity_max" style="width: 100%;">';
	echo '</div>';

	// Price Input
	echo '<div style="grid-column: span 1;">';
	echo '<label for="price">Price:</label>';
	echo '<input type="number" step="0.01" name="price" id="price" required style="width: 100%;">';
	echo '</div>';

	// Discount Input
	echo '<div style="grid-column: span 1;">';
	echo '<label for="discount">Discount:</label>';
	echo '<input type="number" step="0.01" name="discount" id="discount" style="width: 100%;">';
	echo '</div>';

    // Submit button (spans all columns)
    echo '<div style="grid-column: 1 / -1;">';
	echo '<input type="submit" name="add_product" class="button button-primary" value="Add Product">';
    echo '</div>';
	echo '</form>';
    echo '</div>'; // Close modal-content
    echo '</div>'; // Close modal-container
}
?>