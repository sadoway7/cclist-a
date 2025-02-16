<?php
require_once( plugin_dir_path( __FILE__ ) . '../includes/data-handler.php' );

// Get the product ID and nonce from the POST data
$product_id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
$nonce = isset( $_POST['duplicate_nonce'] ) ? $_POST['duplicate_nonce'] : '';

// Verify the nonce
if ( ! wp_verify_nonce( $nonce, 'duplicate_product_' . $product_id ) ) {
    wp_send_json_error( 'Invalid security token.' );
    exit;
}

// Get the product data
$product = get_product_by_id( $product_id );

if ( $product ) {
    // Remove the ID to create a new product
    unset( $product['id'] );

    // Add the product
    if ( add_product( $product ) ) {
        wp_send_json_success( 'Product duplicated successfully.' );
    } else {
        wp_send_json_error( 'Failed to duplicate product.' );
    }
} else {
    wp_send_json_error( 'Product not found.' );
}
?>