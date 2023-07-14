<?php

namespace App\Http\Resources\Departure;

use App\Http\Resources\Client\ClientExportResource;
use App\Http\Resources\Client\ClientRoomingResource;
use App\Http\Resources\RoomType\RoomTypeAvailabilityResource;
use App\Http\Resources\RoomType\RoomTypeResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Schema(
 *      required={"id"},
 *      @OA\Xml(name="DepartureRoomingResource"),
 *      @OA\Property(property="id", type="integer", readOnly="true", example="1"),
 *      @OA\Property(property="title", type="string", description="Trip title", example="Antartida desde el mar"),
 *      @OA\Property(property="description", type="string", description="Trip description", example="Es un viaje muy chulo"),
 *      @OA\Property(property="commentary", type="string", description="Trip commentary", example="Especial para ciegos"),
 *      @OA\Property(property="state", type="string", description="Trip state", example="OPEN"),
 * )
 *
 * Class DepartureRoomingResource
 *
 */
class DepartureRoomingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $clients = ClientRoomingResource::collection($this->activeClients()->with(['rooms' => function ($query) {
            return $query->where('departure_id', $this->id);
        }])->get());

        // --- Calc total ----------------------------------------------------------------------------------------------
        $types = $this->roomTypes;
        $arr = $types->map(function ($type) {
            return ['name' => $type->name, 'quantity' => $type->pivot->quantity];
        });
        $total = $arr->pluck('quantity')->sum();
        // -------------------------------------------------------------------------------------------------------------

        $waiting = ClientRoomingResource::collection($this->waitingClients);

        $room_availability = RoomTypeAvailabilityResource::collection($this->roomTypes)->resolve();

        foreach ($room_availability as $key => $room) {
            $occupied = collect($clients)->filter(function($client, $key) use ($room) {
                return $client->resolve()['type_room_id'] === $room['id'];
            })->count();

            $occupied = $occupied / $room['capacity'];

            $room_availability[$key]['available'] = $room['quantity'] - $occupied;
        }

        return [
            'id'                    => $this->id,
            'trip_title'            => $this->trip->title,
            'start'                 => $this->start,
            'final'                 => $this->final,
            'commentary'            => $this->commentary,
            'price'                 => $this->price,
            'pax_capacity'          => $this->pax_capacity,
            'pax_available'         => $this->pax_capacity - count($clients),
            'individual_supplement' => $this->individual_supplement,
            'taxes'                 => $this->taxes,
            'expedient'             => $this->expedient,
            'active'                => $this->when(!empty($clients), fn () => $clients),
            'waiting'               => $this->when(!empty($waiting), fn () => $waiting),
            //'room_types'            => RoomTypeResource::collection($this->roomTypes),
            'rooms_total'           => $total,
            'room_availability'     => $room_availability,
        ];
    }
}
