<?php

declare(strict_types=1);

namespace ACP\Editing\View;

trait OptionsTrait
{
    /**
     * @return static
     */
    public function set_options(array $options): self
    {
        return $this->set('options', $options);
    }

}
