<html>
<h3>Beste,</h3><br><br>
De lottobingoresultaten werden bijgewerkt.<br><br>
Deze website is nog in ontwikkeling. De getoonde resultaten zijn nog niet officieel.


@if (isset(App\Http\Middleware\Instelling::get('app')['appurl']))
    @php
        $appurl = App\Http\Middleware\Instelling::get('app')['appurl'];
    @endphp
    Surf naar <a href="{{ $appurl }}">Lottobingo Proviron</a> voor de resultaten.<br><br>
@endif

U hebt een geldige login (emailadres) nodig.<br>

@if (isset(App\Http\Middleware\Instelling::get('app')['lottobingo']))
    @php
        $logo = App\Http\Middleware\Instelling::get('app')['lottobingo'];
    @endphp
    <img src="{{ URL::to($logo) }}" id="logo" alt="">
@endif
</html>
