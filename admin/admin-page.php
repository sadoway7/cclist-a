<?php
require_once( plugin_dir_path( __FILE__ ) . '../includes/data-handler.php' );
require_once( plugin_dir_path( __FILE__ ) . 'ui-components.php' );
require_once( plugin_dir_path( __FILE__ ) . 'product-table.php' );

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
        $import_message = '<div class="updated"><p>' . esc_html( import_products( $_POST['import_data'] ) ) . '</p></div>';
    }
    // Redirect to avoid form resubmission and load updated product list.
    wp_redirect( add_query_arg( 'import_message', urlencode( $import_message ), menu_page_url( 'product-management', false ) ) );
    exit;
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

    echo '<div class="wrap">';
    echo '<h1>Product Management</h1>';

    // Display any messages
    echo $messages;

    // --- Add Product Form ---
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
    echo '<hr>';

    // --- Filtering and Search ---
    echo '<h2>Filter Products</h2>';
    echo '<form method="get" style="margin-bottom: 20px;">';
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
    echo '<input type="submit" class="button" value="Filter" />';
    echo '</form>';

    // --- Product Table ---
    echo '<h2>Product List</h2>';
    echo '<div style="overflow-x: auto;">'; // Make table horizontally scrollable
    echo get_product_table($products);
    echo '</div>';
    echo '<hr>';

    // --- Import Data Form ---
    echo '<h2>Import Data</h2>';
    echo '<form method="post">';
    wp_nonce_field( 'import_products', 'import_nonce' );
    echo '<textarea name="import_data" rows="10" cols="80" placeholder="Paste JSON data here..." style="width: 100%; max-width: 800px;"></textarea><br>'; // Wider textarea
    echo '<input type="submit" name="import_products" class="button" value="Import Data">';
    echo '</form>';

    echo '</div>'; // Close .wrap
    ?>
	<style>
        .wrap label {
            display: block; /* Labels above inputs */
            margin-bottom: 5px; /* Space between label and input */
			font-weight: bold;
        }
       .wrap input[type="text"],
		.wrap input[type="number"],
        .wrap select,
        .wrap textarea {
            width: 100%; /* Inputs take full width of their container */
            box-sizing: border-box; /* Include padding and border in width */
			margin-bottom: 10px;
        }
        .wrap th.actions,
        .wrap td.actions {
            max-width: 100px; /* Limit width of Actions column */
        }
		.product-table {
			border-collapse: collapse;
			width: 100%;
		}

		.product-table th,
		.product-table td {
			padding: 8px;
			border: 1px solid #ddd;
			text-align: left;
		}
        /* New CSS for grouping */
        .product-table .new-item-group {
            border-top: 2px solid #0073aa; /* A thicker, colored border for the first item in a group */
        }
        .product-table .sub-item:nth-child(odd) {
            background-color: #f9f9f9; /* Lighter background for odd sub-items */
        }
        .product-table .sub-item:nth-child(even) {
            background-color: #ffffff; /* White background for even sub-items */
        }
		.duplicate-form {
			display: inline-block;
			margin-left: 5px;
		}

    </style>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			document.querySelectorAll('.duplicate-form').forEach(function(form) {
				form.addEventListener('submit', function(event) {
					event.preventDefault();

					// Get values from the duplicate form's hidden inputs
					var category = this.querySelector('[name="duplicate_category"]').value;
					var item = this.querySelector('[name="duplicate_item"]').value;
					var size = this.querySelector('[name="duplicate_size"]').value;

					// Populate the "Add Product" form
					document.getElementById('category').value = category;
					document.getElementById('item').value = item;
					document.getElementById('size').value = size;
					
					//Clear other inputs
					document.getElementById('quantity_min').value = '';
					document.getElementById('quantity_max').value = '';
					document.getElementById('price').value = '';
					document.getElementById('discount').value = '';

					// Scroll to the "Add Product" form
					document.getElementById('add-product-form').scrollIntoView({ behavior: 'smooth' });
				});
			});
		});
	</script>
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