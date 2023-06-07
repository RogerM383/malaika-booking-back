<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Malaika</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="{{ asset('js/malaika.js') }}" defer></script>


    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">
    <!-- <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous"> -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/malaika.css') }}" rel="stylesheet">
    {{-- <link href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css" rel="stylesheet"> --}}

    <!-- <link href="{{ asset('css/jquery-ui.css') }}" rel="stylesheet">
    <link href="{{ asset('css/jquery-ui.structure.css') }}" rel="stylesheet">
    <link href="{{ asset('css/jquery-ui.theme.css') }}" rel="stylesheet">  -->


    <!-- DATA PICKER -->
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
     <link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.js"></script>

    <!-- SELECT 2-->
    <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script> -->
    <!-- <script src="{{Config::get('app.url')}}/node_modules/select2/dist/js/select2.min.js"></script> -->

    <link href="{{ asset('css/select2.css') }}" rel="stylesheet">
    <script src="{{ asset('js/select2.min.js') }}"></script>



    <!-- <link rel="shortcut icon" href="http://www.malaikaviatges.com/img/favicon/favicon.ico"> -->

    <link rel="shortcut icon" href="{{ asset('img/faviconMalaika.ico') }}">



</head>
<body>

<script>
       $(document).ready(function($) {

        //Datepicker
          $('.datepicker').datepicker({
              dateFormat: "dd-mm-yy",
              changeYear: true,
              changeMonth: true,
              yearRange: "-100:+20",
              firstDay: 1,

              monthNamesShort: [ "<?php  echo __('Jen')  ?>",
                                "<?php  echo __('Feb')  ?>",
                                "<?php  echo __('Mar')  ?>",
                                "<?php  echo __('Apr')  ?>",
                                "<?php  echo __('Maj')  ?>",
                                "<?php  echo __('Jun')  ?>",
                                "<?php  echo __('Jul')  ?>",
                                "<?php  echo __('Aug')  ?>",
                                "<?php  echo __('Sep')  ?>",
                                "<?php  echo __('Okt')  ?>",
                                "<?php  echo __('Nov')  ?>",
                                "<?php  echo __('Dec')  ?>"

               ],

                dayNamesMin: [ "<?php  echo __('Su')  ?>", "<?php  echo __('Mo')  ?>", "<?php  echo __('Tu')  ?>", "<?php  echo __('We')  ?>",
                "<?php  echo __('Th')  ?>", "<?php  echo __('Fr')  ?>", "<?php  echo __('Sa')  ?>"],

                });

                // $("#e1").select2();


      });
</script>





    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
            <div class="container-fluid" style="max-width:75%;">
                <a class="navbar-brand" href="{{ route('clients.index') }}">
                    <img src="{{ asset('img/logoMalaika.png') }}" style="height:120px; margin-top:20px;">
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                              <li class="nav-item">
                                {{--<a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>--}}
                            </li>
                            @if (Route::has('register'))
                               <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                        <li class="nav-item">
                                <a class="nav-link" href="{{ route('client.create') }}">+ {{ __('New Client') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('client.index') }}"> {{ __('Clients') }}</a>
                            </li>
                            <!-- <li class="nav-item">
                                <a class="nav-link" href="{{ route('export') }}">{{ __('Export Data') }}</a>
                            </li> -->
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('trip.create') }}">+ {{ __('New Trip') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('trip.index') }}"> {{ __('Trips') }}</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('archivedList') }}"> {{ __('Archived trips') }}</a>
                            </li>


                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('opendepartures') }}">{{ __('Open Departures') }}</a>
                            </li>


                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>

                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
        <main class="py-4">
            @yield('content')
        </main>
    </div>
    @yield('scripts')
</body>








</html>
