<?php
/**
 * Displays the "Add Product" form.
 *
 * @param array $available_categories An array of available product categories.
 * @param array $available_sizes An array of available product sizes.
 */
function display_product_form( $available_categories, $available_sizes ) {
    echo '<div class="cclist-accordion-section">';
    echo '<div class="cclist-accordion-trigger" data-target="add-product-form">';
    echo '<h2>Add New Product</h2>';
    echo '</div>';
    echo '<div class="cclist-accordion-content">';
    echo '<form id="add-product-form" class="cclist-grid-form">';

    // Add nonce field for security
    wp_nonce_field( 'add_product', 'add_product_nonce' );

    // First row: Category, Item, Size
    echo '<div class="cclist-form-field">';
    
    // Category Input with Datalist
    echo '<div class="cclist-filter-section">';
    echo '<label for="category">CATEGORY</label>';
    echo '<input type="text" name="category" id="category" list="category_list" required class="cclist-dropdown-input">';
    echo '<datalist id="category_list">';
    foreach ( $available_categories as $category ) {
        echo '<option value="' . esc_attr( $category ) . '">';
    }
    echo '</datalist>';
    echo '</div>';

    // Item Input
    echo '<div class="cclist-filter-section">';
    echo '<label for="item">ITEM</label>';
    echo '<input type="text" name="item" id="item" required>';
    echo '</div>';

    // Size Input with Datalist
    echo '<div class="cclist-filter-section">';
    echo '<label for="size">SIZE</label>';
    echo '<input type="text" name="size" id="size" list="size_list" class="cclist-dropdown-input">';
    echo '<datalist id="size_list">';
    foreach ( $available_sizes as $size ) {
        echo '<option value="' . esc_attr( $size ) . '">';
    }
    echo '</datalist>';
    echo '</div>';
    
    echo '</div>'; // Close first form-field

    // Second row: Quantity range and Price
    echo '<div class="cclist-form-field">';
    
    // Quantity Range
    echo '<div class="cclist-filter-section">';
    echo '<label>QUANTITY</label>';
    echo '<div class="cclist-range-inputs">';
    echo '<input type="number" name="quantity_min" id="quantity_min" placeholder="Min" required>';
    echo '<span class="cclist-range-separator">-</span>';
    echo '<input type="number" name="quantity_max" id="quantity_max" placeholder="Max">';
    echo '</div>';
    echo '</div>';

    // Price Input
    echo '<div class="cclist-filter-section">';
    echo '<label for="price">PRICE</label>';
    echo '<input type="number" step="0.01" name="price" id="price" required>';
    echo '</div>';

    // Discount Input
    echo '<div class="cclist-filter-section">';
    echo '<label for="discount">DISCOUNT</label>';
    echo '<input type="number" step="0.01" name="discount" id="discount">';
    echo '</div>';

    echo '</div>'; // Close second form-field

    // Button row
    echo '<div class="cclist-submit-container">';
    echo '<div class="cclist-button-group">';
    echo '<button type="button" id="submit-add-product" class="button-primary">Add Product</button>';
    echo '</div>';
    echo '</div>';
    
    echo '</form>'; // Close form
    echo '</div>'; // Close accordion-content
    echo '</div>'; // Close accordion-section
}
?>