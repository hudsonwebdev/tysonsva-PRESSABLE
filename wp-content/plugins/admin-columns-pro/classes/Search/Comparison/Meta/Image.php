<?php

declare(strict_types=1);

namespace ACP\Search\Comparison\Meta;

use AC\Meta\Query;

class Image extends Attachment
{
    public function __construct(string $meta_key, ?Query $query = null)
    {
        parent::__construct($meta_key, $query, 'image');
    }

}
