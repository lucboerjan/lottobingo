<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// -- toevoegen begin --
use Auth;
// use Carbon\Carbon;
// use App\Http\Middleware\Instelling;
//use App\Http\Controllers\TaalController;
use App\Models\Speler;
use App\Models\Betaling;

// -- toevoegen einde --

class SpelerController extends Controller
{
    public function __construct()
    {
        $this->_oSpeler = new Speler();
        $this->_oBetaling = new Betaling();
    }
    public function jxSpelersOverzicht()
    {
        $json = [];
        $json['isAdmin'] = Auth::user()->level & 0x02;
        $rslt = $this->_oSpeler->spelersLijst();
        $json['data'] = $rslt['data'];
        return response()->json($json);
    }

    public function jxSpelerGet(Request $request)
    {
        $mode = $request->mode;
        $json = [
            'succes' => false,
            'mode' => $mode
        ];
        $id = intval($request->id);
        if ($id == 0) {
            $json['speler'] = [
                'id' => 0,

            ];
        } else {
            $json['speler'] = $this->_oSpeler->getSpeler($id);
        }
        ;

        $json['succes'] = true;
        return response()->json($json);

    }

    public function jxBetalingToevoegen(Request $request)
    {
        $spelerIDs = $request->ids;
        $periode = $request->periode;
        $json = [
            'succes' => false,
            'spelersIDs' => $spelerIDs,
            'periode' => $periode,
        ];

        $rslt = $this->_oSpeler->betalingToevoegen($spelerIDs, $periode);
        $json['succes'] = $rslt['succes'];

        return response()->json($json);

    }

    public function jxBetalingVerwijderen(Request $request)
    {
        $reeksID = $request->reeksID;
        $json = [
            'succes' => false,
            'reeksID' => $reeksID
        ];
        $rslt = $this->_oBetaling->betalingVerwijderen($reeksID);
        $json['succes'] = $rslt['succes'];
        $json['id'] = $rslt['id'];
        return response()->json($json);

    }
}
