# Code Review Notes - Backend UI Refinements

## Prioritized Action Plan (Importance and Implementation Order)

1.  **Security Fixes (Nonces):**
    *   Add nonce fields to the "Add Product" form (`ProductForm.php`).
    *   Add nonce fields to the "Filter Products" form (`FilterForm.php`).
    *   Add nonce fields to the "Delete", "Duplicate", and "Edit" forms within each table row (`ProductTable.php`).
    *   Add a nonce check to the `update-product.php` AJAX handler.

2.  **Refactor `table-handlers.js`:**
    *   Refactor the price break string formatting into a separate function.
    *   Improve error handling to display user-friendly messages.
    *   Add client-side input validation (numeric checks for price, quantity, discount).

3.  **Implement AJAX for "Edit" and "Delete":**
    *   Modify `table-handlers.js` to send AJAX requests for edit and delete actions.
    *   Update `update-product.php` to handle the edit AJAX request (add nonce verification).
    *   Create a new PHP file (e.g., `delete-product.php`) to handle the delete AJAX request.
xxx
4.  **Implement AJAX for "Add Product":**
    *   Modify `ProductForm.php` and `admin-page.php` to handle form submission via AJAX.
    *   Remove the redirect after adding a product.

5.  **Implement AJAX for "Import Data":**
     * Modify `admin/admin-page.php` to remove redirect.

6.  **AJAX URL:**
    *   Use a WordPress function (e.g., `admin_url('admin-ajax.php')`) to generate the AJAX URL in `table-handlers.js`.

## Detailed Changes

### `admin/admin-page.php`

-   **Redirects after form submissions:** The redirects after adding, deleting, or importing products should be replaced with AJAX submissions for a better user experience (especially with the modal for adding products).
-   **Missing nonce fields:** The edit, delete, and duplicate forms in the product table are missing nonce fields for CSRF protection. This is a security vulnerability.

### `admin/components/forms/ProductForm.php`

- **Missing Nonce:** The form is missing a nonce field.

### `admin/components/forms/FilterForm.php`

- **Missing Nonce:** The form is missing a nonce field.

### `admin/components/tables/ProductTable.php`
- **Missing Nonce:** The "Delete", "Duplicate" and "Edit" forms within each table row are missing nonce fields.

### `admin/assets/js/table-handlers.js`
- **AJAX URL:** The URL for the AJAX request (`../admin/update-product.php`) is hardcoded. It would be better to use a WordPress function to generate this URL.
- **Code Duplication:** The price break string formatting is duplicated in both `handleEditClick` and `handleSaveClick`. This could be refactored into a separate function.
- **Input Validation:** Adding client-side validation would improve the user experience.
- **Error Handling:** The error handling could be improved. Instead of just displaying an alert, it could display a more user-friendly message in a designated area on the page.

### `admin/update-product.php`
- **Missing Nonce:** The AJAX handler is missing a nonce check for security.