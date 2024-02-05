
<div style="padding:40px; padding-bottom:60px; width:800px;font-size:17px;">
    <a href="">
        <img src="https://api.malaika-clients.fruntera.dev/images/logoamics.jpg" alt="Amics del MNAC" style="width:180px; height: auto;" />
    </a>&nbsp;&nbsp;&nbsp;
    &nbsp;&nbsp;&nbsp;
    <a href="http://malaikaviatges.com">
        <img src="https://api.malaika-clients.fruntera.dev/images/logo-malaika.svg" alt="Malaikaviatges" style="width:180px; height: auto;display:block; float:right" />
    </a>
</div>

<div style="padding-left:40px;width:800px;font-size:17px;">

    <br>

    <p syle="font-weight:600">Benvolgut/s Amic/s,<br><br>La vostra inscripció al viatge <strong>{{$title}}</strong> s’ha realitzat correctament. <br>Si us plau, repasseu la reserva que acabeu de rebre i contacteu amb Malaika Viatges (930 011 176) si hi ha alguna dada incorrecta:<br><br>
        <span>Nº d’inscripció: <strong> {{$number}} </strong></span>
    </p>

    <h4 style="font-size:19px;">DADES DELS VIATGERS</h4>

    <table class="taula" cellpadding="0" style="font-size:17px;">
        @foreach ($clients as $client)
            <tr>
                <td style="padding-left:0">
                    {{ $client['name'] }} {{ $client['surname'] }}
                </td>
                <td style="padding-left:40px">
                    {{ $client['dni'] }}
                </td>
                <td style="padding-left:40px">
                    {{ $client['MNAC'] }}
                </td>
        @endforeach
    </table>

    <h4 style="font-size:19px;">DATES</h4>

    <p>
        {{$dates}}
    </p>

    <h4 style="font-size:19px;">HABITACIÓ</h4>

    <p>
    @foreach ($rooms as $room)
        {{ $room['quantity'] }} {{ $room['name'] }}<br>
    @endforeach
    </p>

    <h4 style="font-size:19px;">DADES DE CONTACTE</h4>

    <p>
        {{ $contact['name'] }} {{ $contact['surname'] }}<br>
        {{ $contact['phone'] }}<br>
        {{ $contact['email'] }}<br>
    </p>

    <p style="padding-top: 40px;font-size:17px;">Per poder confirmar la vostra reserva és <strong>imprescindible</strong> fer una <strong>transferència bancària</strong> al número de compte IBAN: ES75 2100 1358 1102 0023 7631 per un import de <strong> {{$booking_price}}€ per persona</strong> en el termini de les properes 24 hores. A l`hora de fer la transferència, us preguem que feu constar el vostre nom i el número d`inscripció. Si al cap de 24 hores no rebem cap pagament, entenem que la plaça/les places queden lliures per a altres Amics que puguin estar interessats en el viatge. Podeu consultar la política de cancel·lacions al <a href="{{$pdf}}" style="text-decoration:none; color:#dd6437">full informatiu (PDF)</a>.<br><br>
        Per a més informació podeu contactar amb Malaika Viatges al 930 011 176 o <a href="aayats@malaikaviatges.com" style="color:#dd6437">aayats@malaikaviatges.com</a>
    </p>

</div>
