<?php

declare(strict_types=1);

namespace ACA\DataSources\Setting;

use AC;
use AC\Setting\Config;

class Context extends AC\Column\Context
{
    private string $id;

    public function __construct(Config $config, string $id)
    {
        parent::__construct($config, __('Data Source', 'codepress-admin-columns'));

        $this->id = $id;
    }

    /**
     * Returns the id of the datasource
     */
    public function get_id(): string
    {
        return $this->id;
    }

}
