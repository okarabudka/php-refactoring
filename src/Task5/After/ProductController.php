<?php

declare(strict_types=1);

namespace Okarabudka\Refactoring\Task5\After;

final readonly class ProductController
{
    public const string MISSING_ID = 'Missing product ID';
    public const string NOT_FOUND = 'Product not found';

    public function __construct(
        private ProductRepository $products,
        private ProductView $view,
    ) {}

    /**
     * Returns the page body instead of echoing it; the front controller decides how to send it.
     *
     * @param array<mixed> $query the request's query parameters, e.g. $_GET
     */
    public function show(array $query): string
    {
        $id = filter_var($query['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($id === false) {
            return self::MISSING_ID;
        }

        $product = $this->products->find($id);

        return $product === null ? self::NOT_FOUND : $this->view->render($product);
    }
}
