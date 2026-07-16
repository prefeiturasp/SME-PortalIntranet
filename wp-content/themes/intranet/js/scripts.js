/*Anima Menu
/*Verifico o tamanho da tela, se for maior que 992 crio e add a classe que contem a animação ao hover do li do wp-nav-menu*/
var $s = jQuery.noConflict();

/*Ativacao Hamburger Menu Icons*/
$s(document).ready(function () {
    $s('#nav-icon1, #nav-icon2, #nav-icon3, #nav-icon4').click(function () {
        $s(this).toggleClass('open');
    });
});


/*Scripts para o Botao Voltar ao topo aparecer somente quando tiver rolagem para baixo*/
$s(function () {
    $s(window).scroll(function () {
        if ($s(this).scrollTop() != 0) {
            $s('#toTop').fadeIn();
        } else {
            $s('#toTop').fadeOut();
        }
    });
    $s('#toTop').click(function () {
        $s('body,html').animate({scrollTop: 0}, 800);
    });
});
///////////////////////////////////////////////////////////////////////////////
///////////////////////////icones persona home/////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
function removeBackgroundColor(id_link_atual) {
    $s('.container-a-icones-home').each(function (e) {
        var id_li_atual = this.id;
        if (id_li_atual != id_link_atual) {
            $s(this).css('background-color', '#F6F6F6')
        }
    })
}

$s(document).ready(function () {
    $s(".a-icones-home").each(function (index) {
        $s(this).click(function (e) {
            var id_link_atual = e.currentTarget.id;
            var elemento_pai = $s(this).parent();
            elemento_pai.css('background-color', '#ECECEC');
            removeBackgroundColor(id_link_atual);
			//add hover no avg
			$s(this).on('icones-home').addClass('icones-home-svg');	 		
        });
    });
});
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

// Removendo rodape Hand-Talk{
$s("._2l9ogse-9T4ParAkBl58xA").waitUntilExists(function (e) {
    var link_rodape = $s('._2l9ogse-9T4ParAkBl58xA').contents().find('.ht-ac-copy');
    link_rodape.remove();
});

// Removendo cabecalho twitter
$s("iframe#twitter-widget-0").waitUntilExists(function (e) {
    var iframeBody = $s("iframe#twitter-widget-0").contents().find('body');
    var timeline = iframeBody.find('.timeline-Widget');
    timeline.find('.timeline-Header').remove();
    timeline.find('.twitter-timeline').remove();

});

// Fechando Janela Galeria ao clicar na Tab swipebox-overlay
$s(document).ready(function () {
    $s(".gallery-item").on('click ', function (e) {
        $s("body").keydown(function (e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode == 9) {
                var bt_close = $s('#swipebox-close');
                bt_close.trigger('click');
            }
        });
    });

});

/*
$s(document).ready(function(){
    $s("._2p3a").removeAttr( 'style' );
    $s("._2p3a").css({"min-width": "180px", " width:": "540px"});

});*/

// Trocando texto em ingles das midias sociais do plugin addThis
$s("span.at4-visually-hidden").waitUntilExists(function (e) {

    var texto = $s(this).text();

    if (texto === 'AddThis Sharing Buttons') {
        $s(this).remove();
    }
    if (texto === "Share to WhatsApp") {
        $s(this).text("Compartilhar com WhatsApp")
    }

    if (texto === "Share to Facebook") {
        $s(this).text("Compartilhar com Facebook")
    }

    if (texto === "Share to Twitter") {
        $s(this).text("Compartilhar com Twitter")
    }

    if (texto === "Share to Imprimir") {
        $s(this).text("Imprimir este conteúdo")
    }

    //var texto_alterado = texto.replace('Share to', 'Compartilhar com');
    //$s(this).text(texto_alterado);
});

$s("span.at-icon-wrapper > svg > title").waitUntilExists(function (e) {
    var texto = $s(this).text();
    if (texto === "Print") {
        $s(this).text("Imprimir")
    }
});

/*Ativação do Tool Tip Bootstrap*/
$s(document).ready(function () {
    $s(function () {
        $s('[data-toggle="tooltip"]').tooltip({html: true})
    });
});

$s(document).ready(function () {
    $s(function () {
        $s('img').addClass('img-fluid');
    });

    $s('#categoria').change(function() {
        //Use $option (with the "$") to see that the variable is a jQuery object
        var $option = $s(this).find('option:selected');
        //Added with the EDIT
        var value = $option.val();//to get content of "value" attrib
        //var text = $option.text();//to get <option>Text</option> content
        if(value == 'portais'){
            $s("#data-ini").prop("disabled", true);
            $s("#data-end").prop("disabled", true);
        } else {
            $s("#data-ini").removeAttr('disabled');
            $s("#data-end").removeAttr('disabled');
        }
        //alert(value);
    });
});

$s(".a-icones-home").click(function() {
    if ($s(this).hasClass('active')) {
        var href = $s(this).attr('href');
        $s(href).toggleClass('active');
    } else {
        $s(".a-icones-home").removeClass('exibe');
    }
    $s(this).toggleClass('exibe');
});

/* Ativacao Wow*/
new WOW().init();

$s('#telefone').mask('(00) 00000-0000');

$s('.check-search').click(function() {
    $s('.check-search').not(this).prop('checked', false);
});

/* Scripts da tabela de listagem dos sorteados */
$s(document).ready(function () {
    $s('table.datatables').each(function () {

        const $table = $s(this);
        const count = $table.find('tbody tr').length;
        const $collapse = $table.closest('.collapse').parent();

        let currentTable = $table.DataTable({
            pageLength: 5,
            lengthChange: false,
            ordering: false,
            paging: count > 5,
            searching: true,
            info: false,
            stripeClasses: [],
            autoWidth: false,
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json',
                searchPlaceholder: 'Sorteados',
                paginate: {
                    previous: '<i class="fa fa-chevron-left"></i>',
                    next: '<i class="fa fa-chevron-right"></i>'
                }
            },
            pagingType: "simple_numbers",
            dom: 'rtip',
        });

        $table.removeClass('dataTable');

        //Busca personalizada
        $collapse.find('.input-nome-participante').on('keyup', function() {
            currentTable.search($s(this).val()).draw();
        });

        // Botão limpar
        $collapse.find('.btn-limpar-filtro').on('click', function() {
            $collapse.find('.input-nome-participante').val('');
            currentTable.search('').draw();
        });
    });
});

//Ajusta a inicialização do menu de perfil e dos elementos do menu principal que contenham dropdown.
jQuery(function ($) {
    $(document).on('click', '#irmenu a.nav-link', function () {
        $(this).attr('aria-expanded', true);
        $(this).parent().addClass('show');
        $(this).parent().find('.dropdown-menu').addClass('show');
    })

    $(document).on('click', '.profile-menu .nav-item a', function () {
        $(this).attr('aria-expanded', true);
        $(this).parent().toggleClass('show');
        $(this).parent().find('.dropdown-menu')
            .toggleClass('show')
            .attr('x-placement', 'bottom-start')
            .css({
                position: 'absolute',
                top: '0px',
                left: '0px',
                transform: 'translate3d(0px, 44px, 0px)',
                willChange: 'transform'
              });
    })
});

jQuery(function ($) {
    let mainSlider = $(document).find('.main-content-slider');

    if (mainSlider.length) {
        const swiper = new Swiper('.main-content-slider', {
            slidesPerView: 1,
            speed: 500,
            loop: true,
            navigation: {
                nextEl: '.custom-next',
                prevEl: '.custom-prev',
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true
            },
            autoplay: {
                delay: 10000,
                disableOnInteraction: false,
            },
        });
    }
});

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('toggle-replies')) {

        const btn = e.target;
        const li = btn.closest('li');
        const children = li.querySelector('.children');

        if (!children) return;

        const isCollapsed = children.classList.contains('collapsed');

        if (!isCollapsed) {
            // FECHAR
            children.style.maxHeight = children.scrollHeight + 'px';

            requestAnimationFrame(() => {
                children.style.maxHeight = '0px';
                children.classList.add('collapsed');
            });

            btn.textContent = 'Ver mais';

        } else {
            // ABRIR
            children.classList.remove('collapsed');
            children.style.maxHeight = children.scrollHeight + 'px';

            setTimeout(() => {
                children.style.maxHeight = 'none';
            }, 300);

            btn.textContent = 'Ver menos';
        }
    }
});

/*
Controla os eventos relacionados aos collapses de listagem
dos participantes contemplados no evento.
*/
jQuery(function($) {

    function atualizarFiltro($collapse, event) {

        const $bloco = $collapse.closest('.conteudo-tab-lista-sorteados');
        const $filter = $bloco.find('.filtro-contemplados');
        const showFilter = $bloco.find('.dataTables_paginate').length // Se a paginação estiver ativa, exibe também o filtro de busca

        if (event === 'hide.bs.collapse') {
            $filter.addClass('d-none');
        }

        if (showFilter && event === 'show.bs.collapse' ) {
            $filter.removeClass('d-none');
        }

    }

    $(document).on('show.bs.collapse', '#accordion-sorteados .collapse', function(){
        atualizarFiltro($(this), 'show.bs.collapse')
    });

    $(document).on('hide.bs.collapse', '#accordion-sorteados .collapse', function(){
        atualizarFiltro($(this), 'hide.bs.collapse')
    });
})

/* Scripts da tabela de listagem das inscrições do usuário aba "Minhas Inscrições" */

jQuery(function ($) {

    const $table = $('#minhas-inscricoes #tabela-inscricoes-participante');

    let instance = $table.DataTable({
        pageLength: 10,
        lengthChange: false,
        ordering: false,
        paging: true,
        searching: false,
        info: false,
        stripeClasses: [],
        autoWidth: false,
        responsive: false,
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json',
            searchPlaceholder: 'Sorteados',
            paginate: {
                previous: '<i class="fa fa-chevron-left"></i>',
                next: '<i class="fa fa-chevron-right"></i>'
            }
        },
        pagingType: "simple_numbers",
        dom: 'rtip',
    });

    $table.removeClass('dataTable');
    $table.removeClass('table-responsive');
});

/* Scripts do modal de instruções da listagem das inscrições do usuário aba "Minhas Inscrições" */

jQuery(function($){

    $(document).on('click', '.ver-email-instrucao', function(e){

        e.preventDefault();

        var inscricao_id = $(this).data('inscricao');

        $('#email-participante-nome').text('');
        $('#email-participante-email1').text('');
        $('#email-participante-email2').text('');

        $('.content-attachment').hide();
        $('.content-attachment a').attr('href', '');

        $('#email-evento').text('');
        $('#email-data').text('');

        $('#email-mensagem').html('Carregando...');

        $.ajax({

            url: ajax_obj.ajax_url,
            type: 'POST',

            data:{
                action: 'buscar_email_instrucao',
                inscricao_id: inscricao_id
            },

            success:function(response){
                console.log(response)
                if(response.success){

                    $('#email-participante-nome').text(response.data.nome);
                    $('#email-participante-email1').text(response.data.email1);
                    $('#email-participante-email2').text(response.data.email2);
                    $('#email-evento').text(response.data.evento);
                    $('#email-data').text(response.data.data_envio);
                    $('#email-mensagem').html(response.data.mensagem);

                    if(response.data.anexo) {
                        $('.content-attachment a').attr('href', response.data.anexo);
                        $('.content-attachment').show();
                    }

                }else{

                    $('#email-mensagem').html('Nenhuma informação encontrada.');

                }

                $('#modalEmailInstrucao').modal('show');

            },

            error:function(){

                $('#email-mensagem').html('Erro ao carregar os dados.');
                $('#modalEmailInstrucao').modal('show');

            }

        });

    });

});

/** Scripts do fluxo de confirmação/cancelamento da participação da listagem de eventos na aba de "Minhas inscrições" */

jQuery(function($){

    let $dropdowns = $(document).find('#tabela-inscricoes-participante .btn-acoes .seletor-acoes');

    if ($dropdowns.length) {
       $dropdowns.click(); //Inicializa corretamente os dropdowns de ação da tabela 
    }

    $(document).on('click', '#tabela-inscricoes-participante .btn-confirmar-presenca', function () {

        const postId = $(this).data('post');
        const tipo = $(this).data('tipo');
        const prazo = $(this).data('prazo');
        const inscricaoId = $(this).data('inscricao');
        const modalidade = $(this).data('modalidade');

        let htmlConteudo = '';

        if (modalidade === 'sorteio') {

            if (tipo === 'premio') {
                htmlConteudo = `
                    <p>Ao confirmar sua participação, você receberá um novo e-mail com instruções relacionadas a este sorteio.</p>
                    <p>Se não puder prosseguir, você pode <strong>cancelar sua participação</strong> e a oportunidade será disponibilizada para outro participante.</p>
                    <p><strong>Atenção:</strong> caso confirme e não siga as instruções, você poderá ficar impedido de participar de novos sorteios por um período.</p>
                `;
            } else {
                htmlConteudo = `
                    <p>Ao confirmar sua presença, você receberá um <strong>novo e-mail com instruções para utilização do seu ingresso</strong>.</p>
                    <p>Se não puder comparecer, recomendamos <strong>cancelar sua participação</strong> para que outra pessoa possa aproveitar o evento.</p>
                    <p><strong>Atenção:</strong> Se confirmar e não comparecer, poderá ficar impedido de participar de novos sorteios por um período.</p>
                `;
            }
        }

        if (modalidade === 'cortesia') {
            htmlConteudo = `
                <p>Caso confirme, você receberá um <strong>novo e-mail com instruções relacionadas a esta participação.</strong></p>
                <p>Se não puder prosseguir, <strong>pedimos que cancele clicando no botão abaixo</strong>. Dessa forma, a oportunidade poderá ser disponibilizada para outro participante.</p>
                <p><strong>Atenção:</strong> Caso a participação seja confirmada e as instruções não sejam seguidas conforme orientado, você poderá ficar impedido(a) de participar de novas inscrições por um período determinado.</p>
            `;  
        }

        Swal.fire({
            title: 'Confirme sua participação!',
            html: `
                ${htmlConteudo}
                <p style="margin-top:15px;">
                    <strong>Prazo para confirmação:</strong><br>${prazo}
                </p>
            `,
            icon: "info",

            showCancelButton: true,
            showCloseButton: true,

            confirmButtonText: 'Confirmar presença',
            cancelButtonText: 'Cancelar participação',

            confirmButtonColor: '#268618',
            cancelButtonColor: '#011257',

            reverseButtons: false,
            focusConfirm: false,
            width: 600,

            showLoaderOnConfirm: true,
            allowOutsideClick: () => !Swal.isLoading(),

            //Confirmar presença
            preConfirm: () => {
                return $.ajax({
                    url: ajax_obj.ajax_url,
                    type: 'POST',
                    data: {
                        action: modalidade == 'sorteio' ? 'confirmar_cancelar_presenca_sorteio' : 'confirmar_cancelar_presenca_cortesia',
                        nonce: ajax_obj.nonce,
                        post_id: postId,
                        inscricao_id: inscricaoId,
                        acao: 1 // 1 => Confirmar presença 2 => Cancelar participação
                    }
                }).catch(() => {
                    Swal.showValidationMessage('Erro ao confirmar presença');
                });
            }

        }).then((result) => {
            if (result.isConfirmed) {

                response = result.value;

                Swal.fire({
                    icon: response.success ? 'success' : 'error',
                    html: response.data.message,
                    confirmButtonText: 'Fechar'
                }).then(() => location.reload());
            }

            //Cancelar participação
            if (result.dismiss === Swal.DismissReason.cancel) {

                Swal.fire({
                    title: 'Cancelar participação?',
                    text: 'Essa ação não pode ser desfeita.',
                    icon: 'warning',

                    showCancelButton: true,
                    confirmButtonText: 'Sim, cancelar',
                    cancelButtonText: 'Voltar',

                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',

                    showLoaderOnConfirm: true,
                    allowOutsideClick: () => !Swal.isLoading(),

                    preConfirm: () => {
                        return $.ajax({
                            url: ajax_obj.ajax_url,
                            type: 'POST',
                            data: {
                                action: modalidade == 'sorteio' ? 'confirmar_cancelar_presenca_sorteio' : 'confirmar_cancelar_presenca_cortesia',
                                nonce: ajax_obj.nonce,
                                post_id: postId,
                                inscricao_id: inscricaoId,
                                acao: 2 // 1 => Confirmar presença 2 => Cancelar participação
                            }
                        }).catch(() => {
                            Swal.showValidationMessage('Erro ao cancelar participação');
                        });
                    }

                }).then((cancelResult) => {
                    if (cancelResult.isConfirmed) {

                        response = cancelResult.value;

                        Swal.fire({
                            icon: response.success ? 'success' : 'error',
                            html: response.data.message,
                            confirmButtonText: 'Fechar'
                        }).then(() => location.reload());
                    }
                });
            }

        });

    });
});

/* Scripts da tabela de listagem das inscrições do usuário aba "Minhas Oportunidades" */

function formatarDataHora(dataHora) {

    if (!dataHora) {
        return '';
    }

    const [data, hora] = dataHora.split(' ');
    const [ano, mes, dia] = data.split('-');
    const [horas, minutos] = hora.split(':');

    return `${dia}/${mes}/${ano} às ${horas}:${minutos}`;
}

/**
 * Scripts da listagem das inscrições do candidado na página de "Minhas Oportunidades"
 */
jQuery(function($) {

    const $table = $('#minhas-candidaturas #tabela-minhas-oportunidades');

    let instance = $table.DataTable({
        pageLength: 20,
        lengthChange: false,
        ordering: false,
        paging: true,
        searching: true,
        info: false,
        stripeClasses: [],
        autoWidth: false,
        responsive: false,
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json',
            paginate: {
                previous: '<i class="fa fa-chevron-left"></i>',
                next: '<i class="fa fa-chevron-right"></i>'
            }
        },
        pagingType: "simple_numbers",
        dom: 'rtip',
    });

    $table.removeClass('dataTable');
    $table.removeClass('table-responsive');

    $(document).on('click', '.btn-visualizar-comunicado', function () {

        const publicId = $(this).data('id');
        const linha = $(this).closest('tr');
    
        if (!publicId) {
            return;
        }
    
        $.ajax({
    
            url: ajax_obj.ajax_url,
            type: 'POST',
    
            data: {
                action: 'get_envio',
                public_id: publicId,
                nonce: ajax_obj.nonces.visualizar_envio
            },
    
            beforeSend() {
                Swal.fire({
                    title: 'Carregando...',
                    allowOutsideClick: false,
                    didOpen() {
                        Swal.showLoading();
                    }
                });
            },
            success(response) {
                Swal.close();
    
                if (!response.success) {
    
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: response.data.message
                    });
    
                    return;
                }
    
                preencherModalComunicado(response.data, linha);
            },
    
            error() {
                Swal.close();
    
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: 'Não foi possível carregar o comunicado.'
                });
            }
        });
    });

    function preencherModalComunicado(envio, linha) {

        const modal = $('#modal-comunicado');
        const titulo = linha.find('#titulo-oportunidade a').text();
        const subTitulo = linha.find('.subtitulo-oportunidade').html();
        const dataInscricao = linha.data('data-inscricao')
        const lista = modal.find('.js-anexos');

        modal.find('.js-oportunidade').text(envio.oportunidade);
        modal.find('.js-data-envio').text(formatarDataHora(envio.data_envio));
        modal.find('.js-data-inscricao').html(dataInscricao);
        
        modal.find('.js-titulo').html(`<strong>${titulo}</strong>`);
        modal.find('.js-titulo').append(subTitulo);
    
        lista.empty();

        if (envio.mensagem && envio.mensagem.length) {
            modal.find('#info-complementar').removeClass('d-none');
            modal.find('.js-mensagem').html(envio.mensagem);
        } else {
            modal.find('#info-complementar').addClass('d-none');
            modal.find('.js-mensagem').html('');
        }
    
        if (envio.anexos && envio.anexos.length) {
    
            modal.find('.js-bloco-anexos').removeClass('d-none');
    
            envio.anexos.forEach(anexo => {
                lista.append(`
                    <a
                        href="${anexo.url}"
                        download
                        class="list-group-item list-group-item-action d-flex align-items-center"
                        >
    
                        <i class="fa fa-lg fa-download" aria-hidden="true"></i>
    
                        <div class="ml-3">
                            <div class="fw-semibold">
                                ${anexo.nome}
                            </div>
    
                            <small class="text-muted">
                                Clique para baixar o arquivo.
                            </small>
                        </div>
    
                    </a>
                `);
            });
    
        } else {
            modal.find('.js-bloco-anexos').addClass('d-none');
        }
    
        modal.modal('show');
    }

    /**
     * Scripts do filtro de oportunidades em "Minhas Oportunidades"
     */

    //Filtro personalizado para o tipo de oportunidade
    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {

        if (settings.nTable !== instance.table().node()) {
            return true;
        }

        const tipoSelecionado = $('#filtro-tipo').val();

        if (!tipoSelecionado) {
            return true;
        }

        const td = instance.cell(dataIndex, 0).node();

        if (!td) {
            return true;
        }

        const tipos = ($(td).data('tipo') || '').toString().split(',');

        return tipos.includes(tipoSelecionado);
    });

    // Função para aplicar os filtros selecionados na instancia atual da tabela
    function aplicarFiltros() {

        const titulo = $('#filtro-titulo').val().trim();
        const etapa  = $('#filtro-etapa').val();

        // Coluna do título da oportunidade
        instance.column(0).search(titulo);
        // Coluna da etapa etapa do processo seletivo.
        instance.column(2).search(etapa);

        instance.draw();
    }

    // Função para exibir o componente de sem resultados.
    function atualizarEstadoTabela() {

        const total = instance.rows({filter: 'applied'}).count();
    
        if (total === 0) {
            $('#minhas-candidaturas').addClass('d-none');
            $('#sem-resultado').removeClass('d-none');
    
        } else {
            $('#minhas-candidaturas').removeClass('d-none');
            $('#sem-resultado').addClass('d-none');
        }
    }

    // Ação de clique no botão de filtrar da página de Minhas Oportunidades
    $('#btn-filtrar').on('click', function () {
        aplicarFiltros();
    });

    // Evento para tratar ação da tecla enter no campo de busca pelo titulo da oportunidade
    $('#filtro-titulo').on('keypress', function (e) {

        if (e.which === 13) {
            e.preventDefault();
            aplicarFiltros();
        }

    });

    // Ação de clique no botão de limpar filtros da página de Minhas Oportunidades
    $('#btn-limpar-filtros').on('click', function () {

        $('#filtro-titulo').val('');
        $('#filtro-etapa').val('');
        $('#filtro-tipo').val('');

        instance.columns().search('');
        instance.draw();

    });

    // Evento que é disparado sempre que a tabela é construida novamente
    instance.on('draw', function () {
        atualizarEstadoTabela();
    });
})

/** Scripts das açoes de confirmação e cancelamento de interesse nas etapas do processo seletivo */
jQuery(function($) {

    $(document).on('click', '.btn-visualizar-confirmacao', function () {

        const publicId = $(this).data('id');
        const linha = $(this).closest('tr');
    
        if (!publicId) {
            return;
        }
    
        $.ajax({
    
            url: ajax_obj.ajax_url,
            type: 'POST',
    
            data: {
                action: 'get_envio',
                public_id: publicId,
                nonce: ajax_obj.nonces.visualizar_envio
            },
    
            beforeSend() {
                Swal.fire({
                    title: 'Carregando...',
                    allowOutsideClick: false,
                    didOpen() {
                        Swal.showLoading();
                    }
                });
            },
            success(response) {
                Swal.close();
    
                if (!response.success) {
    
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: response.data.message
                    });
    
                    return;
                }
    
                preencherModalConfirmacao(response.data, linha);
            },
    
            error() {
                Swal.close();
    
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: 'Não foi possível carregar o comunicado.'
                });
            }
        });
    });

    $(document).on('click', '.btn-confirmar-interesse-etapa', function () {

        const postId = $(this).data('post-id');
        const inscricaoId = $(this).data('inscricao-id');;
    
        Swal.fire({
            icon: 'question',
            title: 'Confirmar participação',
            html: 'Deseja realmente confirmar seu interesse em continuar participando desta etapa do processo seletivo?',
            showCancelButton: true,
            confirmButtonText: 'Sim',
            cancelButtonText: 'Não',
            confirmButtonColor: '#14447C',
            reverseButtons: true
        }).then(result => {
    
            if (!result.isConfirmed) {
                return;
            }
    
            atualizarConfirmacao(inscricaoId, postId, 1);
        });
    });

    $(document).on('click', '.btn-cancelar-interesse-etapa', function () {

        const postId = $(this).data('post-id');
        const inscricaoId = $(this).data('inscricao-id');
    
        Swal.fire({
            icon: 'question',
            title: 'Cancelar participação',
            html: 'Deseja realmente cancelar sua participação nesta etapa do processo seletivo?',
            showCancelButton: true,
            confirmButtonText: 'Sim',
            cancelButtonText: 'Não',
            confirmButtonColor: '#dc3545',
            reverseButtons: true
        }).then(result => {
    
            if (!result.isConfirmed) {
                return;
            }
    
            atualizarConfirmacao(inscricaoId, postId, 2);
        });
    
    });

    function preencherModalConfirmacao(envio, linha) {

        const modal = $('#modal-confirmacao');
        const titulo = linha.find('#titulo-oportunidade a').text();
        const subTitulo = linha.find('.subtitulo-oportunidade').html();
        const dataInscricao = linha.data('data-inscricao')
        const lista = modal.find('.js-anexos');
        const inscricaoId = linha.data('inscricao-id');

        modal.find('.js-oportunidade').text(envio.oportunidade);
        modal.find('.js-data-envio').text(formatarDataHora(envio.data_envio));
        modal.find('.js-data-inscricao').html(dataInscricao);
        modal.find('.js-prazo-confirmacao').html(formatarDataHora(envio.prazo_confirmacao));
        
        modal.find('.js-titulo').html(`<strong>${titulo}</strong>`);
        modal.find('.js-titulo').append(subTitulo);

        modal.find('.btn-confirmar-interesse-etapa')
            .data('inscricao-id', inscricaoId)
            .data('post-id', envio.post_id);

        modal.find('.btn-cancelar-interesse-etapa')
            .data('inscricao-id', inscricaoId)
            .data('post-id', envio.post_id);
    
        lista.empty();

        if (envio.mensagem && envio.mensagem.length) {
            modal.find('#info-complementar').removeClass('d-none');
            modal.find('.js-mensagem').html(envio.mensagem);
        } else {
            modal.find('#info-complementar').addClass('d-none');
            modal.find('.js-mensagem').html('');
        }
    
        if (envio.anexos && envio.anexos.length) {
    
            modal.find('.js-bloco-anexos').removeClass('d-none');
    
            envio.anexos.forEach(anexo => {
                lista.append(`
                    <a
                        href="${anexo.url}"
                        download
                        class="list-group-item list-group-item-action d-flex align-items-center"
                        >
    
                        <i class="fa fa-lg fa-download" aria-hidden="true"></i>
    
                        <div class="ml-3">
                            <div class="fw-semibold">
                                ${anexo.nome}
                            </div>
    
                            <small class="text-muted">
                                Clique para baixar o arquivo.
                            </small>
                        </div>
    
                    </a>
                `);
            });
    
        } else {
            modal.find('.js-bloco-anexos').addClass('d-none');
        }
    
        modal.modal('show');
    }

    function atualizarConfirmacao(inscricaoId, postId, confirmouPresenca) {

        $.ajax({
    
            url: ajax_obj.ajax_url,
            type: 'POST',
    
            data: {
                action: 'confirmar_participacao',
                nonce: ajax_obj.nonces.confirmar_participacao,
                inscricao_id: inscricaoId,
                post_id: postId,
                confirmou_presenca: confirmouPresenca
            },
    
            beforeSend() {
    
                Swal.fire({
                    title: 'Salvando sua resposta, aguarde...',
                    allowOutsideClick: false,
                    didOpen() {
                        Swal.showLoading();
                    }
                });
    
            },
    
            success(response) {
    
                if (!response.success) {
    
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: response.data.message,
                        confirmButtonText: 'Fechar',
                        confirmButtonColor: '#14447C',
                    });
    
                    return;
                }
    
                Swal.fire({
                    icon: 'success',
                    title: 'Resposta registrada!',
                    text: response.data.message,
                    confirmButtonText: 'Fechar',
                    confirmButtonColor: '#14447C',
                }).then(() => {
    
                    $('#modal-confirmacao').modal('hide');
                    $(`[data-inscricao-id="${inscricaoId}"]`).replaceWith(response.data.html);
    
                    // Atualizar a tabela ou recarregar a página
    
                });
    
            },
    
            error() {
    
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: 'Não foi possível registrar sua resposta.',
                    confirmButtonText: 'Fechar',
                    confirmButtonColor: '#14447C',
                });
    
            }
    
        });
    
    }
})
