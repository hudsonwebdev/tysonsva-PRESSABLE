<?php

declare(strict_types=1);

namespace ACA\SeoPress\ColumnFactory\Redirect\Original;

use AC\MetaType;
use AC\Setting\Config;
use ACP;
use ACP\Column\OriginalColumnFactory;
use ACP\Metric;

class RedirectionType extends OriginalColumnFactory
{
    private const META_KEY = '_seopress_redirections_type';

    private function get_redirection_types(): array
    {
        return [
            '301' => __('301 Moved Permanently', 'wp-seopress-pro'),
            '302' => __('302 Found / Moved Temporarily', 'wp-seopress-pro'),
            '307' => __('307 Moved Temporarily', 'wp-seopress-pro'),
            '410' => __('410 Gone', 'wp-seopress-pro'),
            '451' => __('451 Unavailable For Legal Reasons', 'wp-seopress-pro'),
        ];
    }

    protected function get_editing(Config $config): ?ACP\Editing\Service
    {
        return new ACP\Editing\Service\Basic(
            new ACP\Editing\View\Select($this->get_redirection_types()),
            new ACP\Editing\Storage\Post\Meta(self::META_KEY)
        );
    }

    protected function get_search(Config $config): ?ACP\Search\Comparison
    {
        return new ACP\Search\Comparison\Meta\Select(self::META_KEY, $this->get_redirection_types());
    }

    protected function get_metric(Config $config): ?Metric\Config
    {
        $keys = array_map('strval', array_keys($this->get_redirection_types()));
        ;

        return Metric\Config\Meta\Choice::from_choices(
            MetaType::create_post_meta(),
            self::META_KEY,
            array_combine($keys, $keys)
        );
    }

}
