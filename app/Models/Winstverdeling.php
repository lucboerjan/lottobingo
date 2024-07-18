<?php

namespace App\Models;

use App\Http\Middleware\Instelling;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

class Winstverdeling extends Model
{
    use HasFactory;
    protected $table = 'winstverdeling';

    public function uitbetaling($trekkingID, $bedrag, $winnaars)
    {
        foreach ($winnaars as $winnaar) {
            $reeksID = $winnaar;
            $uitbetaling = new Winstverdeling();
            $uitbetaling->trekkingID = $trekkingID;
            $uitbetaling->reeksID = $reeksID;
            $uitbetaling->bedrag = $bedrag;
            $uitbetaling->save();
        }

        // in instelling.json de waarde van laatsteuitbetaling bijwerken
        Instelling::set('laatsteuitbetaling', $trekkingID);

        $dta['succes'] = true;
        return $dta;
    }

    public function verwijderUitbetaling($id)
    {
        //haal de uitbetalingsrecords op
        $dbSql = sprintf("
            SELECT id 
            FROM winstverdeling
            WHERE trekkingID = %d
        ", $id);

        $verwijderdeUitbetalingen = [];

        $uitbetalingen = DB::SELECT($dbSql);
        foreach ($uitbetalingen as $uitbetaling) {

            Winstverdeling::where('id', '=', $uitbetaling->id)->delete();
           array_push($verwijderdeUitbetalingen,$uitbetaling->id);
            $dta['succes'] = true;
        }
        $dta['$verwijderdeUitbetalingen'] = $verwijderdeUitbetalingen;
        //instelling.json bijwerken met de juiste waarde van de laatste uitbetaling
        $dbSql = sprintf("
            SELECT trekkingID 
            FROM winstverdeling
            ORDER BY trekkingID DESC
            LIMIT 1
        ");
        $trekkingID = DB::SELECT($dbSql)[0]->trekkingID;
        Instelling::set('laatsteuitbetaling', $trekkingID);
    

        return $dta;
    }
}
