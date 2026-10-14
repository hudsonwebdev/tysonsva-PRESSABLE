<?php

declare(strict_types=1);

namespace ACP\Access;

interface Rule
{
    public function modify(Permissions $permissions): Permissions;

}
