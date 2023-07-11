<?php

namespace App\Http\Resources\Client;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/**
 *
 * @OA\Schema(
 *      required={"id"},
 *      @OA\Xml(name="ClientListResource"),
 *      @OA\Property(property="id", type="integer", readOnly="true", example="1"),
 *      @OA\Property(property="surname", type="string", description="Client surname", example="Birgulilla"),
 *      @OA\Property(property="name", type="string", description="Client name", example="Maria"),
 *      @OA\Property(property="phone", type="string", description="Client phone", example="648595623"),
 *      @OA\Property(property="email", type="string", description="Client email", example="mariabirgulilla@gmail.com"),
 *      @OA\Property(property="DNI", type="string", description="Client DNI", example="47864512"),
 * )
 *
 * Class ClientListResource
 *
 */
class ClientListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        // Estados de pasaporte 1 - ok, 2 - caducado, 3 - no esta
        $passportState = 3;
        $passport = $this->passport;
        if (!empty($passport)) {
            $passportState = strtotime($passport->exp) >= strtotime(Carbon::now()) ? 1 : 2;
        }

        return [
            'id'        => $this->id,
            'surname'   => $this->surname,
            'name'      => $this->name,
            'phone'     => $this->phone,
            'email'     => $this->email,
            'dni'       => $this->dni,
            'passport_state' => $passportState,
        ];
    }
}
