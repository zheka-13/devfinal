<?php

namespace App\Services;

use App\Entities\SchemaEntity;
use App\Enums\FieldTypeEnum;
use App\Exceptions\FieldEntityException;
use App\Exceptions\ModelException;
use App\Exceptions\SchemaException;
use App\Storages\ModelStorage;

class ModelService
{
    private ModelStorage $modelStorage;
    private SchemaService $schemaService;

    /**
     * @param ModelStorage $modelStorage
     * @param SchemaService $schemaService
     */
    public function __construct(ModelStorage $modelStorage, SchemaService $schemaService)
    {
        $this->modelStorage = $modelStorage;
        $this->schemaService = $schemaService;
    }

    /**
     * @throws FieldEntityException
     * @throws SchemaException
     * @throws ModelException
     */
    public function addModel(string $schema_name, array $data): array
    {
        $schema = $this->schemaService->getSchema($schema_name);
        $model_data = $this->validateModelData($data, $schema);
        if (empty($model_data)){
            throw new ModelException("No valid fields found" );
        }
        $id = $this->modelStorage->store_model($schema_name, $model_data);
        $model_data["id"] = $id;
        return $model_data;
    }

    /**
     * @param array $data
     * @param SchemaEntity $schema
     * @return array
     * @throws ModelException
     */
    private function validateModelData(array $data, SchemaEntity $schema): array
    {
        $valid_data = [];
        foreach ($schema->getFields() as $field){
            if (!isset($data[$field->getName()]) && $field->isRequired()){
                throw new ModelException("Field ".$field->getName()." is required");
            }
            if (!isset($data[$field->getName()])){
                $valid_data[$field->getName()] =  $field->getDefault();
                continue;
            }
            if ($field->getType() == FieldTypeEnum::TYPE_INTEGER && !is_numeric($data[$field->getName()])){
                throw new ModelException("Field ".$field->getName()." must be numeric");
            }
            if ($field->getType() == FieldTypeEnum::TYPE_BOOLEAN && !is_bool($data[$field->getName()])){
                throw new ModelException("Field ".$field->getName()." must be boolean");
            }
            $valid_data[$field->getName()] = $data[$field->getName()];
        }
        return $valid_data;

    }
}
