@extends('layouts.app')

@section('content')



<!-- MENSAJES FLASH -->
    @if(session()->has('message'))

    <div class="alert alert-success sticky-top" style=" position: fixed; width:100%; top:0;">
        <div class="container-fluid" style="max-width:75%">
            {{ session()->get('message') }}
            <button type="button" class="close" data-dismiss="alert">×</button>
        </div>
    </div>

    @endif

    @if(session()->has('warning'))

    <div class="alert alert-warning sticky-top" style=" position: fixed; width:100%; top:0;">
    <div class="container-fluid" style="max-width:75%">
        {{ session()->get('warning') }}
        <button type="button" class="close" data-dismiss="alert">×</button>
    </div>
    </div>

    @endif

<!-- END MENSAJES FLASH -->


<!--FORMULARIO  Nuevo Cliente -->
<div id="newClient" style="display:none">


    <div id="createTravelerli" class="container-fluid" style="max-width: 100%; margin-top:20px;">

    <div class="container-fluid">

    <div class="row mt-3">
		<div class="col-md-6">
			<div class="row">
				<div class="col-md-3">
                <span class="input-group-addon">{{__('Name')}}</span>
				</div>
				<div class="col-md-9">
                <input type="text" maxlength="190" class="form-control" name="name" id="name" placeholder="{{__('Enter Name')}}" required />

				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="row">
				<div class="col-md-3">
               {{__('Surname')}}
				</div>
				<div class="col-md-9">
                <input type="text" maxlength="190" class="form-control" name="surname" id="surname" placeholder="{{__('Enter Surname')}}" />

				</div>
			</div>
		</div>
    </div>

    <div class="row mt-3">
		<div class="col-md-6">
			<div class="row">
				<div class="col-md-3">
                {{__('Phone')}}
				</div>
				<div class="col-md-9">
                <input type="text" maxlength="190" class="form-control" name="phone" id="phone" placeholder="{{__('Enter Phone')}}" />

				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="row">
				<div class="col-md-3">
                {{__('Mail')}}
				</div>
				<div class="col-md-9">
                <input type="text" maxlength="190" class="form-control" name="email" id="email" placeholder="{{__('Enter Email')}}" />

				</div>
			</div>
		</div>
    </div>


    <div class="row mt-3">
		<div class="col-md-6">
			<div class="row">
				<div class="col-md-3">
                {{__('DNI')}}
				</div>
				<div class="col-md-9">
                <input type="text" maxlength="190" class="form-control" name="dni" id="dni" placeholder="{{__('Enter DNI')}}" />

				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="row">
				<div class="col-md-3">
                {{__('Dni expiration')}}
				</div>
				<div class="col-md-9">
                <input type="text" maxlength="190" class="form-control datepicker" name="dni_expiration" id="dni_expiration" placeholder="{{__('aaaa-mm-dd')}}" style="margin-right:10px;" />

				</div>
			</div>
		</div>
	</div>


    <div class="row mt-3">
		<div class="col-md-6">
			<div class="row">
				<div class="col-md-3">
                {{__('Address')}}
				</div>
				<div class="col-md-9">
                <input type="text" maxlength="190" class="form-control" name="address" id="address" placeholder="{{__('Enter Phone')}}" />

				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="row">
				<div class="col-md-3">
                {{__('Place of birth')}}
				</div>
				<div class="col-md-9">
                <input type="text" maxlength="190" class="form-control" name="place_birth" id="place_birth" placeholder="{{__('Enter Email')}}" />

				</div>
			</div>
		</div>
	</div>



<!-- container fluid -->
</div>


            <div class="row" style="margin-top:10px;">

                <div class="col-sm-12">
                    <button type="submit" id="button" class="btn btn-info fright">{{__('Add and Create')}}</button>
                </div>
            </div>
        </div>


    </div>


</div>
<!-- FIN FORMULARIO  Nuevo Cliente -->


<!-- Botones Menu  PASAJEROS CANCELADOS y EXPORTAR DATOS -->

    <div class="container-fluid" style="max-width:75%">

        <div class="row">
            <div class="col-6">
                <h3>{{$departure->trip->title}}  {{ date('d-m-Y', strtotime($departure->start)) }} / {{ date('d-m-Y', strtotime($departure->final)) }}</h3>
            </div>
            <div class="col-6">
                <a style="margin-left:10px;" class="btn btn-info fright" href="{{ route('exportdeparture',['departure'=> $departure->id, 'exp' => $departure->expedient != null ? $departure->expedient : '-'   ]) }}">{{ __('Export Data') }}</a>

                <a style="margin-left:10px;" class="btn btn-info fright" href="#second">{{__('Travelers Canceled')}}</a>
            </div>

        </div>

<!-- END Botones Menu -->


<!-- INFORMACION CABEZERA -->
    <div class="row">
    <div class="col-4">
            <div class="card-header" style="border-bottom:none!important; ">
                <h5><i class="fas fa-users indexicon passportgrey" style="font-size:0.8em"></i> {{__('Summary')}}</h5>
                <p style="display:block; border-bottom:1px solid #999; padding-bottom:3px;">{{__('Travelers')}} : {{$departure->clients()->where('state','<',4)->count()}}</></p>
                <div class="row">

                    <div class="col-6">
                        <p> {{__('Pax Available')}}: {{ $departure->pax_capacity - $departure->clients()->where('state','<',4)->count()}}</p>
                    </div>

                    <div class="col-6">
                        <p>
                        {{__('Pax Totals')}}: {{$departure->pax_capacity }}
                        </p>
                    </div>
                </div>

                <p style="display:block; border-bottom:1px solid #999; padding-bottom:3px;">{{__('Rooms')}}: {{ $departure->clients()->distinct('number_room')->count('number_room') }}</p>
                <div class="row">

                    <div class="col-3">
                        <p> Dui<br>{{ $departure->clients()->distinct('number_room')->where('type_room',1)->count('number_room') }}</p>
                    </div>
                    <div class="col-3">
                        <p> Doble<br>{{ $departure->clients()->distinct('number_room')->where('type_room',2)->count('number_room') }}</p>
                    </div>
                    <div class="col-3">
                        <p> Twin<br>{{ $departure->clients()->distinct('number_room')->where('type_room',3)->count('number_room') }}</p>
                    </div>
                    <div class="col-3">
                        <p> Triple<br>{{ $departure->clients()->distinct('number_room')->where('type_room',4)->count('number_room') }}</p>
                    </div>

                 </div>







            </div>
        </div>
        @if($departure->trip->commentary)
        <div class="col-4">
            <div class="card-header" style="border-bottom:none!important; ">
                <h5><i class="far fa-comment indexicon passportgrey" style="font-size:0.8em"></i> {{__('Trip')}}</h5>
                <div style="padding:5px 5px 5px 5px;">
                    <!-- <textarea name="" id="" cols="30" rows="3" readonly> {{$departure->trip->commentary}} </textarea>  -->
                    {{$departure->trip->commentary}}

                </div>
            </div>
        </div>
        @endif

        @if($departure->commentary)
        <div class="col-4">
            <div class="card-header" style="border-bottom:none!important; ">
                <h5><i class="far fa-comment indexicon passportgrey" style="font-size:0.8em"></i> {{__('Departure')}}</h5>
                <div style="padding:5px 5px 5px 5px;">
                    <!-- <textarea name="" id="" cols="30" rows="3" readonly> {{$departure->trip->commentary}} </textarea>  -->
                    {{$departure->commentary}}


                </div>
            </div>
        </div>
        @endif



    </div>

<!-- INFORMACION CABEZERA -->

<br>
</div>
<br>


    @extends('modal.detachRoom')
    @extends('modal.commentaryRooming')
    @extends('modal.deleteRoom')
    @extends('modal.reinsertClientDeparture')




<!-- Table de clientes ROOMING-->

    <div class="container-fluid" style="max-width:75%">
        <div class="row">
            <div class="col-sm-12">

                @php
                $values  = array_diff(array_unique($departure->clients()->pluck('number_room')->toArray()),[null]);
                array_multisort ($values);
                @endphp


                @foreach($values as $i)


                <div class="card" style="width:100%">
                    <div class="card-header">



                        <div class="row ">
                            <div class="col-sm">
                                <div class="justify-content-center">
                                    {{__('Number Room')}} {{ $i }}
                                </div>
                            </div>
                            <div class="col-sm">

                                <!-- HABITACIONES ESCOJER TYPE -->
                                @forelse($clients as $client)


                                @if( $client->pivot->number_room == $i && $client->pivot->type_room == 1 )
                                <a href="{{ route('typeroom', ['number_room' => $i, 'departure' => $departure->id , 'type_room' => 1 ]) }}" class="typeRoom selected">DUI</a>
                                <a href="{{ route('typeroom', ['number_room' => $i, 'departure' => $departure->id , 'type_room' => 2 ]) }}" class="typeRoom">DOBLE</a>
                                <a href="{{ route('typeroom', ['number_room' => $i, 'departure' => $departure->id , 'type_room' => 3 ]) }}" class="typeRoom">TWIN</a>
                                <a href="{{ route('typeroom', ['number_room' => $i, 'departure' => $departure->id , 'type_room' => 4 ]) }}" class="typeRoom">TRIPLE</a>
                                @break
                                @elseif($client->pivot->number_room == $i && $client->pivot->type_room == 2)
                                <a href="{{ route('typeroom', ['number_room' => $i, 'departure' => $departure->id , 'type_room' => 1 ]) }}" class="typeRoom">DUI</a>
                                <a href="{{ route('typeroom', ['number_room' => $i, 'departure' => $departure->id , 'type_room' => 2 ]) }}" class="typeRoom selected">DOBLE</a>
                                <a href="{{ route('typeroom', ['number_room' => $i, 'departure' => $departure->id , 'type_room' => 3 ]) }}" class="typeRoom">TWIN</a>
                                <a href="{{ route('typeroom', ['number_room' => $i, 'departure' => $departure->id , 'type_room' => 4 ]) }}" class="typeRoom">TRIPLE</a>
                                @break
                                @elseif($client->pivot->number_room == $i && $client->pivot->type_room == 3)
                                <a href="{{ route('typeroom', ['number_room' => $i, 'departure' => $departure->id , 'type_room' => 1 ]) }}" class="typeRoom">DUI</a>
                                <a href="{{ route('typeroom', ['number_room' => $i, 'departure' => $departure->id , 'type_room' => 2 ]) }}" class="typeRoom">DOBLE</a>
                                <a href="{{ route('typeroom', ['number_room' => $i, 'departure' => $departure->id , 'type_room' => 3 ]) }}" class="typeRoom selected">TWIN</a>
                                <a href="{{ route('typeroom', ['number_room' => $i, 'departure' => $departure->id , 'type_room' => 4 ]) }}" class="typeRoom">TRIPLE</a>
                                @break
                                @elseif($client->pivot->number_room == $i && $client->pivot->type_room == 4)
                                <a href="{{ route('typeroom', ['number_room' => $i, 'departure' => $departure->id , 'type_room' => 1 ]) }}" class="typeRoom">DUI</a>
                                <a href="{{ route('typeroom', ['number_room' => $i, 'departure' => $departure->id , 'type_room' => 2 ]) }}" class="typeRoom">DOBLE</a>
                                <a href="{{ route('typeroom', ['number_room' => $i, 'departure' => $departure->id , 'type_room' => 3 ]) }}" class="typeRoom">TWIN</a>
                                <a href="{{ route('typeroom', ['number_room' => $i, 'departure' => $departure->id , 'type_room' => 4 ]) }}" class="typeRoom selected">TRIPLE</a>
                                @break


                                @endif

                                @empty
                                @endForelse
                                <!-- FIN HABITACION TYPE -->
                            </div>

                            <div class="col-sm text-right my-auto">


                            </div>


                            <!-- ADD TRAVELER  addSelect click -->

                            <div class="dropdown">
                                <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-user-plus fa-lg"></i>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <button class="dropdown-item createTraveler" id={{$i}} style="padding: 0; border: none; background: none; ">{{__('Create Traveler')}}</button>


                                    <button class="dropdown-item serachTraveler" id={{$i}} style="padding: 0; border: none; background: none; ">{{__('Search Traveler')}}</button>
                                </div>
                            </div>

                        </div>

                    </div>


                    <form id="confirmRoom" action="{{ action('RoomingController@confirmRoom', ['departure'=> $departure->id] )}}" method="POST">
                        @csrf

                        <input type="hidden" name="room" value="{{$i}}">
                        <ul class="list-group list-group-flush">

                            <!-- Añadimos los select nuevo para agregar los nuevos huespedes -->
                            <span class="container{{$i}}"></span>


                            @forelse($clients as $client)
                                @if( $client->pivot->number_room == $i && $client->pivot->state < 4)


                                    <li class="list-group-item">
                                        <div class="row">
                                            <div class="col-2">

                                                <!-- ICONO SHOW-->
                                                 <td> <a href="{{route('client.show', $client)}}" class="btn btn-info" style="border: none; background: none;"><i class="fas fa-eye fa-lg indexicon"></i></a> </td>

                                                <!-- ICONO EDITAR-->
                                                <td> <a href="{{route('client.edit', [ 'id' => $client->id ,'redirection' => 'rooming','departure' =>$departure->id])}}" class="btn btn-info " style="border: none; background: none; float:left"><i class="fas fa-pencil-alt fa-lg indexicon"></i></a> </td>

                                                <!-- ICONO PASSAPORTE-->
                                                @if($client->passport->number_passport)

                                                @if (Carbon\Carbon::parse($client->passport['exp'])->diffInDays(\Carbon\Carbon::now(),false) >= 0)


                                                <!-- rojo -->
                                                <td> <a href="{{route('passport.create',['client'=> $client,'rooming' => 1,'departure' => $departure->id]) }}" class="btn btn-info" style="border: none; background: none; float:left">
                                                        <i class="fas fa-passport fa-lg dangericon"></i></a> </td>

                                                @elseif(Carbon\Carbon::parse($client->passport['exp'])->diffInDays(\Carbon\Carbon::now(),false) < 0 && Carbon\Carbon::parse($client->passport['exp'])->diffInDays(\Carbon\Carbon::now(),false) > -180)



                                                    <!-- naranja -->
                                                    <td> <a href="{{route('passport.create',['client'=> $client,'rooming' => 1,'departure' => $departure->id]) }}" class="btn btn-info" style="border: none; background: none;float:left">
                                                            <i class="fas fa-passport fa-lg warningicon"></i></a> </td>

                                                    @else
                                                    <!-- azul -->
                                                    <td> <a href="{{route('passport.create',['client'=> $client,'rooming' => 1,'departure' => $departure->id]) }}" class="btn btn-info" style="border: none; background: none;float:left">
                                                            <i class="fas fa-passport fa-lg indexicon"></i></a> </td>

                                                    @endif


                                                    @else
                                                    <td> <a href="{{route('passport.create',['client'=> $client, 'rooming' => 1,'departure' => $departure->id]) }}" class="btn btn-info" style="border: none; background: none;float:left">

                                                            <i class="fas fa-passport fa-lg dangericon "></i></a> </td>
                                                    @endif
                                                    <!--FIN ICONO PASSAPORTE-->


                                            </div>
                                            <div class="col-3">

                                            <a class="text-dark" href="{{route('departuresClient', $client)}}" > {{ $client->surname }}, {{ $client->name }}  </a>

                                            </div>

                                            <div class="col-2">
                                                {{ $client->phone }}
                                            </div>
                                            <div class="col-3">
                                                {{ $client->email }}
                                            </div>

                                            <div class="col-2">

                                                <!-- MARCAR COMO CANCELADO  -->
                                                <button data-target="#detachRoomModal" style="float:right;" type="button" class="commenticon" data-observations="{{$client->pivot->observations}}" data-name="{{$client->name}}" data-client="{{$client->id}}" data-departure="{{$departure->id}}" data-toggle="modal"> <i class="fas fa-times" style="color:red; margin-right:10px;"></i> </button>


                                                <!-- MARCAR COMO PAGADO -->
                                                    @if($client->pivot->state == 0)
                                                    <a title="100%" href="{{ route('payconfirm', ['client' => $client, 'departure' => $departure->id,'state' => 3]) }}"><i class="fas fa-euro-sign payicon"></i></a>
                                                    <a title="40%" href="{{ route('payconfirm', ['client' => $client, 'departure' => $departure->id,'state' => 2]) }}"><i class="fas fa-euro-sign payicon"></i></a>
                                                    <a title="Senyal" href="{{ route('payconfirm', ['client' => $client, 'departure' => $departure->id,'state' => 1]) }}"><i class="fas fa-euro-sign payicon"></i></a>
                                                    @elseif($client->pivot->state == 1)
                                                    <a href="{{ route('payconfirm', ['client' => $client, 'departure' => $departure->id, 'state' =>3 ]) }}"><i class="fas fa-euro-sign payicon"></i></a>
                                                    <a href="{{ route('payconfirm', ['client' => $client, 'departure' => $departure->id,'state' => 2]) }}"><i class="fas fa-euro-sign payicon"></i></a>
                                                    <a href="{{ route('payconfirm', ['client' => $client, 'departure' => $departure->id,'state' => 1]) }}"><i class="fas fa-euro-sign payicon green"></i></a>
                                                    @elseif($client->pivot->state == 2)
                                                    <a href="{{ route('payconfirm', ['client' => $client, 'departure' => $departure->id, 'state' =>3]) }}"><i class="fas fa-euro-sign payicon"></i></a>
                                                    <a href="{{ route('payconfirm', ['client' => $client, 'departure' => $departure->id,'state' => 2]) }}"><i class="fas fa-euro-sign payicon green"></i></a>
                                                    <a href="{{ route('payconfirm', ['client' => $client, 'departure' => $departure->id,'state' => 1]) }}"><i class="fas fa-euro-sign payicon green"></i></a>
                                                    @elseif($client->pivot->state == 3)
                                                    <a href="{{ route('payconfirm', ['client' => $client, 'departure' => $departure->id, 'state'=> 3 ]) }}"><i class="fas fa-euro-sign payicon green"></i></a>
                                                    <a href="{{ route('payconfirm', ['client' => $client, 'departure' => $departure->id,'state' => 2]) }}"><i class="fas fa-euro-sign payicon green"></i></a>
                                                    <a href="{{ route('payconfirm', ['client' => $client, 'departure' => $departure->id,'state' => 1]) }}"><i class="fas fa-euro-sign payicon green"></i></a>

                                                    @endif
                                                <!-- END MARCAR COMO PAGADO -->



                                                <!-- AÑADIR COMNENRARIO -->

                                                    @if($client->pivot->observations)
                                                    <button style="float:right;" type="button" class="commenticon" data-observations="{{$client->pivot->observations}}" data-name="{{$client->name}}" data-client="{{$client->id}}" data-departure="{{$departure->id}}" data-toggle="modal" data-target="#commentaryRooming"> <i class="fas fa-comment indexicon"></i> </button>
                                                    @else
                                                    <button style="float:right;" type="button" class="commenticon" data-observations="{{$client->pivot->observations}}" data-name="{{$client->name}}" data-client="{{$client->id}}" data-departure="{{$departure->id}}" data-toggle="modal" data-target="#commentaryRooming"> <i class="fas fa-comment indexicon passportgrey"></i> </button>
                                                    @endif

                                                <!--END  AÑADIR COMNENRARIO -->


                                                <input type="hidden" id="observationshidden" value="{{$client->pivot->observations}}">
                                            </div>




                                            <!-- end row -->
                                        </div>


                                    </li>


                                @endif


                            @empty
                            @endForelse



                        </ul>

                    </form>
                </div>

         <p></p>
                @endforeach

                @if( $departure->clients()->wherePivot('state','<',4)->count() == 0)
                <p>{{__('There are no passengers assigned')}}</p>
                @endif



<!-- END  Table de clientes ROOMING-->



            <!-- AÑADIR HABITACION -->



                <div id="cardRoom"  style="display:none; margin-bottom:20px;" >

                    <div class="card" style="width:100%">
                        <div class="card-header">



                            <div class="row ">
                                <div class="col-sm">
                                    <div class="justify-content-center">
                                  {{__('Number Room')}}   {{ $departure->clients()->distinct('number_room')->count('number_room')+1 }}
                                        <!-- {{__('Number Room')}} {{ $departure->clients()->max('number_room')+1 }} -->
                                    </div>
                                </div>




                                <!-- ADD TRAVELER  addSelect click -->

                                <div class="dropdown">
                                    <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-user-plus fa-lg"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <button class="dropdown-item createTravelerToAddRomm" id={{ $departure->clients()->distinct('number_room')->count('number_room')+1 }} style="padding: 0; border: none; background: none; ">{{__('Create Traveler')}}</button>


                                        <button class="dropdown-item serachTravelerToAddRoom" id={{ $departure->clients()->distinct('number_room')->count('number_room')+1 }} style="padding: 0; border: none; background: none; ">{{__('Search Traveler')}}</button>
                                    </div>
                                </div>

                            </div>

                        </div>


                        <form id="confirmRoom" action="{{ action('RoomingController@confirmRoom', ['departure'=> $departure->id] )}}" method="POST">
                            @csrf

                            @if(isset($i))
                            <input type="hidden" name="room" value="{{$i}}">
                            @endif

                            <ul class="list-group list-group-flush">

                                <!-- Añadimos los select nuevo para agregar los nuevos huespedes -->
                                <span class="containerAppendContent"></span>

                            </ul>
                            </form>
                    </div>

                </div>


            <!-- END AÑADIR HABITACION -->



            <button  style="margin:10px; 20px; 0px; 20px;" type="button" class="btn btn-info fright addRoom" class="btn btn-primary">{{__('Add Room')}}</button>


        </div>

    </div>
</div>


@include('departure.cancelados.cancelados')
@include('departure.waitinglist.waitinglist')



@endsection

@section('scripts')

<script>
    $(document).ready(function($) {




        $('.existuser').trigger('change');
        $('.existuser').select2();

        var cardRoom = $('#cardRoom').html();
        var contents = $('#newClient').html();
        // TODO var contents2 search client



        $(".serachTraveler").click(function() {
            if ($("#serchTravelerli").length) {
                $("#serchTravelerli").detach();

            }


            $(".container" + this.id).append('<li id="serchTravelerli" class="list-group-item "><select required name="searchUser"  class="existuser" style="width:400px;"> ' +
                '@foreach($allclients as $client)<option value="{{ $client->id }}">{{ $client->name }}  {{ $client->surname }} {{ $client->dni }} </option>@endforeach  <input type="hidden" name="room" value="' + this.id + '">' +
                ' <button type="submit" id="button" class="btn btn-info fright"><i class="fas fa-plus"></i></button></li>');


            $('.existuser').select2();
            $(".existuser").prepend('<option selected=""></option>').select2({
                placeholder: "Select Traveler"
            });


            if ($("#createTravelerli").length) {
                $("#createTravelerli").detach();

            }

        });


        $(".createTraveler").click(function() {

            if ($("#createTravelerli").length) {
                $("#createTravelerli").detach();
            }


            if ($("#serchTravelerli").length) {
                $("#serchTravelerli").detach();

            }

            $(".container" + this.id).append('<li style="list-style-type: none;" id="createTravelerli">' + contents + '</li><input type="hidden" name="room" value="' + this.id + '">');




        });


/////////////////////////// Aparecer / Desaparecer el card cuando hacemos click en añadir habitacion


        $( ".addRoom" ).click(function() {

            if ( $('#cardRoom').css('display') == 'none' )
          {
                $('#cardRoom').css('display','block');
                // $('#cardWaitingList').css('display','none');
          }

            else{
                $('#cardRoom').css('display','none');
            }

        });

//////////////////////////////////////////////////////////  CARD ADD ROOMM




        $(".serachTravelerToAddRoom").click(function() {



            if ($("#serchTravelerli").length) {
                $("#serchTravelerli").detach();

            }


            $(".containerAppendContent").append('<li id="serchTravelerli" class="list-group-item "><select required name="searchUser"  class="existuser" style="width:400px;"> ' +
                '@foreach($allclients as $client)<option value="{{ $client->id }}">{{ $client->name }}  {{ $client->surname }} {{ $client->dni }} </option>@endforeach  <input type="hidden" name="room" value="' + this.id + '">' +
                ' <button type="submit" id="button" class="btn btn-info fright"><i class="fas fa-plus"></i></button></li>');


            $('.existuser').select2();
            $(".existuser").prepend('<option selected=""></option>').select2({
                placeholder: "Select Traveler"
            });


            if ($("#createTravelerli").length) {
                $("#createTravelerli").detach();

            }

        });


    $(".createTravelerToAddRomm").click(function() {

    if ($("#createTravelerli").length) {
        $("#createTravelerli").detach();
    }
    if ($("#serchTravelerli").length) {
        $("#serchTravelerli").detach();

    }

    $(".containerAppendContent").append('<li style="list-style-type: none;" id="createTravelerli">' + contents + '</li><input type="hidden" name="room" value="' + this.id + '">');

    });



    ///////////////////////////  Card WAITIN LIST




$(".searchTravelerToWaitingList").click(function() {


                    if ($("#serchTravelerli").length) {
                        $("#serchTravelerli").detach();

                    }


                    $(".containerAppendContentWaiting").append('<li id="serchTravelerli" class="list-group-item "><select required name="searchUser"  class="existuser" style="width:400px;"> ' +
                        '@foreach($allclients as $client)<option value="{{ $client->id }}">{{ $client->name }}  {{ $client->surname }} {{ $client->dni }} </option>@endforeach  <input type="hidden" name="room" value="' + this.id + '">' +
                        ' <button type="submit" id="button" class="btn btn-info fright"><i class="fas fa-plus"></i></button></li>');


                    $('.existuser').select2();
                    $(".existuser").prepend('<option selected=""></option>').select2({
                        placeholder: "Select Traveler"
                    });


                    if ($("#createTravelerli").length) {
                        $("#createTravelerli").detach();

                    }

});


$(".createTravelerToWaitingList").click(function() {

                        if ($("#createTravelerli").length) {
                        $("#createTravelerli").detach();
                        }
                        if ($("#serchTravelerli").length) {
                        $("#serchTravelerli").detach();

                        }

                        $(".containerAppendContentWaiting").append('<li style="list-style-type: none;" id="createTravelerli">' + contents + '</li><input type="hidden" name="room" value="' + this.id + '">');

});














        //Mantener el scroll cuando se modifica la pagina
        (function($) {
            window.onbeforeunload = function(e) {
                window.name += ' [' + $(window).scrollTop().toString() + '[' + $(window).scrollLeft().toString();
            };
            $.maintainscroll = function() {
                if (window.name.indexOf('[') > 0) {
                    var parts = window.name.split('[');
                    window.name = $.trim(parts[0]);
                    window.scrollTo(parseInt(parts[parts.length - 1]), parseInt(parts[parts.length - 2]));
                }
            };
            $.maintainscroll();
        })(jQuery);


    });
</script>




@endsection
