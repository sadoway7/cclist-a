<?php
/**
 * Retrieves all products from the database, sorted for display.
 *
 * @return array An array of products.
 */
function get_products() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'products';
    // Added ORDER BY clause
    $results = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY category, item, size, quantity_min", ARRAY_A );
    return $results ? $results : array();
}

/**
 * Retrieves available categories from the database.
 *
 * @return array An array of categories.
 */
function get_available_categories() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'available_categories';
    $results = $wpdb->get_results("SELECT category FROM $table_name", ARRAY_A);
    return $results ? array_column($results, 'category') : array();
}

/**
 * Retrieves available sizes from the database.
 *
 * @return array An array of sizes.
 */
function get_available_sizes() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'available_sizes';
    $results = $wpdb->get_results("SELECT size FROM $table_name", ARRAY_A);
    return $results ? array_column($results, 'size') : array();
}

/**
 * Adds a new product to the database.
 *
 * This function should add products in the same way as the manual add form.
 *
 * @param array $product The product data.
 * @return bool True on success, false on failure.
 */
function add_product($product) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'products';
    return (bool) $wpdb->insert(
        $table_name,
        array(
            'category'     => $product['category'],
            'item'         => $product['item'],
            'size'         => $product['size'],
            'quantity_min' => $product['quantity_min'],
            'quantity_max' => $product['quantity_max'],
            'price'        => $product['price'],
            'discount'     => isset($product['discount']) ? $product['discount'] : 0,
        ),
        array(
            '%s', '%s', '%s', '%d', '%s', '%f', '%f'
        )
    );
}

/**
 * Updates an existing product in the database.
 *
 * @param int   $id      The product ID.
 * @param array $product The updated product data.
 * @return bool True on success, false on failure.
 */
function update_product($id, $product) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'products';
    return (bool) $wpdb->update(
        $table_name,
        array(
            'category'     => $product['category'],
            'item'         => $product['item'],
            'size'         => $product['size'],
            'quantity_min' => $product['quantity_min'],
            'quantity_max' => $product['quantity_max'],
            'price'        => $product['price'],
            'discount'     => isset($product['discount']) ? $product['discount'] : 0,
        ),
        array('id' => $id),
        array(
            '%s', '%s', '%s', '%d', '%s', '%f', '%f'
        ),
        array('%d')
    );
}

/**
 * Deletes a product from the database.
 *
 * @param int $id The product ID.
 * @return bool True on success, false on failure.
 */
function delete_product($id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'products';
    return (bool) $wpdb->delete(
        $table_name,
        array('id' => $id),
        array('%d')
    );
}

/**
 * Adds a new category to the available categories table.
 *
 * @param string $category The category name.
 * @return bool True on success, false on failure.
 */
function add_available_category($category) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'available_categories';
    if (!$wpdb->get_row($wpdb->prepare("SELECT category FROM $table_name WHERE category = %s", $category))) {
        return (bool) $wpdb->insert($table_name, array('category' => $category), array('%s'));
    }
    return false;
}

/**
 * Adds a new size to the available sizes table.
 *
 * @param string $size The size.
 * @return bool True on success, false on failure.
 */
function add_available_size($size) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'available_sizes';
    if ($size !== null && !$wpdb->get_row($wpdb->prepare("SELECT size FROM $table_name WHERE size = %s", $size))) {
        return (bool) $wpdb->insert($table_name, array('size' => $size), array('%s'));
    }
    return false;
}

/**
 * Imports products from a JSON string.
 *
 * Processes the JSON input and adds each product using the same method as the manual add form.
 *
 * @param string $json_data The JSON string representing an array of products.
 * @return string Summary message of the import process.
 */
function import_products($json_data) {
    // Trim the input.
    $json_data = trim($json_data);

    // Remove BOM if present.
    if (substr($json_data, 0, 3) === "\xEF\xBB\xBF") {
        $json_data = substr($json_data, 3);
    }

    if (empty($json_data)) {
        return 'No data provided.';
    }

    // Remove any non-printable characters and escaped slashes.  This is the key addition.
    $json_data = preg_replace('/[[:cntrl:]]/', '', $json_data);
    $json_data = stripslashes($json_data);


    // Decode JSON.
    $decoded = json_decode($json_data, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return 'Invalid JSON provided: ' . json_last_error_msg();
    }

    // Support JSON wrapped with a top-level "products" key.
    if (isset($decoded['products']) && is_array($decoded['products'])) {
        $decoded = $decoded['products'];
    }

    // If a single product object is provided, wrap it in an array.
    if (isset($decoded['category']) || isset($decoded['item'])) {
        $decoded = array($decoded);
    }

    if (!is_array($decoded)) {
        return 'JSON does not decode to an array.';
    }

    $success_count = 0;
    $error_messages = array();

    // Process each product entry.
    foreach ($decoded as $product) {
        // Validate required fields.
        if (empty($product['category']) || empty($product['item']) || !array_key_exists('size', $product) ||
            !isset($product['quantity_min']) || !isset($product['price'])) {
            $error_messages[] = 'Missing required fields for product: ' . (isset($product['item']) ? $product['item'] : 'Unknown');
            continue;
        }

        // Sanitize textual fields.
        $product['category'] = sanitize_text_field($product['category']);
        $product['item']     = sanitize_text_field($product['item']);
        $product['size']     = ($product['size'] === null) ? null : sanitize_text_field($product['size']); // Allow null size.

        // Convert numerical fields exactly as the manual form does.
        $product['quantity_min'] = intval($product['quantity_min']);
        if (array_key_exists('quantity_max', $product) && $product['quantity_max'] === null) {
            $product['quantity_max'] = null;
        } else {
            $product['quantity_max'] = isset($product['quantity_max']) ? intval($product['quantity_max']) : 0;
        }
        $product['price']        = floatval($product['price']);
        $product['discount']     = isset($product['discount']) ? floatval($product['discount']) : 0;

        // Add the product using the same function as the manual form.
        if (add_product($product)) {
            $success_count++;
            add_available_category($product['category']);
            // Only add size if it's not null
            if ($product['size'] !== null) {
                add_available_size($product['size']);
            }
        } else {
            $error_messages[] = 'Failed to insert product: ' . $product['item'];
        }
    }

    $message = "Imported: $success_count product(s).";
    if (!empty($error_messages)) {
        $message .= " Errors: " . implode(' ', $error_messages);
    }
    return $message;
}
?>