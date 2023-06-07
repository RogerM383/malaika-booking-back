@extends('layouts.app')

@section('content')

<div class="container">
	<div class="row justify-content-center">
		<div class="col-md-8">

			@if (session('status'))
			<div class="alert alert-success" role="alert">
				{{ session('status') }}
			</div>
			@endif

			<form action="{{ action('\App\Http\Controllers\ClientController@store') }}" method="POST" autocomplete="off">
				@csrf
				<div style="margin-bottom:10px;">
					<a id="backbutton" href=""><i class="fas fa-arrow-left fa-lg"></i> Tornar</a>
				</div>

				<div class="card">
					<div class="card-header">{{__('New Client')}} </div>
					<div class="card-body">

						<div class="form-group">
							<label for="name" class="cols-sm-2 control-label">{{__('Name')}} </label>
							<div class="cols-sm-10">
								<div class="input-group">
                                    <span style="float:left" class="input-group-addon w-100">

                                        <i class="fa fa-user fa" aria-hidden="true" style="margin-right: 20px"></i>
                                        <select id="test" name="name" class="select2 form-control"  required>
                                            @foreach($clients as $client)
                                                <option disabled value="">{{$client->name}} - {{$client->surname}} - {{$client->dni}}</option>
                                            @endforeach
                                        </select>
                                    </span>
								</div>
							</div>
						</div>

						<div class="form-group">
							<label for="username" class="cols-sm-2 control-label">{{__('Surname')}}</label>
							<div class="cols-sm-10">
								<div class="input-group">

									<span class="input-group-addon">
										<i class="fa fa-users fa" aria-hidden="true"></i>
									</span>

									<input type="text" maxlength="190" class="form-control" name="surname" id="surname"
										placeholder="{{__('Enter Surname')}}" />
								</div>
							</div>
						</div>

						<div class="form-group">
							<label for="phone" class="cols-sm-2 control-label">{{__('Phone')}}</label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fas fa-phone"></i></span>
									<input type="text" maxlength="190" class="form-control" name="phone" id="phone"
										placeholder="{{__('Enter Phone')}}" />
								</div>
							</div>
						</div>

						<div class="form-group">
							<label for="email" class="cols-sm-2 control-label">{{__('Email')}}</label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fas fa-envelope"></i></span>
									<input type="text" maxlength="190" class="form-control" name="email" id="email"
										placeholder="{{__('Enter Email')}}" />
								</div>
							</div>
						</div>


						<div class="form-group">
							<label for="email" class="cols-sm-2 control-label">{{__('DNI')}}</label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fas fa-id-card"></i></span>
									<input type="text" maxlength="190" class="form-control" name="dni" id="dni"
										placeholder="{{__('Enter DNI')}}" />
								</div>
							</div>
						</div>



						<div class="form-group">
							<label for="email" class="cols-sm-2 control-label">{{__('Dni expiration')}}</label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fas fa-calendar-alt"></i></span>
									<input type="text" maxlength="190" class="form-control datepicker"
										name="dni_expiration" id="dni_expiration"
										placeholder="{{__('Enter DNI expiration')}}" />
								</div>
							</div>
						</div>

						<div class="form-group">
							<label for="place_birth" class="cols-sm-2 control-label">{{__('Place of birth')}}</label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fas fa-globe-europe"></i></span>
									<input type="text" maxlength="190" class="form-control" name="place_birth"
										id="place_birth" placeholder="{{__('Enter place of birth')}}" />
								</div>
							</div>
						</div>


						<div class="form-group">
							<label for="email" class="cols-sm-2 control-label">{{__('Address')}}</label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fas fa-home"></i></span>
									<input type="text" maxlength="190" class="form-control" name="address" id="address"
										placeholder="{{__('Enter address')}}" />
								</div>
							</div>

						</div>

					</div>



					<div class="card">
						<div class="card-header">{{__('Passport')}}</div>
						<div class="card-body">


							<div class="form-group">
								<label for="Passport" class="cols-sm-2 control-label">{{__('Passport number')}}</label>
								<div class="cols-sm-10">
									<div class="input-group">
										<span class="input-group-addon"><i class="fas fa-passport"></i></span>
										<input type="text" maxlength="190" class="form-control" name="number_passport"
											id="number_passport" placeholder="{{__('Enter Passport number')}}" />
									</div>
								</div>
							</div>


							<div class="form-group">
								<label for="ISSUE" class="cols-sm-2 control-label">{{__('ISSUE')}}</label>
								<div class="cols-sm-10">
									<div class="input-group">
										<span class="input-group-addon"><i class="fas fa-calendar-alt"></i></span>
										<input type="text" maxlength="190" class="form-control datepicker" name="issue"
											id="issue" placeholder="{{__('Enter ISSUE')}}" />
									</div>
								</div>
							</div>


							<div class="form-group">
								<label for="Exp" class="cols-sm-2 control-label">{{__('Exp')}}</label>
								<div class="cols-sm-10">
									<div class="input-group">
										<span class="input-group-addon"><i class="fas fa-calendar-alt"></i></span>
										<input type="text" maxlength="190" class="form-control datepicker" name="exp"
											id="exp" placeholder="{{__('Enter exp')}}" />
									</div>
								</div>
							</div>

							<div class="form-group">
								<label for="Nationality" class="cols-sm-2 control-label">{{__('Nationality')}}</label>
								<div class="cols-sm-10">
									<div class="input-group">
										<span class="input-group-addon"><i class="fas fa-globe-americas"></i></span>
										<input type="text" maxlength="190" class="form-control" name="nac" id="nac"
											placeholder="{{__('Enter nationality')}}" />
									</div>
								</div>
							</div>

							<div class="form-group">
								<label for="Birthdate" class="cols-sm-2 control-label">{{__('Birthdate')}}</label>
								<div class="cols-sm-10">
									<div class="input-group">
										<span class="input-group-addon"><i class="fas fa-calendar-alt"></i></span>
										<input type="text" maxlength="190" class="form-control datepicker" name="birth"
											id="birth" placeholder="{{__('Enter birthdate')}}" />
									</div>
								</div>
							</div>

						</div>

					</div>


					<div class="card">
						<div class="card-header">{{__('Traveler')}}</div>
						<div class="card-body">



							<div class="form-group">
								<label for="Seat" class="cols-sm-2 control-label">{{__('Seat')}}</label>
								<div class="cols-sm-10">
									<div class="input-group">
										<span class="input-group-addon"><i class="fas fa-chair"></i></span>
										<input type="text" maxlength="190" class="form-control" name="seat" id="seat"
											placeholder="{{__('Enter seat')}}" />
									</div>
								</div>
							</div>

							<div class="form-group">
								<label for="Seat" class="cols-sm-2 control-label">{{__('Language')}}</label>
								<div class="cols-sm-10">
									<div class="input-group">
										<span class="input-group-addon"><i class="fas fa-flag"></i></span>
										<input type="text" maxlength="190" class="form-control" name="lang" id="lang"
											placeholder="{{__('Enter language')}}" />
									</div>
								</div>
							</div>


							<div class="form-group">
								<label for="Observations" class="cols-sm-2 control-label">{{__('Observations')}}</label>
								<div class="cols-sm-10">
									<div class="input-group">
										<span class="input-group-addon"><i class="fas fa-eye"></i></span>
										<input type="text" class="form-control" name="observations" id="observations"
											placeholder="{{__('Enter Observations')}}" />
									</div>
								</div>
							</div>



							<div class="form-group">
								<label for="password" class="cols-sm-2 control-label">{{__('Intolerances')}}</label>
								<div class="cols-sm-10">
									<div class="input-group">
										<span class="input-group-addon"><i class="fas fa-exclamation-circle"></i></span>
										<input type="text" maxlength="190" class="form-control" name="intolerances"
											id="intolerances" placeholder="{{__('Enter Intolerances')}}" />
									</div>
								</div>
							</div>

							<div class="form-group">
								<label for="Room" class="cols-sm-2 control-label">{{__('Room Type')}}</label>
								<div class="cols-sm-10">
									<div class="input-group">
										<span class="input-group-addon"><i class="fas fa-door-closed"></i></span>
										<input type="text" maxlength="190" class="form-control" name="type_room"
											id="type_room" placeholder="{{__('Enter Room Type')}}" />
									</div>
								</div>
							</div>






							<div class="form-group">
								<label for="Frequency" class="cols-sm-2 control-label">{{__('Frequent Flyer')}}</label>
								<div class="cols-sm-10">
									<div class="input-group">
										<span class="input-group-addon"><i class="fas fa-plane"></i></span>
										<input type="text" maxlength="190" class="form-control" name="frequency_fly"
											id="frequency_fly" placeholder="{{__('Enter Frequent Flyer')}}" />
									</div>
								</div>
							</div>

							<!-- <div class="form-group">
							<label for="Client" class="cols-sm-2 control-label">{{__('Client Type')}}  </label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fas fa-id-badge"></i></span>
									<input type="text" maxlength="190" class="form-control" name="client_type" id="client_type"  placeholder="{{__('Enter Client Type')}}"/>
								</div>
							</div>
						</div> -->

							<div class="form-group">
								<label for="confirm" class="cols-sm-2 control-label">{{__('Client Type')}}</label>
								<div class="cols-sm-10">
									<div class="input-group">
										<span class="input-group-addon"><i class="fas fa-id-badge"></i></span>
										<select name="client_type" id="client_type" class="form-control"
											style="display:inline!important;">
											<option value="MALAIKA">MALAIKA</option>
											<option value="MNAC">MNAC</option>
											<option value="ARQUEONET">ARQUEONET</option>
											<option value="ALTRES">ALTRES</option>
										</select>

									</div>
								</div>
							</div>





							<div class="form-group">
								<label for="Member" class="cols-sm-2 control-label">{{__('Member Number')}}</label>
								<div class="cols-sm-10">
									<div class="input-group">
										<span class="input-group-addon"><i class="fas fa-credit-card"></i></span>
										<input type="text" maxlength="190" class="form-control" name="member_number"
											id="member_number" placeholder="{{__('Enter Member Number')}}" />
									</div>
								</div>
							</div>


							<div class="form-group">
								<label for="Notes" class="cols-sm-2 control-label">{{__('Notes')}}</label>
								<div class="cols-sm-10">
									<div class="input-group">
										<span class="input-group-addon"><i class="fas fa-sticky-note"></i></span>
										<input type="text" class="form-control" name="notes" id="notes"
											placeholder="{{__('Enter notes')}}" />
									</div>
								</div>
							</div>






						</div>
					</div>



				</div>


				<div class="form-group ">
					<button type="submit" id="button"
						class="btn btn-primary btn-lg btn-block login-button">{{__('Register')}}</button>


			</form>


		</div>
	</div>
</div>
</div>
</div>

<script>

    $(document).ready(function($) {
		$('#test').select2({
			tags: true,
			// theme: "bootstrap"
		});

	// 	$('#test').on('select2:select', function(e) {

	// 	console.log(e)
	// 	alert(e.params.data);;
	// });


	});
</script>


@endsection
