@extends('layouts.app')

@section('content')


<div class="container">
	<div class="row justify-content-center">
		<div class="col-md-8">

			<form action="{{ route('departure.update', $departure) }}" method="POST"  autocomplete="off">
				@method('PATCH')
				@csrf

				<div style="margin-bottom:10px;">

					<a id="backbutton" href=""><i class="fas fa-arrow-left fa-lg"></i>  Tornar</a>
					</div>
					<div class="card">
					<div class="card-header">{{__('Edit departure')}}</div>
					<div class="card-body">


						<div class="form-group">
							<label for="name" class="cols-sm-2 control-label">{{__('Start')}} </label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-user fa" aria-hidden="true"></i></span>
									<!-- <input type="text" class="form-control" name="start" id="start" value="{{$departure->start}}" required maxlength="190" /> -->
									<input type="text" maxlength="190" class="form-control datepicker" name="start" id="start" value="{{ date('d-m-Y', strtotime($departure->start)) }}"  />
								</div>
							</div>
						</div>

						<div class="form-group">
							<label for="username" class="cols-sm-2 control-label">{{__('Final')}}</label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-users fa" aria-hidden="true"></i></span>
									<input type="text" maxlength="190" class="form-control datepicker" name="final" id="final"  value="{{ date('d-m-Y', strtotime($departure->final)) }}" />
								</div>
							</div>
						</div>

						<div class="form-group">
							<label for="username" class="cols-sm-2 control-label">{{__('Price')}}</label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-users fa" aria-hidden="true"></i></span>
									<input type="text" maxlength="190" class="form-control" name="price" id="price" value="{{$departure->price}}" />
								</div>
							</div>
						</div>

						<div class="form-group">
							<label for="name" class="cols-sm-2 control-label">{{__('Individual Supplement')}} </label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-user fa" aria-hidden="true"></i></span>
									<input type="text" maxlength="190" class="form-control " name="individual_supplement" id="individual_supplement" value="{{$departure->individual_supplement}}" />
								</div>
							</div>
						</div>

						<div class="form-group">
							<label for="name" class="cols-sm-2 control-label">{{__('Pax Available')}} </label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-user fa" aria-hidden="true"></i></span>
									<input type="number" maxlength="190" class="form-control" name="pax_capacity" id="pax_capacity" value="{{$departure->pax_capacity}}"  />
								</div>
							</div>
						</div>


						<div class="form-group">
							<label for="name" class="cols-sm-2 control-label">{{__('Expedient')}} </label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-user fa" aria-hidden="true"></i></span>
									<input type="number" maxlength="190" class="form-control" name="expedient" id="expedient" max="999999999" value="{{$departure->expedient}}" />
								</div>
							</div>
						</div>


						<div class="form-group">
							<label for="name" class="cols-sm-2 control-label">{{__('Commentary')}} </label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-user fa" aria-hidden="true"></i></span>

									<textarea name="commentary" id="commentary" class="form-control" rows="3">{{$departure->commentary}}</textarea>
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
