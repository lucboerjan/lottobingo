@extends('layouts.app')

@section('content')
    <div class="card mb-1 card-body" id="spelers">

        <div class="container">
            <div class="row">
                <div id="overzichtSpelers" class="card mb-1 card-body col-9">

                </div>
                <div id="actionPanel" class="card mb-1 card-body col-3">
                    
                    <div class="card card-body">
                        <h4>{{ __('boodschappen.spelers_actionpanel') }}</h4>
                        <button class="btn btn-primary  mb-1" type="button" id="betalingToevoegen" disabled>
                            <i class="bi bi-credit-card-2-front"></i>
                            {{ __('boodschappen.spelers_betalingtoevoegen') }}

                            </button>
                            <br>
                    </div>
                    <div class="card card-body">        
                        
                    </div>
                </div>
            </div>

        </div>
@endsection
