<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Start toevoegen
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\Betaling;

// Einde toevoegen

class Speler extends Model
{
    use HasFactory;
    protected $table = 'users';

    public function spelersLijst()
    {
        $dbSql = sprintf("
        SELECT rks.id, usr.name, usr.fullname, usr.email, usr.level, MAX(CONCAT(RIGHT(bet.maand, 4), LEFT(bet.maand, 2))) AS periode, usr.laatste_bezoek
        FROM reeks rks
        JOIN betaling bet ON bet.reeksID = rks.id
        JOIN users usr ON usr.id = rks.userID
        GROUP BY rks.id, usr.name, usr.fullname, usr.email,  usr.level, usr.laatste_bezoek
        ORDER BY periode DESC, usr.fullname;
    ");

        $rslt = DB::select($dbSql);
        $dta['data'] = [];

        foreach ($rslt as $row) {
            $dbRij = [];
            $dbRij['id'] = substr('0' . $row->id, -2);
            $dbRij['name'] = $row->name;
            $dbRij['fullname'] = $row->fullname;
            $dbRij['email'] = $row->email;
            $dbRij['level'] = $row->level;
            $dbRij['periode'] = $row->periode;
            $dbRij['maand'] = substr($row->periode,4,2)."/".substr($row->periode,0,4);
            $laatstebezoek = substr($row->laatste_bezoek,8,2) .'-'.substr($row->laatste_bezoek,5,2) .'-' .substr($row->laatste_bezoek,0,4) .' ' . substr($row->laatste_bezoek,11,8);   ;
            
            $dbRij['laatstebezoek'] = $laatstebezoek == '-- ' ? '' : $laatstebezoek;
            array_push($dta['data'], $dbRij);

        }
      
        return $dta;
    }

    public function getSpeler($id)
    {
        return DB::select(sprintf("
        SELECT id, name, fullname, email, level
        FROM users
        WHERE id=%d
        ", $id))[0];
    }

    public function betalingToevoegen($spelerIDs, $periode) {
        
        $dta['succes'] = true;
        $maand = substr($periode,0,2) . ' ' . substr($periode,3,4);
        foreach ($spelerIDs as $id) {

            $betaling = new Betaling();
            $betaling->reeksID = $id;
            $betaling->maand = $maand;
            $betaling->save();
        }
        return $dta;
    }


    
}
