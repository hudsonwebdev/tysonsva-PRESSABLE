<?php

declare(strict_types=1);

namespace ACP\Editing\Storage\User;

use AC;
use AC\Helper;
use ACP\Editing\Storage;
use WP_User;

class Role implements Storage
{
    private bool $allow_non_editable_roles;

    public function __construct(bool $allow_non_editable_roles)
    {
        $this->allow_non_editable_roles = $allow_non_editable_roles;
    }

    public function get(int $id)
    {
        $user = get_user_by('id', $id);
        $roles = $user ? $this->get_role_names($user) : null;

        return $roles
            ? array_values(array_filter($roles, [$this, 'is_editable_role']))
            : false;
    }

    /**
     * @return string[]
     */
    private function get_role_names(WP_User $user): array
    {
        return Helper\UserRoles::create()->find_role_names($user);
    }

    private function get_editable_roles(): AC\Type\UserRoles
    {
        static $editable_roles;

        if (null === $editable_roles) {
            $editable_roles = Helper\UserRoles::create()->find_all($this->allow_non_editable_roles);
        }

        return $editable_roles;
    }

    private function is_editable_role(string $role): bool
    {
        foreach ($this->get_editable_roles() as $editable_role) {
            if ($role === $editable_role->get_name()) {
                return true;
            }
        }

        return false;
    }

    private function is_not_editable_role(string $role): bool
    {
        return ! $this->is_editable_role($role);
    }

    public function update(int $id, $data): bool
    {
        if (! current_user_can('edit_users') || ! current_user_can('promote_user', $id)) {
            return false;
        }

        $user = get_user_by('id', $id);

        if (! $user) {
            return false;
        }

        $params = $data;

        if (! isset($params['method'])) {
            $params = [
                'method' => 'replace',
                'value'  => $params,
            ];
        }

        $roles = $params['value'] ?: [];

        switch ($params['method']) {
            case 'add':
                $this->add_roles($user, $roles);

                break;
            case 'remove':
                $this->safely_remove_roles($user, $roles);

                break;
            default:
                $this->replace_roles($user, $roles);
        }

        return true;
    }

    private function replace_roles(WP_User $user, array $roles): void
    {
        foreach ($roles as $role) {
            if (! in_array($role, $this->get_role_names($user), true)) {
                $user->add_role($role);
            }
        }

        $remove = [];

        foreach ($this->get_role_names($user) as $role) {
            if (! in_array($role, $roles, true)) {
                $remove[] = $role;
            }
        }

        $this->safely_remove_roles($user, $remove);
    }

    private function add_roles(WP_User $user, array $roles): void
    {
        foreach ($roles as $role) {
            if (! in_array($role, $this->get_role_names($user), true)) {
                $user->add_role($role);
            }
        }
    }

    private function safely_remove_roles(WP_User $user, array $roles): void
    {
        $exluded_roles = $this->get_non_removeable_roles($user);

        foreach ($roles as $role) {
            if (! in_array($role, $exluded_roles, true)) {
                $this->remove_role($user, $role);
            }
        }
    }

    /**
     * WP_User::remove_role() compares the given role against WP_User::$roles with a strict in_array(),
     * so a numeric role slug can only be removed by passing back the integer that WordPress stores.
     */
    private function remove_role(WP_User $user, string $role): void
    {
        foreach ($user->roles as $stored_role) {
            if ($role === (string)$stored_role) {
                $user->remove_role($stored_role);

                return;
            }
        }
    }

    private function get_non_removeable_roles(WP_User $user): array
    {
        $roles = [];

        // prevent the removal of your own administrator role
        if (get_current_user_id() === $user->ID && current_user_can('administrator')) {
            $roles[] = 'administrator';
        }

        // prevent the removal of existing non-editable roles
        $non_editable_roles = array_values(
            array_filter($this->get_role_names($user), [$this, 'is_not_editable_role'])
        );

        return array_merge($roles, $non_editable_roles);
    }

}
