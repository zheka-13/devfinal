<?php

namespace App\Http\Controllers;

use App\Entities\FieldEntity;
use App\Exceptions\FieldEntityException;
use App\Exceptions\SchemaException;
use App\Services\SchemaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SchemaController extends Controller
{
    private SchemaService $schemaService;


    /**
     * @param SchemaService $schemaService
     */
    public function __construct(SchemaService $schemaService)
    {
        $this->schemaService = $schemaService;
    }

    /**
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        try {
            $schemas = $this->schemaService->getAllSchemas();
            $data = [];
            foreach ($schemas as $schema) {
                $data[] = [
                    "endpoint" => '/api/' . config('app.api_version') . "/" . $schema->getSchema(),
                    "schema" => $schema->getSchema(),
                ];
            }
            return new JsonResponse($data);
        }
        catch (FieldEntityException $e){
            return new JsonResponse([
                "errors" => [
                    $e->getMessage()
                ]
            ], 422);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function add(Request $request): JsonResponse
    {
        try {
            $this->validate($request, [
                "schema" => 'required|string',
                "fields" => 'required|array',
            ]);
            $fields = $this->getFieldsFromRequest($request);
            $this->schemaService->addSchema((string)$request->input("schema"), $fields);
            return new JsonResponse([
               "status" => "created"
            ], 201);
        }
        catch (ValidationException){
            return new JsonResponse([
                "errors" => [
                    "Schema or Fields are empty"
                ]
            ], 422);
        }
        catch (SchemaException|FieldEntityException $e){
            return new JsonResponse([
                "errors" => [
                    $e->getMessage()
                ]
            ], 422);
        }
    }

    /**
     * @param string $schema
     * @return JsonResponse
     */
    public function delete(string $schema): JsonResponse
    {
        $this->schemaService->deleteSchema($schema);
        return new JsonResponse([
            "status" => "deleted"
        ], 204);
    }

    /**
     * @param string $schema
     * @param Request $request
     * @return JsonResponse
     */
    public function update(string $schema, Request $request): JsonResponse
    {
        try {
            $this->validate($request, [
                "fields" => 'required|array',
            ]);
            $fields =$this->getFieldsFromRequest($request);
            $this->schemaService->updateSchema($schema, $fields);
            return new JsonResponse([
                "status" => "updated"
            ], 200);
        }
        catch (ValidationException){
            return new JsonResponse([
                "errors" => [
                    "Schema or Fields are empty"
                ]
            ], 422);
        }
        catch (SchemaException|FieldEntityException $e){
            return new JsonResponse([
                "errors" => [
                    $e->getMessage()
                ]
            ], 422);
        }
    }

    /**
     * @param Request $request
     * @return FieldEntity[]
     * @throws FieldEntityException
     */
    private function getFieldsFromRequest(Request $request): array
    {
        $fields = [];
        foreach ($request->input("fields") as $field) {
            if (!is_array($field)){
                throw new FieldEntityException('Fields data is broken');
            }
            $fields[] = $this->makeFieldFromArray($field);
        }
        return $fields;
    }

    /**
     * @throws FieldEntityException
     */
    private function makeFieldFromArray(array $data): FieldEntity
    {
        $field = new FieldEntity($data['name'] ?? "", $data['type'] ?? "");
        if (isset($data['required'])){
            $field->setRequired((bool)$data['required']);
        }
        if (isset($data['unique'])){
            $field->setUnique((bool)$data['unique']);
        }
        if (isset($data['queryable'])){
            $field->setQueryable((bool)$data['queryable']);
        }
        if (isset($data['readonly'])){
            $field->setReadonly((bool)$data['readonly']);
        }
        if (isset($data['default'])){
            $field->setDefault($data['default']);
        }
        return $field;
    }


}
