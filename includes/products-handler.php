<?php

/**
 * Retrieves all products from the database.
 *
 * @return array An array of products.
 */
function get_products() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'products';
    $results = $wpdb->get_results( "SELECT * FROM $table_name", ARRAY_A );
    return $results ? $results : array();
}

/**
 * Retrieves available categories from the database
 * 
 * @return array An array of categories
 */
function get_available_categories() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'available_categories';
    $results = $wpdb->get_results( "SELECT category FROM $table_name", ARRAY_A );
    return $results ? array_column($results, 'category') : array();
}

/**
 * Retrieves available sizes from the database
 * 
 * @return array An array of sizes
 */
function get_available_sizes() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'available_sizes';
    $results = $wpdb->get_results( "SELECT size FROM $table_name", ARRAY_A );
    return $results ? array_column($results, 'size') : array();
}

/**
 * Adds a new product to the database.
 *
 * @param array $product The product data.
 * @return bool True on success, false on failure.
 */
function add_product($product) {
    global $wpdb;
    var_dump($product); // Debug: Check the product data

    var_dump($product); // Debug: Check the product data

    $table_name = $wpdb->prefix . 'products';
    return (bool) $wpdb->insert(
        $table_name,
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

    var_dump($wpdb->last_query); // Debug: Check the generated SQL query
    var_dump($wpdb->last_error); // Debug: Check for database errors

/**
 * Updates an existing product in the database.
    var_dump($wpdb->last_query); // Debug: Check the generated SQL query
    var_dump($wpdb->last_error); // Debug: Check for database errors

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
            'category' => $product['category'],
            'item' => $product['item'],
            'size' => $product['size'],
            'quantity_min' => $product['quantity_min'],
            'quantity_max' => $product['quantity_max'],
            'price' => $product['price'],
            'discount' => isset($product['discount']) ? $product['discount'] : null,
        ),
        array('id' => $id),
        array('%s', '%s', '%s', '%d', '%d', '%f', '%f'),
        array('%d')
    );
}

/**
 * Deletes a product from the database.
 *
 * @param int $id The product ID.
 * @return bool True on success, false on failure.
 */
function delete_product($category, $item, $size, $quantity_min, $quantity_max, $price, $discount) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'products';

    return (bool) $wpdb->delete(
        $table_name,
        array(
            'category' => $category,
            'item' => $item,
            'size' => $size,
            'quantity_min' => $quantity_min,
            'quantity_max' => $quantity_max,
            'price' => $price,
            'discount' => $discount
            ),
        array('%s', '%s', '%s', '%d', '%d', '%f', '%f')
    );
}

/**
 * Adds a new category to the available categories table
 */
function add_available_category($category){
    global $wpdb;
    $table_name = $wpdb->prefix . 'available_categories';
    if (!$wpdb->get_row($wpdb->prepare("SELECT category FROM $table_name WHERE category = %s", $category))) {
        return (bool) $wpdb->insert($table_name, array('category' => $category), array('%s'));
    }
    return false;
}

/**
 * Adds a new size to the available sizes table
 */
function add_available_size($size){
    global $wpdb;
    $table_name = $wpdb->prefix . 'available_sizes';
    if (!$wpdb->get_row($wpdb->prepare("SELECT size FROM $table_name WHERE size = %s", $size))) {
        return (bool) $wpdb->insert($table_name, array('size' => $size), array('%s'));
    }
    return false;
}