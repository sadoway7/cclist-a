<?php
/**
 * Displays the import form for importing product data via JSON.
 */
function display_import_form() {
    echo '<div class="cclist-wrap">';
    echo '<form method="post" class="cclist-grid-form">';
    // Security nonce field
    wp_nonce_field( 'import_products', 'import_nonce' );
    
    echo '<div class="cclist-form-field">';
    // Textarea for pasting JSON data
    echo '<label for="import_data">JSON Data</label>';
    echo '<textarea name="import_data" id="import_data" rows="10" cols="80" placeholder="Paste JSON data here..."></textarea>';
    // Div to display JSON validation preview
    echo '<div id="json-preview" class="cclist-json-preview"></div>';
    echo '</div>';
    
    // Hidden input to store JSON validity status
    echo '<input type="hidden" name="is_json_valid" id="is_json_valid" value="0">';
    
    // Submit button container
    echo '<div class="cclist-submit-container">';
    echo '<button type="submit" name="import_products" class="button-primary">Import Data</button>';
    echo '</div>';
    
    echo '</form>';
    echo '</div>';
}
?>