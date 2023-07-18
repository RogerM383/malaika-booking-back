<?php

namespace App\Http\Resources\Trip;

use App\Http\Resources\Departure\DepartureResource;
use App\Http\Resources\Images\ImageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 *
 * @OA\Schema(
 *      required={"id"},
 *      @OA\Xml(name="TripFormResource"),
 *      @OA\Property(property="id", type="integer", readOnly="true", example="1"),
 *      @OA\Property(property="title", type="string", description="Trip title", example="Antartida desde el mar"),
 *      @OA\Property(property="slug", type="string", description="Trip slug", example="antartida-desde-el-mar"),
 *      @OA\Property(property="description", type="string", description="Trip description", example="Es un viaje muy chulo"),
 *      @OA\Property(property="commentary", type="string", description="Trip commentary", example="Especial para ciegos"),
 *      @OA\Property(property="state", type="string", description="Trip state", example="OPEN"),
 * )
 *
 * Class TripFormResource
 *
 */
class TripFormResource extends JsonResource
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
            'title'         => $this->title,
            'image'         => $this->image,
            'pdf'           => $this->pdf,
            'slug'          => $this->slug,
            'description'   => $this->description,
            'commentary'    => $this->commentary,
            'departures'    => DepartureResource::collection($this->departures),
            'state'         => $this->when(!empty($this->state), fn () => $this->state->name),
        ];
    }
}
