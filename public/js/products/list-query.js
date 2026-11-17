/**
 * The product list's search term and page number, and their representation
 * in the URL query string (`?search=...&page=...`).
 */

/**
 * @typedef {object} ListQuery
 * @property {string} search Name filter ('' for none).
 * @property {number} page   1-based page number.
 */

/** @returns {ListQuery} The query of the current browser URL. */
export function readListQuery() {
    const params = new URLSearchParams(window.location.search);

    return {
        search: (params.get('search') ?? '').trim(),
        page: Math.max(1, parseInt(params.get('page'), 10) || 1),
    };
}

/**
 * @param {ListQuery} query
 * @returns {string} '?search=...&page=...' without the default values, or ''.
 */
export function toQueryString({ search = '', page = 1 }) {
    const params = new URLSearchParams();

    if (search !== '') {
        params.set('search', search);
    }

    if (page > 1) {
        params.set('page', String(page));
    }

    const query = params.toString();

    return query === '' ? '' : `?${query}`;
}
