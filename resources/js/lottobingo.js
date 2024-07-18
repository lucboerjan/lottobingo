$(() => {
    if ($('#lottobingo').length) {
        LOTTOBINGO.init(0);

    }
});

const LOTTOBINGO = {
    csrfToken: $('meta[name="csrf-token"]').attr('content'),
    boodschappen: [],
    getrokkenGetallen: [],




    init: (pagina) => {

        // werk de user bij met de laatste timestamp van het bezoek
        let frmDta = {
            id: userData.id
        }
        


        fetch('/jxSetVisit', {
            method: 'post',
            body: JSON.stringify(frmDta),

            headers: {
                "X-CSRF-Token": LOTTOBINGO.csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        }).then((response) => {
            return response.json();
        }).then((res) => {
            if (res.succes) {
                console.log(res);

                
            }
        }).catch((error) => {
            console.log(error);
        });

        //paginering
        $('#uitbetalingen').on('click', '#paginering a', function (evt) {
            LOTTOBINGO.uitbetaling($(this).data('pagina'));
        });

        //ophalen boodschappen
        LOTTOBINGO.getBoodschappen();
        frmDta = {
            pagina: pagina
        }
        let rangschikking = [];

        fetch('jxLottobingoOverzicht', {
            method: 'post',
            body: JSON.stringify(frmDta),
            headers: {
                "X-CSRF-Token": LOTTOBINGO.csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        }).then((response) => {

            return response.json();
        }).then((res) => {

            LOTTOBINGO.getrokkenGetallen = res.getrokkengetallen;

            let startDatum = LOTTOBINGO.datum(res.startdatum[0]['datum']);
            let eindDatum = LOTTOBINGO.datum(res.einddatum[0]['datum']);
            $('.reeksen').html(LOTTOBINGO.boodschappen.resultaten_titel_p1 + ' ' + startDatum + ' ' + LOTTOBINGO.boodschappen.resultaten_titel_p2 + ' ' + eindDatum);

            let lijst = $('#resultaten');
            // $(lijst).empty();

            let inhoud = `<table id="reeksResultaten"><tr>
                <td>`;
            inhoud += LOTTOBINGO.boodschappen.resultaten_speler;
            inhoud += `</td>
                <td>G1</td>
                <td>G2</td>
                <td>G3</td>
                <td>G4</td>
                <td>G5</td>
                <td>G6</td>
                <td>G7</td>
                <td>G8</td>
                <td>G9</td>
                <td>G10</td>
                <td>Juist</td>
            </tr> `;

            res.reeksen.forEach(reeks => {
                inhoud += `<tr><td class="reeksNaam">`;
                inhoud += reeks.fullname;
                inhoud += `</td>`;
                inhoud += `<td class="reeksNummer">`;
                inhoud += reeks.g1;
                inhoud += `</td>`;
                inhoud += `<td class="reeksNummer">`;
                inhoud += reeks.g2;
                inhoud += `</td>`;
                inhoud += `<td class="reeksNummer">`;
                inhoud += reeks.g3;
                inhoud += `</td>`;
                inhoud += `<td class="reeksNummer">`;
                inhoud += reeks.g4;
                inhoud += `</td>`;
                inhoud += `<td class="reeksNummer">`;
                inhoud += reeks.g5;
                inhoud += `</td>`;
                inhoud += `<td class="reeksNummer">`;
                inhoud += reeks.g6;
                inhoud += `</td>`;
                inhoud += `<td class="reeksNummer">`;
                inhoud += reeks.g7;
                inhoud += `</td>`;
                inhoud += `<td class="reeksNummer">`;
                inhoud += reeks.g8;
                inhoud += `</td>`;
                inhoud += `<td class="reeksNummer">`;
                inhoud += reeks.g9;
                inhoud += `</td>`;
                inhoud += `<td class="reeksNummer">`;
                inhoud += reeks.g10;
                inhoud += `</td>`;
                inhoud += `<td class="score">`;
                inhoud += `</td>`;

                inhoud += `</tr>`;

            });

            inhoud += `</table>`;
            $(lijst).append($('<div>').html(inhoud));

            $(".reeksNummer").each(function () {

                $(this).removeClass('getalIsGetrokken');
                let idx = parseInt($(this).text());
                if (LOTTOBINGO.getrokkenGetallen.indexOf(idx) !== -1) {
                    $(this).addClass('getalIsGetrokken');
                }
            });

            $('#reeksResultaten tr').each(function (index, row) {
                // Iterate through each <td> within the current <tr>

                if (index > 0) {
                    let aantalJuist = 0;
                    $(row).find('td').each(function (index, cell) {
                        if ($(cell).hasClass('getalIsGetrokken')) {
                            aantalJuist += 1;
                        }
                    });

                    $(this).find('td.score').text(aantalJuist);
                    //array rangschikking invullen
                    rangschikking.push({ key: $(this).find('td.reeksNaam').text(), value: aantalJuist });
                }
            });


            //rangschikking weergeven
            rangschikking.sort((a, b) => b.value - a.value);
            inhoud = `<table id="rangschikking"><tr>
                            <td colspan="3">`
            inhoud += LOTTOBINGO.boodschappen.rangschikking_titel;
            inhoud += `</td></tr> `;
            let idx = 0;
            rangschikking.forEach(reeks => {
                idx += 1;

                inhoud += `<tr><td class="reeksNummer">`;
                inhoud += idx;

                inhoud += `<td class="reeksNaam">`;
                inhoud += reeks.key;
                inhoud += `</td>`;
                inhoud += `<td class="score">`;
                inhoud += reeks.value;
                inhoud += `</td></tr>`;
            });
            inhoud += `</table>`;

            lijst = $('#rangschikking');
            $(lijst).append($('<div>').html(inhoud));


            //getrokken getallen inkleuren + titel
            $('#thGetrokkenGetallen').html(LOTTOBINGO.boodschappen.thGetrokkenGetallen);

            $(".getrokkenGetal").each(function () {
                $(this).removeClass('getalIsGetrokken');
                idx = parseInt($(this).prop('id').split('_')[1]);
                if (LOTTOBINGO.getrokkenGetallen.indexOf(idx) !== -1) {
                    $(this).addClass('getalIsGetrokken');
                }
            });

            //inhoud van  de pot
            let bedrag = res.aantaltrekkingeninsessie * res.aantalreekseninsessie;
            $('#inhoudPot').html('&nbsp;' + LOTTOBINGO.boodschappen.teVerdienenBedrag + ' : &euro;&nbsp' + bedrag);


            //winstverdeling per speler
            inhoud = LOTTOBINGO.boodschappen.winstperspeler;
            inhoud += `<table>`;

            res.winstperspeler.forEach(reeks => {
                inhoud += `<tr><td class="reeksNaam">`;
                inhoud += reeks.fullname;
                inhoud += `</td>`;
                inhoud += `<td class="bedrag">`;
                inhoud += reeks.bedrag;
                inhoud += `</tr></td>`;
            });
            inhoud += `</table>`;
            $('#winstPerSpeler').html(inhoud);




            // trekkingen in de sessie
            inhoud = LOTTOBINGO.boodschappen.trekkingeninsessie;
            inhoud += `<table>`;

            res.trekkingeninsessie.forEach(trekking => {
                inhoud += `<tr><td class="reeksNaam">`;
                inhoud += LOTTOBINGO.datum(trekking.datum);
                inhoud += `</td>`;
                inhoud += `<td class="trekkingGetal">`;
                inhoud += trekking.g1;
                inhoud += `</td>`;
                inhoud += `<td class="trekkingGetal">`;
                inhoud += trekking.g2;
                inhoud += `</td>`;
                inhoud += `<td class="trekkingGetal">`;
                inhoud += trekking.g3;
                inhoud += `</td>`;
                inhoud += `<td class="trekkingGetal">`;
                inhoud += trekking.g4;
                inhoud += `</td>`;
                inhoud += `<td class="trekkingGetal">`;
                inhoud += trekking.g5;
                inhoud += `</td>`;
                inhoud += `<td class="trekkingGetal">`;
                inhoud += trekking.g6;
                inhoud += `<td class="score">`;
                inhoud += trekking.res;
                inhoud += `</td>`;
                inhoud += `</tr></td>`;
            });
            inhoud += `</table>`;
            $('#trekkingenInSessie').html(inhoud);



            //uitbetalingen
            LOTTOBINGO.uitbetaling(0);


        }).catch((error) => {
            console.log(error);
        })



    },


    //datum omzetten naar dd-mm-jjjj
    datum: (dtm) => {
        if (dtm) {
            let tmp = dtm.split('-');
            tmp.reverse()
            return tmp.join('-');
        }
        else {
            return '-';
        }
    },

    /* --- FOUTBOODSCHAPPEN / LABELS --- */
    getBoodschappen: () => {
        fetch('/jxLottobingoBoodschappen', {
            method: 'post',
            body: new FormData(),

            headers: {
                "X-CSRF-Token": LOTTOBINGO.csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        }).then((response) => {
            return response.json();
        }).then((res) => {
            if (res.succes) {
                res.boodschappen.split(',').forEach(boodschap => {
                    let tmp = boodschap.split(':');
                    LOTTOBINGO.boodschappen[tmp[0]] = tmp[1];

                });
                console.log(LOTTOBINGO.boodschappen);
            }
        }).catch((error) => {
            console.log(error);
        });
    },

    uitbetaling: (pagina) => {
        //omdat deze lijst met paginering werkt wordt deze apart aangeroepen
        let frmDta = {
            pagina: pagina
        }
        fetch('/jxUitbetalingen', {
            method: 'post',
            body: JSON.stringify(frmDta),

            headers: {
                "X-CSRF-Token": LOTTOBINGO.csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        }).then((response) => {
            return response.json();
        }).then((res) => {
            if (res.succes) {
                let inhoud = LOTTOBINGO.boodschappen.uitbetalingen;
                inhoud += `<table>`;
                res.uitbetalingen.forEach(uitbetaling => {
                    inhoud += `<tr><td class="reeksNaam">`;
                    inhoud += uitbetaling.fullname;
                    inhoud += `</td>`;
                    inhoud += `<td class="datum">`;
                    inhoud += LOTTOBINGO.datum(uitbetaling.datum);
                    inhoud += `</td>`;
                    inhoud += `<td class="bedrag">`;
                    inhoud += uitbetaling.bedrag;
                    inhoud += `</tr></td>`;
                });
                inhoud += `</table>`;
                $('#uitbetalingen').html(inhoud);

                //paginering
                if (res.aantalpaginas > 0) {

                    $('#uitbetalingen').append(
                        $('<div>').css('text-align', 'center')
                            //.addClass('mt-1')
                            .append(PAGINERING.pagineer(res.pagina, res.knoppen, res.aantalpaginas))
                    );
                }

            }
        }).catch((error) => {
            console.log(error);
        });

    }
}
