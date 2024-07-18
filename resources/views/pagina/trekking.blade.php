@extends('layouts.app')

@section('content')
    <div class="card mb-1 card-body">
        <h4 style="margin-left: 20px;">{{ __('boodschappen.trekking_titel') }}</h4>
        <div class="container" id="trekking">
            <div class="row">

                <div class="col-5" id="overzichtTrekkingen"></div>
                <div class="col-3" style="padding-left: 3px;>
                    <div id="getrokkenGetallen">
                    <div class="card mb-1 card-body position-relative centercolumn">

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

                    <div id="eigenLottoReeksen"></div>
                </div>

                <div class="col-4">
                    <div class="card card-body position-relative">
                        <button class="btn btn-primary  mb-1" type="button" id="trekkingNieuw">
                            <i class="bi bi-plus-square"></i>
                            {{ __('boodschappen.trekking_nieuw') }}

                        </button>


                        <a href="https://www.nationale-loterij.be/uitslagen-trekkingen" target="_blank" class="btn btn-secondary mb-1" id="nationaleloterij" role="button" style=" opacity: 0.7;">
                            <img src="/afbeelding/app/nationale-loterij.png" style="height: 16px;">
                            <span>{{ __('boodschappen.spelers_nationaleloterij') }}</span>
                        </a>
                    </div>
                
                    <div class="card card-body position-relative">

                        <button class="btn btn-secondary mb-1" type="button" id="mailVersturen">
                            <i class="bi bi-envelope-at"></i>
                            {{ __('boodschappen.trekking_verstuurmail') }}
                        </button>
                        <div id="emailResult"></div>
                    </div>
                </div>    


            </div>
        </div>



    </div>
@endsection
