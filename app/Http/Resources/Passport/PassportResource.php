<?php

namespace App\Http\Resources\Passport;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 *
 * @OA\Schema(
 *      required={"id"},
 *      @OA\Xml(name="PassportResource"),
 *      @OA\Property(property="id", type="integer", readOnly="true", example="1"),
 *      @OA\Property(property="number_passport", type="string", description="Passport number", example="4567845454WW"),
 *      @OA\Property(property="birth", type="string", description="Passport owner bith date", example="1975/12/01"),
 *      @OA\Property(property="issue", type="string", description="Passport issue", example="2021/12/01"),
 *      @OA\Property(property="exp", type="string", description="Passport expiration date", example="2026/12/01"),
 *      @OA\Property(property="nac", type="string", description="Passport owner nacinality", example="Spanish"),
 * )
 *
 * Class PassportResource
 *
 */
class PassportResource extends JsonResource
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
            'id'                => $this->id,
            'number_passport'   => $this->number_passport,
            'birth'             => $this->birth,
            'issue'             => $this->issue,
            'exp'               => $this->exp,
            'nationality'       => $this->nationality,
        ];
    }
}
