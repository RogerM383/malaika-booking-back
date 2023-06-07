@extends('layouts.app')

@section('content')

@if(session()->has('message'))

<div class="alert alert-success">
	<div class="container-fluid" style="max-width:75%">
		{{ session()->get('message') }}
	</div>
</div>

@endif



<div class="container">
	<div class="row justify-content-center">
		<div class="col-md-8">

			<form action="{{ action('DepartureController@store') }}" method="POST"  autocomplete="off">
				@csrf
				<input type="hidden" value="{{$trip}}" name="trip">



				<div style="margin-bottom:10px;">

					<a id="backbutton" href=""><i class="fas fa-arrow-left fa-lg"></i> Tornar</a>
				</div>


				<div class="card">
					<div class="card-header">{{__('New Departure')}} </div>
					<div class="card-body">

						<div class="form-group">
							<label for="name" class="cols-sm-2 control-label">{{__('Start')}} </label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-user fa" aria-hidden="true"></i></span>
									<input type="text" maxlength="190" class="form-control datepicker" name="start" id="start" placeholder="{{__('Enter start date')}}" required />
								</div>
							</div>
						</div>

						<div class="form-group">
							<label for="name" class="cols-sm-2 control-label">{{__('Final')}} </label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-user fa" aria-hidden="true"></i></span>
									<input type="text" maxlength="190" class="form-control datepicker" name="final" id="final" placeholder="{{__('Enter final date')}}" required />
								</div>
							</div>
						</div>

						<div class="form-group">
							<label for="name" class="cols-sm-2 control-label">{{__('Price')}} </label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-user fa" aria-hidden="true"></i></span>
									<input type="text" maxlength="190" class="form-control " name="price" id="price" placeholder="{{__('Enter price')}}" required />
								</div>
							</div>
						</div>

						<div class="form-group">
							<label for="name" class="cols-sm-2 control-label">{{__('Individual Supplement')}} </label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-user fa" aria-hidden="true"></i></span>
									<input type="text" maxlength="190" class="form-control " name="individual_supplement" id="individual_supplement" placeholder="{{__('Enter price')}}" required />
								</div>
							</div>
						</div>

						<div class="form-group">
							<label for="name" class="cols-sm-2 control-label">{{__('Pax Available')}} </label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-user fa" aria-hidden="true"></i></span>
									<input type="number" maxlength="190" class="form-control" name="pax_available" id="pax_available" placeholder="{{__('Enter rooms available')}}" required />
								</div>
							</div>
						</div>

						<div class="form-group">
							<label for="name" class="cols-sm-2 control-label">{{__('Expedient')}} </label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-user fa" aria-hidden="true"></i></span>
									<input type="number" maxlength="190" class="form-control" name="expedient" id="expedient" max="999999999" placeholder="{{__('Enter number expedient')}}" />
								</div>
							</div>
						</div>





						<div class="form-group">
							<label for="name" class="cols-sm-2 control-label">{{__('Commentary')}} </label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-user fa" aria-hidden="true"></i></span>

									<textarea name="commentary" id="commentary" class="form-control" rows="3" placeholder="{{__('Enter commentary')}}"></textarea>
								</div>
							</div>
						</div>


						<div class="form-group ">
							<button type="submit" id="button" class="btn btn-primary btn-lg btn-block login-button">{{__('Register')}}</button>


			</form>




		</div>


	</div>
</div>


@endsection