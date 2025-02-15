<?php
function display_product_table( $products, $sort_by = 'category', $sort_order = 'ASC' ) {
    /**
     * Generates the HTML for the product table, handling individual price break display with visual grouping.
     *
     * @param array $products An array of product data.
     * @param string $sort_by The column to sort by.
     * @param string $sort_order The sort order (ASC or DESC).
     * @return string The HTML for the product table.
     */
    function get_product_table( $products, $sort_by, $sort_order ) {
        $base_url = menu_page_url( 'product-management', false );

        $category_sort_url = add_query_arg( array('sort_by' => 'category', 'sort_order' => ($sort_by == 'category' && $sort_order == 'ASC') ? 'DESC' : 'ASC'), $base_url );
        $item_sort_url = add_query_arg( array('sort_by' => 'item', 'sort_order' => ($sort_by == 'item' && $sort_order == 'ASC') ? 'DESC' : 'ASC'), $base_url );
        $size_sort_url = add_query_arg( array('sort_by' => 'size', 'sort_order' => ($sort_by == 'size' && $sort_order == 'ASC') ? 'DESC' : 'ASC'), $base_url );
        $price_sort_url = add_query_arg( array('sort_by' => 'price', 'sort_order' => ($sort_by == 'price' && $sort_order == 'ASC') ? 'DESC' : 'ASC'), $base_url );

        ob_start(); // Start output buffering
        ?>
        <table class="wp-list-table widefat fixed striped table-view-list products product-table">
            <thead>
                <tr>
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
                        $item_key = $product['category'] . '_' . $product['item'] . '_' . $product['size'];
                        $is_new_item = $item_key !== $last_item_key;

                        ?>
                        <tr class="<?php echo $is_new_item ? 'new-item-group' : 'sub-item'; ?>">
                            <?php // Only display category, item, and size if it's a new item
                            if ( $is_new_item ) : ?>
                                <td><?php echo esc_html( $product['category'] ); ?></td>
                                <td><?php echo esc_html( $product['item'] ); ?></td>
                                <td><?php echo esc_html( $product['size'] ); ?></td>
                            <?php else : ?>
                                <td></td>
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
                                <form method="post" style="display: inline;">
                                    <input type="hidden" name="id" value="<?php echo esc_attr( $product['id'] ); ?>">
                                    <input type="submit" name="delete_product" value="Delete" class="button">
                                </form>
                                <form method="post" style="display: inline;" class="duplicate-form">
                                    <input type="hidden" name="duplicate_category" value="<?php echo esc_attr( $product['category'] ); ?>">
                                    <input type="hidden" name="duplicate_item" value="<?php echo esc_html( $product['item'] ); ?>">
                                    <input type="hidden" name="duplicate_size" value="<?php echo esc_attr( $product['size'] ); ?>">
                                    <input type="submit" name="duplicate_product" value="Duplicate" class="button">
                                </form>
                                <form method="post" style="display: inline;" class="edit-form">
                                    <input type="hidden" name="edit_id" value="<?php echo esc_attr( $product['id'] ); ?>">
                                    <input type="submit" name="edit_product" value="Edit" class="button edit-product">
                                </form>
                            </td>
                        </tr>
                        <?php
                        $last_item_key = $item_key; // Update the last item key
                    endforeach;
                else : ?>
                    <tr>
                        <td colspan="5">No products found.</td>>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <?php
        return ob_get_clean(); // Return the buffered content
    }

    echo get_product_table( $products, $sort_by, $sort_order );
}
?>