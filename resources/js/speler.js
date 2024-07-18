$(() => {
    if ($('#spelers').length) {
        SPELERS.init();
    }
});

const SPELERS = {
    csrfToken: $('meta[name="csrf-token"]').attr('content'),
    currentPeriode: '',


    init: () => {

        var currentDate = new Date();
        let val2use = ('0' + (currentDate.getMonth() + 1)) + "/" + currentDate.getFullYear();
        SPELERS.currentPeriode = val2use.slice(-7);

        // toevoegen
        $('#toevoegen').on('click', () => { SPELERS.bewerk(0, 'nieuw') });

        // bewerken
        $('body').on('click', '.spelerBewerk', () => { SPELERS.bewerk(row.id, 'bewerk') });

        // uitbetaling verwijderen

        //$('#overzichtSpelers').on('click', '.betalingVerwijderen', function (evt) { SPELERS.verwijderBetaling($(this).parent().parent().attr('id').split('_')[1]); });
        //$('#betalingVerwijderen').on('click', () => { SPELERS.verwijderBetaling($(this).parent().parent().attr('id').split('_')[1]); });



        //betaling
        $('#betalingToevoegen').on('click', () => { SPELERS.betalingToevoegen() });

        $('#overzichtSpelers').on('click', '.betalingVerwijderen', function (evt) { SPELERS.verwijderBetaling($(this).attr('id').split('_')[1]); });


        let frmDta = {
        }

        fetch('jxSpelersOverzicht', {
            method: 'post',
            body: JSON.stringify(frmDta),
            headers: {
                "X-CSRF-Token": SPELERS.csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        }).then((response) => {

            return response.json();
        }).then((res) => {

            let lijst = $('#overzichtSpelers');
            $(lijst).empty();
            let idx = 0;

            let inhoud = '<table class="table table-striped">';
            inhoud += '<thead  class="table-light"><th colspan="2">&nbsp;</th><th>Speler</th><th>email</th><th>Betaling</th><th>Laatste bezoek</th><th>&nbsp;</th></thead>';

            res.data.forEach(speler => {
                let spelersLevel = parseInt(speler.level);
                //$(lijst).append($('<div>').addClass('card card-body mb-1 trekking'));

                let class2add = spelersLevel & 2 ? 'bi bi-pen' : 'bi bi-eye';
                let name2use = "chkBetaling_" + speler.id;
                let periode = (speler.periode).slice(-2) + "/" + (speler.periode).substring(0, 4);
                idx++;
                inhoud += `
                        <tr>
                            <td><label>${('0' + idx).slice(-2)}</label></td>
                            <td><label for="${name2use}"><input type="checkbox" id="${name2use}"</td>
                            <td class="spelerFullname">${speler.fullname}</td>
                            <td class="spelerEmail">${speler.email}</td>
                            <td class="betaling">${periode}</td>
                            <td class="timestamp">${speler.laatstebezoek}</td>
                            <td><button class="btn btn-secondary `
                inhoud += class2add;
                inhoud += `"></button>&nbsp;<button class="spelerBewerk btn btn-primary"><i class ="bi bi-pencil-square"></i></button>&nbsp;
                                      <button id="${'verwijder_'+speler.id}" class="betalingVerwijderen btn btn-secondary"><i class="bi bi-scissors"></i></button></td>
                        </tr>
                        `
            });

            inhoud += '</table>';

            $(lijst).append($('<div>').addClass('card-body mb-1 trekking').html(inhoud));


            $("[id^=chkBetaling]").checkboxradio();


            //checkbox betaling enkel actief indien laatste betaling niet voor de huidige periode is
            $('[id^=chkBetaling]').each(function () {

                var checkboxId = $(this).attr('id');
                let val2use = checkboxId.split('-')[1];
                var betalingHtml = $(this).parent().parent().siblings('.betaling').html();
                
                if (betalingHtml == SPELERS.currentPeriode) {
                    $(this).prop('disabled', true).checkboxradio('refresh');;
                }
            });

            $('[id^=chkBetaling]').on('click', function () {
                $("#betalingToevoegen").prop('disabled', true);
                $('[id^=chkBetaling]:checkbox:checked').each(function () {
                    if ($(this).prop('checked')) {
                        $("#betalingToevoegen").prop('disabled', false);

                    }
                });

            });

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
        fetch('jxSpelerGet', {
            method: 'post',
            body: JSON.stringify(frmDta),
            headers: {
                "X-CSRF-Token": SPELERS.csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        }).then((response) => {
            return response.json();
        }).then((res) => {
            console.log(res);
        }).catch((error) => {
            console.log(error);
        })

    },

    betalingToevoegen: () => {
        let betaling = [];
        let val2use = 0;


        $('[id^=chkBetaling]:checkbox:checked').each(function () {
            var checkboxId = $(this).attr('id');
            val2use = checkboxId.split('_')[1];
            betaling.push(val2use);

            // Or you can perform other actions with the checkbox ID
        });
        let frmDta = {
            'ids': betaling,
            'periode': SPELERS.currentPeriode
        }
        fetch('/jxBetalingToevoegen', {
            method: 'post',
            body: JSON.stringify(frmDta),
            headers: {
                "X-CSRF-Token": SPELERS.csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        }).then((response) => {
            return response.json();
        }).then((res) => {
            console.log(res);
            if (res.succes) {

                $('[id^=chkBetaling]:checkbox:checked').each(function () {
                    $(this).prop('checked', false).prop('disabled', true).checkboxradio('refresh');
                    $(this).parent().parent().siblings('.betaling').html(res.periode);
                });
                $("#betalingToevoegen").prop('disabled', true);

            }

        }).catch((error) => {
            console.log(error);
        })
    },

    verwijderBetaling: (reeksID) => {

        //verwijder de laatste betaling voor de speler met de opgegeven id
        let frmDta = {
            'reeksID': reeksID
        }
        fetch('/jxBetalingVerwijderen', {
            method: 'post',
            body: JSON.stringify(frmDta),
            headers: {
                "X-CSRF-Token": SPELERS.csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        }).then((response) => {
            return response.json();
        }).then((res) => {
            console.log(res);
            if (res.succes) {
                console.log(res)

            }

        }).catch((error) => {
            console.log(error);
        })
    }








}