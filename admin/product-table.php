<?php
/**
 * Generates the HTML for the product table, handling individual price break display with visual grouping.
 *
 * @param array $products An array of product data.
 * @return string The HTML for the product table.
 */
function get_product_table( $products ) {
    ob_start(); // Start output buffering
    ?>
    <table class="wp-list-table widefat fixed striped table-view-list products product-table">
        <thead>
            <tr>
                <th>Category</th>
                <th>Item</th>
                <th>Size</th>
                <th>Price Break</th>
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
?>