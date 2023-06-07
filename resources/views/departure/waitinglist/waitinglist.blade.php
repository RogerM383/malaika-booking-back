
<div class="container-fluid" style="max-width:75%; margin-bottom:50px; margin-top:100px;">

    <div class="row" id="third">
        <div class="col-8">
            <h3>{{__('Waiting List')}}</h3>
        </div>

        <div class="col-4">
            <!-- AÑADIR LISTA DE ESPERA -->
            <div id="cardWaitingList" style="  margin-bottom:20px;">



                <!-- ADD TRAVELER  addSelect click -->

                <div class="dropdown" style="float:right;">


                    <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-user-plus fa-lg"></i>
                    </button>

                    <!-- botones Waiting list -->
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <button class="dropdown-item createTravelerToWaitingList" id={{ $departure->clients()->max('number_room')+1 }} style="padding: 0; border: none; background: none; ">{{__('Create Traveler')}}</button>
                        <button class="dropdown-item searchTravelerToWaitingList" id={{ $departure->clients()->max('number_room')+1 }} style="padding: 0; border: none; background: none; ">{{__('Search Traveler')}}</button>
                    </div>

                </div>

            </div>

        </div>

        <!-- END AÑADIR LISTA DE ESPERA -->
    </div>




    <form id="confirmRoom" action="{{ action('RoomingController@inserWaitingList', ['departure'=> $departure->id] )}}" method="POST">
        @csrf

        @if(isset($i))
        <input type="hidden" name="room" value="{{$i}}">
        @endif

        <div class="row">
            <div class="col-12" style="margin-bottom:15px;">

                <ul class="list-group list-group-flush">

                    <!-- Añadimos los select nuevo para agregar los nuevos huespedes -->
                    <span class="containerAppendContentWaiting"></span>

                </ul>


            </div>

    </form>

</div>

<div class="row">
    <div class="col-sm-12">



        @foreach($clients as $client)

        @if($client->pivot->state == 5)



        <div class="card" style="margin-bottom:20px;">

            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <div class="row">


                        <div class="col-4">
                        <a class="text-dark" href="{{route('departuresClient', $client)}}" >{{$client->surname}}, {{$client->name}}</div></a>


                        <div class="col-4"> {{__('Phone')}}: {{$client->phone}}</div>


                        <div class="col-4">

                        <button style="float:right;" type="button" class="commenticon" data-observations="{{$client->pivot->observations}}"
                        data-name="{{$client->name}}"
                        data-client="{{$client->id}}"
                        data-departure="{{$departure->id}}"
                        data-state=" {{ $client->pivot->state }} "
                        data-toggle="modal" data-target="#deleteRoomModal"> <i style="color:red;" class="fas fa-trash-alt  indexicon"></i>


                    </button>

                        <button data-target="#detachRoomModal" style="float:right;" type="button" class="commenticon"


                        data-state=" {{ $client->pivot->state }} "
                         data-observations="{{$client->pivot->observations}}"
                         data-name="{{$client->name}}"
                         data-client="{{$client->id}}"
                         data-departure="{{$departure->id}}"

                          data-toggle="modal"> <i class="fas fa-times" style="color:red; "></i> </button>



                            <button style="float:right;" type="button" class="commenticon" data-observations="{{$client->pivot->observations}}"

                            data-state=" {{ $client->pivot->state }} "
                            data-name="{{$client->name}}" data-client="{{$client->id}}" data-departure="{{$departure->id}}" data-toggle="modal" data-target="#commentaryRooming">
                                <i class="fas fa-comment indexicon {{ $client->pivot->observations != null ? '' : 'passportgrey' }}"></i>
                           </button>







                            <button style="float:right;" type="button" class="commenticon"

                            data-state=" {{ $client->pivot->state }} "
                             data-name="{{$client->name}}"
                             data-client="{{$client->id}}"
                             data-departure="{{$departure->id}}" data-toggle="modal" data-target="#reinsertClientDeparture"> <i class="fas fa-undo-alt indexicon"></i>
                            </button>

                            {{ date('d-m-Y', strtotime($client->pivot->updated_at)) }}


                    </div>
                </li>
            </ul>
        </div>

        @endif

        @endforeach
        @if( $departure->clients()->wherePivot('state',5)->count() == 0)
        <p>{{__('There are no passengers on the waiting list')}}</p>
        @endif

    </div>

</div>
</div>

