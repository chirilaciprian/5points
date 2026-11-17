/**
 * Confirmation shown before a product is permanently deleted.
 */
import { productService } from '../services/product-service.js';
import { toast } from '../ui/toast.js';

export class DeleteDialog {
    /**
     * @param {object} options
     * @param {HTMLDialogElement} options.dialog
     * @param {() => void} options.onDone Called after the request, whether it succeeded or not.
     */
    constructor({ dialog, onDone }) {
        this.dialog = dialog;
        this.onDone = onDone;

        this.nameElement = dialog.querySelector('#delete-dialog-name');
        this.confirmButton = dialog.querySelector('#delete-dialog-confirm');

        /** Id of the product waiting for a confirmation. */
        this.productId = null;

        this.confirmButton.addEventListener('click', () => this.confirm());
    }

    /**
     * @param {string} id
     * @param {string} name
     */
    open(id, name) {
        this.productId = id;
        this.nameElement.textContent = name;
        this.dialog.showModal();
    }

    async confirm() {
        this.confirmButton.disabled = true;

        try {
            const { message } = await productService.delete(this.productId);

            toast(message);
        } catch (error) {
            toast(error.message, 'error');
        } finally {
            this.confirmButton.disabled = false;
            this.productId = null;
            this.dialog.close();

            // Refresh in both cases: after an error the list may be stale.
            this.onDone();
        }
    }
}
