@extends('layouts.app')

@section('content')

@if(session()->has('message'))

<div class="alert alert-success">
    <div class="container-fluid" style="max-width:75%">
        {{ session()->get('message') }}
        <button type="button" class="close" data-dismiss="alert">×</button>
    </div>
</div>

@endif

@if(session()->has('warning'))

<div class="alert alert-warning">
    <div class="container-fluid" style="max-width:75%">
        {{ session()->get('warning') }}
        <button type="button" class="close" data-dismiss="alert">×</button>
    </div>
</div>

@endif









<div class="container-fluid" style="max-width: 75%;">

    <div class="row">
        <!-- <span><h3>{{__('Travels')}}</h3></span> -->
        <span>
            <h3>Salidas de {{$client->name}}</h3>
        </span>
    </div>


    <table class="table" style="margin-top:30px;">
        <thead>
            <tr>
                <th>{{__('Trip')}}</th>
                <th>{{__('Start')}}</th>
                <th>{{__('Final')}}</th>
                <th>{{__('Rooms')}}</th>
                <th>{{__('Expedient')}}</th>
                <!-- <th  >{{__('Actions')}}</th> -->
                <!-- <th tyle="text-align:center;">{{__('Edit')}}</th> -->
                <th tyle="text-align:center;">{{__('Rooming')}}</th>
                <th tyle="text-align:center;">{{__('Status')}}</th>
                <!-- <th style="text-align:center;">{{__('Commentary')}}</th> -->

            </tr>
        </thead>
        <tbody>



            <tr>
                @forelse($departures as $departure)
            <tr>
                <td>{{$departure->trip->title}}</td>
                <td>{{ date('d-m-Y', strtotime($departure->start)) }}</td>
                <td>{{ date('d-m-Y', strtotime($departure->final)) }}</td>
                <td>{{ $departure->pax_capacity - $departure->clients()->where('state','<',4)->count()}} / {{$departure->pax_capacity}}</td>
                <td>{{$departure->expedient}}</td>




                <!-- <td> <a href="{{route('departure.edit', $departure->id)}}" class="btn btn-info "
                        style="border: none; background: none;"><i class="fas fa-pencil-alt fa-lg indexicon"></i></a>
                </td> -->

                <td> <a href="{{route('departure.clients',['departure' => $departure->id])}}" class="btn btn-info"
                        style="border: none; background: none;"><i class="fas fa-users indexicon"></i></a>
             </td>


<!-- status -->
                @if($departure->pivot->state < 4)
                <td><span class="text-success" style="padding-top:6px;display:block"> <i class="fas fa-check fa-lg"></i> </span> </a></td>
                @endif

<!-- wish -->
                @if($departure->pivot->state == 4)
                <td> <span class="text-danger" style="padding-top:6px;display:block"><i class="fas fa-times fa-lg"></span></i></td>
                @endif
<!-- cancel -->
                @if($departure->pivot->state == 5)
                <td> <span style="color:#FF803E;  padding-top:6px;display:block"><i class="far fa-list-alt fa-lg"> </span></i>
                </td>
                @endif




{{--   <td style="text-align: center; vertical-align : middle;">
                    @if($departure->commentary)
                    <button type="button" class="commenticon" data-target="#commentaryDeparture" data-toggle="modal"
                        data-departure="{{$departure->id}}" data-title="{{$departure->trip->title}}"
                        data-commentary="{{ $departure->commentary }}"> <i class="fas fa-comment indexicon"></i>
                    </button>
                    @else
                    <button type="button" class="commenticon" data-target="#commentaryDeparture" data-toggle="modal"
                        data-departure="{{$departure->id}}" data-title="{{$departure->trip->title}}"
                        data-commentary="{{ $departure->commentary }}"> <i
                            class="fas fa-comment indexicon passportgrey"></i> </button>

                    @endif
                </td>  --}}




                <!-- <td>         <button data-toggle="modal" data-target="#deleteDeparture" data-departure="{{$departure->id}}" data-name="{{$departure->start}}"
                 style="padding: 0; border: none; background: none; " class="btn btn-info "><i class="far fa-trash-alt fa-lg" style="color:red;"></i></button>
            </td> -->



            </tr>


            @empty
            <td>{{__('No departures found')}}</td>
            @endForelse
            </tr>



        </tbody>
    </table>

    <div class="row justify-content-center align-items-center">







    </div>



</div>

</div>





@endsection
