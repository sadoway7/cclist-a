<?php
/**
 * Displays the "Add Product" form.
 *
 * @param array $available_categories An array of available product categories.
 * @param array $available_sizes An array of available product sizes.
 */
function display_product_form( $available_categories, $available_sizes ) {
    ?>
    <input type="radio" name="product_form_toggle" id="add_product_toggle" style="display:none;">
    <label for="add_product_toggle" style="cursor: pointer; display: block; padding: 10px; background-color: #f0f0f1; border: 1px solid #ddd;">
        <h2>Add New Product</h2>
    </label>
    <fieldset style="border: 1px solid #ddd; padding: 10px; margin-bottom: 20px;">
        <form id="add-product-form" class="filter-form">

        <?php wp_nonce_field( 'add_product', 'add_product_nonce' ); ?>

        <!-- First row: Category, Item, Size -->
    echo '<div class="filter-row">';
    
    // Category Input with Datalist
    echo '<div class="filter-section">';
    echo '<label for="category">CATEGORY</label>';
    echo '<input type="text" name="category" id="category" list="category_list" required class="dropdown-input">';
    echo '<datalist id="category_list">';
    foreach ( $available_categories as $category ) {
        echo '<option value="' . esc_attr( $category ) . '">';
    }
    echo '</datalist>';
    echo '</div>';

    // Item Input
    echo '<div class="filter-section">';
    echo '<label for="item">ITEM</label>';
    echo '<input type="text" name="item" id="item" required>';
    echo '</div>';

    // Size Input with Datalist
    echo '<div class="filter-section">';
    echo '<label for="size">SIZE</label>';
    echo '<input type="text" name="size" id="size" list="size_list" class="dropdown-input">';
    echo '<datalist id="size_list">';
    foreach ( $available_sizes as $size ) {
        echo '<option value="' . esc_attr( $size ) . '">';
    }
    echo '</datalist>';
    echo '</div>';
    
    echo '</div>'; // Close first filter-row

    // Second row: Quantity range and Price
    echo '<div class="filter-row">';
    
    // Quantity Range
    echo '<div class="filter-section">';
    echo '<label>QUANTITY</label>';
    echo '<div class="range-inputs">';
    echo '<input type="number" name="quantity_min" id="quantity_min" placeholder="Min" required>';
    echo '<span class="range-separator">-</span>';
    echo '<input type="number" name="quantity_max" id="quantity_max" placeholder="Max">';
    echo '</div>';
    echo '</div>';

    // Price Input
    echo '<div class="filter-section">';
    echo '<label for="price">PRICE</label>';
    echo '<input type="number" step="0.01" name="price" id="price" required>';
    echo '</div>';

    // Discount Input
    echo '<div class="filter-section">';
    echo '<label for="discount">DISCOUNT</label>';
    echo '<input type="number" step="0.01" name="discount" id="discount">';
    echo '</div>';

    echo '</div>'; // Close second filter-row

    // Button row
    echo '<div class="filter-row">';
    echo '<div class="button-group" style="margin-left: auto;">';
    echo '<button type="button" id="submit-add-product" class="button-primary">Add Product</button>';
    echo '</div>';
        echo '</div>';
        ?>
        </form>
    </fieldset>
    <?php
}
?>