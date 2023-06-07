@extends('layouts.app')

@section('content')

<div class="container">
	<div class="row justify-content-center">
		<div class="col-md-8">

			<form action="{{ route('trip.update', $trip) }}" method="POST"  autocomplete="off">
				@method('PATCH')
				@csrf


				<div style="margin-bottom:10px;">

					<a id="backbutton" href=""><i class="fas fa-arrow-left fa-lg"></i> Tornar</a>
				</div>


				<div class="card">
					<div class="card-header">{{__('Edit trip')}}</div>
					<div class="card-body">


						<div class="form-group">
							<label for="name" class="cols-sm-2 control-label">{{__('Title')}} </label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-user fa" aria-hidden="true"></i></span>
									<input type="text" class="form-control" name="title" id="title" value="{{$trip->title}}" required maxlength="190" />
								</div>
							</div>
						</div>

						<div class="form-group">
							<label for="username" class="cols-sm-2 control-label">{{__('Description')}}</label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-users fa" aria-hidden="true"></i></span>
									<input type="text" maxlength="190" class="form-control" name="description" id="description" value="{{$trip->description}}" />
								</div>
							</div>
						</div>



						<div class="form-group">
							<label for="username" class="cols-sm-2 control-label">{{__('Category')}}</label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-users fa" aria-hidden="true"></i></span>
									<input type="text" maxlength="190" class="form-control" name="category" id="category" value="{{$trip->category}}" />
								</div>
							</div>
						</div>

						<div class="form-group">
							<label for="name" class="cols-sm-2 control-label">{{__('Commentary')}} </label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-user fa" aria-hidden="true"></i></span>

									<textarea name="commentary" id="commentary" class="form-control" rows="3">{{$trip->commentary}}</textarea>
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