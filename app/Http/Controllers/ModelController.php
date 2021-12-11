<?php

namespace App\Http\Controllers;

use App\Exceptions\FieldEntityException;
use App\Exceptions\ModelException;
use App\Exceptions\SchemaException;
use App\Services\ModelService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ModelController extends Controller
{
    private ModelService $modelService;


    /**
     * @param ModelService $modelService
     */
    public function __construct(ModelService $modelService)
    {
        $this->modelService = $modelService;
    }

    /**
     * @param string $schema
     * @param Request $request
     * @return JsonResponse
     */
    public function add(string $schema, Request $request): JsonResponse
    {
        try {
            $data = $request->all();
            if (empty($data)) {
                return new JsonResponse([
                    "errors" => [
                        "Data is empty"
                    ]
                ], 422);
            }
            $model = $this->modelService->addModel($schema, $data);
            return new JsonResponse([
               Str::singular($schema) => $model
            ], 201);
        }
        catch (SchemaException|FieldEntityException|ModelException $e){
            return new JsonResponse([
                "errors" => [
                    $e->getMessage()
                ]
            ], 422);
        }
    }


}
