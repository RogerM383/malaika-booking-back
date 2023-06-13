<?php

namespace App\Http\Controllers;

use App\Exceptions\ClientNotFoundException;
use App\Http\Resources\TripResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class TripController extends Controller
{

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function getTripsByClientId(Request $request): JsonResponse
    {
        $validatedData = Validator::make($request->all(), [
            'client_id' => 'required|integer',
        ])->validate();

        return $this->sendResponse(
            new TripResource($this->service->getById($validatedData['id'])),
            'Client retrieved successfully'
        );
    }
}
