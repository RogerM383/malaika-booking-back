<?php

namespace App\Services;

use App\Models\Language;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use JetBrains\PhpStorm\Pure;

class LanguageService extends ResourceService
{
    /**
     * @param Language $model
     */
    #[Pure] public function __construct(Language $model)
    {
        parent::__construct($model);
    }

    /**
     * @return Collection|LengthAwarePaginator
     */
    public function get(): Collection|LengthAwarePaginator
    {
        $query = $this->model::query();
        return $query->get();
    }

    /**
     * @param $data
     * @return mixed
     */
    public function make($data): mixed
    {

    }

    /**
     * @param $data
     * @return mixed
     */
    public function create($data): mixed
    {

    }

    /**
     * @param int $id
     * @param array $data
     * @return Model
     */
    public function update(int $id, array $data): Model
    {

    }
}
