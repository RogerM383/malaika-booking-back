<?php

namespace App\Http\Resources\Departure;

use App\Http\Resources\Client\ClientExportResource;
use App\Http\Resources\RoomType\RoomTypeAvailabilityResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
class DepartureExportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $clients = ClientExportResource::collection($this->activeClients()->with(['rooms' => function ($query) {
            return $query->where('departure_id', $this->id);
        }])->get());

        $waiting = ClientExportResource::collection($this->waitingClients);

        return [
            'id'            => $this->id,
            'trip_title'    => $this->trip->title,
            'start'         => $this->start,
            'final'         => $this->final,
            'commentary'    => $this->commentary,
            'active'        => $this->when(!empty($clients), fn () => $clients),
            'waiting'       => $this->when(!empty($waiting), fn () => $waiting),
            'room_types'    => $this->roomTypes,

            //'rooms'         => $this->rooms()->clients()->get()

            /*
            'price'                 => $this->price,
            'pax_capacity'          => $this->pax_capacity,
            'individual_supplement' => $this->individual_supplement,
            'state'                 => $this->state->name,
            'commentary'            => $this->commentary,
            'taxes'                 => $this->taxes,
            'expedient'             => $this->expedient,
            'room_availability'     => RoomTypeAvailabilityResource::collection($this->roomTypes),*/
        ];
    }
}
