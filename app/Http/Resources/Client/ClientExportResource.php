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
class ClientExportResource extends JsonResource
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

        if ($this->state <= 4) {
            $room = $this->rooms()
                ->where('departure_id', $this->pivot->departure_id)
                ->where('client_id', $this->id)
                ->first();
        } else {
            $room = null;
        }

        return [
            'id'                => $this->id,
            'room_number'       => $this->when(!empty($room), fn () => $room->room_number),
            'state'             => $this->pivot->state,
            'surname'           => $this->when(!empty($this->surname), fn () => strtoupper($this->surname)),
            'name'              => $this->when(!empty($this->name), fn () => strtoupper($this->name)),
            'room_type_id'      => $this->when(!empty($room), fn () => $room->room_type_id),
            'type_room'         => $this->when(!empty($room), fn () => $room->roomType->name),
            'phone'             => $this->phone,
            'email'             => $this->email,
            'seat'              => $this->seat,
            //'rm_observations'   => $this->when(!empty($room), fn () => $room->observations), //$room->observations,
            'intolerances'      => $this->intolerances,
            'dni'               => $this->dni,
            'dni_expiration'    => $this->when(!empty($this->dni_expiration), fn () => date('d-m-Y', strtotime($this->dni_expiration)) ),

            'number_passport'   => $this->when(!empty($this->passport->number_passport), fn () => $this->passport->number_passport),
            'issue'             => $this->when(!empty($this->passport->issue), fn () => date('d-m-Y', strtotime($this->passport->issue))),
            'exp'               => $this->when(!empty($this->passport->exp), fn () => date('d-m-Y', strtotime($this->passport->exp))),
            'place_birth'       => $this->place_birth,
            'birth'             => $this->when(!empty($this->passport->birth), fn () => date('d-m-Y', strtotime($this->passport->birth))),
            'nationality'       => $this->when(!empty($this->passport), fn () => $this->passport->nationality),
            'dp_observations'   => $this->observations,
            'rm_observations'   => $this->room_observations,
            'pn_observations'   => $this->pivot->observations
        ];
    }
}
