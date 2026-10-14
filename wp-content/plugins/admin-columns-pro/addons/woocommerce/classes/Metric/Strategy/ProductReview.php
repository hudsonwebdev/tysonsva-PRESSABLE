<?php

declare(strict_types=1);

namespace ACA\WC\Metric\Strategy;

use ACP\Metric\Strategy\Comment;
use ACP\Query\Bindings;

class ProductReview extends Comment
{
    public function handle(Bindings $bindings): void
    {
        ob_start();
        parent::handle($bindings);
    }

    protected function calculate_metric(array $clauses, Bindings $bindings): void
    {
        ob_end_clean();
        parent::calculate_metric($clauses, $bindings);
    }

}
