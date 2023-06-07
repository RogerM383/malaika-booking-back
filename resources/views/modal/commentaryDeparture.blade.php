<!-- MODAL AÑADIR COMENTARIO -->
<div id="commentaryDeparture" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
          <h4 class="modal-title">{{__('Commentary')}}</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
     
      <form action="{{route('addObservationDepartures')}}">
                    <div class="form-group">

                       
                       
                        <input type="hidden" class="form-control" id="departure" name="departure">

                        <p>{{__('Add a comment to the departure')}}: {{$trip->title}} 
                      
                        </p>
                      
                      <textarea name="commentary" id="commentary" class="form-control" rows="3"></textarea>
                        
                
                    </div>

                    <button type="submit" class="btn btn-sm btn-info ">{{__('Add Commentary')}}</button>
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">{{__('Close')}}</button>

                </form>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">{{__('Close')}}</button>
      </div>
    </div>

  </div>
</div>


<!-- END  MODAL  -->