jQuery(document).ready(function ($) {

    var primeiro_clique = false;

    var date = moment().locale('pt-br'); //Get the current date
    var data_formatada = date.format('dddd[,] D [de] MMMM [de] YYYY'); //2014-07-10
    $('.data_agenda').html(data_formatada);
    var data_para_funcao = moment(date).format('YYYYMMDD');

    var div_com_array_datas = $('#array_datas_agenda');

    // Para input
    var array_datas_agenda = div_com_array_datas.val();

    //Para Div
    //var array_datas_agenda = div_com_array_datas.text();

    if (array_datas_agenda) {
        var datas_agendas = JSON.parse(array_datas_agenda);
        div_com_array_datas.remove();
    }

    var anos_dispo = $('#array_anos').val();

    redebe_data(data_para_funcao);

    $('.calendario-agenda-sec').ionCalendar({
        lang: "pt-br",
        years: anos_dispo,

        onReady: function(){
            getAnoMesCalendario()
        },

        onClick: function (date) {
            primeiro_clique = true;
            moment.locale('pt-br');
            $('.data_agenda').html(moment(date).format('dddd[,] D [de] MMMM [de] YYYY'));
            let data_pt_br = moment(date).format('YYYYMMDD');
			var data = new Date();
			var data_atual = moment(data).format('YYYYMMDD');
			var data_clicada = moment(date).format('YYYYMMDD');

            if (data_atual === data_clicada){
                primeiro_clique = false;
            }

			$(".agenda-ordenada").html("<div class='lds-ring'><div></div><div></div><div></div><div></div></div>");
            $("#mostra_data").html("");
			
            redebe_data(data_pt_br);
        }
    });

    function getAnoMesCalendario() {

        if (primeiro_clique){
            $( ".ic__day_state_current" ).each(function( index ) {
                $( this ).removeClass("ic__day_state_current");
            });
        }

        var selectedAno= $('.ic__year-select').children("option:selected").val();

        var selectedMes= $('.ic__month-select').children("option:selected").val();
        var selectedMes= parseInt(selectedMes)+1;
        if (selectedMes <= 9){
            var selectedMes= '0'+selectedMes;
        }else {
            var selectedMes= selectedMes.toString();
        }

        $('.ic__day').each(function (e) {

            var dia_corrente = parseInt(this.textContent);
            if (dia_corrente <= 9) {
                dia_corrente = '0' + dia_corrente;
            }else {
                dia_corrente = dia_corrente.toString();
            }
            var data_completa = dia_corrente+'/'+selectedMes+'/'+selectedAno;

            var classe_css = '';
            if (dia_corrente <= '09'){
                classe_css = 'destaque-evento-agenda-menor-que-10';

            }else{
                classe_css = 'destaque-evento-agenda';
            }

            // Encontra o item onde a data (índice 0) corresponde a `data_completa`
            const itemEncontrado = datas_agendas.find(item => item[0] === data_completa);

            if (itemEncontrado) {
                const categoria = itemEncontrado[1]; // Pega a categoria (índice 1)
                this.innerHTML = `<span class="${classe_css} ${categoria}">${this.textContent}</span>`;
            }
            
        });
    }

    function redebe_data(data_recebida) {
        var conteudo_a_ser_exibido = $('#mostra_data');
        
        jQuery.ajax({
            url: bloginfo.ajaxurl,
            type: 'post',
            data: {
                // você sempre deve passar o parâmetro 'action' com o nome da função que você criou no seu functions.php ou outro que você esteja incluindo nele
                action: 'montaHtmlListaEventos',
                data_pt_br: data_recebida,
            },

            success: function (data) {
                $('.agenda-ordenada').html("");
                var $data = $(data);
                conteudo_a_ser_exibido.html($data);
				var atual = new Date();
				var data_atual = moment(atual).format('YYYYMMDD');
            },

            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
                alert(thrownError);
            }
        });

        
    }
});