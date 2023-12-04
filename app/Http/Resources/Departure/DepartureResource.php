<?php

namespace App\Http\Resources\Departure;

use App\Http\Resources\Client\ClientRoomingResource;
use App\Http\Resources\RoomType\RoomTypeAvailabilityResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;
use function PHPUnit\Framework\logicalOr;

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
        // --- Pilla los clientes de activos apuntados a la Departure --------------------------------------------------
        $clients = ClientRoomingResource::collection($this->activeClients()->with(['rooms' => function ($query) {
            return $query->where('departure_id', $this->id);
        }])->get());

        // --- Calcula las habitaciones de cada tipo de que dispone la Departure ---------------------------------------
        $roomAvailability   = RoomTypeAvailabilityResource::collection($this->roomTypes)->resolve();
        $formRoomTypes      = RoomTypeAvailabilityResource::collection($this->formRoomTypes)->resolve();

        // --- Calcula las habitaciones de cada tipo disponibles en el formulario --------------------------------------
        foreach ($formRoomTypes as $key => $room) {
            $rA = array_search($room['id'], array_column($roomAvailability, 'id'));

            Log::debug(json_encode($rA));
            Log::debug(json_encode($room['quantity']));
            Log::debug(json_encode($roomAvailability[$rA]['quantity']));

            if (empty($room['quantity'])) {
                $formRoomTypes[$key]['available'] = 10000000;
            } else if ($rA) {
                $formRoomTypes[$key]['available'] = max($room['quantity'] - $roomAvailability[$rA]['quantity'], 0);
            } else {
                $formRoomTypes[$key]['available'] = 0;
            }
        }

        return [
            'id'                    => $this->id,
            'start'                 => $this->start,
            'final'                 => $this->final,
            'price'                 => $this->price,
            'booking_price'         => $this->booking_price,
            'pax_capacity'          => $this->pax_capacity,
            'individual_supplement' => $this->individual_supplement,
            'state'                 => $this->state->name,
            'commentary'            => $this->commentary,
            'taxes'                 => $this->taxes,
            'expedient'             => $this->expedient,
            'title'                 => $this->trip->title,
            'room_availability'     => $roomAvailability,
            'form_rooms'            => $formRoomTypes,
            'clients_count'         => $clients->count(),
            'hidden'                => $this->hidden
        ];
    }
}
