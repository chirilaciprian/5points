/**
 * The image input of the product form: preview of the selected or stored
 * image, the "remove current image" checkbox, and quick client-side checks so
 * the user does not wait for an upload to learn it is rejected (the server
 * validates again).
 */
export class ImageField {
    /**
     * @param {object} options
     * @param {HTMLInputElement} options.input        The file input (`accept` and `data-max-size` drive the checks).
     * @param {HTMLImageElement} options.preview
     * @param {HTMLElement} options.placeholder       Shown when there is no image.
     * @param {HTMLElement} options.removeField       Wrapper of the remove checkbox.
     * @param {HTMLInputElement} options.removeInput
     * @param {import('../ui/form-errors.js').FormErrors} options.errors
     */
    constructor({ input, preview, placeholder, removeField, removeInput, errors }) {
        this.input = input;
        this.preview = preview;
        this.placeholder = placeholder;
        this.removeField = removeField;
        this.removeInput = removeInput;
        this.errors = errors;

        /** URL of the stored image of the product being edited (null if none). */
        this.currentUrl = null;

        /** Object URL of the preview of a newly selected image. */
        this.objectUrl = null;

        input.addEventListener('change', () => {
            const message = this.error();

            if (message) {
                errors.set(input.name, message);
            } else {
                errors.remove(input.name);
            }

            this.updatePreview();
        });

        removeInput.addEventListener('change', () => this.updatePreview());
    }

    /**
     * Start over, e.g. when the form is opened or closed. Call after the
     * form has been reset.
     *
     * @param {string|null} currentUrl The stored image of the product being edited.
     */
    reset(currentUrl = null) {
        this.currentUrl = currentUrl;
        this.updatePreview();
    }

    /** @returns {string|null} Why the selected file would be rejected, or null. */
    error() {
        const file = this.input.files[0];

        if (!file) {
            return null;
        }

        if (!this.input.accept.split(',').includes(file.type)) {
            return 'The image must be a JPG, PNG, GIF or WEBP file.';
        }

        const maxSize = Number(this.input.dataset.maxSize);

        if (file.size > maxSize) {
            return `The image may not be larger than ${Math.round(maxSize / 1024 / 1024 * 10) / 10} MB.`;
        }

        return null;
    }

    /** Show the error of the selected file, if any. Returns false if there is one. */
    validate() {
        const message = this.error();

        if (message) {
            this.errors.set(this.input.name, message)?.focus();
        }

        return message === null;
    }

    /**
     * Show the newly selected image, else the stored one (unless it is marked
     * for removal), else the placeholder.
     */
    updatePreview() {
        if (this.objectUrl) {
            URL.revokeObjectURL(this.objectUrl);
            this.objectUrl = null;
        }

        const file = this.input.files[0];
        let url = null;

        if (file && this.error() === null) {
            url = this.objectUrl = URL.createObjectURL(file);
        } else if (!file && this.currentUrl && !this.removeInput.checked) {
            url = this.currentUrl;
        }

        this.preview.hidden = url === null;
        this.placeholder.hidden = url !== null;

        if (url === null) {
            this.preview.removeAttribute('src');
        } else {
            this.preview.src = url;
        }

        // Removing only makes sense while the stored image would be kept.
        this.removeField.hidden = this.currentUrl === null || Boolean(file);
    }
}
