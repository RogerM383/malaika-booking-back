<!-- MODAL AÑADIR COMENTARIO -->
<div id="archiveTrip" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
          <h4 class="modal-title">Archivar viatje</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
     
      <form action="{{route('archiveTrip')}}">
                    <div class="form-group">

                       
                        <input type="hidden" class="form-control" id="trip" name="trip">
                       

                        <p>Estas segur d'archivar el viatje
                        <input type="text" class="modalText" id="title" name="title" readonly>
                        </p>
                       
                        
                
                    </div>

                    <button type="submit" class="btn btn-sm btn-info ">{{__('Archive trip')}}</button>
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