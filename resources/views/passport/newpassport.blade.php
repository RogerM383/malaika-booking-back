@extends('layouts.app')

@section('content')




<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">


		<div style="margin-bottom:10px;">

<a id="backbutton" href=""><i class="fas fa-arrow-left fa-lg"></i>  Tornar</a>
</div>
            <div class="card">
                <div class="card-header">{{__('Passport')}}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

               

                
                <form action="{{ action('PassportController@store',['client'=> $client]) }}" method="POST"  autocomplete="off">
				@csrf

				
				
				<input type="hidden" value="{{$rooming}}" name="rooming">
				<input type="hidden" value="{{$departure}}" name="departure">
		

                <div class="form-group">
							<label for="name" class="cols-sm-2 control-label">{{__('Name')}}</label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-user fa" aria-hidden="true"></i></span>
									<input type="text" class="form-control" name="name" id="name"  value="{{$client->name}}" readonly />
								</div>
							</div>
						</div>

						<div class="form-group">
							<label for="username" class="cols-sm-2 control-label">{{__('Surname')}}</label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-users fa" aria-hidden="true"></i></span>
									<input type="text" class="form-control" name="surname" id="surname"  value="{{$client->surname}}" readonly />
								</div>
							</div>
						</div>
						@if($client->passport)
					
							
					

                        <div class="form-group">
							<label for="phone" class="cols-sm-2 control-label">{{__('Passport number')}}</label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fas fa-passport"></i></span>
									<input type="text"  maxlength="190" class="form-control" name="number_passport" id="number_passport"  value="{{$client->passport->number_passport}}"/>
								</div>
							</div>
						</div>

               
						<div class="form-group">
							<label for="email" class="cols-sm-2 control-label">{{__('ISSUE')}}</label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fas fa-calendar-alt"></i></span>
									<input type="text"  maxlength="190"  maxlength="190" class="form-control datepicker" name="issue" id="issue"  value="{{$client->passport->issue}}"/>
								</div>
							</div>
						</div>


						<div class="form-group">
							<label for="password" class="cols-sm-2 control-label">{{__('Exp')}}</label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fas fa-calendar-alt"></i></span>
									<input type="text"  maxlength="190" class="form-control datepicker" name="exp" id="exp"  value="{{$client->passport->exp}}"/>
								</div>
							</div>
						</div>

						<div class="form-group">
							<label for="confirm" class="cols-sm-2 control-label">{{__('Nationality')}}</label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fas fa-globe-americas"></i></span>
									<input type="text"  maxlength="190" class="form-control" name="nac" id="nac"  value="{{$client->passport->nac}}"/>
								</div>
							</div>
						</div>

						<div class="form-group ">
							<button type="submit" id="button" class="btn btn-primary btn-lg btn-block login-button">{{__('Register Passport')}}</button>
						</div>
						
						
						@else

						<div class="form-group">
							<label for="phone" class="cols-sm-2 control-label">{{__('Passport number')}}</label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-users fa" aria-hidden="true"></i></span>
									<input type="text"  maxlength="190" class="form-control" name="number_passport" id="number_passport"   required/>
								</div>
							</div>
						</div>

               
						<div class="form-group">
							<label for="email" class="cols-sm-2 control-label">{{__('ISSUE')}}</label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-envelope fa" aria-hidden="true"></i></span>
									<input type="text"  maxlength="190" class="form-control" name="issue" id="issue"   required/>
								</div>
							</div>
						</div>


						<div class="form-group">
							<label for="password" class="cols-sm-2 control-label">Exp</label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-lock fa-lg" aria-hidden="true"></i></span>
									<input type="text"  maxlength="190" class="form-control" name="exp" id="exp"   required />
								</div>
							</div>
						</div>

						<div class="form-group">
							<label for="confirm" class="cols-sm-2 control-label">{{__('Nationality')}}</label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-lock fa-lg" aria-hidden="true"></i></span>
									<input type="text"  maxlength="190" class="form-control" name="nac" id="nac"   required/>
								</div>
							</div>
						</div>

						<div class="form-group ">
							<button type="submit" id="button" class="btn btn-primary btn-lg btn-block login-button">{{__('Register Passport')}}</button>
						</div>

						@endif
                </form>
             </div>
            </div>
        </div>
    </div>
</div>
@endsection