/**
 * Short success / error messages shown in the corner of the page.
 */

const container = document.getElementById('toasts');

/**
 * @param {string} message
 * @param {'success'|'error'} [type]
 */
export function toast(message, type = 'success') {
    const element = document.createElement('div');
    element.className = `toast toast--${type}`;

    if (type === 'error') {
        // Errors are announced immediately, successes politely.
        element.setAttribute('role', 'alert');
    }

    const text = document.createElement('span');
    text.textContent = message;

    const close = document.createElement('button');
    close.type = 'button';
    close.className = 'toast__close';
    close.setAttribute('aria-label', 'Dismiss');
    close.textContent = '×';
    close.addEventListener('click', () => element.remove());

    element.append(text, close);
    container.append(element);

    setTimeout(() => element.remove(), type === 'error' ? 8000 : 4000);
}
