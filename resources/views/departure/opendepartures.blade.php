@extends('layouts.app')

@section('content')

@if(session()->has('message'))

<div class="alert alert-success">
  <div class="container-fluid" style="max-width:75%">
    {{ session()->get('message') }}
  </div>
</div>

@endif


@extends('modal.openDeparturesCommentary')
@extends('modal.openDeparturesDelete')


<div class="container-fluid" style="max-width:75%">
  <div class="row">

    <div class="col-sm-2">
      <span>
        <h3>{{__('Open Departures')}}</h3>
      </span>
    </div>

  </div>
</div>


<div class="container-fluid" style="max-width: 75%;">
  <table class="table" style="margin-top:30px;">
    <thead>
      <tr>
        <th>{{__('Trip')}}</th>
        <th>{{__('Start')}}</th>
        <th>{{__('Final')}}</th>

        <th>{{__('Pax')}}</th>


        <th>{{__('Rooms')}}</th>
        <th>{{__('Expedient')}}</th>
        <!-- <th  >{{__('Actions')}}</th> -->
        <th style="text-align:center;">{{__('Edit')}}</th>
        <th style="text-align:center;">{{__('Commentary')}}</th>
        <th style="text-align:center;">{{__('Rooming')}}</th>
        <th style="text-align:center;">{{__('Lock')}}</th>
        <!-- <th  >{{__('Delete')}}</th> -->

      </tr>
    </thead>
    <tbody>


      @forelse($departures as $departure)
      <tr>
        <td>{{$departure->trip->title ?? ""}}</td>
        <td style="white-space: nowrap;">{{ date('d-m-Y', strtotime($departure->start)) }}</td>
        <td style="white-space: nowrap;">{{ date('d-m-Y', strtotime($departure->final)) }}</td>

        <td style="white-space: nowrap;">{{ $departure->pax_available - $departure->clients()->where('state','<',4)->count()}} / {{$departure->pax_available}}</td>

        <td> {{ $departure->clients()->distinct('number_room')->count('number_room') }}</td>


        <td>{{$departure->expedient}}</td>



        <td style="text-align: center; vertical-align : middle;"> <a href="{{route('departure.edit', $departure->id)}}" class="btn btn-info " style="border: none; background: none;"><i class="fas fa-pencil-alt fa-lg indexicon"></i></a> </td>

        <td style="text-align: center; vertical-align : middle;">
          <button type="button" class="commenticon" data-target="#openDeparturesCommentary" data-toggle="modal" data-departure="{{$departure->id }}" data-title="{{$departure->trip->title ?? 'hola'}}" data-commentary="{{ $departure->commentary}}"> <i class="fas fa-comment indexicon "></i></button>
        </td>

        <td style="text-align: center; vertical-align : middle;"> <a href="{{route('departure.clients',['departure' => $departure->id])}}" class="btn btn-info" style="border: none; background: none;"><i class="fas fa-users indexicon"></i></a> </td>

        @if($departure->state == 1)
        <td style="text-align: center; vertical-align : middle;"> <a href="{{route('lockdeparture',['departure' => $departure->id])}}" class="btn btn-info" style="border: none; background: none;"><i class="fas fa-lock-open indexicon"></i></a></td>

        @else
        <td style="text-align: center; vertical-align : middle; "> <a href="{{route('lockdeparture',['departure' => $departure->id])}}" class="btn btn-info" style="border: none; background: none;"><i style="color:red;" class="fas fa-lock indexicon"></i></a></td>

        @endif


        <!-- <td>         <button data-toggle="modal" data-target="#openDeparturesDelete" data-departure="{{$departure->id}}" data-name="{{$departure->start}}"
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
