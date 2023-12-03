<?php

namespace App\Http\Resources\Departure;

use App\Http\Resources\Client\ClientRoomingResource;
use App\Http\Resources\RoomType\RoomTypeAvailabilityResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

/**
 *TODO update data
 * @OA\Schema(
 *      required={"id"},
 *      @OA\Xml(name="TripResource"),
 *      @OA\Property(property="id", type="integer", readOnly="true", example="1"),
 *      @OA\Property(property="title", type="string", description="Trip title", example="Antartida desde el mar"),
 *      @OA\Property(property="description", type="string", description="Trip description", example="Es un viaje muy chulo"),
 *      @OA\Property(property="commentary", type="string", description="Trip commentary", example="Especial para ciegos"),
 *      @OA\Property(property="state", type="string", description="Trip state", example="OPEN"),
 * )
 *
 * Class TripResource
 *
 */
class DepartureClientListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        // --- Pilla los clientes de activos apuntados a la Departure --------------------------------------------------
        $clients = ClientRoomingResource::collection($this->activeClients()->with(['rooms' => function ($query) {
            return $query->where('departure_id', $this->id);
        }])->get());

        // --- Calcula las habitaciones de cada tipo de que dispone la Departure ---------------------------------------
        $roomAvailability = RoomTypeAvailabilityResource::collection($this->roomTypes)->resolve();

        return [
            'id'                    => $this->id,
            'title'                 => $this->trip->title,
            'start'                 => $this->start,
            'final'                 => $this->final,
            'pax_capacity'          => $this->pax_capacity,
            'expedient'             => $this->expedient,
            'room_availability'     => $roomAvailability,
            'clients_count'         => $clients->count(),
            'state'                 => !in_array($this->pivot->state, [5,6]) // Estado en espera o cancelado
        ];
    }
}
