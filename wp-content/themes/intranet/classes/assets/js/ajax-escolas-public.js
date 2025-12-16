jQuery(document).ready(function ($) {

    var href = $(location).attr('href');
    var pathname = $(location).attr('pathname');

    if (pathname === '/furuba-educacao-intranet/busca-de-escolas/' || pathname === '/busca-de-escolas/') {
        busca_escola();
        busca_tipo_escola();
        busca_dre();
    }


    function exibir_escolas_filtradas_total(link, paginaAtual){

        $.ajax({
            url: link,
            type: 'GET',
            dataType: 'json',

        })
            .success(function (data) {
                exibe_escolas_filtradas(data, paginaAtual);
            })
            .fail(function () {
                console.log("error");
            });
    }

    $('#formulario_busca_escola').submit(function (e) {
        e.preventDefault();

        var busca_tipo_de_escola = $('#busca_tipo_de_escola').val();
        var busca_escola = $('#busca_escola').val();
        var busca_dre = $('#busca_dre').val();
        var endpoint_consulta_escola = 'https://hom-escolaaberta.sme.prefeitura.sp.gov.br/api/escolas/?tipoesc=' + busca_tipo_de_escola + '&search=' + busca_escola + '&dre=' + busca_dre;

        exibir_escolas_filtradas_total(endpoint_consulta_escola);

    });

    function exibe_escolas_filtradas(data, paginaAtual) {

        var $data = $(data);
        var resultados_consulta_escolas = $data[0]['results'];

        /*Container onde vai ser exibida a tabela com os dados da escola*/
        var container_tabela_busca_escola = $('#container_tabela_busca_escola');

        // Limpando a Div
        container_tabela_busca_escola.html('');

        // Descobrindo o numero de itens retornados
        var total_escolas_filtradas = resultados_consulta_escolas.length;

        if (total_escolas_filtradas > 0) {

            // Montando a Tabela que receberá as escolas filtradas
            container_tabela_busca_escola.append(
                '<table id="conteudo_a_ser_exibido" class="table table-striped table-bordered table-responsive">' +
                '<thead>' +
                '<tr>' +
                '<th scope="col">Código</th>' +
                '<th scope="col">Nome</th>' +
                '<th scope="col">Tipo</th>' +
                '<th scope="col">Diretoria Regional de Educação</th>' +
                '<th scope="col">Endereço</th>' +
                '<th scope="col">Mais Informações</th>' +
                '</tr>' +
                '</thead>'
            );

            var table = $('#conteudo_a_ser_exibido');
            table.find("tbody tr").remove();

            $.each(resultados_consulta_escolas, function (indice, valor) {

                table.append(
                    "<tr>" +
                    "<td>" + valor.codesc +"</td>"+
                    "<td>" + valor.nomesc + "</td>" +
                    "<td>" + valor.tipoesc + "</td>" +
                    "<td>" + valor.diretoria + "</td>" +
                    "<td>" + valor.endereco + ", " + valor.numero + "<br>" +
                    valor.bairro + "<br>" +
                    "CEP: " + valor.cep +
                    "</td>" +
                    "<td><a target='_blank' class='btn btn-primary' href='https://eolgerenciamento.prefeitura.sp.gov.br/frmgerencial/NumerosEscola.aspx?Cod=" + valor.codesc + "/'>Ver mais</a></td>" +
                    "</tr>"
                );

            });

            paginacao(total_escolas_filtradas, $data, paginaAtual)

        } else {
            container_tabela_busca_escola.append(
                '<p><strong>Nenhum registro foi encontrado!! Tente outra pesquisa</strong>'
            );
        }
    }
    
    function paginacao(total_escolas_filtradas, data, paginaAtual ){

        var busca_tipo_de_escola = $('#busca_tipo_de_escola').val();
        var busca_escola = $('#busca_escola').val();
        var busca_dre = $('#busca_dre').val();

        if (data[0].count > 10){

            var container_tabela_busca_escola = $('#container_tabela_busca_escola');

            container_tabela_busca_escola.append(
                '<section class="row">'+
                '<nav class="col">'+
                '<nav aria-label="Paginação das escolas selecionadas">'+
                '<ul class="pagination" id="pagination"></ul>'+
                '</nav>'+
                '</section>'+
                '</div>'

            );

            var totalPages = Math.ceil((data[0].count)/10);

            $(function () {
                window.pagObj = $('#pagination').twbsPagination({
                    totalPages: totalPages,
                    visiblePages: 5,
                    startPage: paginaAtual,
                    first: 'Primeira',
                    last: 'Última',
                    prev: '<< Anter',
                    next: 'Próx >>',
/*                    onPageClick: function (event, page) {
                        console.info(page + ' (from options)');
                    }*/
                }).on('page', function (event, page) {
                    var endpoint_consulta_escola = 'https://hom-escolaaberta.sme.prefeitura.sp.gov.br/api/escolas/?tipoesc=' + busca_tipo_de_escola + '&search=' + busca_escola + '&dre=' + busca_dre + '&page='+page;
                    exibir_escolas_filtradas_total(endpoint_consulta_escola, page);
                });
            });

        }

    }


    function busca_tipo_escola() {
        var endpoint_search_tipo_escola = "https://hom-escolaaberta.sme.prefeitura.sp.gov.br/api/tipo_escola";
        var busca_tipo_de_escola = $('#busca_tipo_de_escola');

        $.ajax({
            url: endpoint_search_tipo_escola,
            type: 'GET',
            dataType: 'json',

        })
            .success(function (data) {
                var $data = $(data);
                var resultados_tipo_escola = $data[0]['results'];

                $.each(resultados_tipo_escola, function (indice, valor) {
                    $('<option>').val(valor.tipoesc).text(valor.tipoesc).appendTo(busca_tipo_de_escola);
                });

            })
            .fail(function () {
                console.log("error");
            });
    }


    function busca_escola() {
        /*Autocomplete Busca Escola*/
        $("#busca_escola").on('keyup paste', function () {

            var busca_escola = $(this).val();
            var endpoint_search_escolas = "https://hom-escolaaberta.sme.prefeitura.sp.gov.br/api/escolas/?search=" + busca_escola;

            var array_resultado_busca_escolas = [];

            $.ajax({
                url: endpoint_search_escolas,
                type: 'GET',
                dataType: 'json',

            })
                .success(function (data) {
                    var $data = $(data);
                    var resultados_busca_escola = $data[0]['results'];

                    $.each(resultados_busca_escola, function (indice, valor) {
                        array_resultado_busca_escolas.push(valor.nomesc)
                    });

                })
                .fail(function () {
                    console.log("error");
                });


            /*Método do Autocomplete do JQuery UI*/
            $(this).autocomplete({
                source: array_resultado_busca_escolas
            });

        });

    }

    function busca_dre() {

        var endpoint_search_dre = "https://hom-escolaaberta.sme.prefeitura.sp.gov.br/api/diretorias";
        var busca_dre = $('#busca_dre');

        $.ajax({
            url: endpoint_search_dre,
            type: 'GET',
            dataType: 'json',
        })
            .success(function (data) {

                var $data = $(data);
                var resultados_tipo_dre = $data[0]['results'];
                $('<option>').val('').text('Selecione uma DRE').appendTo(busca_dre);

                $.each(resultados_tipo_dre, function (indice, valor) {
                    $('<option>').val(valor.dre).text(valor.diretoria).appendTo(busca_dre);
                });

            })
            .fail(function () {
                console.log("error");
            });

    }


});