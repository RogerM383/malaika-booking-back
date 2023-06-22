@extends('layouts.app')

@section('content')

@if(session()->has('message'))

  <div class="alert alert-success">
    <div class="container-fluid" style="max-width:75%">
        {{ session()->get('message') }}
    </div>
  </div>

@endif


@extends('modal.deleteDeparture')
@extends('modal.commentaryDeparture')

<div class="container-fluid" style="max-width:75%">
  <div class="row">

  <div class="col-sm-3">
  <span><a  href="{{route('trip.index', ['trip' => $trip])}}"style="margin-right:11px;"><i class="fas fa-arrow-left fa-lg"></i></a><h3 style="display:inline">{{$trip->title}}</h3></span>

  </div>

    <div class="col-sm-9">
     <a href="{{route('departure.create', ['trip' => $trip])}}"  id="myBtn" class="btn btn-info fright">{{__('Add new Departure')}}</a>
    </div>
  </div>
</div>

<div class="container-fluid" style="max-width: 75%;" >
<table class="table" style="margin-top:30px;">
  <thead>
    <tr>
      <th >{{__('Start')}}</th>
      <th  >{{__('Final')}}</th>
      <th>{{__('Pax')}}</th>
      <th>{{__('Rooms')}}</th>
      <th>{{__('Expedient')}}</th>
      <!-- <th  >{{__('Actions')}}</th> -->
      <th  tyle="text-align:center;" >{{__('Edit')}}</th>
      <th  tyle="text-align:center;" >{{__('Rooming')}}</th>
      <th tyle="text-align:center;"  >{{__('Lock')}}</th>
      <th style="text-align:center;"  >{{__('Commentary')}}</th>
      <!-- <th  >{{__('Delete')}}</th> -->

    </tr>
  </thead>
  <tbody>


    @forelse($departures as $departure)
        <tr>

          <td>{{ date('d-m-Y', strtotime($departure->start)) }}</td>

            <td>{{ date('d-m-Y', strtotime($departure->final)) }}</td>

            <td>{{ $departure->pax_capacity - $departure->clients()->where('state','<',4)->count()}} / {{$departure->pax_capacity}}</td>

       <td> {{ $departure->clients()->distinct('number_room')->count('number_room') }}</td>


             <!-- <td> {{ $departure->clients->count()  }} / {{$departure->pax_capacity}}</td>      -->

             <td>{{$departure->expedient}}</td>




            <td> <a href="{{route('departure.edit', $departure->id)}}" class="btn btn-info " style="border: none; background: none;"><i class="fas fa-pencil-alt fa-lg indexicon" ></i></a> </td>
            <td> <a href="{{route('departure.clients',['departure' => $departure->id])}}" class="btn btn-info" style="border: none; background: none;"><i class="fas fa-users indexicon"></i></a> </td>

          @if($departure->state == 1)
          <td> <a href="{{route('lockdeparture',['departure' => $departure->id])}}" class="btn btn-info" style="border: none; background: none;"><i  class="fas fa-lock-open indexicon"></i></a></td>

          @else
          <td> <a href="{{route('lockdeparture',['departure' => $departure->id])}}" class="btn btn-info" style="border: none; background: none;"><i style="color:red;" class="fas fa-lock indexicon"></i></a></td>

          @endif




          <td style="text-align: center; vertical-align : middle;">
          @if($departure->commentary)
               <button  type="button" class="commenticon" data-target="#commentaryDeparture" data-toggle="modal"
                data-departure="{{$departure->id}}" data-title="{{$trip->title}}" data-commentary="{{ $departure->commentary }}" >   <i class="fas fa-comment indexicon"></i> </button>
          @else
          <button  type="button" class="commenticon" data-target="#commentaryDeparture" data-toggle="modal"
                data-departure="{{$departure->id}}" data-title="{{$trip->title}}" data-commentary="{{ $departure->commentary }}" >   <i class="fas fa-comment indexicon passportgrey"></i> </button>


          @endif
           </td>



            <!-- <td>         <button data-toggle="modal" data-target="#deleteDeparture" data-departure="{{$departure->id}}" data-name="{{$departure->start}}"
                 style="padding: 0; border: none; background: none; " class="btn btn-info "><i class="far fa-trash-alt fa-lg" style="color:red;"></i></button>
            </td> -->



        </tr>


    @empty
    <td>{{__('No departures found')}}</td>
    @endForelse
        </tbody>
    </table>

    <div class="row justify-content-center align-items-center">
      {{ $departures->appends(request()->query())->links() }}

    </div>


</div>







@endsection
