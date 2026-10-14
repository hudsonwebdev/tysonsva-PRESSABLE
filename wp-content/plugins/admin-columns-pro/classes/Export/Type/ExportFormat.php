<?php

declare(strict_types=1);

namespace ACP\Export\Type;

use InvalidArgumentException;

final class ExportFormat
{
    private const CSV = 'csv';
    private const XLSX = 'xlsx';
    private const JSON = 'json';

    private const VALID = [self::CSV, self::XLSX, self::JSON];

    private string $format;

    public function __construct(string $format)
    {
        if (! in_array($format, self::VALID, true)) {
            throw new InvalidArgumentException(sprintf('Invalid export format "%s".', $format));
        }

        $this->format = $format;
    }

    public static function csv(): self
    {
        return new self(self::CSV);
    }

    public function get_format(): string
    {
        return $this->format;
    }

    public function __toString(): string
    {
        return $this->format;
    }

}
