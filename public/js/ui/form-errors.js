/**
 * Shows validation errors in a form: one message per field, in the element
 * marked `data-error-for="<field name>"`, plus a summary for errors that do
 * not belong to a field (network, session, server error...).
 */
export class FormErrors {
    /**
     * @param {HTMLFormElement} form
     * @param {HTMLElement} summary
     */
    constructor(form, summary) {
        this.form = form;
        this.summary = summary;
    }

    clear() {
        this.summary.hidden = true;
        this.summary.textContent = '';

        this.form.querySelectorAll('[data-error-for]').forEach((element) => {
            element.hidden = true;
            element.textContent = '';
        });

        this.form.querySelectorAll('[aria-invalid]').forEach((field) => field.removeAttribute('aria-invalid'));
    }

    /**
     * Show the errors of a failed request and focus the first invalid field.
     *
     * @param {import('../lib/http.js').HttpError} error
     */
    show(error) {
        let firstInvalidField = null;

        for (const [name, message] of Object.entries(error.errors ?? {})) {
            const field = this.set(name, message);

            firstInvalidField ??= field;
        }

        if (firstInvalidField) {
            firstInvalidField.focus();
        } else {
            this.summary.textContent = error.message;
            this.summary.hidden = false;
        }
    }

    /**
     * @param {string} name
     * @param {string} message
     * @returns {HTMLElement|null} The field, or null if the form has no such field.
     */
    set(name, message) {
        const field = this.form.elements.namedItem(name);
        const element = this.messageElement(name);

        if (!field || !element) {
            return null;
        }

        element.textContent = message;
        element.hidden = false;
        field.setAttribute('aria-invalid', 'true');

        return field;
    }

    /** @param {string} name */
    remove(name) {
        const element = this.messageElement(name);

        if (element) {
            element.hidden = true;
            element.textContent = '';
        }

        this.form.elements.namedItem(name)?.removeAttribute('aria-invalid');
    }

    messageElement(name) {
        return this.form.querySelector(`[data-error-for="${CSS.escape(name)}"]`);
    }
}
