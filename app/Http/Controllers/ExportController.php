<?php

namespace App\Http\Controllers;

use App\Exceptions\DeparturePaxCapacityExceededException;
use App\Exceptions\ModelNotFoundException;
use App\Exports\DeparturesExport;
use App\Exports\DeparturesExport2;
use App\Http\Resources\Departure\DepartureCollection;
use App\Http\Resources\Departure\DepartureExportResource;
use App\Http\Resources\Departure\DepartureResource;
use App\Models\Departure;
use App\Services\DepartureService;
use App\Traits\HasPagination;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    private DepartureService $departureService;

    public function __construct(DepartureService $departureService)
    {
        $this->departureService = $departureService;
    }

    /**
     * @OA\Get(
     *      path="/api/exports/departure/{id}",
     *      tags={"Exports"},
     *      summary="Exporta datos de una salida",
     *      security={{"bearer_token":{}}},
     *      description="Exporta datos de una salida",
     *      operationId="exportDeparture",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Id de slaida",
     *          required=false,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Departure data exported successfully"
     *      )
     *  )
     *
     * @param Request $request
     * @param $id
     * @return BinaryFileResponse
     * @throws ModelNotFoundException
     * @throws ValidationException
     */
    public function departure(Request $request, $id)
    {
        $validatedData = Validator::make(['id' => $id], [
            'id'       => 'integer|min:1'
        ])->validate();

        $departure = new DepartureExportResource($this->departureService->getById($validatedData['id']));
        $expedient = $departure->expedient ?? 'trip-'.$departure->start.'-'.$departure->final;

        /*return $this->sendResponse(
            ['expedient' => null, 'departure' => $d],
            'Departure created successfully'
        );*/

        return Excel::download(new DeparturesExport2(3), 'Rooming-'.$expedient.'.xlsx');
    }
}
