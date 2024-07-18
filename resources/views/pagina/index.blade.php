@extends('layouts.app')

@section('content')
    <script>
        var userData = @json(auth()->user());
    </script>

    <div class="card mb-1 card-body" id="lottobingo">
        <div class="container">
            <div class="row">
                <h5 class="card-title reeksen"></h5>
                <div class="card card-body col-6" id="resultaten"></div>
                <div class="card card-body col-3" id="rangschikking"></div>
                <div class="card card-body position-relative col-3" id="getrokkenGetallen">

                    @php
                        $idx = 0;
                        $inhoud = '<table style="border: solid; margin-top:10px;">';
                        $inhoud .= '<tr style="border: solid;"><th colspan="7" id="thGetrokkenGetallen"></th></tr>';
                        for ($i = 1; $i <= 7; $i++) {
                            $inhoud .= '<tr style="border: solid;">';

                            for ($j = 1; $j <= 7; $j++) {
                                $idx++;
                                if ($idx <= 45) {
                                    $inhoud .= '<td style="border: solid; text-align: center;" class="getrokkenGetal" id="getal_' . $idx . '"" >' . $idx . '</td>';
                                }
                                if ($j == 7) {
                                    $inhoud .= '</tr>';
                                }
                            }
                        }
                        $inhoud .= '<tr><td colspan="7" id="inhoudPot"></td></tr>';

                        $inhoud .= '</table>';
                        echo $inhoud; // Use echo to output the generated HTML
                    @endphp

                </div>
            </div>
            <div class="row">
                <div class="card card-body col-3" id="winstPerSpeler"></div>
                <div class="card card-body col-5" id="uitbetalingen"></div>
                <div class="card card-body col-4" id="trekkingenInSessie"></div>

            </div>

        </div>
    </div>
@endsection
