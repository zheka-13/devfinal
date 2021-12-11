<?php

namespace App\Storages;

use App\Entities\FieldEntity;
use App\Entities\SchemaEntity;
use App\Exceptions\FieldEntityException;
use App\Exceptions\SchemaException;
use Illuminate\Database\DatabaseManager;
use stdClass;

class SchemaStorage
{

    private DatabaseManager $db;

    /**
     * @param DatabaseManager $db
     */
    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    /**
     * @throws FieldEntityException
     */
    public function getAllSchemas(): array
    {
        $data = $this->db->table("schemas")->get();
        $schemas = [];
        foreach ($data as $row){
            $schemas[] = $this->makeSchema($row);
        }
        return $schemas;
    }

    /**
     * @param string $schema
     * @return SchemaEntity
     * @throws FieldEntityException
     * @throws SchemaException
     */
    public function getSchema(string $schema): SchemaEntity
    {
        $data = $this->db->table("schemas")
            ->where("schema", "=", $schema)->first();
        if (empty($data)){
            throw new SchemaException("Schema ".$schema." does not exist.");
        }
        return $this->makeSchema($data);
    }

    /**
     * @param string $schema
     * @return bool
     */
    public function schemaExists(string $schema): bool
    {
        return $this->db->table("schemas")
            ->where("schema", "=", $schema)->exists();
    }

    /**
     * @param SchemaEntity $schemaEntity
     * @return int
     */
    public function store(SchemaEntity $schemaEntity): int
    {
        $fields = [];
        foreach ($schemaEntity->getFields() as $field){
            $fields[] = $field->asArray();
        }
        return $this->db->table("schemas")
            ->insertGetId([
                "schema" => $schemaEntity->getSchema(),
                "fields" => json_encode($fields),
            ], 'id');
    }

    /**
     * @param SchemaEntity $schemaEntity
     * @return int
     */
    public function update(SchemaEntity $schemaEntity): int
    {
        $fields = [];
        foreach ($schemaEntity->getFields() as $field){
            $fields[] = $field->asArray();
        }
        return $this->db->table("schemas")
            ->where('schema', '=', $schemaEntity->getSchema())
            ->update([
                "fields" => json_encode($fields),
            ]);
    }

    /**
     * @param string $schema
     */
    public function delete(string $schema)
    {
        $this->db->table("schemas")
            ->where('schema', '=', $schema)
            ->delete();
    }

    /**
     * @param stdClass $row
     * @return SchemaEntity
     * @throws FieldEntityException
     */
    private function makeSchema(stdClass $row): SchemaEntity
    {
        return new SchemaEntity($row->schema, $this->makeSchemaFields(json_decode($row->fields, true)));
    }


    /**
     * @param array $fields
     * @return FieldEntity[]
     * @throws FieldEntityException
     */
    private function makeSchemaFields(array $fields): array
    {
        $entities = [];
        foreach ($fields as $field){
            $entities[] = $this->makeField($field);
        }
        return $entities;
    }

    /**
     * @throws FieldEntityException
     */
    private function makeField(array $data): FieldEntity
    {
        $field = new FieldEntity($data['name'], $data['type']);
        return $field
            ->setDefault($data['default'] ?? null)
            ->setQueryable($data['queryable'] ?? $field->isQueryable())
            ->setReadonly($data['readonly'] ?? $field->isReadonly())
            ->setRequired($data['required'] ?? $field->isRequired())
            ->setUnique($data['unique'] ?? $field->isUnique());
    }
}
