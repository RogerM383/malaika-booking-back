<?php

namespace App\Services;

use App\Exceptions\ModelNotFoundException;
use App\Models\Trip;
use App\Traits\HandleFiles;
use App\Traits\Slugeable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\Pure;

use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\File;

class TripService extends ResourceService
{
    use Slugeable, HandleFiles;

    /**
     * @param Trip $model
     */
    #[Pure] public function __construct(Trip $model)
    {
        parent::__construct($model);
    }

    /**
     * @param null $client
     * @param null $trip_state
     * @param null $per_page
     * @param null $page
     * @return Collection|LengthAwarePaginator
     */
    public function get(
        $client = null,
        $trip_state = null,
        $per_page = null,
        $page = null
    ): Collection|LengthAwarePaginator
    {
        $query = $this->model::query();

        if ($trip_state) {
            $this->addTripStateIdFilter($query, $trip_state);
        }

        /*if ($client_id) {
            $this->addClientIdFilter($query, $client_id);
        }*/

        if ($this->isPaginated($per_page, $page)) {
            return $query->orderBy('id', 'desc')->paginate(
                $per_page ?? $this->defaultPerPage,
                ['*'],
                'trips',
                $page ?? $this->defaultPage
            );
        } else {
            return $query->get();
        }
    }

    /**
     * @param string $slug
     * @return Trip
     * @throws ModelNotFoundException
     */
    public function getBySlug(string $slug): Trip
    {
        return $this->model::where('slug', '=', $slug)->first() ?? throw new ModelNotFoundException($this->model, $slug);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function make(array $data): mixed
    {
        if (isset($data['title']) && !isset($data['slug'])) {
            $data['slug'] = $this->slugify($data['title']);
        }
        return $this->model::make($data);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data): mixed
    {
        if (isset($data['title']) && !isset($data['slug'])) {
            $data['slug'] = $this->slugify($data['title']);
        }

        if (isset($data['image']) && $this->isBase64Image($data['image'], true)) {
            $image = $this->storeBase64File($data['image'], $data['slug'], 'image', 'images');
            $data['image'] = asset('storage/'.$image);
        }

        if (isset($data['pdf']) && $this->isBase64Pdf($data['pdf'], true)) {
            $pdf = $this->storeBase64File($data['pdf'], $data['slug'], 'application/pdf', 'pdfs');
            $data['pdf'] = asset('storage/'.$pdf);
        }

        return $this->model::create($data);
    }

    /**
     * @param int $id
     * @param array $data
     * @return Model
     * @throws ModelNotFoundException
     */
    public function update(int $id, array $data): Model
    {
        $model = $this->getById($id);

        if (isset($data['title']) && !isset($data['slug'])) {
            $data['slug'] = $this->slugify($data['title']);
        }

        if (isset($data['image']) && $this->isBase64Image($data['image'])) {
            $image = $this->storeBase64File($data['image'], $data['slug'] ?? $model->slug, 'image', 'images');
            $data['image'] = asset('storage/'.$image);
        }

        if (isset($data['pdf']) && $this->isBase64Pdf($data['pdf'], true)) {
            $pdf = $this->storeBase64File($data['pdf'], $data['slug'] ?? $model->slug, 'application/pdf', 'pdfs');
            $data['pdf'] = asset('storage/'.$pdf);
        }

        $model->update($data);
        return $model;
    }

    /**
     * @param $query
     * @param $trip_state
     * @return void
     */
    private function addTripStateIdFilter(&$query, $trip_state)
    {
        $query->orWhere('trip_state_id', $trip_state);
    }

    /**
     * @param $query
     * @param $client
     * @return void
     */
    private function addClientIdFilter(&$query, $client)
    {
        $query->whereHas('departures', function ($q) use ($client) {
            $q->where('client_id', '=', $client);
        });
    }
}
