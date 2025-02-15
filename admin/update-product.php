<?php
require_once( plugin_dir_path( __FILE__ ) . '../includes/data-handler.php' );

// Get the raw POST data
$raw_data = file_get_contents('php://input');

// Decode the JSON data
$data = json_decode($raw_data, true);

if ( ! empty( $data ) ) {
    // Sanitize the data (you can add more specific validation as needed)
    $id = isset($data['id']) ? intval($data['id']) : 0;
    $category = sanitize_text_field( $data['category'] );
    $item = sanitize_text_field( $data['item'] );
    $size = sanitize_text_field( $data['size'] );
    $quantity_min = intval( $data['quantity_min'] );
    $quantity_max = $data['quantity_max'] === '' ? null : intval($data['quantity_max']);
    $price = floatval( $data['price'] );
    $discount = floatval( $data['discount'] );

    // Update the product
    $product = array(
        'category'     => $category,
        'item'         => $item,
        'size'         => $size,
        'quantity_min' => $quantity_min,
        'quantity_max' => $quantity_max,
        'price'        => $price,
        'discount'     => $discount
    );

    if ( update_product( $id, $product ) ) {
        wp_send_json_success( 'Product updated successfully.' );
    } else {
        wp_send_json_error( 'Failed to update product.' );
    }
} else {
    wp_send_json_error( 'No data received.' );
}
?>