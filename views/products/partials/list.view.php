<?php
/**
 * The products table and its pagination. Rendered inside the page on first
 * load and returned on its own for AJAX refreshes.
 *
 * @var array<array<string, mixed>> $products
 * @var Core\Paginator $paginator
 * @var string $search
 */

use Core\UploadedImage;

$pageUrl = fn (int $page) => '/?' . http_build_query(
    ($search !== '' ? ['search' => $search] : []) + ['page' => $page]
);
?>

<?php if (!$products) : ?>
    <p class="empty">
        <?php if ($search !== '') : ?>
            No products match &ldquo;<?= e($search) ?>&rdquo;.
        <?php else : ?>
            There are no products yet. Use &ldquo;Add product&rdquo; to create the first one.
        <?php endif; ?>
    </p>
<?php else : ?>
    <table class="products">
        <thead>
            <tr>
                <th scope="col">Name</th>
                <th scope="col">Description</th>
                <th scope="col" class="products__price">Price</th>
                <th scope="col">Image</th>
                <th scope="col">Availability date</th>
                <th scope="col">Stock status</th>
                <th scope="col"><span class="sr-only">Actions</span></th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($products as $product) : ?>
                <tr>
                    <td data-label="Name" class="products__name"><?= e($product['name']) ?></td>

                    <td data-label="Description" class="products__description">
                        <?php if ($product['description'] !== null) : ?>
                            <span title="<?= e($product['description']) ?>"><?= e($product['description']) ?></span>
                        <?php else : ?>
                            <span class="muted">&mdash;</span>
                        <?php endif; ?>
                    </td>

                    <td data-label="Price" class="products__price"><?= e(number_format((float) $product['price'], 2)) ?></td>

                    <td data-label="Image">
                        <?php if ($imageUrl = UploadedImage::url($product['image'])) : ?>
                            <a href="<?= e($imageUrl) ?>" target="_blank" rel="noopener">
                                <img class="thumbnail thumbnail--image" src="<?= e($imageUrl) ?>" alt="<?= e($product['name']) ?>" width="48" height="48" loading="lazy">
                            </a>
                        <?php else : ?>
                            <span class="thumbnail" role="img" aria-label="No image">No image</span>
                        <?php endif; ?>
                    </td>

                    <td data-label="Availability date">
                        <?php if ($product['availability_date'] !== null) : ?>
                            <time datetime="<?= e($product['availability_date']) ?>">
                                <?= e(date('M j, Y', strtotime($product['availability_date']))) ?>
                            </time>
                        <?php else : ?>
                            <span class="muted">&mdash;</span>
                        <?php endif; ?>
                    </td>

                    <td data-label="Stock status">
                        <?php if ($product['in_stock']) : ?>
                            <span class="badge badge--success">In stock</span>
                        <?php else : ?>
                            <span class="badge badge--muted">Out of stock</span>
                        <?php endif; ?>
                    </td>

                    <td class="products__actions">
                        <button type="button" class="btn btn--small" data-action="edit" data-id="<?= e($product['id']) ?>">
                            Edit<span class="sr-only"> <?= e($product['name']) ?></span>
                        </button>
                        <button type="button" class="btn btn--small btn--danger-outline" data-action="delete" data-id="<?= e($product['id']) ?>" data-name="<?= e($product['name']) ?>">
                            Delete<span class="sr-only"> <?= e($product['name']) ?></span>
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="list-footer">
        <p class="list-footer__summary">
            Showing <?= $paginator->from() ?>&ndash;<?= $paginator->to() ?> of <?= $paginator->total ?>
            <?= $paginator->total === 1 ? 'product' : 'products' ?>
        </p>

        <?php if ($paginator->hasPages()) : ?>
            <nav class="pagination" aria-label="Pagination">
                <?php if ($paginator->page > 1) : ?>
                    <a class="pagination__link" href="<?= e($pageUrl($paginator->page - 1)) ?>" data-page="<?= $paginator->page - 1 ?>" rel="prev">Previous</a>
                <?php else : ?>
                    <span class="pagination__link pagination__link--disabled" aria-disabled="true">Previous</span>
                <?php endif; ?>

                <?php foreach ($paginator->pages() as $number) : ?>
                    <?php if ($number === null) : ?>
                        <span class="pagination__gap" aria-hidden="true">&hellip;</span>
                    <?php elseif ($number === $paginator->page) : ?>
                        <span class="pagination__link pagination__link--current" aria-current="page"><?= $number ?></span>
                    <?php else : ?>
                        <a class="pagination__link" href="<?= e($pageUrl($number)) ?>" data-page="<?= $number ?>" aria-label="Page <?= $number ?>"><?= $number ?></a>
                    <?php endif; ?>
                <?php endforeach; ?>

                <?php if ($paginator->page < $paginator->lastPage) : ?>
                    <a class="pagination__link" href="<?= e($pageUrl($paginator->page + 1)) ?>" data-page="<?= $paginator->page + 1 ?>" rel="next">Next</a>
                <?php else : ?>
                    <span class="pagination__link pagination__link--disabled" aria-disabled="true">Next</span>
                <?php endif; ?>
            </nav>
        <?php endif; ?>
    </div>
<?php endif; ?>
