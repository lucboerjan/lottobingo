<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// -- toevoegen begin --
use Auth;
// use Carbon\Carbon;
use App\Http\Middleware\Instelling;
//use App\Http\Controllers\TaalController;
use App\Models\Lottobingo;

class LottobingoController extends Controller
{

    public function __construct()
    {
        $this->_oLottobingo = new Lottobingo();
    }
    
    /**
     * public function jxLottobingoOverzicht()
     * Haalt alle gegevens uit de database op om aan de gebruiker te tonen
     * @return array
     */

     public function jxLottobingoOverzicht(Request $request) {
         $json = [
            'succes' => true,
        ];
        $rslt = $this->_oLottobingo->resultaten();
        $json['succes'] = true;
        $json['reeksen'] = $rslt['reeksen'];
        $json['einddatum'] = $rslt['einddatum'];
        $json['startdatum'] = $rslt['startdatum'];
        $json['starttrekking'] = $rslt['starttrekking'];
        $json['getrokkengetallen'] = $rslt['getrokkengetallen'];
        $json['aantalreekseninsessie'] = $rslt['aantalreekseninsessie'];
        $json['aantaltrekkingeninsessie'] = $rslt['aantaltrekkingeninsessie'];
        $json['winstperspeler'] = $rslt['winstperspeler'];
        //$json['uitbetalingen'] = $rslt['uitbetalingen'];
        $json['trekkingeninsessie'] = $rslt['trekkingeninsessie'];
        $json['pagina'] = 0; //$rslt['pagina'];
        $json['aantalpaginas'] = 10; //$rslt['aantalPaginas'];
        $json['knoppen'] = Instelling::get('paginering')['knoppen'];

        return response()->json($json);     
     }

    /**
    * jxLottobingoBoodschappen()
    * @return string
    */ 
    public function jxLottobingoBoodschappen() {
        $json = [
            'succes' => true,
            'boodschappen' => trans('boodschappen.lottobingo_boodschappen')
        ];

        return response()->json($json);
    }     
    
    public function jxGetActieveReeksen() {
        $json = [
            'succes' => true,
            'reeksen' => $this->_oLottobingo->getActieveReeksen(),
            'laatsteuitbetaling' => Instelling::get('laatsteuitbetaling')
        ];
       
        return response()->json($json);
    }

    public function jxUitbetalingen(Request $request) {
        $json = ['succes' => false];
        $pagina = $request->pagina;
        $aantalUitbetalingenPerPagina = Instelling::get('paginering')['aantaluitbetalingenperpagina'];
        $rslt = $this->_oLottobingo->getUitbetalingen($pagina, $aantalUitbetalingenPerPagina );

        $json['succes'] = $rslt['succes'];
        $json['uitbetalingen'] = $rslt['uitbetalingen'];
        $json['pagina'] = $pagina;
        $json['aantalpaginas'] = $rslt['aantalpaginas'];
        $json['knoppen'] = Instelling::get('paginering')['knoppen'];


        return response()->json($json);

    }
}
