
@extends('layouts.app')

@section('content')

@extends('modal.deleteClient')


<div class="container">

    <div class="row justify-content-start">

    <div class="col-sm-4">
    <a id="backbutton" href=""><i class="fas fa-arrow-left fa-lg"></i>  Tornar</a>
    </div>

    <div class="col-sm-4">
    <h3 style="color:var(--blue); margin-bottom:25px; display:inline">{{__('Client card')}}</h3>
    </div>


     <div class="col-sm-4" style="text-align: right ;">

        <a href="{{route('client.edit', $client)}}"   style="border: none; background: none;"><label style=" cursor:pointer;" class="indexicon">{{__('Edit')}}</label></a>

        <span style="color:grey; margin: 0px 5px 0px 5px;"> | </span>
        <button data-hasdeparture="{{ $client->departures()->first() }}" data-toggle="modal"data-target="#deleteClient" data-whatever="{{$client->id}}" data-nombre="{{$client->name}}" data-surname="{{$client->surname}}"

        style="padding: 0; border: none; background: none; " class="indexicon deleteText"><span style=" cursor:pointer;" >{{__('Delete')}}</span></button>
     </div>








</div>
<br>

<div class="container border">

  <div class="row justify-content-start showrow ">
    <div class="col-4">
    <label class="showlabel" class="cols-sm-2 control-label" >{{__('Name')}}:</label>
    <span> {{$client->name}}</span>
    </div>

    <div class="col-4">
    <label class="showlabel" class="cols-sm-2 control-label" >{{__('Surname')}}:</label>
    <span> {{$client->surname}}</span>
    </div>

    <div class="col-4">
    <label class="showlabel" class="cols-sm-2 control-label" >{{__('Phone')}}:</label>
    <span> {{$client->phone}}</span>
    </div>
  </div>

  <div class="row justify-content-start showrow ">



    <div class="col-4">
    <label class="showlabel" class="cols-sm-2 control-label" >{{__('Email')}}:</label>
    <span> {{$client->email}}</span>
    </div>

    <div class="col-4">
    <label class="showlabel" class="cols-sm-2 control-label" >{{__('Address')}}:</label>
    <span> {{$client->address}}</span>
    </div>

    <div class="col-4">
    <label class="showlabel" class="cols-sm-2 control-label" >{{__('Place of birth')}}:</label>
    <span> {{$client->place_birth}}</span>
    </div>

</div>

<div class="row justify-content-start showrow ">
    <div class="col-4">
    <label class="showlabel" class="cols-sm-2 control-label" >{{__('DNI')}}:</label>
    <span> {{$client->dni}}</span>
    </div>

    <div class="col-4">
    <label class="showlabel" class="cols-sm-2 control-label" >{{__('Dni expiration')}}:</label>
    <span> {{$client->dni_expiration}}</span>
    </div>

    <div class="col-4">
    <label class="showlabel" class="cols-sm-2 control-label" >{{__('Birthdate')}}:</label>
    <span>{{$client->passport->birth}}</span>
    </div>
</div>

<div class="row justify-content-start showrow ">
    <div class="col-4">
    <label class="showlabel" class="cols-sm-2 control-label" >{{__('Passport')}}:</label>
    <span>{{$client->passport->number_passport}}</span>
    </div>

    <div class="col-4">
    <label class="showlabel" class="cols-sm-2 control-label" >{{__('Issue')}}:</label>
    <span>{{$client->passport->issue}}</span>
    </div>

    <div class="col-4">
    <label class="showlabel" class="cols-sm-2 control-label" >{{__('Exp')}}:</label>
    <span>{{$client->passport->exp}}</span>
    </div>


</div>

<div class="row justify-content-start showrow ">
    <div class="col-4">
    <label class="showlabel" class="cols-sm-2 control-label" >{{__('Client Type')}}:</label>
    <span>{{$client->traveler->client_type}}</span>
    </div>

    <div class="col-4">
    <label class="showlabel" class="cols-sm-2 control-label" >{{__('Member Number')}}:</label>
   <span>{{$client->traveler->member_number}}</span>
    </div>

    <div class="col-4">
    <label class="showlabel" class="cols-sm-2 control-label" >{{__('Frequent Flyer')}}:</label>
    <span>{{$client->traveler->frequency_fly}}</span>
    </div>
</div>


<div class="row justify-content-start showrow ">
    <div class="col-4">
    <label class="showlabel" class="cols-sm-2 control-label" >{{__('Seat')}}:</label>
    <span>{{$client->traveler->seat}}</span>
    </div>

    <div class="col-4">
    <label class="showlabel" class="cols-sm-2 control-label" >{{__('Room Type')}}:</label>
   <span>{{$client->traveler->type_room}}</span>
    </div>

    <div class="col-4">
    <label class="showlabel" class="cols-sm-2 control-label">{{__('Intolerances')}}:</label>
    <span>{{$client->traveler->intolerances}}</span>
    </div>




</div>

<div class="row justify-content-start showrow ">


    <div class="col-4">
    <label class="showlabel" class="cols-sm-2 control-label" >{{__('Language')}}:</label>
    <span>{{$client->traveler->lang}}</span>
    </div>

    <div class="col-4">
    <label class="showlabel" class="cols-sm-2 control-label" >{{__('Nationality')}}:</label>
   <span>{{$client->passport->nac}}</span>
    </div>
</div>

<div class="row justify-content-start showrow showrowbottom ">

    <div class="col-4">
    <label class="showlabel" class="cols-sm-2 control-label" >{{__('Observations')}}:</label>
   <span>{{$client->traveler->observations}}</span>
    </div>



    <div class="col-8">
    <label class="showlabel" class="cols-sm-2">{{__('Notes')}}:</label>
    <span>{{$client->traveler->notes}}</span>
    </div>
</div>












@endsection
