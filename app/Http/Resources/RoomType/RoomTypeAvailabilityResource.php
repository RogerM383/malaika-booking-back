<?php

namespace App\Http\Resources\RoomType;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 *
 * @OA\Schema(
 *      required={"id"},
 *      @OA\Xml(name="RoomTypeAviabilityResource"),
 *      @OA\Property(property="id", type="integer", readOnly="true", example="1"),
 *      @OA\Property(property="name", type="string", description="Room types nmame", example="Dui"),
 *      @OA\Property(property="quantity", type="integer", description="Room type avaiable", example="10")
 * )
 *
 * Class PassportResource
 *
 */
class RoomTypeAvailabilityResource extends JsonResource
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
            'id'        => $this->id,
            'name'      => $this->name,
            'capacity'  => $this->capacity,
            'quantity'  => $this->pivot->quantity
        ];
    }
}
