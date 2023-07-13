<?php

namespace App\Http\Resources\RoomType;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 *
 * @OA\Schema(
 *      required={"id"},
 *      @OA\Xml(name="RoomTypeResource"),
 *      @OA\Property(property="id", type="integer", readOnly="true", example="1"),
 *      @OA\Property(property="name", type="string", description="Room type nmame", example="Dui"),
 *      @OA\Property(property="description", type="string", description="Room type description", example="Habitacion doble de uso individual"),
 *      @OA\Property(property="capacity", type="string", description="Room type capacity", example="1")
 * )
 *
 * Class RoomTypeResource
 *
 */
class RoomTypeResource extends JsonResource
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
            'id'            => $this->id,
            'name'          => $this->name,
            'description'   => $this->description,
            'capacity'      => $this->capacity
        ];
    }
}
