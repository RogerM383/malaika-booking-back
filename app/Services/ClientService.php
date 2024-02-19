<?php

namespace App\Services;

use App\Exceptions\ModelNotFoundException;
use App\Models\Client;
use App\Traits\HandleDNI;
use App\Traits\HasPagination;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\Pure;

class ClientService extends ResourceService
{
    use HandleDNI;

    /**
     * @param Client $model
     */
    #[Pure] public function __construct(Client $model)
    {
        parent::__construct($model);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function make(array $data): mixed
    {
        if (isset($data['dni'])) {
            $data['dni'] = $this->trimDNI($data['dni']);
        }

        return $this->model::firstOrNew(
            ['dni' => $data['dni']],
            $data
        );
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data): mixed
    {
        $data['deleted_at'] = null;

        if (isset($data['dni'])) {
            $data['dni'] = $this->trimDNI($data['dni']);
        }

        if (isset($data['dni']) && !empty($data['dni'])) {
            $client =  $this->model->withTrashed()->updateOrCreate(
                ['dni' => $data['dni']],
                $data
            );
        } else {
            $client =  $this->model->create($data);
        }

        return $client;
    }

    /**
     * @param null $client_type
     * @param null $name
     * @param null $surname
     * @param null $phone
     * @param null $email
     * @param null $dni
     * @param null $passport
     * @param null $per_page
     * @param null $page
     * @return Collection|LengthAwarePaginator
     */
    public function get(
        $client_type = null,
        $name = null,
        $surname = null,
        $phone = null,
        $email = null,
        $dni = null,
        $passport = null,
        $per_page = null,
        $page = null,
    ): Collection|LengthAwarePaginator
    {
        $query = $this->model::query();

        if (!empty($client_type)) {
            $this->addClientTypeFilter($query, $client_type);
        }

        if (!empty($name)) {
            $this->addNameFilter($query, $name);
        }

        if (!empty($surname)) {
            $this->addSurnameFilter($query, $surname);
        }

        if (!empty($phone)) {
            $this->addPhoneFilter($query, $phone);
        }

        if (!empty($email)) {
            $this->addEmailFilter($query, $email);
        }
        if (!empty($dni)) {
            $this->addDniFilter($query, $dni);
        }

        if (!empty($passport)) {
            $this->addPassportFilter($query, $passport);
        }

        $query = $query->orderBy('surname', 'asc');

        if ($this->isPaginated($per_page, $page)) {
            return $query->paginate(
                $per_page ?? $this->defaultPerPage,
                ['*'],
                'clients',
                $page ?? $this->defaultPage
            );
        } else {
            return $query->get();
        }
    }

    /**
     * @param $query
     * @param $id
     * @return void
     */
    public function addClientTypeFilter(&$query, $id)
    {
        $query->where('clients.client_type_id', '=', $id);
        /*$query->whereHas('traveler', function ($q) use ($id) {
            $q->where('travelers.client_type_id', '=', $id);
        });*/
    }

    public function addNameFilter(&$query, $name)
    {
        $query->where('clients.name', 'LIKE', '%'.$name.'%');
    }

    public function addSurnameFilter(&$query, $surname)
    {
        $query->where('clients.surname', 'LIKE', '%'.$surname.'%');
    }

    public function addPhoneFilter(&$query, $phone)
    {
        $query->where('clients.phone', 'LIKE', '%'.$phone.'%');
    }

    public function addEmailFilter(&$query, $email)
    {
        $query->where('clients.email', 'LIKE', '%'.$email.'%');
    }

    public function addDniFilter(&$query, $dni)
    {
        $query->where('clients.dni', 'LIKE', '%'.$dni.'%');
    }

    public function addPassportFilter(&$query, $number)
    {
        $query->whereHas('passport', function ($q) use ($number) {
            $q->where('passports.number_passport', $number);
        });
    }

    public function getClientDepartures(int $id)
    {
        return $this->getById($id)->departures()->orderBy('departures.start', 'desc')->get();
    }

    /**
     * @throws ModelNotFoundException
     * @throws Exception
     */
    public function mergeClients($clientId, $mergedId)
    {

        $originClient = $this->getById($clientId);
        $mergedClient = $this->getById($mergedId);

        // Merge related data
        if ($originClient && $mergedClient) {
            DB::table('rel_client_departure')
                ->where('client_id', $mergedId)
                ->update(['client_id' => $clientId]);

            DB::table('rel_client_room')
                ->where('client_id', $mergedId)
                ->update(['client_id' => $clientId]);
        } else {
            throw new Exception('Two clients are needed for merge');
        }

        // Merge passports
        $originalPassport = $originClient->passport;
        $mergedPassport = $mergedClient->passport;

        if ($originalPassport && $mergedPassport) {
            if ($originalPassport->updated_at < $mergedPassport->updated_at) {
                $originalPassport = $mergedPassport;
                $mergedPassport = $originalPassport;
            }
        }

        if ($originalPassport || $mergedPassport) {

            $newPassport = [
                'number_passport' => $originalPassport->number_passport ?? $mergedPassport->number_passport ?? null,
                'birth' => $originalPassport->birth ?? $mergedPassport->birth ?? null,
                'issue' => $originalPassport->issue ?? $mergedPassport->issue ?? null,
                'exp' => $originalPassport->exp ?? $mergedPassport->exp ?? null,
                'nationality' => $originalPassport->nationality ?? $mergedPassport->nationality ?? null,
                'updated_at' => $originalPassport->updated_at ?? $mergedPassport->updated_at ?? null,
            ];


            // TODO: If mergedPassport es mas nuevo, modificar number passport para que no salte restricion de number
            $mergedPassport->update(['number_passport' => $mergedPassport->number_passport ? $mergedPassport->number_passport.'-old' : null]);
            // TODO: check if origin client has passport before passport update o peta pr ser null
            $originClient->passport->update($newPassport);

            if ($originClient->updated_at > $mergedClient->updated_at) {
                $originClient = $mergedClient;
                $mergedClient = $originClient;
            }
        }

        // Merge client Data
        $newClient = [
            'notes' => $originClient->notes ?? $mergedClient->notes,
            'intolerances' => $originClient->intolerances ?? $mergedClient->intolerances,
            'frequent_flyer' => $originClient->frequent_flyer ?? $mergedClient->frequent_flyer,
            'member_number' => $originClient->member_number ?? $mergedClient->member_number,
            'client_type_id' => $originClient->client_type_id ?? $mergedClient->client_type_id,
            'language_id' => $originClient->language_id ?? $mergedClient->language_id,
            'name' => $originClient->name ?? $mergedClient->name,
            'surname' => $originClient->surname ?? $mergedClient->surname,
            'phone' => $originClient->phone ?? $mergedClient->phone,
            'email' => $originClient->email ?? $mergedClient->email,
            'dni' => $originClient->dni ?? $mergedClient->dni,
            'address' => $originClient->address ?? $mergedClient->address,
            'dni_expiration' => $originClient->dni_expiration ?? $mergedClient->dni_expiration,
            'place_birth' => $originClient->place_birth ?? $mergedClient->place_birth,
            'observations' => $originClient->observations ?? $mergedClient->observations,
            'seat' => $originClient->seat ?? $mergedClient->seat,
            'deleted_at' => $originClient->deleted_at ?? $mergedClient->deleted_at,
            'room_observations' => $originClient->room_observations ?? $mergedClient->room_observations
        ];

        $originClient->update($newClient);
        $mergedClient->delete();
    }
}
