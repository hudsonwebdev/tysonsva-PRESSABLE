<?php

declare(strict_types=1);

namespace ACP\Type;

interface ActivationToken
{
    public function get_token(): string;

    public function get_type(): string;

}
