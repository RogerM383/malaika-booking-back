<!-- MODAL AÑADIR COMENTARIO -->
<div id="reinsertClientDeparture" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{__('You want to add the traveler to the trip again?')}}</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">

                <!-- NAME CLIENT DEPARTURE -->
                <form action="{{route('reinsertClient')}}" method="POST">
                    @csrf
                    <div class="form-group">

                       
                  
                        <input type="hidden" class="form-control" id="client" name="client">
                        <input type="hidden" class="form-control" id="departure" name="departure">

                        <p>{{__('Indicate the room number')}}
                            <input type="hidden" class="modalText" id="name" name="name" readonly>
                        </p>

                       
                        <select  name="room" id="room" class="form-control" required>

                    
                                            @if($values)

                                                @foreach($values as $i)                         
                            
                                                    <option value="{{$i}}" selected>{{$i}}</option>
                                                
                                                @endforeach
                                                <option value="{{$i+1}}" selected>{{__('New Room')}}</option>
                                            @endif
                                          

                        </select>

                    </div>

                    <button type="submit" class="btn btn-sm btn-info ">{{__('Add to Room')}}</button>
                    <!-- <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">{{__('Close')}}</button>
                     -->
                </form>
                
                <hr>
                

                <form action="{{route('cancelToWaitingList')}}" method="POST">
                    @csrf
                <input type="hidden" class="form-control" id="clientwaiting" name="client">
                <input type="hidden" class="form-control" id="departurewaiting" name="departure">
                <button id="buttonReinsertWishList" type="submit" class="btn btn-sm btn-info ">{{__('Add to Whislist')}}</button>

                </form>


            </div>
            


            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{{__('Close')}}</button>
            </div>
        </div>

    </div>
</div>
<!-- END  MODAL  -->