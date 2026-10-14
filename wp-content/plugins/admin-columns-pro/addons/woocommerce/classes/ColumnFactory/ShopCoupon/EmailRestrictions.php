<?php

declare(strict_types=1);

namespace ACA\WC\ColumnFactory\ShopCoupon;

use AC\FormatterCollection;
use AC\Setting\Config;
use ACA\WC\ColumnFactory\WooCommerceGroupTrait;
use ACA\WC\Editing;
use ACA\WC\Search;
use ACA\WC\Value\Formatter\ShopCoupon\EmailRestrictionsCollection;
use ACP;
use ACP\Metric;

class EmailRestrictions extends ACP\Column\AdvancedColumnFactory
{
    use ACP\ConditionalFormat\ConditionalFormatTrait;
    use WooCommerceGroupTrait;

    public function get_column_type(): string
    {
        return 'column-wc-email-restrictions';
    }

    public function get_label(): string
    {
        return __('Email Restrictions', 'codepress-admin-columns');
    }

    protected function get_formatters(Config $config): FormatterCollection
    {
        return parent::get_formatters($config)->add(new EmailRestrictionsCollection());
    }

    protected function get_editing(Config $config): ?ACP\Editing\Service
    {
        return new Editing\ShopCoupon\EmailRestrictions();
    }

    protected function get_search(Config $config): ?ACP\Search\Comparison
    {
        return new Search\ShopCoupon\EmailRestriction();
    }

    protected function get_metric(Config $config): ?Metric\Config
    {
        return Metric\Config\Meta\Text::for_post('customer_email');
    }
}
