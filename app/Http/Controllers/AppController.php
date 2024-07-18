<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// -- toevoegen begin --
use Auth;
use App\Http\Middleware\Instelling;
use App\Http\Controllers\TaalController;
use App\Models\User;
use Carbon\Carbon;

// -- toevoegen einde --

class AppController extends Controller
{
    public function __construct()
    {
        $this->_oUser = new User();
    }
    /**
     * staat in voor zetten taal interface
     * en controle op aangemeld
     * toont weergave homepage (indien aangemeld en taal)
     */
    public function index() {
        // zet taal interface
        TaalController::taal();

        // redirect naar login indien niet aangemeld
        if (!Auth::check()) return redirect('login');

        // anders toon pagina
        return view('pagina.index');
    }

    public function jxSetVisit (Request $request) {
        $id = $request->id;
        $json = [
            'succes' => false,
            'id' => $id  
        ];
        $oDatum = Carbon::now()->timezone('Europe/Brussels');
        $datum = $oDatum->format('Y-m-d H:i:s');
        $rslt = $this->_oUser->setTimestamp($id, $datum);
        $json['succes'] = $rslt['succes'];
        return response()->json($json);
    }


    public function spelers () {
        if (!Auth::check()) return redirect('login');
        return view('pagina.spelers');
    }

    public function trekkingen () {
        if (!Auth::check()) return redirect('login');
        return view('pagina.trekking');
    }
}