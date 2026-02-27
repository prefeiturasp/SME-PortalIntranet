jQuery(document).ready(function ($) {

    var date = moment().locale('pt-br'); //Get the current date
    var data_formatada = date.format('dddd[,] D [de] MMMM [de] YYYY'); //2014-07-10
    $('.data_agenda').html(data_formatada);
    var data_para_funcao = moment(date).format('YYYYMMDD');

    redebe_data(data_para_funcao);

    $('.calendario-agenda-sec').ionCalendar({
        lang: "pt-br",
        years: "1",

        onReady: function(){
            debugger;
            //$('.container-loading-agenda-secretario').css('display', 'block');
            //$('.calendario-agenda-sec').attr('style', 'display:none !important');
            getAnoMesCalendario()
        },

        onClick: function (date) {
            moment.locale('pt-br');
            $('.data_agenda').html(moment(date).format('dddd[,] D [de] MMMM [de] YYYY'));
            let data_pt_br = moment(date).format('YYYYMMDD');
            redebe_data(data_pt_br);
        }
    });

    function getAnoMesCalendario() {

        debugger;

        var selectedAno= $('.ic__year-select').children("option:selected").val();

        var selectedMes= $('.ic__month-select').children("option:selected").val();
        selectedMes= parseInt(selectedMes)+1;
        if (selectedMes <= 9){
            selectedMes= '0'+selectedMes;
        }else {
            selectedMes= selectedMes.toString();
        }

        var ano_mes = selectedAno+selectedMes;

        montaQueryMesAtual(ano_mes, selectedMes, selectedAno);

    }

    function montaQueryMesAtual(ano_mes, selectedMes, selectedAno) {

        debugger;

        jQuery.ajax({
            url: bloginfo.ajaxurl,
            type: 'post',
            data: {
                action: 'recebeDadosAjax',
                ano_mes: ano_mes,
            },
            success: function (data) {
                var $data = $(data);
                var datas_retornadas_pela_query = $data[2].value;
                //var datas_retornadas_pela_query = input_com_valor.value;

                marcadorCalendario(datas_retornadas_pela_query, selectedMes, selectedAno)

            }
        });

    }

    function marcadorCalendario(array_datas, selectedMes, selectedAno) {



        //console.log('Ollyver ', array_datas);

        //var calendario_montado = $('.ic__day');

        //console.log('Ollyver ', calendario_montado);

        $('.ic__day').each(function (e) {

            var dia_corrente = parseInt(this.textContent);
            if (dia_corrente <= 9) {
                dia_corrente = '0' + dia_corrente;
            }else {
                dia_corrente = dia_corrente.toString();
            }
            var data_completa = dia_corrente+'/'+selectedMes+'/'+selectedAno;

            var datas_agendas = JSON.parse(array_datas);

            //debugger;

            debugger;

            if(jQuery.inArray( data_completa, datas_agendas) >= 0 ){
                //debugger;
                this.innerHTML = '<span class="destaque-evento-agenda aa">'+this.textContent+'</span>';
            }

            debugger;
            //$('.container-loading-agenda-secretario').css('display', 'none');
            //$('.calendario-agenda-sec').attr('style', 'display:block !important');

        });

    }

    function redebe_data(data_recebida) {

        var conteudo_a_ser_exibido = $('#mostra_data');

        jQuery.ajax({
            url: bloginfo.ajaxurl,
            type: 'post',
            data: {
                action: 'montaHtmlListaEventos',
                data_pt_br: data_recebida,
            },

            success: function (data) {
                var $data = $(data);
                conteudo_a_ser_exibido.html($data);
            },
        });
    }
});