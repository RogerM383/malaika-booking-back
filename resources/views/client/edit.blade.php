@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">

		<form action="{{ route('client.update',[ 'id' => $client->id ,'redirection' => 'edit','departure' => $departure]) }}" method="POST"  autocomplete="off">
							@method('PATCH')
							@csrf


							@if($redirection)
							<input name="redirection" type="hidden" value="{{ $redirection }}">
							@endif
           <div style="margin-bottom:10px;">

			   <a id="backbutton" href=""><i class="fas fa-arrow-left fa-lg"></i>  Tornar</a>
		   </div>

		    <div class="card">
                <div class="card-header">{{__('Edit Client')}}</div>
					<div class="card-body">
					
				
									<div class="form-group">
										<label for="name" class="cols-sm-2 control-label" >{{__('Name')}} </label>
										<div class="cols-sm-10">
											<div class="input-group">
												<span class="input-group-addon"><i class="fa fa-user fa" aria-hidden="true"></i></span>
												<input type="text" class="form-control" name="name" id="name"  value="{{$client->name}}" required maxlength="190" />
											</div>
										</div>
									</div>

									<div class="form-group">
										<label for="username" class="cols-sm-2 control-label">{{__('Surname')}}</label>
										<div class="cols-sm-10">
											<div class="input-group">
												<span class="input-group-addon"><i class="fa fa-users fa" aria-hidden="true"></i></span>
												<input type="text" maxlength="190"class="form-control" name="surname" id="surname"  value="{{$client->surname}}"/>
											</div>
										</div>
									</div>

									<div class="form-group">
										<label for="phone" class="cols-sm-2 control-label">{{__('Phone')}}</label>
										<div class="cols-sm-10">
											<div class="input-group">
												<span class="input-group-addon"><i class="fas fa-phone"></i></span>
												<input type="text" maxlength="190" class="form-control" name="phone" id="phone"  value="{{$client->phone}}"/>
											</div>
										</div>
									</div>

									<div class="form-group">
										<label for="email" class="cols-sm-2 control-label">{{__('Email')}}</label>
										<div class="cols-sm-10">
											<div class="input-group">
												<span class="input-group-addon"><i class="fas fa-envelope"></i></span>
												<input type="text" maxlength="190" class="form-control" name="email" id="email"  value="{{$client->email}}"/>
											</div>
										</div>
									</div>


									<div class="form-group">
										<label for="email" class="cols-sm-2 control-label">{{__('DNI')}}</label>
										<div class="cols-sm-10">
											<div class="input-group">
												<span class="input-group-addon"><i class="fas fa-id-card"></i></span>
												<input type="text" maxlength="190" class="form-control" name="dni" id="dni"  value="{{$client->dni}}"/>
											</div>
										</div>
									</div>

									<div class="form-group">
										<label for="email" class="cols-sm-2 control-label">{{__('Dni expiration')}}</label>
										<div class="cols-sm-10">
											<div class="input-group">
												<span class="input-group-addon"><i class="fas fa-id-card"></i></span>
												<input type="text" maxlength="190" class="form-control" name="dni_expiration" id="dni_expiration"  value="{{$client->dni_expiration}}"/>
											</div>
										</div>
									</div>

									<div class="form-group">
										<label for="email" class="cols-sm-2 control-label">{{__('Place of birth')}}</label>
										<div class="cols-sm-10">
											<div class="input-group">
												<span class="input-group-addon"><i class="fas fa-globe-europe"></i></span>
												<input type="text" maxlength="190" class="form-control" name="place_birth" id="place_birth"  value="{{$client->place_birth}}"/>
											</div>
										</div>
									</div>


									<div class="form-group">
										<label for="email" class="cols-sm-2 control-label">{{__('Address')}}</label>
										<div class="cols-sm-10">
											<div class="input-group">
												<span class="input-group-addon"><i class="fas fa-home"></i></span>
												<input type="text" maxlength="190" class="form-control" name="address" id="address"  value="{{$client->address}}"/>
											</div>
										</div>
									</div>


							</div>

							
			
				
			<div class="card">
				<div class="card-header">{{__('Passport')}}</div>
					<div class="card-body">


						<div class="form-group">
							<label for="phone" class="cols-sm-2 control-label">{{__('Passport number')}}</label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fas fa-passport"></i></span>
									<input type="text" maxlength="190" class="form-control" name="number_passport" id="number_passport" value="{{  isset($client->passport->number_passport)   ? $client->passport->number_passport : '' }}"/>
								</div>
							</div>
						</div>																								


						<div class="form-group">
							<label for="email" class="cols-sm-2 control-label">{{__('ISSUE')}}</label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fas fa-calendar-alt"></i></span>
									<input type="text" maxlength="190" class="form-control datepicker" name="issue" id="issue"   value="{{ isset($client->passport->issue)   ? $client->passport->issue : '' }}"/>
								</div>
							</div>
						</div>


						<div class="form-group">
							<label for="password" class="cols-sm-2 control-label">{{__('Exp')}}</label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fas fa-calendar-alt"></i></span>
									<input type="text" maxlength="190" class="form-control datepicker" name="exp" id="exp"   value="{{ isset($client->passport->exp)   ? $client->passport->exp : '' }}"/>
								</div>
							</div>
						</div>

						<div class="form-group">
							<label for="confirm" class="cols-sm-2 control-label">{{__('Nationality')}}</label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fas fa-globe-americas"></i></span>
									<input type="text" maxlength="190" class="form-control" name="nac" id="nac"   value="{{ isset($client->passport->nac)   ? $client->passport->nac : '' }}"/>
								</div>
							</div>
						</div>

						<div class="form-group">
							<label for="confirm" class="cols-sm-2 control-label">{{__('Birthdate')}}</label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fas fa-calendar-alt"></i></span>
									<input type="text" maxlength="190" class="form-control datepicker" name="birth" id="birth"   value="{{ isset($client->passport->birth)   ? $client->passport->birth : '' }}"/>
								</div>
							</div>
						</div>

					</div>

			</div>



			<div class="card">
				<div class="card-header">{{__('Traveler')}}</div>
					<div class="card-body">

			

								<div class="form-group">
									<label for="confirm" class="cols-sm-2 control-label">{{__('Seat')}}</label>
									<div class="cols-sm-10">
										<div class="input-group">
											<span class="input-group-addon"><i class="fas fa-chair"></i></span>
											<input type="text" maxlength="190" class="form-control" name="seat" id="seat"   value="{{ isset($client->traveler->seat)   ? $client->traveler->seat : '' }}"/>
										</div>
									</div>
								</div>


								<div class="form-group">
									<label for="Seat" class="cols-sm-2 control-label">{{__('Language')}}</label>
										<div class="cols-sm-10">
										<div class="input-group">
									<span class="input-group-addon"><i class="fas fa-flag"></i></span>
									<input type="text" maxlength="190" class="form-control" name="lang" id="lang"  value="{{ isset($client->traveler->lang)   ? $client->traveler->lang : '' }}"/>
										</div>
									</div>
									</div>



								<div class="form-group">
									<label for="confirm" class="cols-sm-2 control-label">{{__('Observations')}}</label>
									<div class="cols-sm-10">
										<div class="input-group">
											<span class="input-group-addon"><i class="fas fa-eye"></i></span>
											<input type="text"  class="form-control" name="observations" id="observations" value="{{ isset($client->traveler->observations)   ? $client->traveler->observations : '' }}"/>
										</div>
									</div>
								</div>



								<div class="form-group">
									<label for="password" class="cols-sm-2 control-label">{{__('Intolerances')}}</label>
									<div class="cols-sm-10">
										<div class="input-group">
											<span class="input-group-addon"><i class="fas fa-exclamation-circle"></i></span>
											<input type="text" maxlength="190" class="form-control" name="intolerances" id="intolerances"  value="{{ isset($client->traveler->intolerances)   ? $client->traveler->intolerances : '' }}"/>
										</div>
									</div>
								</div>

								<div class="form-group">
									<label for="confirm" class="cols-sm-2 control-label">{{__('Room Type')}}</label>
									<div class="cols-sm-10">
										<div class="input-group">
											<span class="input-group-addon"><i class="fas fa-door-closed"></i></span>
											<input type="text"  maxlength="190" class="form-control" name="type_room" id="type_room"  value="{{ isset($client->traveler->type_room)   ? $client->traveler->type_room : '' }}"/>
										</div>
									</div>
								</div>

										<div class="form-group">
									<label for="confirm" class="cols-sm-2 control-label">{{__('Frequent Flyer')}}</label>
									<div class="cols-sm-10">
										<div class="input-group">
											<span class="input-group-addon"><i class="fas fa-plane"></i></span>
											<input type="text" maxlength="190" class="form-control" name="frequency_fly" id="frequency_fly"   value="{{ isset($client->traveler->frequency_fly)   ? $client->traveler->frequency_fly : '' }}"/>
										</div>
									</div>
								</div>

								

								<!-- <div class="form-group">
									<label for="confirm" class="cols-sm-2 control-label">{{__('Client Type')}}</label>
									<div class="cols-sm-10">
										<div class="input-group">
											<span class="input-group-addon"><i class="fas fa-id-badge"></i></span>
											<input type="text" maxlength="190"  class="form-control" name="client_type" id="client_type"   value="{{ isset($client->traveler->client_type)   ? $client->traveler->client_type : '' }}"/>
										</div>
									</div>
								</div> -->

							

								<div class="form-group">
									<label for="confirm" class="cols-sm-2 control-label">{{__('Client Type')}}</label>
									<div class="cols-sm-10">
										<div class="input-group">
											<span class="input-group-addon"><i class="fas fa-id-badge"></i></span>
											<select  name="client_type" id="client_type" class="form-control" >
												@if($client->traveler->client_type == 'MALAIKA')
												<option value="MALAIKA" selected>MALAIKA</option>
												<option value="MNAC" >MNAC</option> 
												<option value="ARQUEONET" >ARQUEONET</option> 
												<option value="ALTRES" >ALTRES</option> 

												@elseif($client->traveler->client_type == 'MNAC')
												<option value="MNAC" selected>MNAC</option> 
												<option value="MALAIKA" >MALAIKA</option>
												<option value="ARQUEONET" >ARQUEONET</option> 
												<option value="ALTRES" >ALTRES</option> 

												@elseif($client->traveler->client_type == 'ARQUEONET')
												<option value="ARQUEONET" selected>ARQUEONET</option> 
												<option value="MALAIKA" >MALAIKA</option>
												<option value="MNAC" >MNAC</option> 
												<option value="ALTRES" >ALTRES</option> 


												@elseif($client->traveler->client_type == 'ALTRES')
												<option value="ALTRES" selected>ALTRES</option> 
												<option value="MALAIKA" >MALAIKA</option>
												<option value="MNAC" >MNAC</option> 
												<option value="ARQUEONET" >ARQUEONET</option> 
												@else
												<option value="ALTRES" selected>ALTRES</option> 
												<option value="MALAIKA" >MALAIKA</option>
												<option value="MNAC" >MNAC</option> 
												<option value="ARQUEONET" >ARQUEONET</option> 

												@endif
											</select>
										
										</div>
									</div>
								</div> 

								
       


						
								<div class="form-group">
									<label for="confirm" class="cols-sm-2 control-label">{{__('Member Number')}}</label>
									<div class="cols-sm-10">
										<div class="input-group">
											<span class="input-group-addon"><i class="fas fa-credit-card"></i></span>
											<input type="text" maxlength="190" class="form-control" name="member_number" id="member_number"  value="{{ isset($client->traveler->member_number)   ? $client->traveler->member_number : '' }}"/>
									</div>
								</div>
								</div>



							




								<div class="form-group">
									<label for="confirm" class="cols-sm-2 control-label">{{__('Notes')}}</label>
									<div class="cols-sm-10">
										<div class="input-group">
											<span class="input-group-addon"><i class="fas fa-sticky-note"></i></span>
											<input type="text"  class="form-control" name="notes" id="notes"  value="{{ isset($client->traveler->notes)   ? $client->traveler->notes : '' }}"/>
										</div>
									</div>
								</div>

						
					</div>
			</div>


	</div>

							<div class="form-group ">
								<button type="submit" id="button" class="btn btn-primary btn-lg btn-block login-button">{{__('Register')}}</button>
							</div>
               
            
        </form>

			
             	
            </div>
        </div>
    </div>
</div>
</div>
@endsection

