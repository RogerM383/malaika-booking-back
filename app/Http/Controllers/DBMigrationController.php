<?php

namespace App\Http\Controllers;

use App\Services\DatabaseMigrationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
     *      operationId="dbMigrate",
     *      @OA\Response(
     *          response="200",
     *          description="DB migrated successfully",
     *           @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="array", @OA\Items())
     *          )
     *      )
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
