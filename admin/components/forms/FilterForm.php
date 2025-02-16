<?php
/**
 * Displays the filter form for the product table.
 *
 * @param array $available_categories An array of available product categories.
 * @param array $available_sizes An array of available product sizes.
 */
function display_filter_form( $available_categories, $available_sizes = array() ) {
    echo '<div class="accordion-section">';
    echo '<div class="accordion-trigger" data-target="filter-product-form">';
    echo '<h2>Filter Products</h2>';
    echo '</div>';
    echo '<div id="filter-product-form" class="accordion-content" style="display: none;">';
    echo '<form method="get" class="filter-form">';
    echo '<input type="hidden" name="page" value="product-management" />';
    // Add nonce for security
    wp_nonce_field( 'filter_products', 'filter_nonce' );

    // Search Row
    echo '<div class="filter-row">';
    echo '<div class="search-section">';
    echo '<label for="search">SEARCH</label>';
    echo '<input type="text" name="search" id="search" value="' . ( isset( $_GET['search'] ) ? esc_attr( $_GET['search'] ) : '' ) . '" placeholder="Search products..." />';
    echo '</div>';
    echo '</div>';

    // Filters Row
    echo '<div class="filter-row">';
    
    // Category Filter
    echo '<div class="filter-section">';
    echo '<label for="category_filter">CATEGORY</label>';
    echo '<select name="category" id="category_filter">';
    echo '<option value="">All Categories</option>';
    foreach ( $available_categories as $category ) {
        $selected = ( isset( $_GET['category'] ) && $_GET['category'] === $category ) ? 'selected' : '';
        echo '<option value="' . esc_attr( $category ) . '" ' . $selected . '>' . esc_html( $category) . '</option>';
    }
    echo '</select>';
    echo '</div>';

    // Size Filter
    echo '<div class="filter-section">';
    echo '<label for="size_filter">SIZE</label>';
    echo '<select name="size" id="size_filter">';
    echo '<option value="">All Sizes</option>';
    foreach ( $available_sizes as $size ) {
        $selected = ( isset( $_GET['size'] ) && $_GET['size'] === $size ) ? 'selected' : '';
        echo '<option value="' . esc_attr( $size ) . '" ' . $selected . '>' . esc_html( $size) . '</option>';
    }
    echo '</select>';
    echo '</div>';

    // Price Filter
    echo '<div class="filter-section">';
    echo '<label>PRICE</label>';
    echo '<div class="range-inputs">';
    echo '<input type="number" step="0.01" name="price_min" id="price_min" value="' . ( isset( $_GET['price_min'] ) ? esc_attr( $_GET['price_min'] ) : '' ) . '" placeholder="Min" />';
    echo '<span class="range-separator">-</span>';
    echo '<input type="number" step="0.01" name="price_max" id="price_max" value="' . ( isset( $_GET['price_max'] ) ? esc_attr( $_GET['price_max'] ) : '' ) . '" placeholder="Max" />';
    echo '</div>';
    echo '</div>';

    // Quantity Filter
    echo '<div class="filter-section">';
    echo '<label>QUANTITY</label>';
    echo '<div class="range-inputs">';
    echo '<input type="number" name="quantity_min" id="quantity_min_filter" value="' . ( isset( $_GET['quantity_min'] ) ? esc_attr( $_GET['quantity_min'] ) : '' ) . '" placeholder="Min" />';
    echo '<span class="range-separator">-</span>';
    echo '<input type="number" name="quantity_max" id="quantity_max_filter" value="' . ( isset( $_GET['quantity_max'] ) ? esc_attr( $_GET['quantity_max'] ) : '' ) . '" placeholder="Max" />';
    echo '</div>';
    echo '</div>';

    // Options and Buttons
    echo '<div class="options-section">';
    
    // Per Page Select
    echo '<div class="filter-section">';
    echo '<label for="per_page">SHOW</label>';
    echo '<select name="per_page" id="per_page">';
    $options = array(10, 25, 50, 100);
    foreach ( $options as $option) {
        $selected = ( isset( $_GET['per_page'] ) && $_GET['per_page'] == $option ) ? 'selected' : '';
        echo '<option value="' . esc_attr( $option ) . '" ' . $selected . '>' . esc_html( $option ) . '</option>';
    }
    echo '</select>';
    echo '</div>';

    // Discounted Only Checkbox
    echo '<div id="discount_only_container">';
    echo '<input type="checkbox" name="discount_only" id="discount_filter" value="1" ' . ( isset( $_GET['discount_only'] ) && $_GET['discount_only'] == '1' ? 'checked' : '' ) . '/>';
    echo '<label for="discount_filter">DISCOUNTED ONLY</label>';
    echo '</div>';

    // Buttons
    echo '<div class="button-group">';
    echo '<button type="submit" class="button-primary">Apply Filters</button>';
    echo '<button type="button" id="remove_selected_filters" class="button-reset">Reset</button>';
    echo '</div>';
    
    echo '</div>'; // Close options-section
    echo '</div>'; // Close filter-row

    echo '</form>';
    echo '</div>'; // Close accordion-content
    echo '</div>'; // Close accordion-section
}
?>