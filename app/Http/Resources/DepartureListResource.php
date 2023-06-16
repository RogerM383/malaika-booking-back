<?php

namespace App\Http\Resources;

use App\Services\ClientService;
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
class DepartureListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        /**
         * TODO: pax ha de devolver algo del formato 11/20 sinedo 11 el resultadod e restar los pillados - el toal
         * TODO: añadir habitaciones, se calcula segun el numero de gente apuntadsa a la salida y las habiatciones en las eus estan
         */
        return [
            'id'                    => $this->id,
            'start'                 => $this->start,
            'final'                 => $this->final,
            'pax_available'         => $this->pax_available,
            'expedient'             => $this->expedient,
        ];
    }
}
