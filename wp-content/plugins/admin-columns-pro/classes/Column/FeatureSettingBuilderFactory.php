<?php

declare(strict_types=1);

namespace ACP\Column;

use ACP\Editing\Setting\ComponentFactory\BulkEdit;
use ACP\Editing\Setting\ComponentFactory\InlineEdit;
use ACP\Export\Setting\ComponentFactory\Export;
use ACP\Filtering\Setting\ComponentFactory\Filtering;
use ACP\Metric\Setting\ComponentFactory\Metric;
use ACP\Search\Setting\ComponentFactory\Search;
use ACP\Sorting\Setting\ComponentFactory\Sort;

final class FeatureSettingBuilderFactory
{
    private InlineEdit $edit;

    private BulkEdit $bulk_edit;

    private Export $export;

    private Filtering $filter;

    private Search $search;

    private Sort $sort;

    private Metric $metric;

    public function __construct(
        InlineEdit $edit,
        BulkEdit $bulk_edit,
        Export $export,
        Filtering $filter,
        Search $search,
        Sort $sort,
        Metric $metric
    ) {
        $this->edit = $edit;
        $this->bulk_edit = $bulk_edit;
        $this->export = $export;
        $this->filter = $filter;
        $this->search = $search;
        $this->sort = $sort;
        $this->metric = $metric;
    }

    public function create(): FeatureSettingBuilder
    {
        return new FeatureSettingBuilder(
            $this->edit,
            $this->bulk_edit,
            $this->export,
            $this->filter,
            $this->search,
            $this->sort,
            $this->metric
        );
    }

}
