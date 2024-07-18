<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// -- toevoegen begin --
use Auth;
use Carbon\Carbon;
use App\Http\Middleware\Instelling;
use App\Http\Controllers\TaalController;
use App\Models\Trekking;
use App\Models\Winstverdeling;

// -- toevoegen einde --

class TrekkingController extends Controller
{
    public function __construct()
    {
        $this->_oTrekking = new Trekking();
        $this->_oWinstverdeling = new Winstverdeling(); 
    }
    public function jxTrekkingenOverzicht(Request $request)
    {
        // zet taal interface
        TaalController::taal();

        // redirect login indien niet aangemeld
        if (! Auth::check()) {
            return redirect('login');
        }        

        $json = [
            'succes' => false,
        ];

        $pagina = $request->pagina;
        $aantalTrekkingenPerPagina = Instelling::get('paginering')['aantaltrekkingenperpagina'];

        $rslt = $this->_oTrekking->trekkingenLijst($pagina, $aantalTrekkingenPerPagina);

            $json['succes'] = true;
            $json['pagina'] = $pagina;
            $json['data'] = $rslt['data'];
            $json['aantal'] = $rslt['aantal'];
            $json['aantalpaginas'] = $rslt['aantalPaginas'];
            $json['knoppen'] = Instelling::get('paginering')['knoppen'];
            $json['startTrekking'] = $rslt['startTrekking'];
            $json['getrokkenGetallen'] = $rslt['getrokkenGetallen'];
            $json['mijnlottoreeksen'] = $rslt['mijnlottoreeksen'];
            $json['actieveTrekking'] = $rslt['laatsteTrekking'];
            $json['aantalTrekkingenInSessie'] = $rslt['aantalTrekkingenInSessie'];
            $json['aantalReeksenInSessie'] = $rslt['aantalReeksenInSessie'];
            $json['laatsteTrekkingID'] = $rslt['laatsteTrekkingID'];


        return response()->json($json);
    }

    public function jxTrekkingGet(Request $request)
    {
        $mode = $request->mode;
        $json = [
            'succes' => false,
            'mode' => $mode
        ];

        foreach (explode(',', __('boodschappen.trekkingBewerk')) as $item) {
            $tmp = explode(':', $item);
            $labels[$tmp[0]] = $tmp[1];
        }
        $json['labels'] = $labels;
        $json['geduld'] = __('boodschappen.geduld');
        $json['fout'] = __('boodschappen.fout');


        $id = intval($request->id);
        if ($id == 0) {
            $oDatum = Carbon::today()->timezone('Europe/Brussels');
            $json['trekking'] = [
                'id' => 0,
                'datum' => $oDatum->format('Y-m-d'),


            ];
        } else {
            $json['trekking'] = $this->_oTrekking->getTrekking($id);
        }

        $json['succes'] = true;
        return response()->json($json);

    }

    public function jxTrekkingSet(Request $request)
    {
        $mode = $request->mode;
        $json = [
            'succes' => false,
            'mode' => $mode
        ];
        $id = $request->id;
        $datum = $request->datum;
        $g1 = $request->g1;
        $g2 = $request->g2;
        $g3 = $request->g3;
        $g4 = $request->g4;
        $g5 = $request->g5;
        $g6 = $request->g6;
        $res = $request->res;

        if ($mode == 'verwijder') {
            $rslt = $this->_oTrekking->verwijderTrekking($id);
            $json['succes'] = $rslt['succes'];
            if ($rslt['succes']== true)  {
                // we moeten ook eventueel de winstverdeling verwijderen voor deze trekking
                $rslt = $this->_oWinstverdeling->verwijderUitbetaling($id);
                $json['$verwijderdeUitbetalingen'] = $rslt['$verwijderdeUitbetalingen'];

            }
        }
        else {

            $rslt = $this->_oTrekking->setTrekking($id, $datum, $g1, $g2, $g3, $g4, $g5, $g6, $res);
            $json['succes'] = $rslt['succes'];

        }

        return response()->json($json);
    }


    /**
    * jxTrekkingzoekBoodschappen()
    * @return string
    */ 
    public function jxTrekkingzoekBoodschappen() {
        $json = [
            'succes' => true,
            'boodschappen' => trans('boodschappen.trekking_boodschappen')
        ];

        return response()->json($json);
    }   

    public function jxSendEmail() {
        $json = ['succes' => false];
        $rslt = $this->_oTrekking->sendEmail();
        $json['spelers'] = $rslt['spelers'];
        $json['succes'] = $rslt['succes'];

        return response()->json($json);

    }

    public function jxUitbetalingWinnaars(Request $request) {
        $trekkingID = $request->trekkingID;
        $bedragPerWinnaar = $request->bedragperwinnaar;
        $winnaars = $request->winnaars;

        $json = ['succes' => false];
        $json['trekkingID'] = $request->trekkingID;
        $json['bedragperwinnaar'] = $request->bedragperwinnaar;
        $json['winnaars'] = $request->winnaars;
        $rslt = $this->_oWinstverdeling->uitbetaling($trekkingID, $bedragPerWinnaar, $winnaars);
        $json['succes'] = $rslt['succes'];

        return response()->json($json);        
    }

}
