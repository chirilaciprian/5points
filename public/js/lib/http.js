/**
 * Thin wrapper around fetch() for the application's JSON endpoints.
 */

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

/**
 * A failed request. `status` is 0 when the server could not be reached and
 * `errors` holds the per-field validation messages of a 422 response.
 */
export class HttpError extends Error {
    constructor(message, { status = 0, errors = {} } = {}) {
        super(message);

        this.name = 'HttpError';
        this.status = status;
        this.errors = errors;
    }
}

/**
 * Call a JSON endpoint. Resolves with the decoded response and rejects with
 * an HttpError.
 *
 * @param {string} url
 * @param {object} [options]
 * @param {string} [options.method]
 * @param {FormData|null} [options.body]
 * @returns {Promise<object>}
 */
export async function request(url, { method = 'GET', body = null } = {}) {
    let response;

    try {
        response = await fetch(url, {
            method,
            body,
            headers: {
                'Accept': 'application/json',
                'X-CSRF-Token': csrfToken,
            },
        });
    } catch {
        throw new HttpError('Could not reach the server. Check your connection and try again.');
    }

    const data = await response.json().catch(() => null);

    if (!response.ok || data === null) {
        throw new HttpError(data?.message ?? 'Something went wrong. Please try again.', {
            status: response.status,
            errors: data?.errors ?? {},
        });
    }

    return data;
}
