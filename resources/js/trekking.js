$(() => {
    if ($('#trekking').length) {
        TREKKING.init();
    }
});

const TREKKING = {
    csrfToken: $('meta[name="csrf-token"]').attr('content'),
    labels: null,
    geduld: null,
    trekkingID: null,
    boodschappen: [],
    getrokkenGetallen: [],
    actieveTrekking: [],
    aantalTrekkingenInSessie: 0,
    aantalReeksenInSessie: 0,

    init: () => {


        //ophalen boodschappen
        TREKKING.getBoodschappen();

        // toevoegen
        $('#trekkingNieuw').on('click', () => { TREKKING.bewerk(0, 'nieuw') });

        // bewerken
        $('#overzichtTrekkingen').on('click', '.trekkingBewerk', function (evt) { TREKKING.bewerk($(this).parent().parent().parent().attr('id').split('_')[1], 'bewerk'); });
        $('#overzichtTrekkingen').on('click', '.trekkingVerwijder', function (evt) { TREKKING.bewerk($(this).parent().parent().parent().attr('id').split('_')[1], 'verwijder'); });
        // eigen lottoreeksen
        $('#overzichtTrekkingen').on('click', '.checkEigenLottoreeksen', function (evt) { TREKKING.checkEigenLottoreeksen($(this).parent().parent().parent().attr('id').split('_')[1]); });

        // knoppen modaal
        $('body').on('click', '#bewerkBewaar', TREKKING.bewerkBewaar);
        $('body').on('click', '#bewerkAnnuleer', () => { MODAAL.verberg(); TREKKING.trekkingID = null; });
        $('body').on('click', '#decrease, #increase', function () { TREKKING.spinreserve($(this).attr('id')) });


        // knop mail versturen

        $('#mailVersturen').on('click', () => { TREKKING.sendEmail() });


        //paginering
        $('#overzichtTrekkingen').on('click', '#paginering a', function (evt) {
            TREKKING.laadTrekkingen($(this).data('pagina'));
        });

        //laad de eerste pagina met trekkingen
        TREKKING.laadTrekkingen(0);
    },

    laadTrekkingen: (pagina) => {
        let frmDta = {
            pagina: pagina
        }

        fetch('jxTrekkingenOverzicht', {
            method: 'post',
            body: JSON.stringify(frmDta),
            headers: {
                "X-CSRF-Token": TREKKING.csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        }).then((response) => {

            return response.json();
        }).then((res) => {
            TREKKING.getrokkenGetallen = res.getrokkenGetallen;
            TREKKING.aantalTrekkingenInSessie = res.aantalTrekkingenInSessie;
            TREKKING.aantalReeksenInSessie = res.aantalReeksenInSessie;

            let lijst = $('#overzichtTrekkingen');
            $(lijst).empty();
            res.data.forEach(record => {
                let disabled = true;
                if (parseInt(res.laatsteTrekkingID) == parseInt(record.id)) {
                    disabled = false;
                }


                let inhoud = `
                    <table>
                        <tr>
                            <td class="datumTrekking">${TREKKING.datum(record.datum)}</td>
                            <td class="getrokkenGetal">${record.g1}</td>
                            <td class="getrokkenGetal">${record.g2}</td>
                            <td class="getrokkenGetal">${record.g3}</td>
                            <td class="getrokkenGetal">${record.g4}</td>
                            <td class="getrokkenGetal">${record.g5}</td>
                            <td class="getrokkenGetal">${record.g6}</td>
                            <td class="reserveGetal">${record.res}</td>
                        </tr>
                    </table>
                `
                $(lijst).append($('<div>').addClass('card mb-1 trekking').prop('id', `trekkingID_${record.id}`)
                    .append($('<div>').addClass('card-body position-relative')

                        .append($('<div>').addClass('float-end').append(
                            $('<button>').addClass('trekkingBewerk btn btn-primary me-1').prop('disabled', disabled)
                                .append($('<i>').addClass('bi bi-pencil-square'))
                        )
                            .append(
                                $('<button>').addClass('trekkingVerwijder btn btn-secondary me-1').prop('disabled', disabled)
                                    .append($('<i>').addClass('bi bi-scissors'))
                            )
                            .append(
                                $('<button>').addClass('checkEigenLottoreeksen btn btn-secondary').prop('id', `checkID_${record.id}`)
                                    .append($('<i>').addClass('bi bi-database-check'))
                            )
                        )
                        .append($('<div>').addClass('card-title').html(inhoud))))
            });


            //eigenlottoreeksen
            lijst = $('#eigenLottoReeksen');
            $(lijst).empty();

            let inhoud = `
            <table>
            
            `;

            res.mijnlottoreeksen.forEach(reeks => {
                inhoud += `
                <tr>
                    <td class="mijnGetal">${reeks.g1}</td>
                    <td class="mijnGetal">${reeks.g2}</td>
                    <td class="mijnGetal">${reeks.g3}</td>
                    <td class="mijnGetal">${reeks.g4}</td>
                    <td class="mijnGetal">${reeks.g5}</td>
                    <td class="mijnGetal">${reeks.g6}</td>
                </tr>
                `
            });

            inhoud += `
                </table>
            
                `

            $(lijst).append($('<div>').addClass('card mb-3 centercolumn')
                .append($('<div>').addClass('card-body position-relative centercolumn').html(TREKKING.boodschappen.mijnLottoReeksen))

                .append($('<div>').addClass('card-title ms-3').html(inhoud)));




            let idx = 0;
            //starttrekking
            $(".trekking").each(function () {
                idx = $(this).prop('id').split('_')[1];
                if (res.startTrekking == idx) {
                    $(this).addClass('isStartTrekking');
                }
            });

            //getrokken getallen inkleuren + titel
            $('#thGetrokkenGetallen').html(TREKKING.boodschappen.thGetrokkenGetallen);

            $(".getrokkenGetal").each(function () {
                $(this).removeClass('getalIsGetrokken');
                idx = parseInt($(this).prop('id').split('_')[1]);
                if (TREKKING.getrokkenGetallen.indexOf(idx) !== -1) {
                    $(this).addClass('getalIsGetrokken');
                }
            });

            //mijn lottoreeksen controleren
            TREKKING.actieveTrekking = res.actieveTrekking;
            TREKKING.checkEigenNummers();

            //inhoud van  de pot
            let bedrag = res.aantalTrekkingenInSessie * res.aantalReeksenInSessie
            $('#inhoudPot').html('&nbsp;' + TREKKING.boodschappen.teVerdienenBedrag + ' : &euro;&nbsp' + bedrag);

            //paginering
            if (res.aantalpaginas > 0) {

                $(overzichtTrekkingen).append(
                    $('<div>').css('text-align', 'center')
                        //.addClass('mt-1')
                        .append(PAGINERING.pagineer(res.pagina, res.knoppen, res.aantalpaginas))
                );
            }

        }).catch((error) => {
            console.log(error);
        })
    },

    bewerk: (id, mode) => {
        //toevoegen-bewerken-verwijderen

        let frmDta = {
            'id': id,
            'mode': mode
        }



        fetch('jxTrekkingGet', {
            method: 'post',
            body: JSON.stringify(frmDta),
            headers: {
                "X-CSRF-Token": TREKKING.csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        }).then((response) => {
            return response.json();
        }).then((res) => {
            TREKKING.labels = res.labels;
            TREKKING.geduld = res.geduld;

            if (res.succes) {
                let disabled = '';

                switch (res.mode) {
                    case 'bewerk':
                        MODAAL.kop(TREKKING.labels.bewerkTitelBewerk);
                        break;
                    case 'nieuw':
                        MODAAL.kop(TREKKING.labels.bewerkTitelNieuw);

                        break;
                    case 'verwijder':
                        disabled = 'disabled'
                        MODAAL.kop(TREKKING.labels.bewerkTitelVerwijder);
                        break;
                }


                let inhoud = '';
                let row2use = 0;
                let idx = 0;
                let name2use = '';
                let getrokken = [];
                getrokken.push(parseInt(res.trekking.g1));
                getrokken.push(parseInt(res.trekking.g2));
                getrokken.push(parseInt(res.trekking.g3));
                getrokken.push(parseInt(res.trekking.g4));
                getrokken.push(parseInt(res.trekking.g5));
                getrokken.push(parseInt(res.trekking.g6));

                inhoud = `
                <form id="bewerkTrekking">
                <div id="bewerkBoodschap" class="alert alert-warning invisible"></div>
                <input type="hidden" id="bewerkID" value="${res.trekking.id}">
                <input type="hidden" id="BewerkMode" value="${res.mode}">
                <div class="mb-3">
                    <label class="form-label" for="bewerkDatum">${TREKKING.labels.bewerkDatum}</label>
                    <input type="date" class="form-control" type="text" id="datum" value="${res.trekking.datum}" >
                </div>
                `
                let checked = '';
                for (var j = 1; j <= 5; j++) {
                    row2use = 'rij-' + j;
                    inhoud += '<fieldset><p id="' + row2use + '">';

                    for (var i = 1; i <= 10; i++) {

                        idx = ((j - 1) * 10) + i;

                        if (idx <= 45) {
                            checked = getrokken.indexOf(idx) !== -1 ? ' checked' : '';
                            name2use = "checkbox-" + idx;
                            inhoud += '<label id="lbl_' + name2use + '" for="' + name2use + '">' + idx + '</label><input type="checkbox" id="' + name2use + '" ' + checked + '>';
                        }

                        if (idx == 50) {
                            inhoud += '<label for="spinner" id="lbl4spinner">Reservegetal</label>';

                            inhoud += '<span><span  id="decrease"><button class="btn btn-primary"><i class="bi bi-chevron-double-down"></i></button></span>';
                            inhoud += '<input id="spinner" name="value" aria-value=' + res.trekking.res + '>';
                            inhoud += '<span  id="increase"><button class="btn btn-primary"><i class="bi bi-chevron-double-up"></i></button><span><span>';
                        }
                    }
                    inhoud += '</p></fieldset></form>';

                }

                MODAAL.inhoud(inhoud);
                let voet = '';
                if (res.mode === 'verwijder')
                    voet += MODAAL.knop('bewerkBewaar', 'primary', 'trash3', TREKKING.labels.bewerkVerwijder);
                else
                    voet += MODAAL.knop('bewerkBewaar', 'primary', 'check-square', TREKKING.labels.bewerkBewaar);
                voet += MODAAL.knop('bewerkAnnuleer', 'secondary', 'x-square', TREKKING.labels.bewerkAnnuleer);
                MODAAL.voet(voet);
                MODAAL.toon();

                $("[id^=checkbox-]").checkboxradio();
                $("[id^=lbl_checkbox-]").css('width', '9.5%')

                $("#lbl4spinner").css('padding-left', '15px');
                $("#lbl4spinner").css('padding-right', '5px');
                $("#spinner").spinner({ min: 1, max: 45 }).val(1);

                $("#spinner").val(res.trekking.res ? res.trekking.res : 1);

                $("[id^=checkbox-]").on('click', function () {

                    //check of reservergetal niet voorkomt in de lijst van de getrokken getallen
                    //check of de bewaar knop mag geactiveerd worden.
                    TREKKING.admin_chk_changes();
                });
                if (mode == 'nieuw') $('#bewerkBewaar').attr('disabled', 'disabled');

                $("#spinner").spinner({
                    change: function (event, ui) { TREKKING.admin_chk_changes(); }
                });


            };
        }).catch((error) => {
            console.log(error);
            MODAAL.verberg();
        })


    },

    admin_chk_changes: () => {

        $('#bewerkBoodschap').removeClass('visible').addClass('invisible').empty()
        var numberOfChecked = $('[id^=checkbox-]:checkbox:checked').length;
        if (numberOfChecked >= 6) {
            $("[id^=checkbox-]:not(:checked)").attr('disabled', 'disabled');
            $('#bewerkBewaar').removeAttr('disabled');
            $('#bewerkBewaar').attr('enabled', 'enabled');
        } else {
            $('[id^=checkbox-]').removeAttr('disabled');
            $('#bewerkBewaar').removeAttr('enabled');
            $('#bewerkBewaar').attr('disabled', 'disabled');
        }

        for (var idx = 1; idx <= 45; idx++) {
            let name2use = "checkbox-" + idx;

            if ($('#' + name2use).is(':checked') == true) {

                if ($('#spinner').val() == idx) {
                    $('#bewerkBewaar').removeAttr('enabled');
                    $('#bewerkBewaar').attr('disabled', 'disabled');

                    $('#bewerkBoodschap').removeClass('invisible').addClass('visible').empty().html('Reservegetal komt voor bij de getrokken getallen');

                }
                ;
            }
        }
    },

    bewerkBewaar: () => {
        let idx = 0;
        let getrokken = [];
        let val2use = 0;

        $('input[type="checkbox"]:checked').each(function () {
            val2use = $(this).attr('id');
            val2use = val2use.split('-')[1];
            getrokken[idx] = val2use;
            idx++;
        });
        let frmDta = {
            'id': $('#bewerkID').val(),
            'mode': $('#BewerkMode').val(),
            'datum': $('#datum').val(),
            'g1': (getrokken[0]),
            'g2': (getrokken[1]),
            'g3': (getrokken[2]),
            'g4': (getrokken[3]),
            'g5': (getrokken[4]),
            'g6': (getrokken[5]),
            'res': $('#spinner').val(),
        };


        fetch('/jxTrekkingSet', {
            method: 'post',
            body: JSON.stringify(frmDta),
            headers: {
                "X-CSRF-Token": TREKKING.csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        }).then((response) => {
            return response.json();
        }).then((res) => {

            if (res.succes) {

                MODAAL.verberg();
                let pagina = 0;
                if ($('.pagination').length > 0) {
                    $('.pagination a').each((ndx, el) => {
                        if ($(el).hasClass('active')) {

                            pagina = $(el).data('pagina');

                        }
                    })
                }
                TREKKING.laadTrekkingen(pagina);
                setTimeout(function () {
                    TREKKING.controleWinnaar();
                }, 1000);


            }
        }).catch((error) => {
            console.log(error);
        });
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

    checkEigenNummers: () => {
        let idx = 0;
        $(".mijnGetal").each(function () {
            $(this).removeClass('getalIsGetrokken');
            $(this).removeClass('reserveGetal');
            idx = parseInt($(this).text());

            if (parseInt(TREKKING.actieveTrekking.g1) == idx) $(this).addClass('getalIsGetrokken');
            if (parseInt(TREKKING.actieveTrekking.g2) == idx) $(this).addClass('getalIsGetrokken');
            if (parseInt(TREKKING.actieveTrekking.g3) == idx) $(this).addClass('getalIsGetrokken');
            if (parseInt(TREKKING.actieveTrekking.g4) == idx) $(this).addClass('getalIsGetrokken');
            if (parseInt(TREKKING.actieveTrekking.g5) == idx) $(this).addClass('getalIsGetrokken');
            if (parseInt(TREKKING.actieveTrekking.g6) == idx) $(this).addClass('getalIsGetrokken');
            if (parseInt(TREKKING.actieveTrekking.res) == idx) $(this).addClass('reserveGetal');
        }
        );

        $('.checkEigenLottoreeksen').each(function () {
            $(this).prop("disabled", false);
            let idx = $(this).attr('id').split('_')[1];
            if (parseInt(idx) == parseInt(TREKKING.actieveTrekking.id)) $(this).prop("disabled", true);

        });



    },


    checkEigenLottoreeksen: (id) => {
        let frmDta = {
            id: id,
            mode: 'check'
        }
        fetch('jxTrekkingGet', {
            method: 'post',
            body: JSON.stringify(frmDta),
            headers: {
                "X-CSRF-Token": TREKKING.csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        }).then((response) => {
            return response.json();
        }).then((res) => {
            TREKKING.actieveTrekking = res.trekking;
            TREKKING.checkEigenNummers();
        }).catch((error) => {
            console.log(error);
        })

    },


    /* --- FOUTBOODSCHAPPEN / LABELS --- */
    getBoodschappen: () => {
        fetch('/jxTrekkingZoekBoodschappen', {
            method: 'post',
            body: new FormData(),

            headers: {
                "X-CSRF-Token": TREKKING.csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        }).then((response) => {
            return response.json();
        }).then((res) => {
            if (res.succes) {
                res.boodschappen.split(',').forEach(boodschap => {
                    let tmp = boodschap.split(':');
                    TREKKING.boodschappen[tmp[0]] = tmp[1];

                });
                //                    console.log(TREKKING.boodschappen);
            }
        });
    },

    // mail versturen naar de gebruikers
    sendEmail: () => {
        fetch('/jxSendEmail', {
            method: 'post',
            body: new FormData(),

            headers: {
                "X-CSRF-Token": TREKKING.csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        }).then((response) => {
            return response.json();
        }).then((res) => {
            console.log(res);

           

            let inhoud = '';
            let email = '';
            inhoud += 'Email verstuurd naar :<br>' ;
            res.spelers.forEach(speler => {
                email = speler.split('(')[1].split(')')[0];
                inhoud+= email + '<br>';
            });

            $('#emailResult').append($('<div>').addClass('alert alert-warning')
            .html(inhoud));

        }).catch((error) => {
            console.log(error);
        });
    },


    controleWinnaar: () => {
        //haal de meespelende reeeksen op en vergelijk de nummers met de getrokken getallen 
        //de model Lottobingo bevat reeds een functie om de actieve reeksen op te halen

        // enkel indien het huidig nummer van de trekking nog niet in de betaling voorkomt, anders volgen er extra uitbetalingen

        fetch('/jxGetActieveReeksen', {
            method: 'post',
            body: new FormData(),

            headers: {
                "X-CSRF-Token": TREKKING.csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        }).then((response) => {
            return response.json();
        }).then((res) => {
            if (parseInt(res.laatsteuitbetaling === TREKKING.actieveTrekking.trekkingID)) return;


            let winnaars = [];
            res.reeksen.forEach(reeks => {
                let aantalJuist = 0;
                for (let i = 1; i <= 10; i++) {
                    let idx = 'g' + i;
                    let value2find = parseInt(reeks[idx]);
                    if (TREKKING.getrokkenGetallen.indexOf(value2find) !== -1) {
                        aantalJuist++;
                    }
                    else {
                        //console.log(reeks.fullname,reeks[idx], parseInt(TREKKING.getrokkenGetallen.indexOf(reeks[idx])) );
                    }

                }
                if (aantalJuist == 10) {
                    winnaars.push(reeks.id);
                }
                //console.log (reeks.fullname, aantalJuist);
            })

            if (winnaars.length) {
                console.log('1 of meerdere winnaars');
                let laatsteuitbetaling = res.laatsteuitbetaling;
                let actieveTrekking = TREKKING.actieveTrekking.id;

                if (parseInt(laatsteuitbetaling) < parseInt(actieveTrekking)) {

                    /***
                     * de lottobingo is gevallen
                     * => winstverdeling
                     * => instelling laatsteuitebetaling bijwerken
                     */
                    let bedragperwinnaar = (parseInt(TREKKING.aantalReeksenInSessie) * parseInt(TREKKING.aantalTrekkingenInSessie)) / parseInt(winnaars.length);
                    let frmDta = {
                        winnaars: winnaars,
                        trekkingID: TREKKING.actieveTrekking.id,
                        bedragperwinnaar: bedragperwinnaar,

                    }

                    fetch('/jxUitbetalingWinnaars', {
                        method: 'post',
                        body: JSON.stringify(frmDta),

                        headers: {
                            "X-CSRF-Token": TREKKING.csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    }).then((response) => {
                        return response.json();
                    }).then((res) => {
                        console.log(res);
                    }).catch((error) => {
                        console.log(error);
                    });

                }
            }
            else {
                console.log('Geen winnaars tot nu toe');
            }

        }).catch((error) => {
            console.log(error);
        });
    },

    spinreserve(action) {
        let val2use = parseInt($('#spinner').val());
        console.log(val2use);
        switch (action) {
            case 'decrease':
                if (val2use >= 2) {
                    val2use -= 1;
                    $('#spinner').val(val2use);
                }
                break;
            case 'increase':
                if (val2use <= 44) {
                    val2use += 1;
                    $('#spinner').val(val2use);
                }
                break;

        }
    }


}