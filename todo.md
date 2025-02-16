# TODO List for cclist Plugin

**Instructions for AI:**

This is a dynamic TODO list for the cclist plugin.  You **must** keep this list updated as you work.

1.  **Status Indicators:**
    *   `[ ]`: Not Started
    *   `[/]`: In Progress
    *   `[x]`: Completed

2.  **Completion Checkbox:**
    *    `[ ]`: Not Done
    *    `[x]`: Done

3. **Updating the List:** When you start or complete a task, update the corresponding status indicator and checkbox in this file (`todo.md`).  **Always save the changes.**

4. **New Issues:** If you encounter new issues or tasks during development, create a separate Markdown file named `new_issues.md` to document them. Do *not* add them directly to this TODO list.

**TODO List (Prioritized by Dependencies and Complexity):**

*   [x] [x] **`code-review-notes.md` Completion:** Address the "xxx" on line 20 of `code-review-notes.md` to complete the action plan.

*   [x] [x] **Data Handler Consolidation:**
    *   [x] Determine the primary data handler file (`includes/data-handler.php` or `includes/products-handler.php`).
    *   [x] Consolidate logic into the chosen file.
    *   [ ] If `includes/products-handler.php` is kept, fix the `delete_product()` function.
    *   [x] Remove the unused file.
    *   [ ] **ISSUE:** and for some reason - the front end (a different app) is not showing all the products anymore, but it still shos some of them - so the data handling music have changed too much.

*   [x] [/] **JavaScript File Consolidation:**
    *   [x] Separate the logic of `admin/assets/js/form-handlers.js` and `admin/assets/js/table-handlers.js` into their respective intended functionalities (form handling vs. table interactions).
    *   [x] Remove the duplicated code.
    *   [/] **ISSUE:** The checkboxes - the one that should check them all - does not make the others checked.
    *   [ ] **ISSUE:** Add product button does not do anything.
    *   [ ] **ISSUE:** Duplicate button does not do anything - it should open the add product with the duplicated info.
    *   [ ] **ISSUE:** Edit button does not do anything

*   [x] [x] **Nonce Implementation:**
    *   [x] Add nonce fields to the "Add Product" form (`admin/components/forms/ProductForm.php`).
    *   [x] Add nonce fields to the "Filter Products" form (`admin/components/forms/FilterForm.php`).
    *   [x] Add nonce fields to the "Delete", "Duplicate", and "Edit" forms within each table row (`admin/components/tables/ProductTable.php`).
    *   [x] Add a nonce check to the `admin/update-product.php` AJAX handler.

*   [x] [x] **AJAX Implementation (Add Product):**
    *   [x] Modify `admin/components/forms/ProductForm.php` and `admin/admin-page.php` to handle form submission via AJAX.
    *   [x] Remove the redirect after adding a product.

*   [ ] [ ] **AJAX Implementation (Edit & Delete):**
    *   [ ] Modify `admin/assets/js/table-handlers.js` to send AJAX requests for edit and delete actions.
    *   [ ] Update `admin/update-product.php` to handle the edit AJAX request (including nonce verification).
    *   [ ] Create `admin/delete-product.php` to handle the delete AJAX request (including nonce verification).
    *   [ ] **ISSUE:** Delete works - but goes to a WordPress critical error page - The delete error is...
    *   [ ] **ISSUE:** When I selected delete - and click apply on the bulk action, nothing happens

*   [ ] [ ] **Modal-Based Editing:**
    *   [ ] Refactor the "Edit" functionality in `admin/components/tables/ProductTable.php` to use a modal (similar to the "Add Product" form) instead of inline editing.
    *   [ ] Pre-populate the modal with the selected product's data.

*   [ ] [ ] **AJAX Implementation (Import Data):**
    *   [ ] Modify `admin/components/forms/ImportForm.php` to remove redirect.
    *   [ ] Update `admin/admin-page.php` to handle form submission via AJAX.

*   [ ] [ ] **Import Form File Upload:**
    *   [ ] Modify the import form (`admin/components/forms/ImportForm.php`) to allow file uploads.
    *   [ ] Update server-side logic (likely in `admin/admin-page.php` and `includes/data-handler.php`) to handle file parsing.

*   [ ] [ ] **UI Enhancements:**
    *   [ ] **ISSUE:** Product Size Column does not need to be so wide - it should adjust to content width.
    *   [ ] **ISSUE:** Price break column doesn't need to be so wide - it should adjust to content width.
    *   [ ] **ISSUE:** The filter fields should be spread on 2 rows.
    *   [ ] **ISSUE:** The lines alternate grey and white on the list, but Grouped items should not have alternating and should be grouped with better differentiation.
    *   [ ] **ISSUE:** The default display count of items should be 35, 65, 100, All.
    *   [ ] **ISSUE:** Filter products should look like it's part of the product list header.
    *   [ ] **ISSUE:** Quantity min max - they do not need very wide input fields - the most would be 4 digits.
    *   [ ] **ISSUE:** Bulk action and page number should be at the bottom AND top

*   [ ] [ ] **Category and Size Editing:**
    *  [ ] **ISSUE:** There is no way to edit the categories - there is no way to edit the sizes.