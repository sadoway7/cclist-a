<?php
/**
 * Plugin Name: cclist-admin
 * Description: Product management plugin.
 * Version: 0.1.5.88
 * GitHub Plugin URI: sadoway7/cclist-a
 * GitHub Plugin URI: https://github.com/sadoway7/cclist-a.git
 */

// Create database tables on plugin activation
function create_product_tables() {
    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    $products_table_name = $wpdb->prefix . 'products';
    $categories_table_name = $wpdb->prefix . 'available_categories';
    $sizes_table_name = $wpdb->prefix . 'available_sizes';

    $sql_products = "CREATE TABLE $products_table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        category varchar(255) NOT NULL,
        item varchar(255) NOT NULL,
        size varchar(255) NULL,
        quantity_min int NULL,
        quantity_max int NULL,
        price decimal(10,2) NOT NULL,
        discount decimal(10,2) NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    $sql_categories = "CREATE TABLE $categories_table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        category varchar(255) NOT NULL UNIQUE,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    $sql_sizes = "CREATE TABLE $sizes_table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        size varchar(255) NOT NULL UNIQUE,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql_products );
    dbDelta( $sql_categories );
    dbDelta( $sql_sizes );
}

function import_initial_data() {
    global $wpdb;
    $products_table_name = $wpdb->prefix . 'products';
    $categories_table_name = $wpdb->prefix . 'available_categories';
    $sizes_table_name = $wpdb->prefix . 'available_sizes';

    $file_path = plugin_dir_path( __FILE__ ) . 'includes/products.json';
    $json_data = file_get_contents( $file_path );

    if ( false !== $json_data ) {
        $data = json_decode( $json_data, true );

        if ( null !== $data ) {
            // Insert available categories
            if (isset($data['available_categories']) && is_array($data['available_categories'])) {
                foreach ($data['available_categories'] as $category) {
                    $wpdb->insert(
                        $categories_table_name,
                        array('category' => $category),
                        array('%s')
                    );
                }
            }

            // Insert available sizes
            if (isset($data['available_sizes']) && is_array($data['available_sizes'])) {
                foreach ($data['available_sizes'] as $size) {
                    $wpdb->insert(
                        $sizes_table_name,
                        array('size' => $size),
                        array('%s')
                    );
                }
            }

            // Insert products
            if (isset($data['products']) && is_array($data['products'])) {
                foreach ($data['products'] as $product) {
                    $wpdb->insert(
                        $products_table_name,
                        array(
                            'category' => $product['category'],
                            'item' => $product['item'],
                            'size' => $product['size'],
                            'quantity_min' => $product['quantity_min'],
                            'quantity_max' => $product['quantity_max'],
                            'price' => $product['price'],
                            'discount' => isset($product['discount']) ? $product['discount'] : null,
                        ),
                        array('%s', '%s', '%s', '%d', '%d', '%f', '%f')
                    );
                }
            }
        }
    }
}

register_activation_hook( __FILE__, 'create_product_tables' );
register_activation_hook( __FILE__, 'import_initial_data' );

// Add REST API endpoint
add_action( 'rest_api_init', function () {
    register_rest_route( 'cclist/v1', '/products', array(
        'methods' => 'GET',
        'callback' => 'get_products_api',
        'permission_callback' => '__return_true'
    ) );
} );

function get_products_api() {
    return get_products();
}

// Enqueue scripts and styles
function enqueue_custom_scripts() {
    //wp_enqueue_script('jquery');
    //wp_enqueue_style( 'admin-styles', plugin_dir_url( __FILE__ ) . 'admin/assets/css/admin.css' );
    wp_enqueue_script( 'form-handlers', plugin_dir_url( __FILE__ ) . 'admin/assets/js/form-handlers.js', array( 'jquery' ), '1.0', true );
    //wp_enqueue_script( 'table-handlers', plugin_dir_url( __FILE__ ) . 'admin/assets/js/table-handlers.js', array( 'jquery' ), '1.0', true );
    
    // Pass ajaxurl to both scripts
    wp_localize_script( 'form-handlers', 'ajaxurl', admin_url( 'admin-ajax.php' ) );
    wp_localize_script( 'table-handlers', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
    
    //wp_enqueue_style( 'table-styles', plugin_dir_url( __FILE__ ) . 'admin/assets/css/tables.css' );
    //wp_enqueue_style( 'form-styles', plugin_dir_url( __FILE__ ) . 'admin/assets/css/forms.css' );
    //wp_enqueue_style( 'components-styles', plugin_dir_url( __FILE__ ) . 'admin/assets/css/components.css' );
    //wp_enqueue_style( 'custom-table-styles', plugin_dir_url( __FILE__ ) . 'admin/assets/css/custom-table.css' );
    //wp_enqueue_style( 'custom-filter-styles', plugin_dir_url( __FILE__ ) . 'admin/assets/css/custom-filter.css' );
}
add_action( 'admin_enqueue_scripts', 'enqueue_custom_scripts' );

// AJAX handler for adding products
// add_action('wp_ajax_add_product', 'handle_add_product');
// function handle_add_product() {
//     require_once( plugin_dir_path( __FILE__ ) . 'admin/add-product.php' );
// }

// AJAX handler for updating products
// add_action('wp_ajax_update_product', 'handle_update_product');
// function handle_update_product() {
//     require_once( plugin_dir_path( __FILE__ ) . 'admin/update-product.php' );
// }

// AJAX handler for deleting products
// add_action('wp_ajax_delete_product', 'handle_delete_product');
// function handle_delete_product() {
//     require_once( plugin_dir_path( __FILE__ ) . 'admin/delete-product.php' );
// }

// AJAX handler for duplicating products
// add_action('wp_ajax_duplicate_product', 'handle_duplicate_product');
// function handle_duplicate_product() {
//     require_once( plugin_dir_path( __FILE__ ) . 'admin/duplicate-product.php' );
// }

require_once( plugin_dir_path( __FILE__ ) . 'admin/admin-page.php' );
