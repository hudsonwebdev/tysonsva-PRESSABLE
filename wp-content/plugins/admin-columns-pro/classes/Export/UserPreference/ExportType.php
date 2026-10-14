<?php

declare(strict_types=1);

namespace ACP\Export\UserPreference;

use AC\Preferences\Preference;
use AC\Preferences\SiteFactory;
use AC\Type\ListScreenId;
use ACP\Export\Type\ExportFormat;
use InvalidArgumentException;

final class ExportType
{
    private function storage(): Preference
    {
        return (new SiteFactory())->create('export_type');
    }

    public function get_export_type(ListScreenId $id): ExportFormat
    {
        $value = $this->storage()->find((string)$id);

        try {
            return new ExportFormat($value ?? '');
        } catch (InvalidArgumentException $e) {
            return ExportFormat::csv();
        }
    }

    public function set_export_type(ListScreenId $id, ExportFormat $export_format): void
    {
        $this->storage()->save(
            (string)$id,
            $export_format->get_format()
        );
    }

}
