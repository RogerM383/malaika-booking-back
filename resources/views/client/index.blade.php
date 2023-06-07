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

<div class="alert alert-warning" >
<div class="container-fluid" style="max-width:75%">
    {{ session()->get('warning') }}
    <button type="button" class="close" data-dismiss="alert">×</button>
</div>
</div>

@endif


<!-- <script>
  function myFunction() {


    var filter = document.getElementById("filter").value;
    var param = document.getElementById("param").value;

    filter = "filter=" + filter + "&" + "param=" + param;

    var url = '{{ route("client.index", ":filter") }}';

    url = url.replace(':filter', filter);

    window.location.href = url;

  }
</script> -->

@extends('modal.deleteClient')

<form action="{{ action('ClientController@index') }}" method="GET" autocomplete="off">


  <div class="container-fluid" style="max-width:75%">

    <div class="row">
      <div class="col-sm-5">
        <div class="form-check-inline">

          <div class="form-inline">

            <p style="margin-right:0.5em">{{__('Category')}}:</p>

            <select class="form-control" id="type" name="type">
              <option value="" disabled selected>Selecciona una categoria</option>
              <option value="MALAIKA">{{__('MALAIKA')}}</option>
              <option value="MNAC">{{__('MNAC')}}</option>
              <option value="ARQUEONET">{{__('ARQUEONET')}}</option>
              <option value="OTROS">{{__('OTROS')}}</option>


            </select>
          </div>


          <!--                        
          <option value="{{ route('client.index') }}">{{__('TODOS')}}</option>
              <option value="{{ route('client.index',['type' => 'MALAIKA']) }}">{{__('MALAIKA')}}</option>
              <option value="{{ route('client.index', ['type' => 'MNAC' ]) }}">{{__('MNAC')}}</option>
              <option value="{{ route('client.index', ['type' => 'ARQUEONET' ]) }}">{{__('ARQUEONET')}}</option>
              <option value="{{ route('client.index', ['type' => 'OTROS']) }}">{{__('OTROS')}}</option> -->



        </div>
      </div>
      <div class="col-sm-2">
        <select required class="custom-select" id="param" name="param">

          <!--  {{ app('request')->input('param') }}
            TODO que coja el valor antiguo si no ha habido resultados
            -->
          <option value="default">{{__('General Search')}}</option>
          <option value="name">{{__('Name')}}</option>
          <option value="surname">{{__('Surname')}}</option>
          <option value="phone">{{__('Phone')}}</option>
          <option value="email">{{__('Email')}}</option>
          <option value="dni">{{__('DNI')}}</option>
          <option value="passport">{{__('Passport')}}</option>
        </select>

      </div>

      <div class="col-sm-4">
        <input type="text" id="filter" name="filter" class="form-control iblock" />
      </div>

      <div class="col-sm-1">
        <button type="submit" id="button" class="btn btn-info fright">{{__('Find')}}</button>
        <!-- <a onClick="myFunction()" id="myBtn" class="btn btn-info fright">{{__('Find')}}</a> -->
      </div>
    </div>
</form>


<div class="row">
  <div class="col-sm-5 align-middle" style="margin-top:2em;">
    @if(request()->get('type') )
    <h4>{{request()->get('type') }}</h4>
    @endif
  </div>

</div>


</div>




<div class="container-fluid" style="max-width: 75%;">
  <table class="table" style="margin-top:30px;">
    <thead>
      <tr>
        <th>{{__('Surname')}}</th>
        <th>{{__('Name')}}</th>
        <th>{{__('Phone')}}</th>
        <th>{{__('Email')}}</th>
        <th>{{__('DNI')}}</th>
        <th>{{__('Actions')}}</th>
        <th></th>
        <th></th>
        <th></th>

      </tr>
    </thead>
    <tbody>


      @forelse($clients as $client)
      <tr>

        <td>{{$client->surname}}</td>
        <td>{{$client->name}}</td>
        <td>{{$client->phone}}</td>
        <td>{{$client->email}}</td>
        <td>{{$client->dni}}</td>

        <td> <a href="{{route('client.edit', $client)}}" class="btn btn-info " style="border: none; background: none;"><i class="fas fa-pencil-alt fa-lg indexicon"></i></a> </td>
        <td> <a href="{{route('client.show', $client)}}" class="btn btn-info" style="border: none; background: none;"><i class="fas fa-eye fa-lg indexicon"></i></a> </td>



        <td style="text-align: center; vertical-align : middle;">
         <a href="{{route('departuresClient', $client)}}" class="btn btn-info" style="border: none; background: none;">
       
        <i class="fas fa-plane fa-lg {{ $client->departures->isNotEmpty() ? 'indexicon' : 'greyicon'  }}"></i></a> 
        </td>


        @if($client->passport->number_passport)
        <td> <a href="{{route('passport.create',['client'=> $client]) }}" class="btn btn-info" style="border: none; background: none;">
            @if (Carbon\Carbon::parse($client->passport['exp'])->diffInDays(\Carbon\Carbon::now(),false) >= 0)
            <i class="fas fa-passport fa-lg dangericon"></i></a> </td>
        @elseif(Carbon\Carbon::parse($client->passport['exp'])->diffInDays(\Carbon\Carbon::now(),false) < 0 && Carbon\Carbon::parse($client->passport['exp'])->diffInDays(\Carbon\Carbon::now(),false) > -180)
          <i class="fas fa-passport fa-lg warningicon"></i></a> </td>
          @else
          <i class="fas fa-passport fa-lg indexicon"></i></a> </td>
          @endif
          @else
          <td> <a href="{{route('passport.create',['client'=> $client]) }}" class="btn btn-info" style="border: none; background: none;">

              <i class="fas fa-passport fa-lg greyicon "></i></a> </td>
          @endif
         



          <td style="text-align: center; vertical-align : middle;">


            <div class="input-group">
              <span class="input-group-addon">
                <button data-toggle="modal" data-target="#deleteClient" data-whatever="{{$client->id}}" data-nombre="{{$client->name}}" data-surname="{{$client->surname}}" style="padding: 0; border: none; background: none; " class="btn btn-info " data-hasdeparture="{{ $client->departures()->first() }}"><i class="far fa-trash-alt fa-lg" style="color:red;"></i></button>
              </span>
            </div>
          </td>
      </tr>


      @empty
      <td>{{__('No clients found')}}</td>
      @endForelse
    </tbody>
  </table>

  <div class="row justify-content-center align-items-center">



    {{ $clients->appends(request()->query())->links() }}




  </div>



</div>

</div>





@endsection