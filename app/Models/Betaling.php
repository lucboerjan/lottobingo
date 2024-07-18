<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;


class Betaling extends Model
{
    use HasFactory;
    protected $table = 'betaling';


            
    public function betalingVerwijderen($reeksID) {
        $dbSql = sprintf('
            SELECT id, CONCAT(RIGHT(maand, 4), LEFT(maand, 2)) AS periode
            FROM betaling
            WHERE reeksID = %d
            ORDER BY periode DESC;

        ', $reeksID);
        $id = DB::select($dbSql)[0]-> id;

        $dta['succes'] = true;
        $dta['id'] = $id;

        $oBetaing = Betaling::find($id);
        $oBetaing->delete();

        return $dta;
    }

}
