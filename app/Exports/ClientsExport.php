<?php

namespace App\Exports;

use App\Models\Client;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;


class ClientsExport implements WithMapping, FromCollection, WithHeadings, ShouldAutoSize
{
    use Exportable;

    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        return Client::with(['passport'])->get();
    }

    public function map($row): array
    {
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

        return [
            $row->name ?? "",
            $row->surname ?? "",
            $row->phone ?? "",
            $row->email ?? "",
            $row->dni ?? "",

            $row->passport->number_passport ?? "",
            $row->passport->issue ?? "",
            $row->passport->exp ?? "",
            $row->passport->nationality ?? "",
            $row->passport->birth ?? "",

            //$row->traveler->seat ?? "",
            //$row->traveler->observations ?? "",
            $row->intolerances ?? "",
            $row->client_type ?? "", // TODO: Esto va a ser una ID mirar como sacar el nombre
            $row->frequent_flyer ?? "",
            //$row->traveler->type_room ?? "", // TODO: Esto va a ser una ID mirar como sacar el nombre
            $row->notes ?? "",
            $row->member_number?? "",
            $row->language_id ?? "", // TODO: Esto va a ser una ID mirar como sacar el nombre
        ];
    }

    public function headings(): array
    {
        return [
            __('Name'),
            __('Surname'),
            __('Phone'),
            __('Email'),
            __('DNI'),
            __('Passport Number'),
            __('Issue'),
            __('Exp'),
            __('Nationality'),
            __('Birthdate'),
            __('Seat'),
            __('Observations'),
            __('Intolerances'),
            __('Client Type'),
            __('Frequent Flyer'),
            __('Room Type'),
            __('Notes'),
            __('Member Number'),
            __('Language'),
        ];
    }
}
