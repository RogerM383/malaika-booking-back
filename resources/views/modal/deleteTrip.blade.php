<!--    MODAL  -->
<div class="modal fade" id="deleteTrip" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">{{__('Are you sure you want to delete to:') }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <form action="{{route('deletetrip')}}" >
               <div class="form-group">
                  <input type="hidden" class="form-control" id="trip" name="id">
                  <input type="text" class="modalText" id="title" name="title" readonly >
                
                  
              </div>
              
              <button type="submit" class="btn btn-sm btn-danger">{{__('Clear Trip')}}</button>
              <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">{{__('Close')}}</button>
              
        </form>
      </div>
    </div>
  </div>
</div>

<!-- END  MODAL  -->