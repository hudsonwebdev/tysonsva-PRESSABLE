<?php

declare(strict_types=1);

namespace ACA\DataSources\DataSource;

use ACA\DataSources\DataSource;
use ACA\DataSources\Type\Cardinality;
use ACP\Sql\Alias;
use ACP\Sql\JoinType;

interface Relation
{
    public function get_cardinality(): Cardinality;

    public function get_foreign_data_source(): DataSource;

    public function get_foreign_table(): string;

    public function get_foreign_column(): string;

    public function get_local_table(): string;

    public function get_local_column(): string;

    public function get_join_type(): JoinType;

    public function has_alias(): bool;

    public function get_alias(): Alias;

    public function with_alias(Alias $alias): self;

}
