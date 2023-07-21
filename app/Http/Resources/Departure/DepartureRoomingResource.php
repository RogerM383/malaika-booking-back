<?php

namespace App\Http\Resources\Departure;

use App\Http\Resources\Client\ClientRoomingResource;
use App\Http\Resources\RoomType\RoomTypeAvailabilityResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *      required={"id"},
 *      @OA\Xml(name="DepartureRoomingResource"),
 *      @OA\Property(property="id", type="integer", readOnly="true", example="1"),
 *      @OA\Property(property="trip_title", type="integer", description="Trip title", example="Tour por Italia"),
 *      @OA\Property(property="start", type="string", description="Departure starting date", example="22/02/2022"),
 *      @OA\Property(property="final", type="string", description="Departure end date", example="02/03/2022"),
 *      @OA\Property(property="commentary", type="string", description="Departure comment", example="LLega 30 segundos tarde"),
 *      @OA\Property(property="price", type="float", description="Departure price", example="1866.98"),
 *      @OA\Property(property="pax_capacity", type="integer", description="Departure pax capacity", example="25"),
 *      @OA\Property(property="pax_available", type="integer", description="Departure pax available", example="0"),
 *      @OA\Property(property="individual_supplement", type="float", description="Departure supplement", example="550.50"),
 *      @OA\Property(property="taxes", type="float", description="Departure taxes", example="155.89"),
 *      @OA\Property(property="expedient", type="string", description="Trip expedient", example="4564845"),
 *      @OA\Property(property="active", type="integer", description="List of clients with assigend room", example="{}"),
 *      @OA\Property(property="waiting", type="string", description="List of clients waiting for a room", example="{}"),
 *      @OA\Property(property="canceled", type="string", description="List of canceled clients", example="{}"),
 *      @OA\Property(property="rooms_total", type="integer", description="Departure rooms total", example="13"),
 *      @OA\Property(property="room_availability", type="integer", description="Departure aviable number", example="0"),
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

        $canceled = ClientRoomingResource::collection($this->canceledClients);

        $room_availability = RoomTypeAvailabilityResource::collection($this->roomTypes)->resolve();

        foreach ($room_availability as $key => $room) {
            $occupied = collect($clients)->filter(function($client, $key) use ($room) {
                return $client->resolve()['room_type_id'] === $room['id'];
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
            'canceled'              => $this->when(!empty($canceled), fn () => $canceled),
            //'room_types'            => RoomTypeResource::collection($this->roomTypes),
            'rooms_total'           => $total,
            'room_availability'     => $room_availability,
        ];
    }
}
