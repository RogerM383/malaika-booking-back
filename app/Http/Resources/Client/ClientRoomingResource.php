<?php

namespace App\Http\Resources\Client;

use App\Http\Resources\ClientType\ClientTypeListResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/**
 *
 * @OA\Schema(
 *      required={"id"},
 *      @OA\Xml(name="ClientRoomingResource"),
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
 * Class ClientRoomingResource
 *
 */
class ClientRoomingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {

        // Estados de pasaporte 1 - ok, 2 - caducado, 3 - no esta
        $passport_id = null;
        $passportState = 3;
        $passport = $this->passport;
        if (!empty($passport)) {
            $passportState = strtotime($passport->exp) >= strtotime(Carbon::now()) ? 1 : 2;
            $passport_id = $passport->id;
        }

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
            'surname'           => $this->surname,
            'name'              => $this->name,
            'type_room_id'      => $this->when(!empty($room), fn () => $room->room_type_id),
            'type_room'         => $this->when(!empty($room), fn () => $room->roomType->name),
            'phone'             => $this->phone,
            'email'             => $this->email,
            'passport_state'    => $this->when(!empty($passportState), fn () => $passportState),
            'passport_id'       => $this->when(!empty($passport_id), fn () => $passport_id),
        ];
    }
}
