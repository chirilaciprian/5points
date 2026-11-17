/**
 * The products table: loading, searching, pagination and browser history.
 *
 * The table and pagination markup is rendered by the server and replaced on
 * every refresh, so clicks are handled with one delegated listener.
 */
import { productService } from '../services/product-service.js';
import { toast } from '../ui/toast.js';
import { readListQuery, toQueryString } from './list-query.js';

export class ProductList {
    /**
     * @param {object} options
     * @param {HTMLElement} options.element         Container of the table.
     * @param {HTMLFormElement} options.searchForm
     * @param {HTMLInputElement} options.searchInput
     * @param {(id: string, button: HTMLButtonElement) => void} options.onEdit
     * @param {(id: string, name: string) => void} options.onDelete
     */
    constructor({ element, searchForm, searchInput, onEdit, onDelete }) {
        this.element = element;
        this.searchInput = searchInput;
        this.onEdit = onEdit;
        this.onDelete = onDelete;

        /** What the list currently shows. */
        this.query = readListQuery();

        /** Used to ignore the responses of list requests that were superseded. */
        this.latestRequest = 0;

        // Search only runs on Enter or a click on the search button (form submit).
        searchForm.addEventListener('submit', (event) => {
            event.preventDefault();
            this.search();
        });

        element.addEventListener('click', (event) => this.handleClick(event));

        // Back / forward buttons.
        window.addEventListener('popstate', () => {
            const query = readListQuery();

            searchInput.value = query.search;
            this.load({ ...query, history: 'none' });
        });
    }

    /**
     * Fetch a page of products from the server and render it.
     *
     * @param {object}  [options]
     * @param {string}  [options.search]  Name filter (defaults to the current one).
     * @param {number}  [options.page]    Page number (defaults to the current one).
     * @param {'push'|'replace'|'none'} [options.history] How to record it in the browser history.
     */
    async load({ search = this.query.search, page = this.query.page, history = 'replace' } = {}) {
        const requestId = ++this.latestRequest;
        const isLatest = () => requestId === this.latestRequest;

        this.element.setAttribute('aria-busy', 'true');

        try {
            const data = await productService.list({ search, page });

            if (!isLatest()) {
                return;
            }

            this.element.innerHTML = data.html;

            // The server may have clamped the page (e.g. after a delete).
            this.query = { search, page: data.page };

            if (history !== 'none') {
                const method = history === 'push' ? 'pushState' : 'replaceState';

                window.history[method](null, '', `/${toQueryString(this.query)}`);
            }
        } catch (error) {
            if (isLatest()) {
                toast(error.message, 'error');
            }
        } finally {
            if (isLatest()) {
                this.element.removeAttribute('aria-busy');
            }
        }
    }

    /** Reload the current page of the current search. */
    refresh() {
        return this.load();
    }

    /** Show the first page, where a newly created product is listed. */
    showFirstPage() {
        return this.load({ page: 1 });
    }

    search() {
        const term = this.searchInput.value.trim();

        if (term !== this.query.search) {
            this.load({ search: term, page: 1 });
        }
    }

    handleClick(event) {
        const pageLink = event.target.closest('a[data-page]');

        if (pageLink) {
            event.preventDefault();

            this.load({ page: Number(pageLink.dataset.page), history: 'push' })
                .then(() => this.element.scrollIntoView({ block: 'nearest' }));

            return;
        }

        const button = event.target.closest('button[data-action]');

        if (button?.dataset.action === 'edit') {
            this.onEdit(button.dataset.id, button);
        } else if (button?.dataset.action === 'delete') {
            this.onDelete(button.dataset.id, button.dataset.name);
        }
    }
}
