# Code Review Notes - Backend UI Refinements

## Issues and Improvements

### `admin/admin-page.php`

-   **Redirects after form submissions:** The redirects after adding, deleting, or importing products should be replaced with AJAX submissions for a better user experience (especially with the modal for adding products).
-   **Missing nonce fields:** The edit, delete, and duplicate forms in the product table are missing nonce fields for CSRF protection. This is a security vulnerability.

### `admin/components/forms/ProductForm.php`

- **Missing Nonce:** The form is missing a nonce field.

### `admin/components/forms/FilterForm.php`

- **Missing Nonce:** The form is missing a nonce field.

### `admin/components/tables/ProductTable.php`
- **Missing Nonce:** The "Delete", "Duplicate" and "Edit" forms within each table row are missing nonce fields.