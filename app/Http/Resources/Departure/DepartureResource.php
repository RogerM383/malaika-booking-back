<?php

namespace App\Http\Resources\Departure;

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
class DepartureResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id'                    => $this->id,
            'start'                 => $this->start,
            'final'                 => $this->final,
            'price'                 => $this->price,
            'pax_capacity'          => $this->pax_capacity,
            'individual_supplement' => $this->individual_supplement,
            'state'                 => $this->state->name,
            'commentary'            => $this->commentary,
            'taxes'                 => $this->taxes,
            'expedient'             => $this->expedient,
            'title'                 => $this->trip->title,
            //'room_availability'     => RoomTypeAvailabilityResource::collection($this->whenLoaded('roomTypes'))
            'room_availability'     => RoomTypeAvailabilityResource::collection($this->roomTypes)
        ];
    }
}
