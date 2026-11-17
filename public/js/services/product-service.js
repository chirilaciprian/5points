/**
 * Product CRUD requests. The only module that knows the products endpoints.
 */
import { request } from '../lib/http.js';
import { toQueryString } from '../products/list-query.js';

/**
 * Copy form data and add the fields the server needs to route the request.
 * HTML forms and multipart requests can only be sent as POST, so PATCH and
 * DELETE travel as POST with a `_method` field.
 */
function withFields(data, fields) {
    const body = new FormData();

    for (const [name, value] of data ?? []) {
        body.append(name, value);
    }

    for (const [name, value] of Object.entries(fields)) {
        body.set(name, value);
    }

    return body;
}

export const productService = {
    /**
     * A page of products, rendered by the server.
     *
     * @param {import('../products/list-query.js').ListQuery} query
     * @returns {Promise<{html: string, page: number, total: number}>}
     */
    list(query) {
        return request(`/products${toQueryString(query)}`);
    },

    /**
     * One product, as needed to fill the edit form.
     *
     * @param {number|string} id
     * @returns {Promise<object>}
     */
    async find(id) {
        const { product } = await request(`/product?id=${encodeURIComponent(id)}`);

        return product;
    },

    /**
     * @param {FormData} data The product form's fields.
     * @returns {Promise<{message: string}>}
     */
    create(data) {
        return request('/products', { method: 'POST', body: withFields(data, {}) });
    },

    /**
     * @param {number|string} id
     * @param {FormData} data The product form's fields.
     * @returns {Promise<{message: string}>}
     */
    update(id, data) {
        return request('/product', { method: 'POST', body: withFields(data, { _method: 'PATCH', id }) });
    },

    /**
     * @param {number|string} id
     * @returns {Promise<{message: string}>}
     */
    delete(id) {
        return request('/product', { method: 'POST', body: withFields(null, { _method: 'DELETE', id }) });
    },
};
