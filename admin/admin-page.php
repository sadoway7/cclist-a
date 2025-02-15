<?php
require_once( plugin_dir_path( __FILE__ ) . '../includes/data-handler.php' );

// Process form submissions before outputting HTML

// Handle Import Data submission
if ( isset( $_POST['import_products'] ) ) {
     // Ensure wp_verify_nonce function is available.
    if ( ! function_exists( 'wp_verify_nonce' ) ) {
        require_once( ABSPATH . 'wp-includes/pluggable.php' );
    }
    if ( ! isset( $_POST['import_nonce'] ) || ! wp_verify_nonce( $_POST['import_nonce'], 'import_products' ) ) {
        $import_message = '<div class="error"><p>Security check failed.</p></div>';
    } else {
        // Check if JSON is valid before importing
        if ( isset( $_POST['is_json_valid'] ) && $_POST['is_json_valid'] === '1' ) {
            $import_message = '<div class="updated"><p>' . esc_html( import_products( $_POST['import_data'] ) ) . '</p></div>';
        } else {
            $import_message = '<div class="error"><p>Invalid JSON data. Please check your input.</p></div>';
        }
    }
 }

// Handle form submission for adding products
if ( isset( $_POST['add_product'] ) ) {
    if ( empty( $_POST['category'] )  || empty( $_POST['item'] ) || empty( $_POST['price'] ) ) {
        $add_message = '<div class="error"><p>Error: Please fill in all required fields.</p></div>';
    } else {
        $category = sanitize_text_field( $_POST['category'] );
        $item = sanitize_text_field( $_POST['item'] );
        $size =  sanitize_text_field( $_POST['size'] ) ;
        $quantity_min = intval( $_POST['quantity_min'] );
		$quantity_max = $_POST['quantity_max'] === '' ? null : intval($_POST['quantity_max']);
        $price = floatval( $_POST['price'] );
        $discount = floatval( $_POST['discount'] );

        $product = array(
            'category' => $category,
            'item' => $item,
            'size' => $size,
            'quantity_min' => $quantity_min,
            'quantity_max' => $quantity_max,
            'price' => $price,
            'discount' => $discount
        );

        // Update available categories and sizes
        add_available_category($category);
		if ($size !== null) {
        	add_available_size($size);
		}

        if ( ! add_product( $product ) ) {
            $add_message = '<div class="error"><p>Error: Could not add product.</p></div>';
        } else {
            $add_message = '<div class="updated"><p>Product added successfully.</p></div>';
        }
    }
    // Redirect to refresh the list.
    wp_redirect( add_query_arg( 'add_message', urlencode( $add_message ), menu_page_url( 'product-management', false ) ) );
    exit;
}

// Handle product deletion
if ( isset( $_POST['delete_product'] ) ) {
    $id = intval( $_POST['id'] );
    if ( ! delete_product($id) ) {
        $delete_message = '<div class="error"><p>Error: Could not delete product.</p></div>';
    } else {
        $delete_message = '<div class="updated"><p>Product deleted successfully.</p></div>';
    }
    if ( ! function_exists( 'wp_redirect' ) ) {
        require_once( ABSPATH . 'wp-includes/pluggable.php' );
    }
    // Add timestamp to force reload
    wp_redirect( add_query_arg( array('delete_message' => urlencode( $delete_message ), 'timestamp' => time()), menu_page_url( 'product-management', false ) ) );
    exit;
}

// Handle bulk delete
if ( isset( $_POST['bulk_delete'] ) && isset( $_POST['product_ids'] ) && is_array( $_POST['product_ids'] ) ) {
    $deleted_count = 0;
    foreach ( $_POST['product_ids'] as $product_id ) {
        if ( delete_product( intval( $product_id ) ) ) {
            $deleted_count++;
        }
    }
    $delete_message = '<div class="updated"><p>' . sprintf( _n( '%s product deleted.', '%s products deleted.', $deleted_count, 'cclist-a' ), $deleted_count ) . '</p></div>';
}

/**
 * Displays the product management page in the WordPress admin.
 */
function display_product_management_page() {
     // Initialize $messages
    $messages = '';

	// Read messages passed via URL query params
    if ( isset( $_GET['import_message'] ) ) {
        $messages .= wp_kses_post( urldecode( $_GET['import_message'] ) );
    }
    if ( isset( $_GET['add_message'] ) ) {
        $messages .= wp_kses_post( urldecode( $_GET['add_message'] ) );
    }
    if ( isset( $_GET['delete_message'] ) ) {
        $messages .= wp_kses_post( urldecode( $_GET['delete_message'] ) );
    }

	// Add $import_message to $messages if it's set
    if ( isset( $import_message ) ) {
        $messages .= $import_message;
    }

    // Get sort parameters from the URL, with defaults
    $sort_by = isset( $_GET['sort_by'] ) ? $_GET['sort_by'] : 'category';
    $sort_order = isset( $_GET['sort_order'] ) ? $_GET['sort_order'] : 'ASC';

    // Get pagination parameters from the URL, with defaults
    $page = isset( $_GET['page_num'] ) ? intval( $_GET['page_num'] ) : 1;
    $per_page = isset( $_GET['per_page'] ) ? intval( $_GET['per_page'] ) : 10;

    // Ensure 'page_num' is always part of the $_GET parameters
    if (!isset($_GET['page_num'])) {
        $_GET['page_num'] = $page;
    }

    // Ensure 'per_page' is always part of the $_GET parameters
    if (!isset($_GET['per_page'])) {
        $_GET['per_page'] = $per_page;
    }

    // Get filtered and sorted products
	$products = get_products($_GET, $sort_by, $sort_order, $page, $per_page);
    // Get total number of products (for pagination)
    $total_products = get_total_products($_GET);

    // Get available categories and sizes for filter dropdowns
	$available_categories = get_available_categories();
    $available_sizes = get_available_sizes();

    echo '<div class="wrap">';
    echo '<h1>Product Management</h1>';

    // Display any messages
    echo $messages;

    // --- Include Components ---
    require_once( plugin_dir_path( __FILE__ ) . 'components/forms/ProductForm.php' );
    require_once( plugin_dir_path( __FILE__ ) . 'components/forms/ImportForm.php' );
    require_once( plugin_dir_path( __FILE__ ) . 'components/forms/FilterForm.php' );
    require_once( plugin_dir_path( __FILE__ ) . 'components/tables/ProductTable.php' );

    // --- Component Calls ---
    // Display the "Add Product" form (modal)
    display_product_form( $available_categories, $available_sizes );

    echo '<h2>Filter Products</h2>';
    // Display the filter form
    display_filter_form( $available_categories, $available_sizes );

    echo '<h2>Product List</h2>';
    // Display the product table with sorting and pagination
    display_product_table( $products, $sort_by, $sort_order, $page, $per_page, $total_products );

    echo '<h2>Import Data</h2>';
    // Display the import form
	display_import_form();

    echo '</div>'; // Close .wrap
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