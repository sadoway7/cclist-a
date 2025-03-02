<?php
require_once( plugin_dir_path( __FILE__ ) . '../../includes/data-handler.php' );

// Process form submissions before outputting HTML

// Handle Import Data submission
if ( isset( $_POST['import_products'] ) ) {
    // Ensure wp_verify_nonce function is available.
    if ( ! function_exists( 'wp_verify_nonce' ) ) {
        require_once( ABSPATH . 'wp-includes/pluggable.php' );
    }
    if ( ! isset( $_POST['import_nonce'] ) || ! wp_verify_nonce( $_POST['import_nonce'], 'import_products' ) ) {
        $import_message = '<div class="cclist-error"><p>Security check failed.</p></div>';
    } else {
        $import_result = import_products( $_POST['import_data'] );
        $import_message = '<div class="cclist-updated"><p>' . esc_html( $import_result ) . '</p></div>';
     }
    // wp_redirect( add_query_arg( 'import_message', urlencode( $import_message ), menu_page_url( 'product-management', false ) ) ); //Commented to show messages
    // exit;
}

// Handle product deletion
if ( isset( $_POST['delete_product'] ) ) {
    $id = intval( $_POST['id'] );
    if ( ! delete_product($id) ) {
        $delete_message = '<div class="cclist-error"><p>Error: Could not delete product.</p></div>';
    } else {
        $delete_message = '<div class="cclist-updated"><p>Product deleted successfully.</p></div>';
    }
    if ( ! function_exists( 'wp_redirect' ) ) {
        require_once( ABSPATH . 'wp-includes/pluggable.php' );
    }
    // Add timestamp to force reload
    wp_redirect( add_query_arg( array('delete_message' => urlencode( $delete_message ), 'timestamp' => time()), menu_page_url( 'product-management', false ) ) );
    exit;
}

/**
 * Displays the product management page in the WordPress admin.
 */
function display_product_management_page() {
    // Read messages passed via URL query params
    $messages = '';
    if ( isset( $_GET['import_message'] ) ) {
        $messages .= wp_kses_post( urldecode( $_GET['import_message'] ) );
    }
    if ( isset( $_GET['add_message'] ) ) {
        $messages .= wp_kses_post( urldecode( $_GET['add_message'] ) );
    }
    if ( isset( $_GET['delete_message'] ) ) {
        $messages .= wp_kses_post( urldecode( $_GET['delete_message'] ) );
    }

    $products = get_products();
    $available_categories = get_available_categories();
    $available_sizes = get_available_sizes();

    echo '<div class="cclist-wrap">';
    echo '<h1>Product Management</h1>';

    // Display any messages
    echo $messages;

    // --- Add Product Form ---
    echo '<div class="cclist-add-product-section">';
    echo '<h2>Add Product</h2>';
    echo '<form method="post" id="add-product-form" class="cclist-grid-form">';

    // Category Input with Datalist
    echo '<div class="cclist-form-field cclist-required-field">';
    echo '<label for="category">Category</label>';
    echo '<input type="text" name="category" id="category" list="category_list" required>';
    echo '<datalist id="category_list">';
    foreach ( $available_categories as $category ) {
        echo '<option value="' . esc_attr( $category ) . '">';
    }
    echo '</datalist>';
    echo '</div>';

    // Item Input
    echo '<div class="cclist-form-field cclist-required-field">';
    echo '<label for="item">Item</label>';
    echo '<input type="text" name="item" id="item" required>';
    echo '</div>';

    // Size Input with Datalist
    echo '<div class="cclist-form-field">';
    echo '<label for="size">Size</label>';
    echo '<input type="text" name="size" id="size" list="size_list">';
    echo '<datalist id="size_list">';
    foreach ( $available_sizes as $size ) {
        echo '<option value="' . esc_attr( $size ) . '">';
    }
    echo '</datalist>';
    echo '</div>';

    // Quantity Min Input
    echo '<div class="cclist-form-field cclist-required-field">';
    echo '<label for="quantity_min">Quantity Min</label>';
    echo '<input type="number" name="quantity_min" id="quantity_min" required min="1">';
    echo '</div>';

    // Quantity Max Input
    echo '<div class="cclist-form-field">';
    echo '<label for="quantity_max">Quantity Max</label>';
    echo '<input type="number" name="quantity_max" id="quantity_max" min="1">';
    echo '</div>';

    // Price Input
    echo '<div class="cclist-form-field cclist-required-field">';
    echo '<label for="price">Price</label>';
    echo '<input type="number" step="0.01" name="price" id="price" required min="0">';
    echo '</div>';

    // Discount Input
    echo '<div class="cclist-form-field">';
    echo '<label for="discount">Discount</label>';
    echo '<input type="number" step="0.01" name="discount" id="discount" min="0" max="1" placeholder="0.00 - 1.00">';
    echo '</div>';

    // Submit button container
    echo '<div class="cclist-submit-container">';
    echo '<input type="submit" name="add_product" class="cclist-button cclist-button-primary" value="Add Product">';
    echo '</div>';
    echo '</form>';
    echo '<hr>';

    // --- Filtering and Search ---
    echo '<h2>Filter Products</h2>';
    echo '<form method="get" style="margin-bottom: 20px;" class="cclist-filter-form">';
    echo '<input type="hidden" name="page" value="product-management" />';
    echo '<label for="category_filter">Category:</label>';
    echo '<select name="category" id="category_filter">';
    echo '<option value="">All Categories</option>';
    foreach ( $available_categories as $category ) {
        $selected = ( isset( $_GET['category'] ) && $_GET['category'] === $category ) ? 'selected' : '';
        echo '<option value="' . esc_attr( $category ) . '" ' . $selected . '>' . esc_html( $category) . '</option>';
    }
    echo '</select> ';
    echo '<label for="search">Search:</label>';
    echo '<input type="text" name="search" id="search" value="' . ( isset( $_GET['search'] ) ? esc_attr( $_GET['search'] ) : '' ) . '" />';
    echo '<input type="submit" class="cclist-button" value="Filter" />';
    echo '</form>';

    // --- Product Table ---
    echo '<h2>Product List</h2>';
    echo '<div style="overflow-x: auto;">'; // Make table horizontally scrollable
    echo get_product_table($products);
    echo '</div>';
    echo '<hr>';

    // --- Import Data Form ---
    echo '<h2>Import Data</h2>';
    echo '<form method="post" class="cclist-grid-form">';
    wp_nonce_field( 'import_products', 'import_nonce' );
    echo '<textarea name="import_data" rows="10" cols="80" placeholder="Paste JSON data here..." style="width: 100%; max-width: 800px;"></textarea><br>'; // Wider textarea
    echo '<input type="submit" name="import_products" class="cclist-button" value="Import Data">';
    echo '</form>';

    echo '</div>'; // Close .cclist-wrap

    // Enqueue styles
    wp_enqueue_style( 'cc-product-management-components', plugin_dir_url( __FILE__ ) . '../assets/css/components.css' );
    wp_enqueue_style( 'cc-product-management-forms', plugin_dir_url( __FILE__ ) . '../assets/css/forms.css' );
    wp_enqueue_style( 'cc-product-management-tables', plugin_dir_url( __FILE__ ) . '../assets/css/tables.css' );
  
     // Enqueue scripts
    wp_enqueue_script( 'cc-product-management-form-handlers', plugin_dir_url( __FILE__ ) . '../assets/js/form-handlers.js', array( 'jquery' ), '1.0', true );?>
    <?php
}

// Add the admin menu item
add_action( 'admin_menu', 'add_product_management_menu' );

/**
 * Adds the product management menu item to the WordPress admin.
 */
function add_product_management_menu() {
    add_menu_page(
        'Product Management', // Page title
        'Product Manager', // Menu title
        'manage_options', // Capability
        'product-management', // Menu slug
        'display_product_management_page' // Callback function
    );
}
?>