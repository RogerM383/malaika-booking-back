<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClientListResource;
use App\Http\Resources\ClientResource;
use App\Services\ClientService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ClientController extends Controller
{
    private ClientService $service;

    public function __construct(ClientService $clietnService)
    {
        $this->service = $clietnService;
    }

    /**
     * @OA\Get(
     *      path="/api/clients",
     *      tags={"Clients"},
     *      summary="Lista de clientes",
     *      security={{"bearer_token":{}}},
     *      description="Lista los clientes",
     *      operationId="clientList",
     *      @OA\Response(
     *          response="200",
     *          description="Client list retrieves successfully",
     *           @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/ClientListResource")
     *              )
     *          )
     *      )
     *  )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function get(Request $request): JsonResponse
    {
        //$validatedData = Validator::make($request->all(), [])->validate();
        return $this->sendResponse(
            ClientListResource::collection($this->service->all()),
            'Client list retrieved successfully'
        );
    }

    /**
     *  @OA\Get(
     *      path="/api/clients/{id}",
     *      tags={"Clients"},
     *      summary="Retorna un cliente por ID",
     *      security={{"bearer_token":{}}},
     *      description="Retorna un cliente",
     *      operationId="getClientById",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Id de cliente",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Client retrived successfully",
     *           @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  ref="#/components/schemas/ClientResource"
     *              )
     *          )
     *      )
     *  )
     */
    public function getById(Request $request, $id)
    {
        $validatedData = Validator::make(['id' => $id], [
            'id' => 'required|integer',
        ])->validate();

        return $this->sendResponse(
            new ClientResource($this->service->getById($validatedData['id'])),
            'Client retrieved successfully'
        );
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
/*

        if ($request->type) {

            $clients = Client::whereHas('traveler', function ($q) use ($request) {
                $q->where('client_type', $request->input('type'));
            });
        } else {
            $clients = Client::query();
        }

        if ($request->filter) {

            if ($request->param == "default") {

                $clients = $clients->where('name', 'LIKE', "%" . $request->input('filter') . "%")
                    ->orWhere('surname', 'LIKE', "%" . $request->input('filter') . "%")
                    ->orWhere('phone', 'LIKE', "%" . $request->input('filter') . "%")
                    ->orWhere('email', 'LIKE', "%" . $request->input('filter') . "%")
                    ->orWhere('dni', 'LIKE', "%" . $request->input('filter') . "%");

                // return view('client.index', compact('clients'));
            } elseif ($request->input('param') == "passport") {

                $filterjobs = function ($q) use ($request) {
                    $q->where('number_passport', 'LIKE', "%" . $request->input('filter') . "%");
                };

                $clients = $clients->with(array('passport' => $filterjobs))
                    ->whereHas('passport', $filterjobs);

                // return view('client.index', compact('clients'));
            } else {
                $clients = $clients->where($request->input('param'), 'LIKE', "%" . $request->input('filter') . "%");
                //  return view('client.index', compact('clients'));
            }
        }


        $clients = $clients->paginate(12);


        return view('client.index', compact('clients'));*/
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $clients = $this->clientService->all();
        return view('client.newclient', compact('clients'));
    }

    /**
     * Store a newly created resource in storage.
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        // TODO: Cambiar all() por only con la lista de params que se le pasen a esta llamda
        try {
            $validatedData = Validator::make($request->all(), [
                'name' => 'required|string',
                'surname' => 'string',
                'phone' => 'string',
                'email' => 'string|email',
                'dni' => 'string',
                'address' => 'string',
                'dni_expiration' => 'string',
                'place_birth' => 'string'
            ])->validate();

            $client = $this->clientService->create($validatedData);

            return redirect('client')->with('message', __('Saved client'));

        } catch (ValidationException $e) {
            return redirect('client')->with('message', _('There has been a problem, try again'));
        } catch (\Exception $e) {
            Log::debug($e->getMessage());
        }



        //
        //$searchdni = $request->dni ?  Client::where('dni','=',$request->dni)->exists() : false ;

        //$searchemail = $request->email ?  Client::where('email','=',$request->email)->exists() : false  ;


        /*if( $searchdni == true || $searchemail == true )
        {

            return redirect('client')->with('warning', __('Ya existe un usuario con ese dni o email'));

        }
        else{

            try {
                $client = new Client();
                $client->fill($request->except('_token'));



                $client->save();

                $client->traveler()->create($request->all());


                $client->passport()->create($request->all());
                //dd($request->all());


            } catch (\Illuminate\Database\QueryException $e) {
                echo 'Caught exception: ',  $e->getMessage(), "\n";
                return redirect('client')->with('message', _('There has been a problem, try again'));
            }
        }*/




        //return redirect('client')->with('message', __('Saved client'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
