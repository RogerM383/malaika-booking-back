<?php

namespace App\Services;

use App\Models\Client;
use App\Models\ClientDepartures;
use App\Models\Departure;
use App\Models\Role;
use App\Models\Room;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\Pure;
use function PHPUnit\Framework\logicalOr;

class DatabaseMigrationService
{
    #[Pure] public function __construct()
    {

    }

    #[Pure] function migrate ()
    {
        //$this->migrateUsers();
        //$this->migrateClients();
        $this->migrateTrips();
        /*$this->migrateDepartures();*/
        /*$this->migrateRoles();*/
        /*$this->migrateRooms();*/
    }

    function migrateUsers (): void
    {
        $columns = [
            'name',
            'email',
            'email_verified_at',
            'password',
            'remember_token',
            'created_at',
            'updated_at',
            'deleted_at'
        ];
        // Get original data
        $users = DB::connection('db2')->table('users')->get();
        // Set connectyion to local
        DB::connection('mysql');
        // Create Users
        $users->each(function ($user) {
            $newUser = User::make([]);
            $newUser->name = $user->name;
            $newUser->email = $user->email;
            $newUser->email_verified_at = $user->email_verified_at;
            $newUser->password = $user->password;
            $newUser->created_at = $user->created_at;
            $newUser->updated_at = $user->updated_at;
            $newUser->save();
        });
    }

    function migrateClients ()
    {
        // client -> travelers.client_id
        $columns = [
            'client_type_id' => 'travelers.client_type', // De donde vienen los client types?
            'dni' => 'clients.dni',
            'dni_expiration' => 'clients.dni_expiration',
            'place_birth' => 'clients.place_birth',
            'name' => 'clients.name',
            'surname' => 'clients.surname',
            'email' => 'clients.email',
            'phone' => 'clients.phone',
            'address' => 'clients.address',
            'intolerances' => 'travelers.intolerances',
            'frequent_flyer' => 'travelers.frequency_fly',
            'member_number' => 'travelers.member_number',
            'notes' => 'travelers.notes',
            'language_id' => 'travelers.lang', // Esta en texto hay que mirar como pasarlo
            'created_at' => 'clients.created_at',
            'updated_at' => 'clients.updated_at',
            'deleted_at' => null
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
            'passadis, pero s\'adapta' => 1,
            'CAST' => 2,
        ];

        // Get original clients data
        $clients = DB::connection('db2')->table('clients')->get();
        // Get original travelers data
        $travelers = DB::connection('db2')->table('travelers')->get();
        // Set connectyion to local
        DB::connection('mysql');

        $clients->each(function ($client) use ($travelers, $clientTypesValues, $languagesValues) {
            // Search related travaler
            $traveler = array_search($client->id, array_column(json_decode($travelers), 'client_id'));
            // Create client
            $newClient = Client::make([]);
            $newClient->client_type_id = $clientTypesValues[$traveler->client_type]; // De donde vienen los client types?
            $newClient->dni = $client->dni;
            $newClient->dni_expiration = $client->dni_expiration;
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
            $newClient->language_id = $languagesValues[$traveler->lang]; // Esta en texto hay que mirar como pasarlo
            $newClient->created_at = $client->created_at;
            $newClient->updated_at = $client->updated_at;
            $newClient->deleted_at = null;
            $newClient->save();
        });
    }

    function migrateTrips ()
    {
        $columns = [
            'title' => 'trips.title',
            'description' => 'trips.description',
            'commentary' => 'trips.commentary',
            'trip_state_id' => 'trips.state', // Va con tripStateValues
            'created_at' => 'trips.created_at',
            'updated_at' => 'trips.updated_at',
            'deleted_at' => null,
        ];

        $tripStateValues = [
            '0' => 1,
            '1' => 2,
        ];

        // Get original clients data
        $trips = DB::connection('db2')->table('trips')->get();

        $trips->each(function ($trip) use ($tripStateValues) {
            $newTrip                = Trip::make([]);
            $newTrip->title         = $tripStateValues[$trip->title];
            $newTrip->description   = $trip->description;
            $newTrip->commentary    = $trip->commentary;
            $newTrip->trip_state_id = $trip->state;
            $newTrip->created_at    = $trip->created_at;
            $newTrip->updated_at    = $trip->updated_at;
            $newTrip->deleted_at    = null;
        });
    }

    function migrateDepartures ()
    {
        $columns = [
            'trip_id' => 'departures.trip_id',
            'state_id' => 'departures.state',
            'start' => 'departures.start',
            'final' => 'departures.final',
            'price' => 'departures.price',
            'taxes' => '', // De donde viene?
            'individual_supplement' => 'departures.individual_supplement',
            'pax_capacity' => 'departures.pax_available',
            'commentary' => 'departures.commentary',
            'expedient' => 'departures.expedient',
            'created_at' => 'departures.created_at',
            'updated_at' => 'departures.updated_at',
            'deleted_at' => 'departures.deleted_at',
        ];

        $departureStateValues = [
            '1' => 1,
            '2' => 2,
        ];

        // Get original clients data
        $departures = DB::connection('db2')->table('departures')->get();

        $departures->each(function ($departure) use ($departureStateValues) {
            $newDeparture                           = Departure::make([]);
            $newDeparture->trip_id                  = $departure->trip_id;
            $newDeparture->state_id                 = $departureStateValues[$departure->state];
            $newDeparture->start                    = $departure->start;
            $newDeparture->final                    = $departure->final;
            $newDeparture->price                    = $departure->price;
            $newDeparture->taxes                    = null;
            $newDeparture->individual_supplement    = $departure->individual_supplement;
            $newDeparture->pax_capacity             = $departure->pax_available;
            $newDeparture->commentary               = $departure->commentary;
            $newDeparture->expedient                = $departure->expedient;
            $newDeparture->created_at               = $departure->created_at;
            $newDeparture->updated_at               = $departure->updated_at;
            $newDeparture->deleted_at               = $departure->deleted_at;
            $newDeparture->save();
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

        $roles->each(function ($rol) {
            $newmRol                = Role::make([]);
            $newmRol->name          = $rol->name;
            $newmRol->description   = $rol->description;
            $newmRol->created_at    = $rol->created_at;
            $newmRol->updated_at    = $rol->updated_at;
            $newmRol->deleted_at    = null;
        });
    }

    function migrateRooms ()
    {
        $columns = [
            'room_type_id' => 'rooms.type_room',
            'departure_id' => 'rooms.departure_id',
            'room_number' => 'rooms.number_room',
            'observations' => 'rooms.observations',
            'created_at' => 'rooms.created_at',
            'updated_at' => 'rooms.updated_at',
            'deleted_at' => null,
        ];

        // TODO: Repasar esto cual es cual
        $roomTypesValues = [
            '0' => 1,
            '1' => 2,
            '2' => 3,
            '3' => 4,
            '4' => null,
        ];

        // Get original clients data
        $rooms = DB::connection('db2')->table('rooms')->get();

        $rooms->each(function ($room) use ($roomTypesValues) {
            $newRoom = Room::make([]);
            $newRoom->room_type_id = $roomTypesValues[$room->type_room];
            $newRoom->departure_id = $room->departure_id;
            $newRoom->room_number = $room->number_room;
            $newRoom->observations = $room->observations;
            $newRoom->created_at = $room->created_at;
            $newRoom->updated_at = $room->updated_at;
            $newRoom->deleted_at = null;
        });
    }

    function migrateClientDeparture ()
    {
        $columns = [
            'departure_id' => 'travalers.departure_id',
            'client_id' => 'travalers.client_id',
            'seat' => 'travalers.seat',
            'state' => '', // TODO: mirar de donde viene este estado
            'observations' => 'travalers.observations',
            'room_type_id' => 'travalers.type_room',
            'created_at' => 'travalers.created_at',
            'updated_at' => 'travalers.updated_at',
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

        // Get original clients data
        $travelers = DB::connection('db2')->table('travelers')->get();

        // Get original clients data
        $travelers = DB::connection('db2')->table('rel_departure_client')->get();

        $travelers->each(function ($traveler) use ($statesValues) {
            $newClientDeparture                 = ClientDepartures::make([]);
            $newClientDeparture->departure_id   = $traveler->departure_id;
            $newClientDeparture->client_id      = $traveler->client_id;
            $newClientDeparture->seat           = $traveler->seat;
            //$newClientDeparture->state        = $statesValues[]; // TODO: mirar de donde viene este esta;
            $newClientDeparture->observations   = $traveler->observations;
            $newClientDeparture->room_type_id   = $traveler->type_room;
            $newClientDeparture->created_at     = $traveler->created_at;
            $newClientDeparture->updated_at     = $traveler->updated_at;
        });
    }

    function migrateClientRoom ()
    {
        // TODO: mirar de donde bienetodo esto
        $columns = [
            'client_id' => 'travalers.client_id',
            'room_id' => '',
            'created_at' => 'travalers.created_at',
            'updated_at' => 'travalers.updated_at',
        ];
    }

    function migrateDepartureRoomType ()
    {
        // TODO: mirar de donde bienetodo esto
        $columns = [
            'departure_id' => '',
            'room_type_id' => '',
            'quantity' => '',
            'created_at' => '',
            'updated_at' => '',
        ];
    }

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
