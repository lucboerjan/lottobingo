<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Start toevoegen
use Illuminate\Support\Facades\DB;
use App\Models\Trekking;

//use Illuminate\Support\Str;
//use Illuminate\Support\Facades\Hash;

// Einde toevoegen

class Lottobingo extends Model
{
  use HasFactory;

  public function resultaten()
  {
    $oTrekking = new Trekking();

    $dta['reeksen'] = $this->getActieveReeksen();

    //starttrekking en laatste trekking datum ophalen voor titel
    $dbSql = sprintf("
        SELECT datum FROM trekking
        ORDER BY datum DESC
        LIMIT 1
      ");

    $dta['einddatum'] = DB::SELECT($dbSql);

    $startTrekking = $oTrekking->getStartTrekking();
    $dta['starttrekking'] = $startTrekking;

    $dbSql = sprintf("
      SELECT datum FROM trekking
      WHERE id=%d
    ", $startTrekking);
    $dta['startdatum'] = DB::SELECT($dbSql);

    // winst per speler
    $dbSql = sprintf("
    SELECT w.reeksID, usr.fullname, SUM(w.bedrag) as bedrag FROM winstverdeling w
    INNER JOIN reeks rks ON w.reeksID = rks.id
    INNER JOIN users usr ON rks.userID = usr.id
    WHERE NOT rks.obsolete
    GROUP BY w.reeksID, usr.fullname
    ORDER BY bedrag DESC
    ");


    $dta['winstperspeler'] = DB::SELECT($dbSql);
    // lijst van de uitbetalingen

    // $dbSql = sprintf("
    // SELECT w.reeksID, usr.fullname, w.bedrag, trk.datum FROM winstverdeling w
    // INNER JOIN reeks rks ON w.reeksID = rks.id
    // INNER JOIN users usr ON rks.userID = usr.id
    // INNER JOIN trekking trk ON w.trekkingID = trk.id
    // WHERE NOT rks.obsolete
    // ORDER BY trk.datum DESC, usr.fullname LIMIT 10
    // ");
    // $dta['uitbetalingen'] = DB::SELECT($dbSql);

    //trekkingen in de sessie ophalen
    $dta['trekkingeninsessie'] = $oTrekking->trekkingenInSessie($dta['starttrekking']);

    //getrokken getallen ophalen

    $dta['getrokkengetallen'] = $oTrekking->getrokkenGetallen($dta['starttrekking']);
    $dta['aantalreekseninsessie'] = $oTrekking->aantalReeksenInSessie();
    $dta['aantaltrekkingeninsessie'] = $oTrekking->aantalTrekkingenInSessie($startTrekking);

    return $dta;
  }

  public function getActieveReeksen()
  {
    $dbSql = sprintf("
    SELECT rks.id, rks.userID, rks.g1, rks.g2, rks.g3, rks.g4,rks.g5, rks.g6,rks.g7, rks.g8,rks.g9, rks.g10,usr.fullname FROM reeks rks
    JOIN users usr
    ON usr.id = rks.userID
    WHERE NOT obsolete
    ORDER BY usr.fullname
  ");
    return DB::select($dbSql);
  }

  public function getUitbetalingen($pagina, $aantalUitbetalingenPerPagina)
  {

    $tmp = $this->zoekUitbetalingen($tel = true, 1e6, 0, $aantalUitbetalingenPerPagina);

    $aantal = $tmp['aantal'];
    $aantalPaginas = $tmp['aantalPaginas'];
    $uitbetalingen = $this->zoekUitbetalingen($tel = false, $aantal, $pagina, $aantalUitbetalingenPerPagina);

    return [
      'aantal' => $aantal,
      'aantalpaginas' => $aantalPaginas,
      'uitbetalingen' => $uitbetalingen,
      'succes' => true
    ];
  }
  public function zoekUitbetalingen($tel, $aantal, $pagina, $aantalPerPagina)
  {

    if ($tel) {
      $dbSql = sprintf("SELECT COUNT(1) AS aantal FROM winstverdeling");
      $aantal = DB::SELECT($dbSql)[0]->aantal;
      $aantalPaginas = ceil($aantal / $aantalPerPagina);

      return [
          'aantal' => $aantal,
          'aantalPaginas' => $aantalPaginas,
      ];
  } else {

       // where clausule
      $where = ' WHERE NOT rks.obsolete ' ;
      // order by 
      $dbOrder = 'ORDER BY trk.datum DESC, usr.fullname';

      // --- limit
      $aantalPaginas = ceil($aantal / $aantalPerPagina);
      if ($pagina > $aantalPaginas)
          $pagina = $aantalPaginas;
      $dbLimit = sprintf("LIMIT %s,%s", $pagina * $aantalPerPagina, $aantalPerPagina);

      $dbSql = sprintf("
      SELECT w.reeksID, usr.fullname, w.bedrag, trk.datum FROM winstverdeling w
      INNER JOIN reeks rks ON w.reeksID = rks.id
      INNER JOIN users usr ON rks.userID = usr.id
      INNER JOIN trekking trk ON w.trekkingID = trk.id
      %s
  %s
  %s
", $where, $dbOrder, $dbLimit);
  }
  // echo($dbSql); die();
  return DB::select($dbSql);


    // try {
    //   $dbSql = sprintf("
    //   SELECT w.reeksID, usr.fullname, w.bedrag, trk.datum FROM winstverdeling w
    //   INNER JOIN reeks rks ON w.reeksID = rks.id
    //   INNER JOIN users usr ON rks.userID = usr.id
    //   INNER JOIN trekking trk ON w.trekkingID = trk.id
    //   WHERE NOT rks.obsolete
    //   ORDER BY trk.datum DESC, usr.fullname LIMIT 10
    //   ");
    //   $dta['uitbetalingen'] = DB::SELECT($dbSql);
    //   $dta['succes'] = true;
    //   return $dta;
    // } catch (Exception $ex) {

    // }

  }




}
