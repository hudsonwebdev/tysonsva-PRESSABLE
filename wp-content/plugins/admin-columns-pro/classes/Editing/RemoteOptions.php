<?php

declare(strict_types=1);

namespace ACP\Editing;

use AC\Helper\Select\Options;

interface RemoteOptions
{
    public function get_remote_options(?int $id = null): Options;

}
