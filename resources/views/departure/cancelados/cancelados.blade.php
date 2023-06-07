

<!-- COLUMNA CANCELADOS   salidas  -->

<div class="container-fluid" style="max-width:75%; margin-bottom:50px; margin-top:100px;">

<div class="row" id="second">
    <div class="col-12">
        <h3>{{__('Travelers Canceled')}}</h3>
    </div>


</div>

<div class="row">
    <div class="col-sm-12">


        @foreach($clients as $client)

        @if($client->pivot->state == 4)

        <div class="card" style="margin-bottom:20px;">

            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <div class="row">


                        <div class="col-4">
                        <a class="text-dark" href="{{route('departuresClient', $client)}}" >
                        {{$client->surname}}, {{$client->name}}</a>
                        </div>


                        <div class="col-4"> {{__('Phone')}}: {{$client->phone}}</div>


                        <div class="col-4">

                            <button style="float:right;" type="button" class="commenticon" data-observations="{{$client->pivot->observations}}" data-name="{{$client->name}}" data-client="{{$client->id}}" data-departure="{{$departure->id}}" data-toggle="modal" data-target="#deleteRoomModal"> <i style="color:red;" class="fas fa-trash-alt  indexicon"></i> </button>

                            <button style="float:right;" type="button" class="commenticon" data-observations="{{$client->pivot->observations}}" data-name="{{$client->name}}" data-client="{{$client->id}}" data-departure="{{$departure->id}}" data-toggle="modal" data-target="#commentaryRooming"> <i class="fas fa-comment indexicon"></i> </button>

                            <button style="float:right;" type="button" class="commenticon" data-name="{{$client->name}}" data-client="{{$client->id}}" data-departure="{{$departure->id}}" data-toggle="modal" data-target="#reinsertClientDeparture"> <i class="fas fa-trash-restore indexicon"></i> </button>



                            {{__('Cancel')}}:  {{ date('d-m-Y', strtotime($client->pivot->updated_at)) }}
                        </div>
                    </div>
                </li>
            </ul>
        </div>

        @endif


        @endforeach


        @if( $departure->clients()->wherePivot('state',4)->count() == 0)
         <p>{{__('There are no canceled passengers')}}</p>
        @endif
    </div>

</div>
</div>

<!-- acaba segunda columna salidas  -->
