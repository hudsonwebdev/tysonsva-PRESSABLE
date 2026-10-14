<?php

declare(strict_types=1);

namespace ACP\Metric\Config\User;

use AC\Helper;
use AC\MetaType;
use ACP\Metric\Config\Meta\Choice;

class Language extends Choice
{
    public static function create(): ?self
    {
        $available = get_available_languages();
        $languages = [];

        // Only installed languages need a native name, so an empty list skips the remote lookup.
        if ($available) {
            $translations = Helper\AvailableTranslations::create()->get_available_translations();

            foreach ($available as $language) {
                $languages[$language] = $translations[$language]['native_name'] ?? $language;
            }

            natcasesort($languages);
        }

        /** @var self|null $config */
        $config = self::from_choices(
            MetaType::create_user_meta(),
            'locale',
            $languages
        );

        return $config;
    }

}
