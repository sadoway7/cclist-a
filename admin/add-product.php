<?php
require_once( plugin_dir_path( __FILE__ ) . '../includes/data-handler.php' );

// Get the raw POST data
$raw_data = file_get_contents('php://input');

// Decode the JSON data
$data = json_decode($raw_data, true);

if ( ! empty( $data ) ) {
    // Verify the nonce
    if ( ! isset( $data['add_product_nonce'] ) || ! wp_verify_nonce( $data['add_product_nonce'], 'add_product' ) ) {
        wp_send_json_error( 'Invalid security token.' );
        exit;
    }

    // Sanitize the data
    $category = sanitize_text_field( $data['category'] );
    $item = sanitize_text_field( $data['item'] );
    $size =  sanitize_text_field( $data['size'] ) ;
    $quantity_min = intval( $data['quantity_min'] );
    $quantity_max = $data['quantity_max'] === '' ? null : intval($data['quantity_max']);
    $price = floatval( $data['price'] );
    $discount = floatval( $data['discount'] );

    // Update available categories and sizes
    add_available_category($category);
    if ($size !== null) {
        add_available_size($size);
    }

    // Create the product array
    $product = array(
        'category'     => $category,
        'item'         => $item,
        'size'         => $size,
        'quantity_min' => $quantity_min,
        'quantity_max' => $quantity_max,
        'price'        => $price,
        'discount'     => $discount
    );

    // Add the product
    if ( add_product( $product ) ) {
        wp_send_json_success( 'Product added successfully.' );
    } else {
        wp_send_json_error( 'Failed to add product.' );
    }
} else {
    wp_send_json_error( 'No data received.' );
}
?>