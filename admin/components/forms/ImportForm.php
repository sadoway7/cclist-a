<?php
/**
 * Displays the import form for importing product data via JSON.
 */
function display_import_form() {
    echo '<form method="post">';
    // Security nonce field
    wp_nonce_field( 'import_products', 'import_nonce' );
    // Textarea for pasting JSON data
    echo '<textarea name="import_data" id="import_data" rows="10" cols="80" placeholder="Paste JSON data here..." style="width: 100%; max-width: 800px;"></textarea><br>'; // Wider textarea
    // Div to display JSON validation preview
    echo '<div id="json-preview"></div>';
    // Hidden input to store JSON validity status
    echo '<input type="hidden" name="is_json_valid" id="is_json_valid" value="0">';
    // Submit button
    echo '<input type="submit" name="import_products" class="button" value="Import Data">';
    echo '</form>';
}
?>