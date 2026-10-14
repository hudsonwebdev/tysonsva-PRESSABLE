<?php

declare(strict_types=1);

namespace ACP\Editing;

use AC\Request;

interface RequestHandler
{
    public function handle(Request $request);

}
