<?php
/**
 * Displays the product table with sorting, pagination, and bulk actions.
 *
 * @param array $products An array of product data.
 * @param string $sort_by The column to sort by (default: 'category').
 * @param string $sort_order The sort order ('ASC' or 'DESC', default: 'ASC').
 * @param int $page The current page number (default: 1).
 * @param int $per_page The number of products per page (default: 10).
 * @param int $total_products The total number of products.
 */
function display_product_table( $products, $sort_by = 'category', $sort_order = 'ASC', $page = 1, $per_page = 10, $total_products = 0 ) {
    /**
     * Generates the HTML for the product table.
     * 
     * @param array $products
     * @param string $sort_by
     * @param string $sort_order
     * @param int $page
     * @param int $per_page
     * @param int $total_products
     * @return string
     */
    function get_product_table( $products, $sort_by, $sort_order, $page, $per_page, $total_products ) {
        $base_url = menu_page_url( 'product-management', false );

        // Construct URLs for sorting
        $category_sort_url = add_query_arg( array('sort_by' => 'category', 'sort_order' => ($sort_by == 'category' && $sort_order == 'ASC') ? 'DESC' : 'ASC'), $base_url );
        $item_sort_url = add_query_arg( array('sort_by' => 'item', 'sort_order' => ($sort_by == 'item' && $sort_order == 'ASC') ? 'DESC' : 'ASC'), $base_url );
        $size_sort_url = add_query_arg( array('sort_by' => 'size', 'sort_order' => ($sort_by == 'size' && $sort_order == 'ASC') ? 'DESC' : 'ASC'), $base_url );
        $price_sort_url = add_query_arg( array('sort_by' => 'price', 'sort_order' => ($sort_by == 'price' && $sort_order == 'ASC') ? 'DESC' : 'ASC'), $base_url );

        ob_start(); // Start output buffering
        ?>
        <div id="error-message" style="color: red;"></div>
        <table class="wp-list-table widefat fixed striped table-view-list products product-table">
            <thead>
                <tr>
                    <th class="manage-column check-column">
                        <input type="checkbox" id="select-all-products">
                    </th>
                    <th><a href="<?php echo esc_url( $category_sort_url ); ?>">Category <?php if ($sort_by == 'category') echo ($sort_order == 'ASC' ? '▲' : '▼'); ?></a></th>
                    <th><a href="<?php echo esc_url( $item_sort_url ); ?>">Item <?php if ($sort_by == 'item') echo ($sort_order == 'ASC' ? '▲' : '▼'); ?></a></th>
                    <th><a href="<?php echo esc_url( $size_sort_url ); ?>">Size <?php if ($sort_by == 'size') echo ($sort_order == 'ASC' ? '▲' : '▼'); ?></a></th>
                    <th><a href="<?php echo esc_url( $price_sort_url ); ?>">Price Break <?php if ($sort_by == 'price') echo ($sort_order == 'ASC' ? '▲' : '▼'); ?></a></th>
                    <th class="actions">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ( ! empty( $products ) ) :
                    $last_item_key = ''; // Keep track of the previous item
                    foreach ( $products as $product ) :
                        $item_key = $product['category'] . '_' . $product['item'];
                        $is_new_item = $item_key !== $last_item_key;

                        ?>
                        <tr class="<?php echo $is_new_item ? 'new-item-group' : 'sub-item'; ?>">
                            <td><input type="checkbox" name="product_ids[]" value="<?php echo esc_attr( $product['id'] ); ?>" class="product-checkbox"></td>
                            <?php // Only display category, item, and size if it's a new item
                            if ( $is_new_item ) : ?>
                                <td><?php echo esc_html( $product['category'] ); ?></td>
                                <td><?php echo esc_html( $product['item'] ); ?></td>
                                <td><?php echo esc_html( $product['size'] ); ?></td>
                            <?php else : ?>
                                <td></td>
                                <td></td>
                            <?php endif; ?>
                            <td>
                                <?php
                                $price_break_string = '';
                                if ( $product['quantity_max'] !== null ) {
                                    $price_break_string = $product['quantity_min'] . '-' . $product['quantity_max'] . ' = $' . $product['price'];
                                } else {
                                    $price_break_string = $product['quantity_min'] . '+ = $' . $product['price'];
                                }
                                if ( isset( $product['discount'] ) && $product['discount'] > 0 ) {
                                    $discounted_price = $product['price'] - ( $product['price'] * $product['discount'] );
                                    $price_break_string .= ' (Discount: ' . ( $product['discount'] * 100 ) . '% = $' . number_format( $discounted_price, 2 ) . ')';
                                }
                                echo $price_break_string;
                                ?>
                            </td>
                            <td class="actions">
                                <input type="hidden" name="id" value="<?php echo esc_attr( $product['id'] ); ?>">
                                <?php wp_nonce_field( 'delete_product_' . $product['id'], 'delete_nonce' ); ?>
                                <button type="button" data-id="<?php echo esc_attr( $product['id'] ); ?>" data-nonce="<?php echo wp_create_nonce( 'delete_product_' . $product['id'] )?>" class="button delete-product">
                                    <span class="dashicons dashicons-trash"></span> Delete
                                </button>
                                <input type="hidden" name="duplicate_category" value="<?php echo esc_attr( $product['category'] ); ?>">
                                <input type="hidden" name="duplicate_item" value="<?php echo esc_html( $product['item'] ); ?>">
                                <input type="hidden" name="duplicate_size" value="<?php echo esc_attr( $product['size'] ); ?>">
                                 <?php wp_nonce_field( 'duplicate_product_' . $product['id'], 'duplicate_nonce' ); ?>
                                <button type="button" data-id="<?php echo esc_attr( $product['id'] ); ?>" data-nonce="<?php echo wp_create_nonce( 'duplicate_product_' . $product['id'] )?>" class="button duplicate-product">
                                   <span class="dashicons dashicons-admin-page"></span> Duplicate
                                </button>
                                <input type="hidden" name="edit_id" value="<?php echo esc_attr( $product['id'] ); ?>">
                                <?php wp_nonce_field( 'edit_product_' . $product['id'], 'edit_nonce' ); ?>
                                <button type="submit" name="edit_product" class="button edit-product" data-nonce="<?php echo wp_create_nonce( 'edit_product_' . $product['id'] ); ?>">
                                    <span class="dashicons dashicons-edit"></span> Edit
                                </button>
                            </td>
                        </tr>
                        <?php
                        $last_item_key = $item_key; // Update the last item key
                    endforeach;
                else : ?>
                    <tr>
                        <td colspan="6">No products found.</td>>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <?php
        // Pagination
        $total_pages = ceil($total_products / $per_page);
        if ($total_pages > 1) {
            echo '<div class="tablenav"><div class="tablenav-pages">';
            $current_page_url = add_query_arg( array('page_num' => $page), $base_url );

            if ($page > 1) {
                echo '<a class="prev-page button" href="' . esc_url(add_query_arg('page_num', $page - 1, $current_page_url)) . '">&lsaquo;</a>';
            }
            for ($i = 1; $i <= $total_pages; $i++) {
                if ($i == $page) {
                    echo '<span class="current-page button disabled">' . $i . '</span>';
                } else {
                    echo '<a class="page-numbers button" href="' . esc_url(add_query_arg('page_num', $i, $current_page_url)) . '">' . $i . '</a>';
                }
            }
            if ($page < $total_pages) {
                echo '<a class="next-page button" href="' . esc_url(add_query_arg('page_num', $page + 1, $current_page_url)) . '">&rsaquo;</a>';
            }
            echo '</div></div>';
        }
        ?>
        <div class="tablenav bottom">
            <div class="alignleft actions bulkactions">
                <select name="action" id="bulk-action-selector-bottom">
                    <option value="-1">Bulk Actions</option>
                    <option value="delete">Delete</option>
                </select>
                <input type="submit" id="doaction2" class="button action" value="Apply">
            </div>
        </div>
        <?php

        return ob_get_clean(); // Return the buffered content
    }

    echo get_product_table( $products, $sort_by, $sort_order, $page, $per_page, $total_products );
}
?>