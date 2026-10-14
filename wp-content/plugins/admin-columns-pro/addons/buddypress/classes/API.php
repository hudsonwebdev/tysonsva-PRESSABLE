<?php

declare(strict_types=1);

namespace ACA\BP;

use BP_Groups_Group;

final class API
{
    public static function get_group(int $id): ?BP_Groups_Group
    {
        if (function_exists('bp_get_group_by')) {
            $group = bp_get_group_by('id', $id);

            return $group instanceof BP_Groups_Group ? $group : null;
        }

        $group = groups_get_group($id);

        return $group instanceof BP_Groups_Group && $group->id ? $group : null;
    }

}
