<?php

declare(strict_types=1);

namespace ACP\Sorting\Model\Post\Author;

use ACP\Query\Bindings;
use ACP\Sorting\Model\Base;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Type\CastType;
use ACP\Sorting\Type\DataType;
use ACP\Sorting\Type\Order;
use ACP\Sql\Join;

class UserMeta extends Base
{
    private string $meta_key;

    protected DataType $data_type;

    public function __construct(string $meta_key, ?DataType $data_type = null)
    {
        $this->meta_key = $meta_key;
        $this->data_type = $data_type ?: new DataType(DataType::STRING);
    }

    protected function configure_query_bindings(Bindings $bindings, string $alias, Order $order): void
    {
        global $wpdb;

        $bindings->join(
            Join::inner($wpdb->usermeta, $alias)
                ->on("$wpdb->posts.post_author", '=', "$alias.user_id")
                ->on("$alias.meta_key", '=', $wpdb->prepare('%s', $this->meta_key))
                ->get_sql()
        );
        $bindings->order_by(
            SqlOrderByFactory::create(
                "$alias.meta_value",
                (string)$order,
                [
                    'cast_type' => $this->get_cast_type(),
                ]
            )
        );
    }

    private function get_cast_type(): ?CastType
    {
        return DataType::STRING !== (string)$this->data_type
            ? CastType::create_from_data_type($this->data_type)
            : null;
    }

}
