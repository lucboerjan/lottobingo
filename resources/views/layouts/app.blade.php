<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">



    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if (isset(App\Http\Middleware\Instelling::get('app')['favicon']))
        @php
            $favicon = App\Http\Middleware\Instelling::get('app')['favicon'];
        @endphp
        <link rel="shortcut icon" type="image/png" href="{{ URL::to($favicon) }}">
    @endif
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">  
    {{-- <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css"> --}}
    {{-- //<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"> --}}


    {{-- <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css"> --}}
    <script src="//code.jquery.com/jquery-1.12.4.js"></script>
    <script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>


    <!-- Scripts -->
    <script src="{{ URL::to('js/jquery.js') }}"></script>
    <script src="{{ URL::to('js/ajx.js') }}"></script>
    <script src="{{ URL::to('js/modaal.js') }}"></script>
    <script src="{{ URL::to('js/paginering.js') }}"></script>
    <script src="{{ URL::to('js/jquery-ui.js') }}"></script>
    <script src="{{ URL::to('js/jquery.mousewheel.js') }}"></script>
    
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body>

    <div id="app">
        @include('include.kop')
        <main class="py-4">
            <div class="container">
                @yield('content')
            </div>
        </main>
    </div>
</body>

</html>
