<?php

namespace App\Storages;

use Illuminate\Database\DatabaseManager;

class ModelStorage
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
     * @param string $schema
     * @param array $data
     * @return int
     */
    public function store_model(string $schema, array $data): int
    {
        return $this->db->table('models')->insertGetId(
            [
                "schema" => $schema,
                "model" => json_encode($data)
            ], "id");
    }
}
