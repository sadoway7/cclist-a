<?php
/**
 * Displays the "Add Product" form within an accordion.
 *
 * @param array $available_categories An array of available product categories.
 * @param array $available_sizes An array of available product sizes.
 */
function display_product_form( $available_categories, $available_sizes ) {
    // Accordion container
    echo '<div class="accordion-section">';
    echo '<button type="button" class="accordion-trigger button button-primary" id="add-product-button">Add Product</button>';
    
    // Form container (initially hidden)
    echo '<div id="add-product-form-container" class="accordion-content" style="display: none;">';
    echo '<form id="add-product-form" class="grid-form">';

    // Add nonce field for security
    wp_nonce_field( 'add_product', 'add_product_nonce' );

    // Category Input with Datalist
    echo '<div class="form-field required-field">';
    echo '<label for="category">Category:</label>';
    echo '<input type="text" name="category" id="category" list="category_list" required>';
    echo '<datalist id="category_list">';
    foreach ( $available_categories as $category ) {
        echo '<option value="' . esc_attr( $category ) . '">';
    }
    echo '</datalist>';
    echo '</div>';

    // Item Input
    echo '<div class="form-field required-field">';
    echo '<label for="item">Item:</label>';
    echo '<input type="text" name="item" id="item" required>';
    echo '</div>';

    // Size Input with Datalist
    echo '<div class="form-field">';
    echo '<label for="size">Size:</label>';
    echo '<input type="text" name="size" id="size" list="size_list">';
    echo '<datalist id="size_list">';
    foreach ( $available_sizes as $size ) {
        echo '<option value="' . esc_attr( $size ) . '">';
    }
    echo '</datalist>';
    echo '</div>';

    // Quantity Min Input
    echo '<div class="form-field required-field">';
    echo '<label for="quantity_min">Quantity Min:</label>';
    echo '<input type="number" name="quantity_min" id="quantity_min" required>';
    echo '</div>';

    // Quantity Max Input
    echo '<div class="form-field">';
    echo '<label for="quantity_max">Quantity Max:</label>';
    echo '<input type="number" name="quantity_max" id="quantity_max">';
    echo '</div>';

    // Price Input
    echo '<div class="form-field required-field">';
    echo '<label for="price">Price:</label>';
    echo '<input type="number" step="0.01" name="price" id="price" required>';
    echo '</div>';

    // Discount Input
    echo '<div class="form-field">';
    echo '<label for="discount">Discount:</label>';
    echo '<input type="number" step="0.01" name="discount" id="discount">';
    echo '</div>';

    // Submit button container
    echo '<div class="submit-container">';
    echo '<button type="button" id="submit-add-product" class="button button-primary">Submit Product</button>';
    echo '</div>';
    
    echo '</form>'; // Close form
    echo '</div>'; // Close accordion-content
    echo '</div>'; // Close accordion-section
}
?>