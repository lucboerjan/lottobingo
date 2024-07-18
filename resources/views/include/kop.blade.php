{{-- <nav class="navbar navbar-expand-md navbar-light shadow-sm"> --}}
<nav class="navbar navbar-expand-md">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
            @if(isset(App\Http\Middleware\Instelling::get('app')['logo']))
                @php
                    $logo = App\Http\Middleware\Instelling::get('app')['logo'];
                @endphp
                <img src="{{ URL::to($logo) }}" id="logo">
            @endif
            {{ config('app.name', 'Laravel') }}
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav me-auto">
                @auth
                    <!-- knop Spelers/Users: level 02 -->
                    @if (Auth::user()->level & 0x02)
                        <li class="nav-item">
                            <a class="nav-link" href="spelers" role="button">
                                <i class="bi bi-people-fill"></i>
                                {{ __('boodschappen.navkop_module1') }}
                            </a>
                        </li>
                    @endif

                    <!-- knop Betalingen: level 08 -->
                    {{-- @if (Auth::user()->level & 0x02)
                        <li class="nav-item">
                            <a class="nav-link" href="betalingen" role="button">
                                <i class="bi bi-currency-euro"></i>
                                {{ __('boodschappen.navkop_module2') }}
                            </a>
                        </li>
                    @endif --}}

                    <!-- knop administrator: level 2 -->
                    @if (Auth::user()->level & 0x02)
                        <li class="nav-item">
                            <a class="nav-link" href="trekkingen" role="button">
                                <i class="bi bi-ui-checks"></i>
                                {{ __('boodschappen.navkop_module4') }}
                            </a>
                        </li>
                    @endif
                @endauth
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ms-auto">
                <!-- Authentication Links -->
                @auth
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ Auth::user()->name }}
                        </a>

                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                                document.getElementById('logout-form').submit();">
                                {{ __('authenticatie.logout') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endauth

                <!-- DROPDOWN TALEN -->
                <li class="nav-item dropdown">
                    <a id="navbarDropdownTalen" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                        {{ __('boodschappen.taal') }}
                    </a>

                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownTalen">
                        @foreach (App\Http\Middleware\Instelling::get('talen') as $taal => $taalOpties)
                            <a class="nav-link" href="/taal/{{ $taal }}">
                                <img src="{{ URL::to($taalOpties[1]) }}">
                                {{ $taalOpties[0] }}
                            </a>
                        @endforeach
                    </div>
                </li>

            </ul>
        </div>
    </div>
</nav>
