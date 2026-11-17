/**
 * Behaviour shared by every <dialog> on the page.
 */

/** Make every `[data-close]` button inside a dialog close that dialog. */
export function initDialogCloseButtons(root = document) {
    root.querySelectorAll('dialog [data-close]').forEach((button) => {
        button.addEventListener('click', () => button.closest('dialog').close());
    });
}
