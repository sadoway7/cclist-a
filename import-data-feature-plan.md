# Import Data Feature Detailed Plan

## Overview
The goal is to implement a robust \"Import Data\" feature for our WordPress plugin that enables admins to import a JSON array of products. This feature must be compatible with the existing database structure and data management functions such as add_product, add_available_category, and add_available_size.

## UI Component (Admin Side)
- **Location:** Integrate the import section on the Product Management page (admin/admin-page.php).
- **Elements:**
  - A textarea for pasting the JSON array.
  - An \"Import\" button to trigger the import process.
  - Use of WordPress nonces for security.
- **User Feedback:**
  - Display error messages if the JSON is invalid or missing required product fields.
  - Show success message detailing the number of products imported and any issues encountered.

## Backend Process: import_products Function
The import process will be handled by a new PHP function called `import_products`, which is integrated into our data handling logic.

### Step 1: Retrieve and Decode JSON Input
- Get the JSON string from the `_POST` request.
- Verify the WordPress nonce to ensure the request is valid.
- Use `json_decode()` to convert the string to an array.
- If decoding fails, return an error about invalid JSON format.

### Step 2: Loop Through the Product Data
For each product in the decoded array:
- **Validation:**
  - Check for required keys: `category`, `item`, `size`, `quantity_min`, and `price`.
  - Validate that numerical values (`quantity_min`, `quantity_max`, `price`, and optionally `discount`) are valid numbers. Set optional fields (like `quantity_max` and `discount`) to `null` if missing.
- **Sanitization:**
  - Sanitize textual data using functions such as `sanitize_text_field()`.
  - Cast numerical values appropriately using `intval()` and `floatval()`.

### Step 3: Database Insertion and Compatibility
- **Insert Products:**
  - Call the existing `add_product()` function for each valid product.
  - Handle any insertion errors and keep a running count of successful vs. failed imports.
- **Update Related Tables:**
  - For new categories, invoke `add_available_category()` if the category is not already present.
  - For new sizes, invoke `add_available_size()` similarly.
- **Compatibility:**
  - Use the same data structure as existing products so that imported products are fully compatible with the product-table display and further manipulations.
  - The function leverages existing sanitization and insertion mechanisms to ensure consistency.

### Step 4: Final Feedback and Reporting
- Once all products have been processed, compile a report that shows:
  - Total number of products imported.
  - Details on any products that failed validation (with reasons, e.g., missing fields or invalid values).
- Display this report on the admin page to inform the user.

## Integration with Existing Data Flow
- **Product Management Page:** The import feature will be an additional form in the admin page. The existing product addition and deletion mechanisms remain unchanged.
- **Data Consistency:** Since the same helper functions for adding products, categories, and sizes are used, the imported data will integrate seamlessly with existing data structures and UI components.

## Error Handling and Security Considerations
- **JSON Validation:** Ensure that if malformed JSON is pasted, the admin receives a clear error message.
- **Field-Level Checks:** Each product object is processed individually to avoid halting the entire import due to one bad entry.
- **Nonce Verification:** Secure the import action using WordPress nonces to prevent unauthorized access.
- **SQL Injection:** Use existing prepared statements in `add_product` and related functions to mitigate SQL injection risks.

## Pseudocode Example
Below is a high-level pseudocode example to illustrate the `import_products` function:

```
function import_products() {
    // Check nonce for security
    if ( ! isset($_POST['import_nonce']) || ! wp_verify_nonce($_POST['import_nonce'], 'import_products') ) {
        return 'Security check failed.';
    }

    // Retrieve JSON input
    $json_input = $_POST['import_data'];
    $products_array = json_decode($json_input, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return 'Invalid JSON provided.';
    }

    $success_count = 0;
    $error_messages = [];
    
    foreach ($products_array as $product) {
        // Validate required fields
        if (empty($product['category']) || empty($product['item']) || empty($product['size']) ||
            !isset($product['quantity_min']) || !isset($product['price'])) {
                $error_messages[] = 'Missing required fields in one entry.';
                continue;
        }
        
        // Sanitize and type cast
        $product['category'] = sanitize_text_field($product['category']);
        $product['item'] = sanitize_text_field($product['item']);
        $product['size'] = sanitize_text_field($product['size']);
        $product['quantity_min'] = intval($product['quantity_min']);
        $product['quantity_max'] = isset($product['quantity_max']) ? intval($product['quantity_max']) : null;
        $product['price'] = floatval($product['price']);
        $product['discount'] = isset($product['discount']) ? floatval($product['discount']) : null;
        
        // Insert product
        if ( add_product($product) ) {
            $success_count++;
            // Update available category and size if needed
            add_available_category($product['category']);
            add_available_size($product['size']);
        } else {
            $error_messages[] = 'Failed to import product: ' . $product['item'];
        }
    }
    
    // Return summary
    return "Imported: $success_count product(s). " . implode(' ', $error_messages);
}
```

## Conclusion
This detailed plan ensures the Import Data feature is:
- Fully compatible with the existing data handlers.
- Secure through nonce verification and proper sanitization.
- Robust in terms of error handling.
- Easy to integrate into the current admin interface, providing clear feedback to end-users.

This concludes the detailed plan for the Import Data feature.