<?php

declare(strict_types=1);

namespace ACA\DataSources\Editing\Storage;

use ACA\DataSources\DataSource;

class AttributeRelation extends Relation
{
    /**
     * @var DataSource\Relation\AttributeRelation
     */
    protected DataSource\Relation $relation;

    protected string $foreign_attribute_value;

    public function __construct(
        DataSource $data_source,
        DataSource\Relation\AttributeRelation $relation,
        string $foreign_attribute_value
    ) {
        parent::__construct(
            $data_source,
            $relation,
            $relation->get_foreign_value_column()
        );

        $this->foreign_attribute_value = $foreign_attribute_value;
    }

    protected function find_one($id): array
    {
        $builder = $this->get_builder($this->get_id($id));

        return $builder
            ->and_where(
                $this->relation->get_foreign_attribute(),
                $this->foreign_attribute_value,
            )
            ->one();
    }

    public function get(int $id)
    {
        return parent::get($id) ?? '';
    }

    public function update(int $id, $data): bool
    {
        if ($this->find_one($id)) {
            return parent::update($id, $data);
        }

        $this->relation->get_foreign_data_source()->get_repository()->insert([
            $this->relation->get_foreign_column()       => $this->get_id($id),
            $this->relation->get_foreign_attribute()    => $this->foreign_attribute_value,
            $this->relation->get_foreign_value_column() => $data,
        ]);

        return true;
    }

}
