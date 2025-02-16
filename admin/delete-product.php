<?php
require_once( plugin_dir_path( __FILE__ ) . '../includes/data-handler.php' );

// Get the product ID and nonce from the POST data
$product_id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
$nonce = isset( $_POST['delete_nonce'] ) ? $_POST['delete_nonce'] : '';

// Verify the nonce
if ( ! wp_verify_nonce( $nonce, 'delete_product_' . $product_id ) ) {
    wp_send_json_error( 'Invalid security token.' );
    exit;
}

// Delete the product
if ( delete_product( $product_id ) ) {
    wp_send_json_success( 'Product deleted successfully.' );
} else {
    wp_send_json_error( 'Failed to delete product.' );
}
?>