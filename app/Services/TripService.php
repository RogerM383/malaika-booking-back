<?php

namespace App\Services;

use App\Exceptions\ModelNotFoundException;
use App\Models\Trip;
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
    use Slugeable;

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
            /*$extension = $data['image']->getClientOriginalExtension();
            $filename = $data['slug'] . '.' . $extension;
            $image = $data['image']->move('images/', $filename);
            $data['image'] = asset($image);*/

            $base64File = $data['image'];

            // decode the base64 file
            $fileData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64File));

            // save it to temporary dir first.
            $tmpFilePath = sys_get_temp_dir() . '/' . Str::uuid()->toString();
            file_put_contents($tmpFilePath, $fileData);

            // this just to help us get file info.
            $tmpFile = new File($tmpFilePath);

            $file = new UploadedFile(
                $tmpFile->getPathname(),
                $tmpFile->getFilename(),
                $tmpFile->getMimeType(),
                0,
                true // Mark it as test, since the file isn't from real HTTP POST.
            );

            $extension = explode('/', $file->getMimeType() )[1];
            $filename = $data['slug'] . '.' . $extension;
            $image = $file->move('images/', $filename);
            // TODO SOLUCIONAR APAÑO PARA METER LA RUTA, SACRLA DEL .env COMO MINIMO
            $data['image'] = 'https://api.malaika-clients.fruntera.dev/images/'.$filename;//asset($image);
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

        /*Log::debug($data['image']);
        Log::debug(json_encode(base64_decode($data['image'], true)));*/

        if (isset($data['image']) && $this->isBase64Image($data['image'])) {
            /*$extension = $data['image']->getClientOriginalExtension();
            $filename = $data['slug'] . '.' . $extension;
            $image = $data['image']->move('images/', $filename);
            $data['image'] = asset($image);*/

            $base64File = $data['image'];

            // decode the base64 file
            $fileData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64File));

            // save it to temporary dir first.
            $tmpFilePath = sys_get_temp_dir() . '/' . Str::uuid()->toString();
            file_put_contents($tmpFilePath, $fileData);

            // this just to help us get file info.
            $tmpFile = new File($tmpFilePath);

            $file = new UploadedFile(
                $tmpFile->getPathname(),
                $tmpFile->getFilename(),
                $tmpFile->getMimeType(),
                0,
                true // Mark it as test, since the file isn't from real HTTP POST.
            );

            $extension = explode('/', $file->getMimeType() )[1];
            $filename = $data['slug'] . '.' . $extension;
            $image = $file->move('images/', $filename);
            // TODO SOLUCIONAR APAÑO PARA METER LA RUTA, SACRLA DEL .env COMO MINIMO
            $data['image'] = 'https://api.malaika-clients.fruntera.dev/images/'.$filename;//asset($image);

        }

        $model->update($data);
        return $model;
    }

    private function isBase64Image($base64String)
    {
        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64String));

        // Verifica si la decodificación fue exitosa y comienza con el marcador de imagen
        return ($imageData !== false) && (strpos($imageData, "\xFF\xD8") === 0 || strpos($imageData, "\x89\x50\x4E\x47\x0D\x0A\x1A\x0A") === 0);
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
