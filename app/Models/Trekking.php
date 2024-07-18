<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Start toevoegen
use App\Http\Middleware\Instelling;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Mail\MyEmail;
use Illuminate\Support\Facades\Mail;

// Einde toevoegen

class Trekking extends Model
{
    use HasFactory;
    protected $table = 'trekking';

    public function trekkingenLijst($pagina, $aantalTrekkingenPerPagina)
    {

        // aantal items zoekenresultaat
        $tmp = $this->zoekTrekkingen($tel = true, 1e6, 0, $aantalTrekkingenPerPagina);

        $aantal = $tmp['aantal'];
        $aantalPaginas = $tmp['aantalPaginas'];
        $trekkingen = $this->zoekTrekkingen($tel = false, $aantal, $pagina, $aantalTrekkingenPerPagina);

        //lijst met getrokken getallen ophalen

        $startTrekking = $this->getStartTrekking();

        $getrokkenGetallen = $this->getrokkenGetallen($startTrekking);



        //mijnlottoreeksen 
        $dbSql = sprintf(
            "SELECT id,g1,g2,g3,g4,g5,g6
            FROM mijnlottoreeksen
            ORDER BY id
            "
        );
        $mijnlottoreeksen = DB::select($dbSql);

        //laatste (actieve) trekking
        $dbSql = sprintf(
            "SELECT *
            FROM trekking
            ORDER BY datum DESC
            LIMIT 1
            "
        );
        $laatsteTrekking = DB::select($dbSql)[0];

        //aantal trekkingen in sessie
        $dbSql = sprintf(
            "SELECT COUNT(1) as aantal
            FROM trekking
            WHERE id >= %d
            ",
            $startTrekking
        );
        $aantalTrekkingenInSessie = DB::select($dbSql)[0]->aantal;

        $aantalTrekkingenInSessie = $this->aantalTrekkingenInSessie($startTrekking);
        $aantalReeksenInSessie = $this->aantalReeksenInSessie();



        return [
            'aantal' => $aantal,
            'aantalPaginas' => $aantalPaginas,
            'data' => $trekkingen,
            'startTrekking' => $startTrekking,
            'getrokkenGetallen' => $getrokkenGetallen,
            'mijnlottoreeksen' => $mijnlottoreeksen,
            'laatsteTrekking' => $laatsteTrekking,
            'aantalTrekkingenInSessie' => $aantalTrekkingenInSessie,
            'aantalReeksenInSessie' => $aantalReeksenInSessie,
            'laatsteTrekkingID' => $laatsteTrekking->id
        ];
    }

    public function zoekTrekkingen($tel, $aantal, $pagina, $aantalPerPagina)
    {

        if ($tel) {
            $dbSql = sprintf("SELECT COUNT(1) AS aantal FROM trekking");
            $aantal = DB::SELECT($dbSql)[0]->aantal;
            $aantalPaginas = ceil($aantal / $aantalPerPagina);

            return [
                'aantal' => $aantal,
                'aantalPaginas' => $aantalPaginas,
            ];
        } else {
            // order by 
            $dbOrder = 'ORDER BY datum DESC';

            // --- limit
            $aantalPaginas = ceil($aantal / $aantalPerPagina);
            if ($pagina > $aantalPaginas)
                $pagina = $aantalPaginas;
            $dbLimit = sprintf("LIMIT %s,%s", $pagina * $aantalPerPagina, $aantalPerPagina);

            $dbSql = sprintf("
        SELECT *
        FROM trekking
        %s
        %s
    ", $dbOrder, $dbLimit);
        }
        // echo($dbSql); die();
        return DB::select($dbSql);
    }

    public function getTrekking($id)
    {
        return DB::select(sprintf("
        SELECT id, datum, g1, g2, g3, g4, g5, g6, res
        FROM trekking
        WHERE id=%d
        ", $id))[0];
    }

    public function setTrekking($id, $datum, $g1, $g2, $g3, $g4, $g5, $g6, $res)
    {
        if ($id == 0) {
            $trekking = new Trekking();
            $trekking->datum = $datum;
            $trekking->g1 = $g1;
            $trekking->g2 = $g2;
            $trekking->g3 = $g3;
            $trekking->g4 = $g4;
            $trekking->g5 = $g5;
            $trekking->g6 = $g6;
            $trekking->res = $res;
            $trekking->save();
            $dta['id'] = $trekking->id;
            $dta['succes'] = true;

        } else {
            $trekking = Trekking::find($id);
            $trekking->datum = $datum;
            $trekking->g1 = $g1;
            $trekking->g2 = $g2;
            $trekking->g3 = $g3;
            $trekking->g4 = $g4;
            $trekking->g5 = $g5;
            $trekking->g6 = $g6;
            $trekking->res = $res;
            $trekking->update();
            $dta['succes'] = true;
        }
        return $dta;
    }

    /**
     * public function verwijderTrekking()
     * verwijdert opgegeven trekking uit tabel 'trekking'
     * @param $id: integer
     * @return array
     */
    public function verwijderTrekking($id)
    {
        $dta = ['succes' => false, 'boodschap' => ''];
        Trekking::where('id', '=', $id)->delete();
        $dta['succes'] = true;
        return $dta;
    }

    /**
     * public function getrokkenGetallen()
     * @return array
     * 
     */

    public function getrokkenGetallen($startTrekking)
    {
        $getrokkenGetallen = [];
        $dbWhere = sprintf("id>= %d", $startTrekking);

        $dbSql = sprintf(
            "SELECT id,datum,g1,g2,g3,g4,g5,g6,res
            FROM trekking
            %s 
            ORDER BY datum DESC 
            ",
            "WHERE " . $dbWhere
        );
        $dbRslt = DB::select($dbSql);

        foreach ($dbRslt as $dbRij) {
            array_push($getrokkenGetallen, intval($dbRij->g1));
            array_push($getrokkenGetallen, intval($dbRij->g2));
            array_push($getrokkenGetallen, intval($dbRij->g3));
            array_push($getrokkenGetallen, intval($dbRij->g4));
            array_push($getrokkenGetallen, intval($dbRij->g5));
            array_push($getrokkenGetallen, intval($dbRij->g6));
        }
        $getrokkenGetallen = array_unique($getrokkenGetallen);
        sort($getrokkenGetallen);
        return ($getrokkenGetallen);

    }
    public function trekkingenInSessie($startTrekking)
    {
        return DB::select(sprintf("
        SELECT id, datum, g1, g2, g3, g4, g5, g6, res
        FROM trekking
        WHERE id>=%d
        ORDER BY datum DESC
        ", $startTrekking));

    }
    public function getStartTrekking()
    {
        $dbSql = sprintf("
        SELECT trekkingID
        FROM winstverdeling
        ORDER BY trekkingID desc
        LIMIT 1
    ");

        $lastIDUitbetaling = DB::select($dbSql)[0]->trekkingID;

        $dbSql = sprintf("
        SELECT id
        FROM trekking
        ORDER BY datum desc
        LIMIT 1
    ");
        $lastIDTrekking = DB::select($dbSql)[0]->id;


        // controleren of deze trekking de laatste is. Indien wel moeten we de vorige trekkingID van winstverdeling gebruiken


        if (intval($lastIDTrekking) === intval($lastIDUitbetaling)) {
            $dbSql = sprintf("
            SELECT trekkingID
            FROM winstverdeling
            WHERE trekkingID < %d
            ORDER BY trekkingID desc
            LIMIT 1
        ", $lastIDUitbetaling);

            // we gebruiken deze trekking ID om de volgende trekking ID te vinden 
            $trekkingID = DB::select($dbSql)[0]->trekkingID;

            $dbSql = sprintf("
                SELECT id
                FROM trekking
                WHERE id > %d
                ORDER BY id
                LIMIT 1
            ", $trekkingID);

            return DB::select($dbSql)[0]->id;

        } else {
            $dbSql = sprintf("
            SELECT id
            FROM trekking
            WHERE id > %d
            ORDER BY id
            LIMIT 1
        ", $lastIDUitbetaling);
            return DB::select($dbSql)[0]->id;
        }





    }

    public function aantalReeksenInSessie()
    {
        //aantal reeksen in sessie
        $dbSql = sprintf(
            "SELECT COUNT(1) as aantal
                    FROM reeks
                    WHERE NOT obsolete
                    "
        );
        return DB::select($dbSql)[0]->aantal;
    }

    public function aantalTrekkingenInSessie($startTrekking)
    {

        //aantal trekkingen in sessie
        $dbSql = sprintf(
            "SELECT COUNT(1) as aantal
            FROM trekking
            WHERE id >= %d
            ",
            $startTrekking
        );
        return DB::select($dbSql)[0]->aantal;
    }

    public function sendEmail()
    {
        $dbSql = sprintf("
            SELECT r.userID, u.fullname, u.name, u.email FROM reeks r
            INNER JOIN users u ON r.userID = u.id
            WHERE r.obsolete = false
        ");
        $spelers = DB::select($dbSql);

        $dta['spelers'] = [];
        foreach ($spelers as $speler) {
            $email = $speler->email;
            $name = $speler->name;
            if ($email == 'luc.boerjan@mail.bredene.be' || $email == 'luc.boerjan@telenet.be' || $email == 'luc.boerjan@proviron.com' || $email == 'luc.boerjan@37.152.127.180' || $name=="Dimitri") {
                
                $recipientEmail = $speler->email;
                Mail::to($recipientEmail)->send(new MyEmail());
           
                array_push($dta['spelers'], $speler->fullname . ' (' . $speler->email .')');
            }
        }
        $dta['succes'] = true;
        return $dta;
    }

}
