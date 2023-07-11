<?php

namespace App\Services;

use App\Models\Client;
use App\Models\ClientDepartures;
use App\Models\Departure;
use App\Models\Role;
use App\Models\Room;
use App\Models\Trip;
use App\Models\User;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use JetBrains\PhpStorm\Pure;
use Illuminate\Support\Facades\Artisan;

class DatabaseMigrationService
{
    #[Pure] public function __construct()
    {

    }

    #[Pure] function migrate ()
    {
        Artisan::call('migrate:refresh');
        Artisan::call('db:seed', [ '--class' => 'LanguagesSeeder']);
        Artisan::call('db:seed', [ '--class' => 'DepartureStatesSeeder']);
        Artisan::call('db:seed', [ '--class' => 'RoomTypesSeeder']);
        Artisan::call('db:seed', [ '--class' => 'TripStatesSeeder']);
        Artisan::call('db:seed', [ '--class' => 'ClientTypesSeeder']);

        $this->migrateUsers();
        $this->migrateClients();
        $this->migrateTrips();
        $this->migrateDepartures();
        $this->migrateRoles();
    }

    function migrateUsers (): void
    {
        // Get original data
        $users = DB::connection('db2')->table('users')->get();
        // Set connection to local
        DB::connection('mysql');
        // Create Users
        $users->each(function ($user) {
            $newUser                    = User::make([]);
            $newUser->name              = $user->name;
            $newUser->email             = $user->email;
            $newUser->email_verified_at = $user->email_verified_at;
            $newUser->password          = $user->password;
            $newUser->created_at        = $user->created_at;
            $newUser->updated_at        = $user->updated_at;
            $newUser->save();
        });
    }

    function migrateClients ()
    {
        // client -> travelers.client_id
        $columns = [
            'client_type_id'    => 'travelers.client_type', // De donde vienen los client types?
            'dni'               => 'clients.dni',
            'dni_expiration'    => 'clients.dni_expiration',
            'place_birth'       => 'clients.place_birth',
            'name'              => 'clients.name',
            'surname'           => 'clients.surname',
            'email'             => 'clients.email',
            'phone'             => 'clients.phone',
            'address'           => 'clients.address',
            'intolerances'      => 'travelers.intolerances',
            'frequent_flyer'    => 'travelers.frequency_fly',
            'member_number'     => 'travelers.member_number',
            'notes'             => 'travelers.notes',
            'language_id'       => 'travelers.lang', // Esta en texto hay que mirar como pasarlo
            'created_at'        => 'clients.created_at',
            'updated_at'        => 'clients.updated_at',
            'deleted_at'        => null
        ];

        $clientTypesValues = [
            'null' => 1,
            ' ' => 1,
            'ARQUEONET' => 3,
            'MALAIKA' => 1,
            'ALTRES' => 4,
            'MNAC' => 2
        ];

        $languagesValues = [
            'null' => 1,
            ' ' => 1,
            'CATALA' => 1,
            'CASTELLANO' => 2,
            'CASTELLA' => 2,
            'ESPAÑOL' => 2,
            'CATALANA' => 1,
            'CFASTELLA' => 2,
            'CATALÁN' => 1,
            "passadis, pero s'adapta" => 1,
            'CAST' => 2,
        ];

        // Get original clients data
        $clients = DB::connection('db2')->table('clients')->get();
        // Get original travelers data
        $travelers = DB::connection('db2')->table('travelers')->get();
        // Set connection to local
        DB::connection('mysql');

        $clients->each(function ($client) use ($travelers, $clientTypesValues, $languagesValues) {
            // Search related traveler
            $index = array_search($client->id, array_column(json_decode($travelers), 'client_id'));
            $traveler = $travelers[$index];

            try {
                // Create client
                $newClient = Client::make([]);
                $newClient->id = $client->id;
                $newClient->client_type_id = isset($clientTypesValues[$traveler->client_type]) ? $clientTypesValues[$traveler->client_type] : 1; // De donde vienen los client types?
                $newClient->dni = !empty($client->dni) ? $client->dni : null;
                $newClient->dni_expiration = $this->formatDate($client->dni_expiration);
                $newClient->place_birth = $client->place_birth;
                $newClient->name = $client->name;
                $newClient->surname = $client->surname;
                $newClient->email = $client->email;
                $newClient->phone = $client->phone;
                $newClient->address = $client->address;
                $newClient->intolerances = $traveler->intolerances;
                $newClient->frequent_flyer = $traveler->frequency_fly;
                $newClient->member_number = $traveler->member_number;
                $newClient->notes = $traveler->notes;
                $newClient->language_id = $languagesValues[$traveler->lang] ?? 1; // Esta en texto hay que mirar como pasarlo
                $newClient->created_at = $client->created_at;
                $newClient->updated_at = $client->updated_at;
                $newClient->deleted_at = null;
                $newClient->save();
            } catch (Exception $e) {

                // Search related traveler
                $index = array_search($client->id, array_column(json_decode($travelers), 'client_id'));
                $traveler = $travelers[$index];

                // Create client
                $newClient = Client::make([]);
                $newClient->id = $client->id;
                $newClient->client_type_id = isset($clientTypesValues[$traveler->client_type]) ? $clientTypesValues[$traveler->client_type] : 1; // De donde vienen los client types?
                $newClient->dni = $client->dni.'-duplicado';
                $newClient->dni_expiration = $this->formatDate($client->dni_expiration);
                $newClient->place_birth = $client->place_birth;
                $newClient->name = $client->name;
                $newClient->surname = $client->surname;
                $newClient->email = $client->email;
                $newClient->phone = $client->phone;
                $newClient->address = $client->address;
                $newClient->intolerances = $traveler->intolerances;
                $newClient->frequent_flyer = $traveler->frequency_fly;
                $newClient->member_number = $traveler->member_number;
                $newClient->notes = $traveler->notes;
                $newClient->language_id = $languagesValues[$traveler->lang] ?? 1; // Esta en texto hay que mirar como pasarlo
                $newClient->created_at = $client->created_at;
                $newClient->updated_at = $client->updated_at;
                $newClient->deleted_at = null;
                $newClient->save();
            }
        });
    }

    function formatDate ($dateString)
    {
        if (empty($dateString)) {
            return null;
        }

        try {
            $validDate = Carbon::createFromFormat('d/m/Y H:i:s', $dateString);
        } catch (\Throwable $e) {
            // Manejar la excepción aquí (por ejemplo, mostrar un mensaje de error, devolver un valor predeterminado, etc.)
            return null;
        }

        /*return $validDate;

        Log::debug('MEFABNJDBDHJIDG H ID GUIDUDU');
        Log::debug($dateString);

        //$validDate = Carbon::createFromFormat('d-m-Y H:i:s', $dateString);

        $validDate = Carbon::createFromFormat('d-m-Y', $dateString);

        // Verificar si se proporcionó la hora en la cadena de fecha
        if (!$validDate->format('H:i:s')) {
            // No se proporcionó la hora, establecer la hora actual
            $validDate->setTime(Carbon::now()->format('H'), Carbon::now()->format('i'), Carbon::now()->format('s'));
        }*/

        // Obtener la cadena de fecha válida
        return $validDate->toDateTimeString();
    }


    function migrateTrips ()
    {
        $columns = [
            'title'         => 'trips.title',
            'description'   => 'trips.description',
            'commentary'    => 'trips.commentary',
            'trip_state_id' => 'trips.state', // Va con tripStateValues
            'created_at'    => 'trips.created_at',
            'updated_at'    => 'trips.updated_at',
            'deleted_at'    => null,
        ];

        $tripStateValues = [
            '0' => 1,
            '1' => 2,
        ];

        // Get original clients data
        $trips = DB::connection('db2')->table('trips')->get();
        // Set connection to local
        DB::connection('mysql');

        $trips->each(function ($trip) use ($tripStateValues) {
            $newTrip                = Trip::make([]);
            $newTrip->id            = $trip->id;
            $newTrip->title         = $trip->title;
            $newTrip->description   = $trip->description;
            $newTrip->commentary    = $trip->commentary;
            $newTrip->trip_state_id = $tripStateValues[$trip->state];
            $newTrip->created_at    = $trip->created_at;
            $newTrip->updated_at    = $trip->updated_at;
            $newTrip->deleted_at    = null;
            $newTrip->save();
        });
    }

    function migrateDepartures ()
    {
        $columns = [
            'trip_id'               => 'departures.trip_id',
            'state_id'              => 'departures.state',
            'start'                 => 'departures.start',
            'final'                 => 'departures.final',
            'price'                 => 'departures.price',
            'taxes'                 => '', // Viene de que edn algunas dentro de price tiene cosas delñ estilo + tasas o + taxes pfff
            'individual_supplement' => 'departures.individual_supplement',
            'pax_capacity'          => 'departures.pax_available',
            'commentary'            => 'departures.commentary',
            'expedient'             => 'departures.expedient',
            'created_at'            => 'departures.created_at',
            'updated_at'            => 'departures.updated_at',
            'deleted_at'            => 'departures.deleted_at',
        ];

        $departureStateValues = [
            '1' => 1,
            '2' => 2,
        ];

        // Get original clients data
        $departures = DB::connection('db2')->table('departures')->get();
        // Set connection to local
        DB::connection('mysql');

        $departures->each(function ($departure) use ($departureStateValues) {

            $price = $departure->price;
            $departurePrice = 0;
            $departureTaxes = 0;
            $departureObservations = null;

            if ($price === '2500€ +  50 TAXES') {
                $departurePrice = 2500;
                $departureTaxes = 50;
            } else if ($price === '1795.00€ + 125€') {
                $departurePrice = 1795;
                $departureTaxes = 125;
            } else if ($price === '1975€ + 195€ TAXES') {
                $departurePrice = 1975;
                $departureTaxes = 195;
            } else if ($price === 'EN PROCES') {
                $departurePrice = null;
                $departureTaxes = null;
                $departureObservations = 'EN PROCES';
            } else if ($price === '.') {
                $departurePrice = null;
                $departureTaxes = null;
                $departureObservations = '.';
            } else if ($price === '8985€ (MINIM 10 PAX)') {
                $departurePrice = 8985;
                $departureObservations = '(MINIM 10 PAX)';
            } else if ($price === 'PAX EN DOBLE: 4375€') {
                $departurePrice = 4375;
                $departureObservations = 'PAX EN DOBLE: 4375€';
            } else if ($price === 'ADT- 6850€ - CHD 4875€') {
                $departurePrice = null;
                $departureObservations = 'ADT- 6850€ - CHD 4875€';
            } else if ($price === 'ADT - 5385€ - CHD 3240€ + TAXES 435€') {
                $departurePrice = null;
                $departureObservations = 'ADT - 5385€ - CHD 3240€ + TAXES 435€';
            } else {
                // Saca los precios y los pasa a floats validos
                $departurePrice = floatval(str_replace(',', '.', trim(preg_split('/\€/', $departure->price)[0])));
            }

            $tax = array_reduce(["tasas", "taxes", "tax"], function ($encontrada, $palabra) use ($price) {
                return $encontrada || str_contains(strtolower($price), strtolower($palabra));
            }, false);

            if ($tax) {
                $palabrasClave = "tasas|taxes|tax";
                $patron = '/(' . $palabrasClave . ')(.*?)€/ims';
                $departureTaxes = null;
                if (preg_match($patron, $departure->price, $matches)) {
                    $departureTaxes = trim($matches[2]);
                }
            }

            $departureSupplement = floatval(str_replace(',', '.', trim(preg_split('/\€/', $departure->individual_supplement)[0])));

            $newDeparture                           = Departure::make([]);
            $newDeparture->id                       = $departure->id;
            $newDeparture->trip_id                  = $departure->trip_id;
            $newDeparture->state_id                 = $departureStateValues[$departure->state];
            $newDeparture->start                    = $departure->start;
            $newDeparture->final                    = $departure->final;
            $newDeparture->price                    = $departurePrice;
            $newDeparture->taxes                    = $departureTaxes;
            $newDeparture->individual_supplement    = $departureSupplement;
            $newDeparture->pax_capacity             = $departure->pax_available;
            $newDeparture->commentary               = $departureObservations ? $departureObservations . ' ' . $departure->commentary : $departure->commentary;
            $newDeparture->expedient                = $departure->expedient;
            $newDeparture->created_at               = $departure->created_at;
            $newDeparture->updated_at               = $departure->updated_at;
            $newDeparture->deleted_at               = null;
            $newDeparture->save();


            $roomTypesValues = [
                '1' => 1,
                '2' => 2,
                '3' => 3,
                '4' => 4
            ];

            // Get original clients data
            /*$departureRooms = DB::connection('db2')
                ->table('rel_departure_client')
                ->selectRaw('SELECT departure_id, type_room, COUNT(*) AS total')
                ->where('departure_id',$departure->id)
                ->groupBy('type_room')
                ->get();*/
            $departureRooms = DB::connection('db2')
                ->table('rel_departure_client')
                ->select('type_room', DB::raw('COUNT(*) AS total'))
                ->where('departure_id', $newDeparture->id)
                ->groupBy('type_room')
                ->get();



            $departureRooms->each(function ($departureRoom) use ($newDeparture, $departure) {
                $room_type = $departureRoom->type_room != 0 ? $departureRoom->type_room : 1;
                // Set connection to local
                DB::connection('mysql')
                    ->table('rel_departure_room_type')
                    ->insert([
                        'departure_id'  => $newDeparture->id,
                        'room_type_id'  => $room_type,
                        'quantity'      => $departureRoom->total,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
            });
        });
    }

    function migrateRoles ()
    {
        $columns = [
            'name' => 'roles.name',
            'description' => 'roles.description',
            'created_at' => 'roles.created_at',
            'updated_at' => 'roles.updated_at',
            'deleted_at' => null,
        ];

        // Get original clients data
        $roles = DB::connection('db2')->table('roles')->get();
        // Set connection to local
        DB::connection('mysql');

        $roles->each(function ($rol) {
            $newmRol                = Role::make([]);
            $newmRol->name          = $rol->name;
            $newmRol->description   = $rol->description;
            $newmRol->created_at    = $rol->created_at;
            $newmRol->updated_at    = $rol->updated_at;
            $newmRol->deleted_at    = null;
            $newmRol->save();
        });
    }

    /*function migrateRooms ()
    {
        $columns = [
            'room_type_id'  => 'rooms.type_room',
            'departure_id'  => 'rooms.departure_id',
            'room_number'   => 'rooms.number_room',
            'observations'  => 'rooms.observations',
            'created_at'    => 'rooms.created_at',
            'updated_at'    => 'rooms.updated_at',
            'deleted_at'    => null,
        ];

        // TODO: Repasar esto cual es cual
        $roomTypesValues = [
            '0' => 1,
            '1' => 2,
            '2' => 3,
            '3' => 4,
            '4' => 5,
        ];

        // ROOMS NO EXISTE LEÑE

        // Get original clients data
        $rooms = DB::connection('db2')->table('rooms')->get();
        // Set connection to local
        DB::connection('mysql');

        $rooms->each(function ($room) use ($roomTypesValues) {
            $newRoom = Room::make([]);
            $newRoom->room_type_id  = $roomTypesValues[$room->type_room];
            $newRoom->departure_id  = $room->departure_id;
            $newRoom->room_number   = $room->number_room;
            $newRoom->observations  = $room->observations;
            $newRoom->created_at    = $room->created_at;
            $newRoom->updated_at    = $room->updated_at;
            $newRoom->deleted_at    = null;
            $newRoom->save();

        });
    }*/

    function migrateClientDeparture ()
    {
        $columns = [
            'departure_id'  => 'travalers.departure_id',
            'client_id'     => 'travalers.client_id',
            'seat'          => 'travalers.seat',
            'state'         => '', // TODO: mirar de donde viene este estado
            'observations'  => 'travalers.observations',
            'room_type_id'  => 'travalers.type_room',
            'created_at'    => 'travalers.created_at',
            'updated_at'    => 'travalers.updated_at',
        ];

        /**
         * TODO: Repasar esto cual es cual
         * Son los distintos estados de una room en el modelo antiguo
         * 1 - Sin pagar
         * 2 - Primer pago
         * 3 - Segundo pago
         * 4 - Tercer pago (todo pagado)
         * 5 - Cancelado
         */
        $statesValues = [
            '0' => 1,
            '1' => 2,
            '2' => 3,
            '3' => 4,
            '4' => 5,
        ];

        $roomTypesValues = [
            '1' => 1,
            '2' => 2,
            '3' => 3,
            '4' => 4
        ];

        // Get original clients data
        $travelers = DB::connection('db2')->table('travelers')->get();

        $travelers->each(function ($traveler) use ($statesValues, $roomTypesValues) {
            // Get original clients data
            $relation = DB::connection('db2')
                ->table('rel_departure_client')
                ->where('departure_id', $traveler->departure_id)
                ->where('client_id', $traveler->client_id)
                ->get();
            // Set connection to local
            DB::connection('mysql');

            $newClientDeparture                 = ClientDepartures::make([]);
            $newClientDeparture->id             = $traveler->id;
            $newClientDeparture->departure_id   = $traveler->departure_id;
            $newClientDeparture->client_id      = $traveler->client_id;
            $newClientDeparture->seat           = $traveler->seat;
            $newClientDeparture->state          = $statesValues[$relation->state]; // TODO: mirar de donde viene este esta;
            $newClientDeparture->observations   = $traveler->observations . ' ' . $traveler->type_room; // En la original type_room contiene comentarios
            $newClientDeparture->room_type_id   = $relation->type_room;
            $newClientDeparture->created_at     = $traveler->created_at;
            $newClientDeparture->updated_at     = $traveler->updated_at;
            $newClientDeparture->save();

            $newRoom = Room::make([]);
            $newRoom->room_type_id  = $roomTypesValues[$relation->type_room];
            $newRoom->departure_id  = $relation->departure_id;
            $newRoom->room_number   = $relation->number_room;
            $newRoom->observations  = $relation->observations;
            $newRoom->created_at    = $relation->created_at;
            $newRoom->updated_at    = $relation->updated_at;
            $newRoom->deleted_at    = null;
            $newRoom->save();

            $newRoom->clients()->attach($traveler->client_id);
        });
    }

    /*function migrateClientRoom ()
    {
        // TODO: mirar de donde bienetodo esto
        $columns = [
            'client_id' => 'travalers.client_id',
            'room_id' => '',
            'created_at' => 'travalers.created_at',
            'updated_at' => 'travalers.updated_at',
        ];
    }*/

    /*function migrateDepartureRoomType ()
    {
        // TODO: mirar de donde bienetodo esto
        $columns = [
            'departure_id' => '',
            'room_type_id' => '',
            'quantity' => '',
            'created_at' => '',
            'updated_at' => '',
        ];

        $roomTypesValues = [
            '1' => 1,
            '2' => 2,
            '3' => 3,
            '4' => 4
        ];

        // Get original clients data
        $travelers = DB::connection('db2')
            ->table('rel_departure_client')
            ->selectRaw('SELECT type_room, COUNT(*) AS total')
            ->where()
            ->groupBy('type_room')
            ->get();

        // SELECT type_room, COUNT(*) AS total FROM rel_departure_client where departure_id = 4 GROUP BY type_room;

        // Set connection to local
        DB::connection('mysql');
    }*/

    function migrateRoleUser ()
    {
        $columns = [
            'role_id' => 'rel_role_user.role_id',
            'user_id' => 'rel_role_user.user_id',
            'created_at' => 'rel_role_user.created_at',
            'updated_at' => 'rel_role_user.updated_at',
        ];

    }
}
