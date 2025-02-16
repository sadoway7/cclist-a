# Product Management Logic Summary

This document summarizes the logic paths for product management in the plugin.

## Overview

The plugin provides functionality to manage products, including:

*   Adding new products
*   Updating existing products
*   Deleting products
*   Duplicating products
*   Importing products
*   Filtering and displaying products

## Admin Page (`admin/admin-page.php`)

This file is the main entry point for the admin page. It handles:

*   **Loading necessary scripts and styles:** Enqueues CSS and JS files from the `assets` directory.
*   **Displaying the main page structure:**  Uses `div.wrap` and `h1` for the title.
*   **Including other components:**  Includes PHP files for forms and tables from the `components` directory using `require_once`.
*   **Handling form submissions:**
    *   **Import Data:** Processes the `import_products` POST request. Validates a nonce and checks if the submitted JSON is valid. Calls `import_products()` if valid.
    *   **Bulk Delete:** Processes the `bulk_delete` POST request. Iterates through selected product IDs and calls `delete_product()` for each.
*   **Displaying messages:**  Displays success or error messages for import and delete operations, both from POST handling and URL query parameters (`import_message`, `add_message`, `delete_message`).
*   **Fetching data:**
    *   Retrieves products using `get_products()`, passing in filter, sort, and pagination parameters from the URL.
    *   Gets the total number of products using `get_total_products()`.
    *   Fetches available categories and sizes using `get_available_categories()` and `get_available_sizes()`.
* **Component Calls:**
    *   Calls `display_product_form()` to display the "Add Product" form.
    *   Calls `display_filter_form()` to display the filter form.
    *   Calls `display_product_table()` to display the product table.
    *   Calls `display_import_form()` to display the import form.
* **Menu Integration:**
    *   Adds a top-level menu item "Product Manager" using `add_menu_page()`. The callback function is `display_product_management_page()`.
* **Script Enqueueing:**
    * Enqueues scripts and styles, and localizes ajax URLs for `cc-product-management-form-handlers` and `cc-product-management-table-handlers`.

## Add Product (`admin/add-product.php`)

*   **Handles add request:** Receives product data as JSON in the request body.
*   **Validates nonce:** Uses `wp_verify_nonce()` with `add_product` action.
*   **Sanitizes and validates data:** Sanitizes text fields (`category`, `item`, `size`) and converts numerical fields to integers or floats. Handles empty `quantity_max` as null.
*   **Updates available categories/sizes:** Calls `add_available_category()` and `add_available_size()` to update the available options.
*   **Adds product:** Calls `add_product($product)` from `includes/data-handler.php`.
*   **Sends JSON response:** Returns JSON success or error messages.

## update Product (`admin/update-product.php`)

*   **Handles update request:** Receives product data as JSON in the request body.
*   **Retrieves product ID:** Gets the `id` from the JSON data.
*   **Validates nonce:** Uses `check_ajax_referer()` with `edit_product_$id` action and `edit_nonce` field.
*   **Sanitizes and validates data:** Sanitizes text fields and converts numerical fields to integers/floats, similar to `add-product.php`.
*   **Updates product:** Calls `update_product($id, $product)` from `includes/data-handler.php`.
*   **Sends JSON response:** Returns JSON success or error messages.

## delete Product (`admin/delete-product.php`)

*   **Handles delete request:**  Receives `product_id` and `delete_nonce` via POST.
*   **Validates nonce:** Uses `wp_verify_nonce()` to ensure security.
*   **Deletes product:** Calls `delete_product($product_id)` from `includes/data-handler.php`.
*   **Sends JSON response:** Returns a JSON success or error message using `wp_send_json_success()` and `wp_send_json_error()`.

## duplicate Product (`admin/duplicate-product.php`)

*   **Handles duplicate request:** Receives `product_id` and `duplicate_nonce` via POST.
*   **Validates nonce:** Uses `wp_verify_nonce()` with `duplicate_product_$product_id` action.
*   **Fetches product data:** Calls `get_product_by_id($product_id)` to retrieve the product to duplicate.
*   **Unsets product ID:** Removes the `id` key from the fetched product data to ensure a new product is created.
*   **Adds duplicated product:** Calls `add_product($product)` to insert the new product into the database.
*   **Sends JSON response:** Returns JSON success or error messages.

## import Product (`admin/components/forms/ImportForm.php`)

*   **Displays import form for JSON data.**
*   **Form fields:** Textarea (`import_data`) for JSON input.
*   **Nonce field (`import_nonce`).**
*   **JSON preview area (`json-preview`) - likely used by JavaScript.**
*   **Hidden input (`is_json_valid`) for JSON validity - likely set by JavaScript.**
*   **Submit button (`import_products`).**
*   **Form submission handled in `admin-page.php`.**

## filter and display (`admin/components/forms/FilterForm.php` and `admin/components/tables/ProductTable.php`)

*   **Filter Form (`admin/components/forms/FilterForm.php`):**
    *   **Displays filter form for product table.**
    *   **Form fields:** Category (dropdown), Size (dropdown), Price Min/Max (number), Quantity Min/Max (number), Discounted Only (checkbox), Search (text), Per Page (dropdown).
    *   **Populated from `$available_categories` and `$available_sizes`.**
    *   **Nonce field (`filter_nonce`).**
    *   **Data persistence using `$_GET` parameters.**
    *   **Form submission handled in `admin-page.php` via `get_products()` function.**

*   **Product Form (`admin/components/forms/ProductForm.php`):**
    *   **Displays "Add Product" form in a modal.**
    *   **Fields:** Category (datalist), Item, Size (datalist), Quantity Min, Quantity Max, Price, Discount.
    *   **Datalists for categories and sizes.**
    *   **Nonce field (`add_product_nonce`).**
    *   **Submit button with ID `submit-add-product`.**

*   **Product Table (`admin/components/tables/ProductTable.php`):**
    *   **Displays product table with sorting, pagination, and bulk actions.**
    *   **Sorting:** Sortable columns for Category, Item, Size, Price Break. URLs maintain sort parameters.
    *   **Pagination:** Previous/next page links and page number buttons. URLs maintain pagination parameters.
    *   **Bulk Actions:** "Delete" action with "Apply" button.
    *   **Actions per product:** "Delete", "Duplicate", "Edit" buttons with nonce fields and data attributes for JavaScript handling.

## Data Handling (`includes/data-handler.php`) vs. `includes/products-handler.php`

It appears there are **two files handling product data**, which might be a source of confusion or errors.

**`includes/data-handler.php`**: Seems to be the more complete and correct version.

*   Contains functions for:
    *   Fetching products (with filtering, sorting, pagination) - `get_products()`
    *   Fetching total product count - `get_total_products()`
    *   Fetching single product by ID - `get_product_by_id()`
    *   Fetching available categories and sizes - `get_available_categories()`, `get_available_sizes()`
    *   Adding, updating, and deleting products - `add_product()`, `update_product()`, `delete_product()` (by ID)
    *   Adding available categories and sizes - `add_available_category()`, `add_available_size()`
    *   Importing products from JSON - `import_products()`

**`includes/products-handler.php`**:  Appears to be an older or incomplete version, possibly for debugging.

*   Contains similar functions, but with differences:
    *   `get_products()`: Retrieves *all* products, no filtering/sorting/pagination.
    *   `add_product()` and `update_product()`: Similar, but include `var_dump` for debugging and handle `discount` differently.
    *   `delete_product()`: **Incorrectly implemented** - deletes by product *details* instead of ID. This is likely a bug.
    *   `get_available_categories()` and `get_available_sizes()`: Identical to `data-handler.php`.

**Recommendation:**

*   **Investigate why there are two data handler files.** Determine which one is intended to be the primary file.
*   **Fix the `delete_product()` function in `products-handler.php`** if it's intended to be used. It should delete by product ID, like in `data-handler.php`.
*   **Consolidate data handling logic** into a single file (`data-handler.php` seems like the better choice) to avoid confusion and potential inconsistencies.
*   **Remove debug code** (`var_dump`) from production files.

## JavaScript

There are two JavaScript files, currently with identical content. This is likely a mistake and should be addressed.

**`admin/assets/js/form-handlers.js` (Intended for form handling):**

*   **Handles "Add Product" form submission (modal):**
    *   Collects form data.
    *   Performs client-side validation.
    *   Sends AJAX POST request to `admin/add-product.php`.
    *   Closes modal and reloads page on success.
*   **Handles JSON validation for the import form:**
    *   Provides real-time validation and preview of JSON input.
    *   Updates a hidden input field with the validation status.

**`admin/assets/js/table-handlers.js` (Intended for table interactions):**

*   **Handles "Edit" button clicks in the product table:**
    *   Replaces table cells with input fields.
    *   Changes "Edit" button to "Save".
*   **Handles "Save" button clicks (after editing):**
    *   Collects updated values.
    *   Performs client-side validation.
    *   Sends AJAX POST request to `admin/update-product.php`.
    *   Updates table cells with new values on success.
*   **Handles "Delete" button clicks:**
    *   Confirms deletion.
    *   Sends AJAX POST request to `admin/delete-product.php`.
    *   Removes table row on success.
*   **Handles "Duplicate" button clicks:**
    *   Sends AJAX POST request to `admin/duplicate-product.php`.
    *   Reloads page on success (ideally, this would insert the new row directly).
*   **Handles "Select All" checkbox:** Selects/deselects all product checkboxes.
*   **Handles bulk actions (currently only "Delete"):**
    *   Collects selected product IDs.
    *   Submits a form to `admin-page.php` for bulk deletion.
*   **Handles row highlighting on checkbox selection.**
* **Uses event delegation for dynamic elements (delete/duplicate buttons).**
* **Includes a helper function `displayError` to show error messages.**
* **Includes a helper function `formatPriceBreak` to format the price break string.**

**Recommendation:**

*   **`admin/assets/js/table-handlers.js` and `admin/assets/js/form-handlers.js` are currently duplicates. Remove the duplicated code and separate the logic as described above.** The file `admin/assets/js/form-handlers.js` should only contain the logic for form handling (add and import), and `admin/assets/js/table-handlers.js` should contain the logic for table interactions.

## Incomplete Parts and Next Steps

Based on the analysis and the "TODO" items in the summary, the following are incomplete or require further action:

1.  **JavaScript File Duplication:** The most critical issue is the duplicated JavaScript files. This needs to be resolved by separating the logic into `form-handlers.js` (add product form, import form) and `table-handlers.js` (edit, delete, duplicate, bulk actions, select all, row highlighting).
2.  **`products-handler.php` vs. `data-handler.php`:** Determine which file is the intended data handler and consolidate/remove the other. Fix the `delete_product()` function in `products-handler.php` if it's kept.
3.  **`admin-ajax.php` Usage:** Examine how `admin-ajax.php` is used with the localized scripts. The scripts are localized with the URL, but the specific AJAX actions need to be clarified. It appears the `action` parameter in the AJAX requests (e.g., `update_product`, `delete_product`, `duplicate_product`) is used to route requests to appropriate handler functions within `admin-ajax.php`. This needs to be confirmed.
4.  **Update Product Form:** Examine how the "Edit" functionality pre-populates the form fields. Currently, the edit functionality replaces table cells with input fields *inline*. It would be better to use a modal similar to the "Add Product" form, pre-populated with the selected product's data.
5.  **Duplicate Item Prevention:** Examine if there's any logic to prevent duplicate items (e.g., checking for item name uniqueness). This might be a desirable feature to add.
6. **Import Form File Handling:** The import form currently takes JSON as text input. It should be updated to allow file uploads, and the server-side logic should handle file parsing.
7. **Missing Logic:** The original task mentioned that we were "in the middle of making updates" and that some parts were incomplete. Based on the current analysis, it's not immediately clear *what* specific updates were being made. The most likely candidates are:
    * The duplication of the JavaScript files.
    * The potential duplication/inconsistency between `data-handler.php` and `products-handler.php`.
    * The inline editing in the table (which should probably be a modal).