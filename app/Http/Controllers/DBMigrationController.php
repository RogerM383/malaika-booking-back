<?php

namespace App\Http\Controllers;

use App\Exceptions\ModelNotFoundException;
use App\Http\Resources\Client\ClientDetailResource;
use App\Http\Resources\Client\ClientListCollection;
use App\Http\Resources\Client\ClientListResource;
use App\Http\Resources\Client\ClientResource;
use App\Services\ClientService;
use App\Services\DatabaseMigrationService;
use App\Traits\HasPagination;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class DBMigrationController extends Controller
{
    private DatabaseMigrationService $dbms;

    public function __construct(DatabaseMigrationService $dbms)
    {
        $this->dbms = $dbms;
    }

    /**
     * @OA\Get(
     *      path="/api/db",
     *      tags={"Database"},
     *      summary="Migra la base de datos",
     *      security={{"bearer_token":{}}},
     *      description="Migra la base de datos",
     *      operationId="dbMigrate"
     *  )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function migrate(Request $request): JsonResponse
    {
        $this->dbms->migrate();
        return $this->sendResponse([],'Data migrated successfully');
    }
}
