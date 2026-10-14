<?php

declare(strict_types=1);

namespace ACA\DataSources\Search\Comparison;

use AC\Helper\Select\Options\Paginated;
use ACA\DataSources;
use ACP\Helper;
use ACP\Search\Comparison\SearchableValues;
use ACP\Search\Operators;

class UserComparison extends DataSources\Search\Comparison implements SearchableValues
{
    public function __construct(string $id)
    {
        parent::__construct($id, new Operators([
            Operators::EQ,
            Operators::IS_EMPTY,
            Operators::NOT_IS_EMPTY,
        ]));
    }

    private function formatter(): Helper\Select\User\LabelFormatter\UserName
    {
        return new Helper\Select\User\LabelFormatter\UserName();
    }

    public function format_label($value): string
    {
        $user = get_user($value);

        return $user
            ? $this->formatter()->format_label($user)
            : '';
    }

    public function get_values(string $search, int $page): Paginated
    {
        $args = [
            'search' => $search,
            'paged'  => $page,
        ];

        return (new Helper\Select\User\PaginatedFactory())->create($args, $this->formatter());
    }

}
