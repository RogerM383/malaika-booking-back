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


@extends('modal.commentaryTrip')
@extends('modal.deleteTrip')
@extends('modal.archiveTrip')
@extends('modal.unArchiveTrip')




<div class="container-fluid" style="max-width: 75%;">


  <div class="row">
    <!-- <span><h3>{{__('Travels')}}</h3></span> -->
    <span>
      <h3>{{ __('Archived Trips') }}</h3>
    </span>
  </div>
  <table class="table" style="margin-top:30px;">
    <thead>
      <tr>
        <th>{{__('Title')}}</th>
        <th>{{__('Description')}}</th>
        <th>{{__('Category')}}</th>

        <th>{{__('Actions')}}</th>
        <th></th>
        <th></th>
        <th></th>

      </tr>
    </thead>
    <tbody>


      @forelse($trips as $trip)
      <tr>

        <td>{{$trip->title}}</td>
        <td>{{$trip->description}}</td>
        <td>{{$trip->category}}</td>



        <!-- <td> <a href="{{route('trip.edit', $trip)}}" class="btn btn-info " style="border: none; background: none;"><i class="fas fa-pencil-alt fa-lg indexicon"></i></a> </td> -->
        <td> <a href="{{route('departure.index', ['trip' => $trip] )}}" class="btn btn-info" style="border: none; background: none;"><i class="fas fa-list fa-lg indexicon"></i></a> </td>

        <td style="text-align: center; vertical-align : middle;">
          @if($trip->commentary)
          <button type="button" class="commenticon" data-target="#commentaryTrip" data-toggle="modal" data-trip="{{$trip->id}}" data-title="{{$trip->title}}" data-commentary="{{$trip->commentary}}"> <i class="fas fa-comment indexicon"></i> </button>
          @else
          <button type="button" class="commenticon" data-target="#commentaryTrip" data-toggle="modal" data-trip="{{$trip->id}}" data-title="{{$trip->title}}" data-commentary="{{$trip->commentary}}"> <i class="fas fa-comment indexicon passportgrey"></i> </button>
          @endif

        </td>

         <td>

        <button type="button" class="commenticon" data-target="#unArchiveTrip" data-toggle="modal" data-trip="{{$trip->id}}" data-title="{{$trip->title}}" data-commentary="{{$trip->commentary}}"> 
        <i class="fas fa-undo indexicon"></i>
        
        </button>
 
        </td> 

        @if(Auth::user()->hasRole('admin'))
     
        <td style="text-align: center; vertical-align : middle;">

          <div class="input-group">
            <span class="input-group-addon">
              <button data-toggle="modal" data-target="#deleteTrip" data-whatever="{{$trip->id}}" data-title="{{$trip->title}}" data-description="{{$trip->description}}" style="padding: 0; border: none; background: none; " class="btn btn-info "><i class="far fa-trash-alt fa-lg" style="color:red;"></i></button>
            </span>
          </div>
        </td>
        @endif

      

      </tr>


      @empty
      <td>{{__('No trips found')}}</td>
      @endForelse
    </tbody>
  </table>

  <div class="row justify-content-center align-items-center">
    {{ $trips->links() }}
  </div>



</div>

</div>









@endsection