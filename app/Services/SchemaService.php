<?php

namespace App\Services;

use App\Entities\FieldEntity;
use App\Entities\SchemaEntity;
use App\Exceptions\FieldEntityException;
use App\Exceptions\FieldNotFoundException;
use App\Exceptions\SchemaException;
use App\Storages\SchemaStorage;

class SchemaService
{
    private SchemaStorage $schemaStorage;

    /**
     * @param SchemaStorage $schemaStorage
     */
    public function __construct(SchemaStorage $schemaStorage)
    {
        $this->schemaStorage = $schemaStorage;
    }

    /**
     * @return SchemaEntity[]
     * @throws FieldEntityException
     */
    public function getAllSchemas():array
    {
        return $this->schemaStorage->getAllSchemas();
    }

    /**
     * @param string $schema
     * @param FieldEntity[] $fields
     * @return int
     * @throws FieldEntityException
     * @throws SchemaException
     */
    public function addSchema(string $schema, array $fields): int
    {
        if ($this->schemaStorage->schemaExists($schema)){
            throw new SchemaException("Schema ".$schema." already exists.");
        }
        $schema = new SchemaEntity($schema, $fields);
        return $this->schemaStorage->store($schema);
    }

    /**
     * @param string $schema
     * @param FieldEntity[] $fields
     * @throws SchemaException
     * @throws FieldEntityException
     */
    public function updateSchema(string $schema, array $fields)
    {
        $oldSchema = $this->schemaStorage->getSchemaByName($schema);
        $newSchema = new SchemaEntity($schema, $fields);
        $this->guardSchemaUpdate($newSchema, $oldSchema);
        $this->schemaStorage->update($newSchema);
    }

    /**
     * @param string $schema
     */
    public function deleteSchema(string $schema)
    {
        $this->schemaStorage->delete($schema);
    }

    /**
     * @param SchemaEntity $newSchema
     * @param SchemaEntity $oldSchema
     * @throws SchemaException
     */
    private function guardSchemaUpdate(SchemaEntity $newSchema, SchemaEntity $oldSchema){
        foreach ($newSchema->getFields() as $field){
            try {
                $old_field = $oldSchema->getField($field->getName());
            }
            catch (FieldNotFoundException){
                continue;
            }
            if ($old_field->getType() != $field->getType()){
                throw new SchemaException("Changing field type is forbidden. Field ".$old_field->getName().":".$old_field->getType()
                    ." =>  ".$field->getName().":".$field->getType());
            }

            if ($field->isUnique() && !$old_field->isUnique()){
                throw new SchemaException("Cannot set unique field ".$field->getName());
            }
        }
    }

}
