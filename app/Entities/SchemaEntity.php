<?php

namespace App\Entities;

use App\Exceptions\FieldEntityException;
use App\Exceptions\FieldNotFoundException;

class SchemaEntity
{
    private string $schema;
    /**
     * @var FieldEntity[]
     */
    private array $fields = [];

    /**
     * @param string $schema
     * FieldEntity[] $fields
     * @param array $fields
     * @throws FieldEntityException
     */
    public function __construct(string $schema, array $fields)
    {
        $this->schema = $schema;
        if (empty($fields)){
            throw new FieldEntityException("No fields in schema ".$schema);
        }
        $names = [];
        foreach ($fields as $field){
            if (!($field instanceof FieldEntity)){
                throw new FieldEntityException("Fields must contain only FieldEntities");
            }
            if (in_array($field->getName(), $names)){
                throw new FieldEntityException("Duplicate fields with the same name - ".$field->getName());
            }
            $names[] = $field->getName();
        }
        $this->fields = $fields;
    }

    /**
     * @return string
     */
    public function getSchema(): string
    {
        return $this->schema;
    }

    /**
     * @return FieldEntity[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @return array
     */
    public function asArray(): array
    {
        $data = [
            "schema" => $this->schema,
            "fields" => []
        ];
        foreach ($this->fields as $field){
            $data['fields'][] = $field->asArray();
        }
        return $data;
    }

    /**
     * @param string $name
     * @return FieldEntity
     * @throws FieldNotFoundException
     */
    public function getField(string $name): FieldEntity
    {
        foreach ($this->fields as $field){
            if ($field->getName() == $name){
                return $field;
            }
        }
        throw new FieldNotFoundException("Field ".$name." not found");
    }


}
