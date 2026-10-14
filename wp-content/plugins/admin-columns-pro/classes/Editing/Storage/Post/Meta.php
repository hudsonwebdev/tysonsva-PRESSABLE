<?php

declare(strict_types=1);

namespace ACP\Editing\Storage\Post;

use AC\MetaType;
use ACP\Editing\Storage;

class Meta extends Storage\Meta
{
    public function __construct(string $meta_key)
    {
        parent::__construct($meta_key, MetaType::create_post_meta());
    }

}
