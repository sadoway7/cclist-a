<?php
function display_import_form() {
    echo '<form method="post">';
    wp_nonce_field( 'import_products', 'import_nonce' );
    echo '<textarea name="import_data" id="import_data" rows="10" cols="80" placeholder="Paste JSON data here..." style="width: 100%; max-width: 800px;"></textarea><br>'; // Wider textarea
    echo '<div id="json-preview"></div>';
    echo '<input type="hidden" name="is_json_valid" id="is_json_valid" value="0">';
    echo '<input type="submit" name="import_products" class="button" value="Import Data">';
    echo '</form>';
}
?>