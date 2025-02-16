<?php
/**
 * Displays the filter form for the product table.
 *
 * @param array $available_categories An array of available product categories.
 * @param array $available_sizes An array of available product sizes.
 */
function display_filter_form( $available_categories, $available_sizes = array() ) {
function display_filter_form( $available_categories, $available_sizes = array() ) {
    echo '<form method="get" class="filter-form">';
    echo '<input type="hidden" name="page" value="product-management" />';
    // Add nonce for security
    wp_nonce_field( 'filter_products', 'filter_nonce' );

  echo '<div><label for="filter_selector">Select Filters:</label>';
  echo '<select name="filter_selector[]" id="filter_selector" multiple>';
  echo '<option value="category">Category</option>';
  echo '<option value="size">Size</option>';
  echo '<option value="price_min">Price Min</option>';
  echo '<option value="price_max">Price Max</option>';
  echo '<option value="quantity_min">Quantity Min</option>';
  echo '<option value="quantity_max">Quantity Max</option>';
  echo '<option value="discount_only">Discounted Only</option>';
  echo '<option value="search">Search</option>';
  echo '<option value="per_page">Per Page</option>';

  echo '</select></div>';

    echo '<div id="category_filter_container"><label for="category_filter">Category:</label>';
    echo '<select name="category" id="category_filter">';
    echo '<option value="">All Categories</option>';
    foreach ( $available_categories as $category ) {
        $selected = ( isset( $_GET['category'] ) && $_GET['category'] === $category ) ? 'selected' : '';
        echo '<option value="' . esc_attr( $category ) . '" ' . $selected . '>' . esc_html( $category) . '</option>';
    }
    echo '</select></div>';

    echo '<div id="size_filter_container"><label for="size_filter">Size:</label>';
    echo '<select name="size" id="size_filter">';
    echo '<option value="">All Sizes</option>';
    foreach ( $available_sizes as $size ) {
        $selected = ( isset( $_GET['size'] ) && $_GET['size'] === $size ) ? 'selected' : '';
        echo '<option value="' . esc_attr( $size ) . '" ' . $selected . '>' . esc_html( $size) . '</option>';
    }
    echo '</select></div>';

    echo '<div id="price_min_container"><label for="price_min">Price Min:</label>';
    echo '<input type="number" step="0.01" name="price_min" id="price_min" value="' . ( isset( $_GET['price_min'] ) ? esc_attr( $_GET['price_min'] ) : '' ) . '" /></div>';

    echo '<div id="price_max_container"><label for="price_max">Price Max:</label>';
    echo '<input type="number" step="0.01" name="price_max" id="price_max" value="' . ( isset( $_GET['price_max'] ) ? esc_attr( $_GET['price_max'] ) : '' ) . '" /></div>';

    echo '<div id="quantity_min_container"><label for="quantity_min_filter">Quantity Min:</label>';
    echo '<input type="number" name="quantity_min" id="quantity_min_filter" value="' . ( isset( $_GET['quantity_min'] ) ? esc_attr( $_GET['quantity_min'] ) : '' ) . '" /></div>';

    echo '<div id="quantity_max_container"><label for="quantity_max_filter">Quantity Max:</label>';
    echo '<input type="number" name="quantity_max" id="quantity_max_filter" value="' . ( isset( $_GET['quantity_max'] ) ? esc_attr( $_GET['quantity_max'] ) : '' ) . '" /></div>';

    echo '<div id="discount_only_container"><label for="discount_filter">Discounted Only:</label>';
    echo '<input type="checkbox" name="discount_only" id="discount_filter" value="1" ' . ( isset( $_GET['discount_only'] ) && $_GET['discount_only'] == '1' ? 'checked' : '' ) . '/></div>';

    echo '<div id="search_container"><label for="search">Search:</label>';
    echo '<input type="text" name="search" id="search" value="' . ( isset( $_GET['search'] ) ? esc_attr( $_GET['search'] ) : '' ) . '" /></div>';

    echo '<div id="per_page_container"><label for="per_page">Per Page:</label>';
    echo '<select name="per_page" id="per_page">';
    $options = array(10, 25, 50, 100);
    foreach ($options as $option) {
        $selected = ( isset( $_GET['per_page'] ) && $_GET['per_page'] == $option ) ? 'selected' : '';
        echo '<option value="' . esc_attr( $option ) . '" ' . $selected . '>' . esc_html( $option ) . '</option>';
    }
    echo '</select></div>';

    echo '<input type="submit" class="button" value="Filter" />';
  echo '<button type="button" id="remove_selected_filters" class="button">Remove Selected Filters</button>';
    echo '</form>';
}
?>