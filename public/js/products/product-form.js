/**
 * The create / edit product dialog.
 */
import { productService } from '../services/product-service.js';
import { FormErrors } from '../ui/form-errors.js';
import { toast } from '../ui/toast.js';
import { ImageField } from './image-field.js';

export class ProductFormDialog {
    /**
     * @param {object} options
     * @param {HTMLDialogElement} options.dialog
     * @param {(result: {isNew: boolean}) => void} options.onSaved Called after a successful save.
     * @param {() => void} options.onMissing Called when the product to edit no longer exists.
     */
    constructor({ dialog, onSaved, onMissing }) {
        this.dialog = dialog;
        this.onSaved = onSaved;
        this.onMissing = onMissing;

        this.form = dialog.querySelector('#product-form');
        this.title = dialog.querySelector('#product-dialog-title');
        this.submitButton = dialog.querySelector('#product-form-submit');

        this.errors = new FormErrors(this.form, dialog.querySelector('#product-form-error'));

        this.image = new ImageField({
            input: dialog.querySelector('#product-image'),
            preview: dialog.querySelector('#product-image-preview'),
            placeholder: dialog.querySelector('#product-image-placeholder'),
            removeField: dialog.querySelector('#product-remove-image-field'),
            removeInput: dialog.querySelector('#product-remove-image'),
            errors: this.errors,
        });

        this.form.addEventListener('submit', (event) => {
            event.preventDefault();
            this.submit();
        });

        // Release the preview of a selected image once the form is closed.
        dialog.addEventListener('close', () => {
            this.form.reset();
            this.image.reset();
        });
    }

    /** Open the empty form. */
    openCreate() {
        this.open(null);
    }

    /**
     * Load a product and open the form filled with it.
     *
     * @param {string} id
     * @param {HTMLButtonElement} [button] Disabled while the product loads.
     */
    async openEdit(id, button) {
        if (button) {
            button.disabled = true;
        }

        try {
            this.open(await productService.find(id));
        } catch (error) {
            toast(error.message, 'error');

            if (error.status === 404) {
                // Somebody else deleted it in the meantime.
                this.onMissing();
            }
        } finally {
            if (button) {
                button.disabled = false;
            }
        }
    }

    /** @param {object|null} product */
    open(product) {
        const fields = this.form.elements;

        this.form.reset();
        this.errors.clear();
        this.image.reset(product?.image_url ?? null);

        this.title.textContent = product ? 'Edit product' : 'Add product';
        this.submitButton.textContent = product ? 'Save changes' : 'Add product';

        fields.namedItem('id').value = product?.id ?? '';

        if (product) {
            fields.namedItem('name').value = product.name;
            fields.namedItem('description').value = product.description;
            fields.namedItem('price').value = product.price;
            fields.namedItem('availability_date').value = product.availability_date;
            fields.namedItem('in_stock').checked = product.in_stock;
        }

        this.dialog.showModal();
        fields.namedItem('name').focus();
    }

    async submit() {
        if (!this.image.validate()) {
            return;
        }

        const id = this.form.elements.namedItem('id').value;
        const data = new FormData(this.form);
        const label = this.submitButton.textContent;

        this.errors.clear();
        this.submitButton.disabled = true;
        this.submitButton.textContent = 'Saving…';

        try {
            const response = id === ''
                ? await productService.create(data)
                : await productService.update(id, data);

            this.dialog.close();
            toast(response.message);
            this.onSaved({ isNew: id === '' });
        } catch (error) {
            this.errors.show(error);
        } finally {
            this.submitButton.disabled = false;
            this.submitButton.textContent = label;
        }
    }
}
