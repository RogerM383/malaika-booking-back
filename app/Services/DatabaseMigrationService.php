<?php

namespace App\Services;

use App\Models\Client;
use App\Models\ClientDepartures;
use App\Models\Departure;
use App\Models\Passport;
use App\Models\Role;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Trip;
use App\Models\User;
use App\Traits\HandleDNI;
use App\Traits\Slugeable;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\Pure;
use Illuminate\Support\Facades\Artisan;

class DatabaseMigrationService
{
    use Slugeable, HandleDNI;

    private RoomService $roomService;

    #[Pure] public function __construct(
        RoomService $roomService,
        protected DepartureService $departureService)
    {
        $this->roomService = $roomService;
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
        $this->migratePassports();
        $this->migrateTrips();

    }

    function migrate2 ()
    {
        Log::debug('MIGRATE 2 START');
        $this->migrateDepartures(0, 50);
        Log::debug('MIGRATE 2 FINISHED');
    }

    function migrate3 ()
    {
        Log::debug('MIGRATE 3 START');
        $this->migrateDepartures(50, 50);
        Log::debug('MIGRATE 3 FINISHED');
    }

    function migrate4 ()
    {
        Log::debug('MIGRATE 4 START');
        $this->migrateDepartures(100, 50);
        Log::debug('MIGRATE 4 FINISHED');
    }

    function migrate5 ()
    {
        Log::debug('MIGRATE 5 START');
        $this->migrateDepartures(150, 50);
        Log::debug('MIGRATE 5 FINISHED');
    }

    function migrate6 ()
    {
        Log::debug('MIGRATE 6 START');
        $this->migrateRoles();
        Log::debug('MIGRATE 6 FINISHED');
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

        Client::create([
            "nif" => "48965258Z",
            "name" => "Selene",
            "username" => "selene",
            "email" => "selene@gmail.com",
            "password" => "1234567"
        ]);
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
            //'deleted_at'        => null
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
                $newClient->observations = $traveler->observations;
                $newClient->seat = $traveler->seat;
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
                $newClient->observations = $traveler->observations;
                $newClient->seat = $traveler->seat;
                $newClient->language_id = $languagesValues[$traveler->lang] ?? 1; // Esta en texto hay que mirar como pasarlo
                $newClient->created_at = $client->created_at;
                $newClient->updated_at = $client->updated_at;
                $newClient->deleted_at = null;
                $newClient->save();
            }
        });
    }

    function formatDate ($dateString): ?string
    {
        if (empty($dateString) || $dateString === "PERMANENT" || $dateString === "indefinit" || $dateString === "NIE NO TE CADUCITAT" || $dateString ===  "685008759") {
            return null;
        }

        if ($dateString === "158/01/2028") {
            $dateString = "15/01/2028";
        } else if ($dateString === "15-022012") {
            $dateString = "15-02-2012";
        } else if ($dateString === "01-01-999") {
            $dateString = "01-01-1999";
        } else if ($dateString === "11-10-78") {
            $dateString = "11-10-1978";
        }

        $dateToParse = str_replace( " ", "-", $dateString);
        $dateToParse = str_replace( "--", "-", $dateToParse);
        $dateToParse = str_replace( "(", "", $dateToParse);
        $dateToParse = str_replace( ")", "", $dateToParse);
        $dateToParse = str_replace( "/", "-", $dateToParse);
        $dateToParse = str_replace( ".", "-", $dateToParse);

         return Carbon::parse( $dateToParse);
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
            $newTrip->slug          = $this->slugify($trip->title);
            $newTrip->description   = $trip->description;
            $newTrip->commentary    = $trip->commentary;
            $newTrip->trip_state_id = $tripStateValues[$trip->state];
            $newTrip->created_at    = $trip->created_at;
            $newTrip->updated_at    = $trip->updated_at;
            $newTrip->deleted_at    = null;
            $newTrip->save();
        });
    }

    function migrateDepartures ($skip, $take)
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
        $departures = DB::connection('db2')->table('departures')->skip($skip)->take($take)->get();
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
                $departureTaxes = 435;
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

            // EL 0 no es nada, se mete con estados (4) 5 - Cancelado o (5) 6 - En espera
            $roomTypesValues = [
                '1' => 1,
                '2' => 2,
                '3' => 3,
                '4' => 4
            ];

            // Get original clients data
            $departureRoomsTotals = DB::connection('db2')
                ->table('rel_departure_client')
                ->select('type_room', DB::raw('COUNT(*) AS total'))
                ->where('departure_id', $newDeparture->id)
                ->whereNot('state', 0)
                ->whereNot('state', 5)
                ->groupBy('type_room')
                ->get();



            // Todos los posibles valores de type_room
            $allPossibleTypeRooms = [1, 2, 3, 4];

            // Crear una colección con todos los posibles valores de type_room
            $newCollection = collect($allPossibleTypeRooms)->map(function ($typeRoom) use ($departureRoomsTotals) {
                // Buscar la entrada correspondiente en la colección original
                $entry = $departureRoomsTotals->firstWhere('type_room', $typeRoom);

                // Devolver una nueva entrada con total a 0 si no existe
                return collect(['type_room' => $typeRoom, 'total' => $entry ? $entry->total : 0]);
            });

            // Crea los totales de rooms assignados al departure
            $newCollection->each(function ($departureRoom) use ($newDeparture, $departure) {

                // Excluye el tipo 0 que solo se aplica a cancelados y esperando
                if ($departureRoom['type_room'] != 0) {

                    $capacity = $departureRoom['type_room'] === 1 ? 1 : ($departureRoom['type_room'] === 2 || $departureRoom['type_room'] === 3 ? 2 : 3);

                    // Set connection to local
                    DB::connection('mysql')
                        ->table('rel_departure_room_type')
                        ->insert([
                            'departure_id' => $newDeparture->id,
                            'room_type_id' => $departureRoom['type_room'],
                            'quantity' => $departureRoom['total'] / $capacity,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                } else {
                    // Set connection to local
                    DB::connection('mysql')
                        ->table('rel_departure_room_type')
                        ->insert([
                            'departure_id' => $newDeparture->id,
                            'room_type_id' => $departureRoom['type_room'],
                            'quantity' => 0,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                }
            });

            $this->migrateDepartureRooms($newDeparture);

        });
    }

    function migrateDepartureRooms ($newDeparture)
    {
        $departureRooms = DB::connection('db2')
            ->table('rel_departure_client')
            ->where('departure_id', $newDeparture->id)
            ->get();

        /**
         * Son los distintos estados de una room en el modelo antiguo
         * 1 - Sin pagar (0)
         * 2 - Primer pago (1)
         * 3 - Segundo pago (2)
         * 4 - Tercer pago (todo pagado) (3)
         * 5 - Cancelado (4)
         * 6 - Apuntado en espera (5)
         */
        $departureRooms->each(function ($departureRoom) use ($newDeparture) {
            //
            $existingRoom = Room::where('departure_id', $newDeparture->id)->where('room_number', $departureRoom->number_room)->first();

            if ($departureRoom->type_room !== 0) {
                if (empty($existingRoom)) {
                    // Añadimos la room
                    $newRoom = Room::make([]);
                    $newRoom->room_type_id = $departureRoom->type_room;
                    $newRoom->departure_id = $newDeparture->id;
                    // SWi es null es que estan en espera comprobar
                    $newRoom->room_number = !empty($departureRoom->number_room) ? $departureRoom->number_room : $this->roomService->getNextRoomNumber($newDeparture->id);
                    // eliminar aixo $newRoom->observations = $departureRoom->observations;
                    $newRoom->created_at = $departureRoom->created_at;
                    $newRoom->updated_at = $departureRoom->updated_at;
                    //$newRoom->deleted_at = null;
                    $newRoom->save();

                    // Count room types
                    $dep = $this->departureService->getById($newDeparture->id);
                    $dep->roomTypes()->newPivotQuery()->where('room_type_id', $newRoom->room_type_id)->increment('quantity');
                } else {
                    $newRoom = $existingRoom;
                }


            }

            $client = Client::find($departureRoom->client_id);
            if (!empty($client)) {
                // Assigna cliente a la room
                if (isset($newRoom)) {
                    $newRoom->clients()->attach($departureRoom->client_id,[
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }

                // Crea la relacion entre cliente y departure, antiguo traveler
                //$columns = [
                //        'departure_id'  => 'travalers.departure_id',
                //        'client_id'     => 'travalers.client_id',
                //        'seat'          => 'travalers.seat',
                //        'state'         => '', // TODO: mirar de donde viene este estado
                //        'observations'  => 'travalers.observations',
                //        'room_type_id'  => 'travalers.type_room',
                //        'created_at'    => 'travalers.created_at',
                //        'updated_at'    => 'travalers.updated_at',
                //    ];
                $traveler = DB::connection('db2')
                    ->table('travelers')
                    ->where('client_id', $client->id)
                    ->first();

                if (!empty($traveler)) {
                    DB::connection('mysql');
                    $newClientDeparture = ClientDepartures::make([]);
                    $newClientDeparture->departure_id = $departureRoom->departure_id;
                    $newClientDeparture->client_id = $departureRoom->client_id;
                    //$newClientDeparture->seat = $traveler->seat;
                    $newClientDeparture->state = $departureRoom->state + 1;
                    $newClientDeparture->observations = $departureRoom->observations;//$traveler->observations;
                    // Si type_room es 0 estan cancedlats
                    $newClientDeparture->room_type_id = $departureRoom->type_room !== 0 ? $departureRoom->type_room : 1;
                    $newClientDeparture->created_at = $traveler->created_at;
                    $newClientDeparture->updated_at = $traveler->updated_at;
                    $newClientDeparture->save();
                }
            }
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

    //function migrateClientDeparture ()
    //{
    //    $columns = [
    //        'departure_id'  => 'travalers.departure_id',
    //        'client_id'     => 'travalers.client_id',
    //        'seat'          => 'travalers.seat',
    //        'state'         => '', // TODO: mirar de donde viene este estado
    //        'observations'  => 'travalers.observations',
    //        'room_type_id'  => 'travalers.type_room',
    //        'created_at'    => 'travalers.created_at',
    //        'updated_at'    => 'travalers.updated_at',
    //    ];
//
    //    /**
    //     * TODO: Repasar esto cual es cual
    //     * Son los distintos estados de una room en el modelo antiguo
    //     * 1 - Sin pagar (0)
    //     * 2 - Primer pago (1)
    //     * 3 - Segundo pago (2)
    //     * 4 - Tercer pago (todo pagado) (3)
    //     * 5 - Cancelado (4)
    //     * 6 - Apuntado en espera (5)
    //     */
    //    $statesValues = [
    //        '0' => 1,
    //        '1' => 2,
    //        '2' => 3,
    //        '3' => 4,
    //        '4' => 5,
    //    ];
//
    //    $roomTypesValues = [
    //        '1' => 1,
    //        '2' => 2,
    //        '3' => 3,
    //        '4' => 4
    //    ];
//
    //    // Get original clients data
    //    $travelers = DB::connection('db2')->table('travelers')->get();
//
    //    $travelers->each(function ($traveler) use ($statesValues, $roomTypesValues) {
    //        // Get original clients data
    //        $relation = DB::connection('db2')
    //            ->table('rel_departure_client')
    //            ->where('departure_id', $traveler->departure_id)
    //            ->where('client_id', $traveler->client_id)
    //            ->get();
    //        // Set connection to local
    //        DB::connection('mysql');
//
    //        $newClientDeparture                 = ClientDepartures::make([]);
    //        $newClientDeparture->id             = $traveler->id;
    //        $newClientDeparture->departure_id   = $traveler->departure_id;
    //        $newClientDeparture->client_id      = $traveler->client_id;
    //        $newClientDeparture->seat           = $traveler->seat;
    //        $newClientDeparture->state          = $statesValues[$relation->state]; // TODO: mirar de donde viene este esta;
    //        $newClientDeparture->observations   = $traveler->observations . ' ' . $traveler->type_room; // En la original type_room contiene comentarios
    //        $newClientDeparture->room_type_id   = $relation->type_room;
    //        $newClientDeparture->created_at     = $traveler->created_at;
    //        $newClientDeparture->updated_at     = $traveler->updated_at;
    //        $newClientDeparture->save();
//
    //        $newRoom = Room::make([]);
    //        $newRoom->room_type_id  = $roomTypesValues[$relation->type_room];
    //        $newRoom->departure_id  = $relation->departure_id;
    //        $newRoom->room_number   = $relation->number_room;
    //        $newRoom->observations  = $relation->observations;
    //        $newRoom->created_at    = $relation->created_at;
    //        $newRoom->updated_at    = $relation->updated_at;
    //        $newRoom->deleted_at    = null;
    //        $newRoom->save();
//
    //        $newRoom->clients()->attach($traveler->client_id);
    //    });
    //}

    function migratePassports()
    {
        $passportValues  = [
            'id' => 'passport.id',
            'client_id' => 'passport.client_id',
            'number_passport' => 'passport.number_passport',
            'nationality' => 'passport.nac',
            'issue' => 'passport.issue',
            'exp' => 'passport.exp',
            'birth' => 'passport.birth',
            'created_at' => 'passport.created_at',
            'updated_at' => 'passport.updated_at',
            'deleted_at' => null,
        ];

        // Get original clients data
        $passports = DB::connection('db2')->table('passports')->get();
        // Set connection to local
        DB::connection('mysql');

        $passports->each(function ($passport) {

            if (!empty($passport->number_passport)) {
                try {
                    $newPassport = Passport::make([]);
                    $newPassport->id                = $passport->id;
                    $newPassport->client_id         = $passport->client_id;
                    $newPassport->number_passport   = $passport->number_passport;
                    $newPassport->nationality       = $passport->nac;
                    $newPassport->issue             = $this->formatDate($passport->issue);
                    $newPassport->exp               = $this->formatDate($passport->exp);
                    $newPassport->birth             = $this->formatDate($passport->birth);
                    $newPassport->created_at        = $passport->created_at;
                    $newPassport->updated_at        = $passport->updated_at;
                    $newPassport->deleted_at        = null;
                    $newPassport->save();
                } catch (Exception $e) {

                    $oldPassport = Passport::where('number_passport', $passport->number_passport)->first();
                    $oldPassport->update(['number_passport' => $oldPassport->number_passport.'-duplicated']);

                    $newPassport = Passport::make([]);
                    $newPassport->id                = $passport->id;
                    $newPassport->client_id         = $passport->client_id;
                    $newPassport->number_passport   = $passport->number_passport;
                    $newPassport->nationality       = $passport->nac;
                    $newPassport->issue             = $this->formatDate($passport->issue);
                    $newPassport->exp               = $this->formatDate($passport->exp);
                    $newPassport->birth             = $this->formatDate($passport->birth);
                    $newPassport->created_at        = $passport->created_at;
                    $newPassport->updated_at        = $passport->updated_at;
                    $newPassport->deleted_at        = null;
                    $newPassport->save();
                }
            }
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

    function updateRoomsNumbers ()
    {
        Departure::with('rooms')->chunk(100, function ($departures) {

            foreach ($departures as $departure) {

                // --- Busca habitaciones que no tengan asignao un cliente asigando a esta departure ---
                $emptyRooms = Room::whereHas('departure', function ($query) use ($departure) {
                    $query->where('departures.id', $departure->id);
                })
                ->whereDoesntHave('clients', function ($query) use ($departure) {
                    $query->whereHas('departures', function ($query) use ($departure) {
                        $query->where('departures.id', $departure->id)
                            ->where('rel_client_departure.state', '!=', 6)
                            ->where('rel_client_departure.state', '!=', 5);
                    });
                });

                if ($emptyRooms->count() >= 1) {
                    // --- Elimina emty rooms ---
                    $emptyRooms->each(function ($room) {
                        $room->delete();
                    });
                }

                $roomTypes = [];
                foreach ($departure->rooms as $room) {
                    $current = $roomTypes[$room->room_type_id] ?? 0;
                    $roomTypes[$room->room_type_id] = $current + 1;
                }

                foreach ($roomTypes as $key => $quantity) {
                    DB::table('rel_departure_room_type')
                        ->where('departure_id', $departure->id)
                        ->where('room_type_id', $key)
                        ->update(['quantity' => $quantity]);
                }
            }



            /*foreach ($departures as $departure) {
                $clients = $departure->clients->pluck('id', 'name');
                foreach ($clients as $name => $client) {
                    Log::debug('name => '.$name.' ID => '.$client);
                }
            }*/
        });
    }

    function importCommentsRoomType ()
    {
        // Obtiene travelers de origin
        DB::connection('db2')
            ->table('travelers')
            ->orderBy('created_at')
            ->chunk(100, function ($travelers) {
                foreach ($travelers as $traveler) {
                    $client = Client::withTrashed()->find($traveler->client_id);
                    $client->room_observations = $traveler->type_room;
                    $client->save();
                }
            });
    }

    function trimDNIs ()
    {
        $total = 0;
        Client::chunk(100, function ($clients) use ($total) {
            // --- Recorre todos los clientes
            foreach ($clients as $client) {
                // Si tienen DNI
                if ($client->dni && !str_contains($client->dni, 'duplicated')) {
                    $older = true;
                    $trimedDNI = $this->trimDNI($client->dni);
                    $others = Client::where('dni', $trimedDNI)->where('id', '!=', $client->id)->get();
                    if ($others->count() >= 1) {
                        foreach ($others as $index => $other) {
                            $total = $total + 1;
                            if ($client->id < $other->id) {
                                $other->dni = $other->dni . '-duplicated-'.$index;
                                $other->save();
                            } else if ($client->dni !== $other->dni) {
                                $older = false;
                                $client->dni = $trimedDNI . '-duplicated-'.$index;
                                $client->save();
                            }
                        }
                    }

                    if ($older && $client->dni !== $trimedDNI) {
                        $client->dni = $trimedDNI;
                        $client->save();
                    }
                }
            }
        });
        return $total;
    }

    function migrateNullPassports()
    {
        $passportValues  = [
            'id' => 'passport.id',
            'client_id' => 'passport.client_id',
            'number_passport' => 'passport.number_passport',
            'nationality' => 'passport.nac',
            'issue' => 'passport.issue',
            'exp' => 'passport.exp',
            'birth' => 'passport.birth',
            'created_at' => 'passport.created_at',
            'updated_at' => 'passport.updated_at',
            'deleted_at' => null,
        ];

        // Get original clients data
        $passports = DB::connection('db2')->table('passports')->where('id', '>', 623)->whereNull('number_passport')->get();
        // Set connection to local
        DB::connection('mysql');

        $passports->each(function ($passport) {

            if (empty($passport->number_passport)) {
                try {
                    $newPassport = Passport::make([]);
                    $newPassport->id                = $passport->id;
                    $newPassport->client_id         = $passport->client_id;
                    $newPassport->number_passport   = $passport->number_passport;
                    $newPassport->nationality       = $passport->nac;
                    $newPassport->issue             = $this->formatDate($passport->issue);
                    $newPassport->exp               = $this->formatDate($passport->exp);
                    $newPassport->birth             = $this->formatDate($passport->birth);
                    $newPassport->created_at        = $passport->created_at;
                    $newPassport->updated_at        = $passport->updated_at;
                    $newPassport->deleted_at        = null;
                    $newPassport->save();
                } catch (Exception $e) {

                    $oldPassport = Passport::where('number_passport', $passport->number_passport)->first();
                    $oldPassport->update(['number_passport' => $oldPassport->number_passport.'-duplicated']);

                    $newPassport = Passport::make([]);
                    $newPassport->id                = $passport->id;
                    $newPassport->client_id         = $passport->client_id;
                    $newPassport->number_passport   = $passport->number_passport;
                    $newPassport->nationality       = $passport->nac;
                    $newPassport->issue             = $this->formatDate($passport->issue);
                    $newPassport->exp               = $this->formatDate($passport->exp);
                    $newPassport->birth             = $this->formatDate($passport->birth);
                    $newPassport->created_at        = $passport->created_at;
                    $newPassport->updated_at        = $passport->updated_at;
                    $newPassport->deleted_at        = null;
                    $newPassport->save();
                }
            }
        });
    }
}
