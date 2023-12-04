
<div>
    New Inscription Client

    <span>Benvolgut/s Amic/s,</span>

    <p>
        La vostra inscripció al viatge <b>{{$title}}</b>
         s'ha realitzat correctament. Si us plau, repasseu la reserva que acabeu de rebre i contacteu amb Malaika Viatges (930 011 176) si hi ha alguna dada incorrecta:
    </p>

    <h2 style="margin-top: 2.3rem">DADES DELS VIATGERS</h2>

    <table style="margin: 1.5rem 0">
        @foreach ($clients as $client)
            <tr>
                <td style="padding-right: 50px">
                    {{ $client['name'] }} {{ $client['surname'] }}
                </td>
                <td style="padding-right: 50px">
                    {{ $client['dni'] }}
                </td>
                <td style="padding-right: 50px">
                    {{ isset($client['MNAC']) ?? $client['MNAC'] }}
                </td>
        @endforeach
    </table>

    <h2 style="margin-top: 2.3rem">DATES</h2>

    <p>
        posar dates
    </p>

    <h2 style="margin-top: 2.3rem">HABITACIÓ</h2>

    @foreach ($rooms as $room)A
        <p>{{ $room['quantity'] }} {{ $room['name'] }}</p>
    @endforeach

    <h2 style="margin-top: 2.3rem">DADES DE CONTACTE</h2>

    <ul style="margin: 1.5rem 0">
        <li>{{ $contact['name'] }} {{ $contact['surname'] }}</li>
        <li>{{ $contact['phone'] }}</li>
        <li>{{ $contact['email'] }}</li>
    </ul>

    <p style="padding-top: 60px">
        Per poder confirmar la vostra reserva és
        <strong>imprescindible</strong> fer una
        <strong>transferència bancària</strong> al número de compte
        IBAN ES75 2100 1358 1102 0023 7631 per un import de
        <strong>
            {booking_price[0].booking_price} per persona
        </strong>
        en el termini de les properes 24 hores. A l&apos;hora de fer
        la transferència, us preguem que feu constar el vostre nom i
        el número d&apos;inscripció. Si al cap de 24 hores no rebem
        cap pagament, entenem que la plaça/les places queden lliures
        per a altres Amics que puguin estar interessats en el
        viatge. Podeu consultar la política de cancel·lacions al
        <a style="color: #F05A23" href={pdf} target={'_blank'}>
            full informatiu (PDF).
        </a>
    </p>

    <p>
        Per a més informació podeu contactar amb Malaika Viatges al
        930 011 176 o
        <a style="color: #F05A23" href="mailto: aayats@malaikaviatges.com">
            aayats@malaikaviatges.com
        </a>
    </p>

</div>
