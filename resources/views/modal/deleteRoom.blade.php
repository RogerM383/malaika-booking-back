<!-- MODAL AÑADIR COMENTARIO -->
<div id="deleteRoomModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
          <h4 class="modal-title">{{__('Delete traveler from the list')}}</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
     
      @if(!empty($client))
        <form action="{{route('deleteRoom', ['client' => $client, 'departure' => $departure->id ]) }}">
                    <div class="form-group">

                        <input type="hidden" class="form-control" id="id" name="departureId" value="{{$departure->id}}">
                        <input type="hidden" class="form-control" id="client" name="client">
                        <input type="hidden" class="form-control" id="departure" name="departure">
                        <input type="text" class="modalText" id="name" name="name" readonly>
                    </div>

                    <button type="submit" class="btn btn-sm btn-danger ">{{__('Delete')}}</button>
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">{{__('Close')}}</button>

        </form>
        @endif

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">{{__('Close')}}</button>
      </div>
    </div>

  </div>
</div>
<!-- END  MODAL  -->