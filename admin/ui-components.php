<?php

/**
 * Generates the "Add Product" form.
 *
 * @param array $available_categories An array of available categories.
 * @param array $available_sizes An array of available sizes.
 * @return string The HTML for the "Add Product" form.
 */
function get_add_product_form($available_categories, $available_sizes) {
    ob_start(); // Start output buffering
    ?>
    <form method="post" class="cclist-grid-form">
        <table class="cclist-form-table">
            <tr>
                <th scope="row"><label for="category_select">Category:</label></th>
                <td>
                    <select name="category_select" id="category_select" class="cclist-regular-text">
                        <?php foreach ($available_categories as $category) : ?>
                            <option value="<?php echo esc_attr($category); ?>"><?php echo esc_html($category); ?></option>
                        <?php endforeach; ?>
                        <option value="__other__">Other...</option>
                    </select>
                    <input type="text" name="category" id="category" class="cclist-regular-text" style="display:none;" placeholder="Enter custom category" />
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="item">Item:</label></th>
                <td><input type="text" name="item" id="item" class="cclist-regular-text" /></td>
            </tr>
            <tr>
                <th scope="row"><label for="size_select">Size:</label></th>
                <td>
                    <select name="size_select" id="size_select" class="cclist-regular-text">
                        <?php foreach ($available_sizes as $size) : ?>
                            <option value="<?php echo esc_attr($size); ?>"><?php echo esc_html($size); ?></option>
                        <?php endforeach; ?>
                        <option value="__other__">Other...</option>
                    </select>
                    <input type="text" name="size" id="size" class="cclist-regular-text" style="display:none;" placeholder="Enter custom size"/>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="quantity_min">Quantity Min:</label></th>
                <td><input type="number" name="quantity_min" id="quantity_min" class="cclist-regular-number" value="1"/></td>
            </tr>
            <tr>
                <th scope="row"><label for="quantity_max">Quantity Max:</label></th>
                <td><input type="number" name="quantity_max" id="quantity_max" class="cclist-regular-number" /></td>
            </tr>
            <tr>
                <th scope="row"><label for="price">Price:</label></th>
                <td><input type="number" step="0.01" name="price" id="price" class="cclist-regular-number" /></td>
            </tr>
            <tr>
                <th scope="row"><label for="discount">Discount:</label></th>
                <td><input type="number" step="0.01" name="discount" id="discount" class="cclist-regular-number" /></td>
            </tr>
        </table>
        <input type="hidden" name="add_product" value="1" />
        <input type="submit" name="submit" class="cclist-button cclist-button-primary" value="Add Product" />
    </form>
    <?php
    return ob_get_clean(); // Return the buffered output
}
?>