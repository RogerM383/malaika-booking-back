@extends('layouts.app')

@section('content')
<form action="{{ action('TripController@store') }}" method="POST"  autocomplete="off">
                @csrf


<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">

                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

         


            <div class="card">
                <div class="card-header">{{__('New Trip')}} </div>
					<div class="card-body">

               

                
						<div class="form-group">
							<label for="name" class="cols-sm-2 control-label" >{{__('Title')}} </label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-user fa" aria-hidden="true"></i></span>
									<input type="text" maxlength="190" class="form-control" name="title" id="title"  placeholder="{{__('Enter title')}}" required/>
								</div>
							</div>
                        </div>
                        

                        <div class="form-group">
							<label for="name" class="cols-sm-2 control-label" >{{__('Description')}} </label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-user fa" aria-hidden="true"></i></span>
									<input type="text" maxlength="190" class="form-control" name="description" id="description"  placeholder="{{__('Enter description')}}" />
								</div>
							</div>
                        </div>
                        

                        
        
                        
                        <div class="form-group">
							<label for="name" class="cols-sm-2 control-label" >{{__('Category')}} </label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-user fa" aria-hidden="true"></i></span>
									<input type="text" maxlength="190" class="form-control" name="category" id="category"  placeholder="{{__('Enter category')}}" />
								</div>
							</div>
						</div>


						<div class="form-group">
							<label for="name" class="cols-sm-2 control-label" >{{__('Commentary')}} </label>
							<div class="cols-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-user fa" aria-hidden="true"></i></span>
									<textarea name="commentary" id="commentary" class="form-control" rows="3" placeholder="{{__('Enter commentary')}}"></textarea>
								
								</div>
							</div>
						</div>


					</div>
				
			</div>

            <div class="form-group ">
                    <button type="submit"  id="button" class="btn btn-primary btn-lg btn-block login-button">{{__('Register')}}</button>
            </div>
					
 
        </div>
    </div>
</div>

</form>
@endsection