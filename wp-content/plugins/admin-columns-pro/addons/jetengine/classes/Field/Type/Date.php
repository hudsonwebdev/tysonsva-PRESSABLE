<?php

declare(strict_types=1);

namespace ACA\JetEngine\Field\Type;

use ACA\JetEngine\Field\DefaultValue;
use ACA\JetEngine\Field\DefaultValueTrait;
use ACA\JetEngine\Field\Field;
use ACA\JetEngine\Field\TimeStamp;
use ACA\JetEngine\Field\TimestampTrait;

final class Date extends Field implements TimeStamp, DefaultValue
{
    use DefaultValueTrait;
    use TimestampTrait;

    public const TYPE = 'date';
}
