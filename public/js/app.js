/**
 * Product Management - front end entry point.
 *
 * Every operation (listing, searching, paginating, creating, updating and
 * deleting products) is done with AJAX calls to the JSON endpoints; see
 * services/product-service.js. This file only connects the page's parts.
 */
import { DeleteDialog } from './products/delete-dialog.js';
import { ProductFormDialog } from './products/product-form.js';
import { ProductList } from './products/product-list.js';
import { initDialogCloseButtons } from './ui/dialogs.js';

const productForm = new ProductFormDialog({
    dialog: document.getElementById('product-dialog'),
    // A new product is listed first, so show the first page.
    onSaved: ({ isNew }) => (isNew ? list.showFirstPage() : list.refresh()),
    onMissing: () => list.refresh(),
});

const deleteDialog = new DeleteDialog({
    dialog: document.getElementById('delete-dialog'),
    onDone: () => list.refresh(),
});

const list = new ProductList({
    element: document.getElementById('product-list'),
    searchForm: document.getElementById('search-form'),
    searchInput: document.getElementById('search'),
    onEdit: (id, button) => productForm.openEdit(id, button),
    onDelete: (id, name) => deleteDialog.open(id, name),
});

document.getElementById('add-product').addEventListener('click', () => productForm.openCreate());

initDialogCloseButtons();
