<?php

namespace App\Http\Resources\Client;

use App\Http\Resources\ClientType\ClientTypeListResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

/**
 *
 * @OA\Schema(
 *      required={"id"},
 *      @OA\Xml(name="ClientListResource"),
 *      @OA\Property(property="id", type="integer", readOnly="true", example="1"),
 *      @OA\Property(property="surname", type="string", description="Client surname", example="Birgulilla"),
 *      @OA\Property(property="name", type="string", description="Client name", example="Maria"),
 *      @OA\Property(property="phone", type="string", description="Client phone", example="648595623"),
 *      @OA\Property(property="email", type="string", description="Client email", example="mariabirgulilla@gmail.com"),
 *      @OA\Property(property="DNI", type="string", description="Client DNI", example="47864512"),
 *      @OA\Property(property="address", type="string", description="Client address", example="Calle falsa 123, 08167, Barcelona"),
 *      @OA\Property(property="dni_expiration", type="string", description="Client DNI expiration date", example="2025-12-01"),
 *      @OA\Property(property="place_birth", type="string", description="Client place of birtgh", example="Barcelona"),
 * )
 *
 * Class ClientListResource
 *
 */
class ClientDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        // TODO repasar esto, el tipo pueden ser varios? o un cliente solo sera de un tipo? si es de varios que hacemos en el listado?
       /* $types = $this->clientTypes ?? null;
        $type = !empty($types) && count($types) >= 0 ? $types[0]->name : null;*/
        return [
            'id'                => $this->id,
            'surname'           => $this->surname,
            'name'              => $this->name,
            'phone'             => $this->phone,
            'email'             => $this->email,
            'dni'               => $this->dni,
            'address'           => $this->address,
            'dni_expiration'    => $this->dni_expiration,
            'place_birth'       => $this->place_birth,

            'notes'             => $this->notes,
            'intolerances'      => $this->intolerances,
            'frequent_flyer'    => $this->frequent_flyer,
            'member_number'     => $this->member_number,
            'language'          => $this->language->name,
            'client_type'       => $this->when(!empty($this->clientType), fn () => new ClientTypeListResource($this->clientType)),

            'passport_id'       => $this->when(!empty($this->passport), fn () => $this->passport->id),
            'number_passport'   => $this->when(!empty($this->passport), fn () => $this->passport->number_passport),
            'birth'             => $this->when(!empty($this->passport), fn () => $this->passport->birth),
            'issue'             => $this->when(!empty($this->passport), fn () => $this->passport->issue),
            'exp'               => $this->when(!empty($this->passport), fn () => $this->passport->exp),
            'nationality'       => $this->when(!empty($this->passport), fn () => $this->passport->nationality),

            //'seat'              => $this->when(!empty($this->departures), fn () => $this->departures[0]->pivot->seat),
            'observations'      => $this->observations,
            'seat'              => $this->seat,
            'room_observations' => $this->room_observations
        ];
    }
}
