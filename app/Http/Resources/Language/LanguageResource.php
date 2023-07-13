<?php

namespace App\Http\Resources\Language;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *      required={"id"},
 *      @OA\Xml(name="LanguageResource"),
 *      @OA\Property(property="id", type="integer", readOnly="true", example="1"),
 *      @OA\Property(property="name", type="string", description="Language name", example="Catala"),
 *      @OA\Property(property="code", type="string", description="Language code", example="ca"),
 *      @OA\Property(property="locale", type="string", description="Language locale", example="es_ES"),
 * )
 *
 * Class LanguageResource
 *
 */
class LanguageResource extends JsonResource
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
            "name"      => $this->name,
            "code"      => $this->code,
            "locale"    => $this->locale
        ];
    }
}
