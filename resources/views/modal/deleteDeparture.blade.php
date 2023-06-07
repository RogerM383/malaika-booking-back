<!--    MODAL  -->
<div class="modal fade" id="deleteDeparture" tabindex="-1" role="dialog" aria-labelledby="ModalDeleteTrip" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">{{__('Are you sure to delete the departure') }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <form action="{{route('deletedeparture')}}" >
               <div class="form-group">

                  <input type="text" class="modalText" id="name" name="name">
                  <input type="hidden" class="modalText" id="departure" name="departure">

                  
              </div>
              
              <button type="submit" class="btn btn-sm btn-danger">{{__('Clear client')}}</button>
              <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">{{__('Close')}}</button>
              
        </form>
      </div>
    </div>
  </div>
</div>

<!-- END  MODAL  -->