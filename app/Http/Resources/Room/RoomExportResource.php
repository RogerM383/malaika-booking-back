<?php

namespace App\Http\Resources\Room;

use App\Http\Resources\RoomType\RoomTypeAvailabilityResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 *TODO update data
 * @OA\Schema(
 *      required={"id"},
 *      @OA\Xml(name="RoomExportResource"),
 *      @OA\Property(property="id", type="integer", readOnly="true", example="1"),
 *      @OA\Property(property="title", type="string", description="Trip title", example="Antartida desde el mar"),
 *      @OA\Property(property="description", type="string", description="Trip description", example="Es un viaje muy chulo"),
 *      @OA\Property(property="commentary", type="string", description="Trip commentary", example="Especial para ciegos"),
 *      @OA\Property(property="state", type="string", description="Trip state", example="OPEN"),
 * )
 *
 * Class RoomExportResource
 *
 */
class RoomExportResource extends JsonResource
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
            "room_type_id"          => $this->room_type_id,
            "room_number"           => $this->room_number,
        ];
    }
}
