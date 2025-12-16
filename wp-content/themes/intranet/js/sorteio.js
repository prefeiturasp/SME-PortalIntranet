var qtdSorteados = 0;

function inicializaPagina(){

    document.querySelectorAll(".copia-email-sorteio").forEach( function(button) {
        button.addEventListener("click", function(event) {
        const el = event.target
        const id = el.id;
        if (id) {
            id.split('-')[2] == 'inst' ? copiaEmail(id.split('-')[3], 1) : copiaEmail(id.split('-')[3], 2);   
        }
        });
    });

    document.querySelectorAll(".remove-participante-sorteado").forEach( function(button) {
        button.addEventListener("click", function(event) {
        const id = event.target.id;
        if (id) {
            let idPart = id.split('-')[3];
            let NomePart = id.split('-')[4];
            const grupoSorteados = event.target.closest('.conteudo-lista');
            const data = grupoSorteados ? grupoSorteados.getAttribute('data-data') : null
            const premio = grupoSorteados ? grupoSorteados.getAttribute('data-tipo') : null

            jQuery(event.target).closest('.sorteado-item').find('.check-item').prop('checked', false).trigger('change');

            removerParticipanteSorteado(idPart, NomePart, data, premio);
        }
        });
        qtdSorteados++;
    });

    document.querySelectorAll(".reevia-email-sorteado").forEach( function(button) {
        button.addEventListener("click", function(event) {
            const id = event.target.id;
            const data = event.target.dataset.data;            
            let postId = $s('#post_ID').val();

            if (id) {
                let idPart = id.split('-')[3];
                Swal.fire({
                    html: renderHtmlFormPrazoConfirmacao(id.split('-')[4]),
                    showCancelButton: true,
                    confirmButtonText: 'Enviar',
                    cancelButtonText: 'Fechar',
                    reverseButtons: true,
                    customClass: {
                        popup: 'popup-notificar-sorteados sem-borda p-4',
                    },
                    preConfirm: () => {
                        const $selected = jQuery('input[name="tipo"]:checked');
        
                        if (!$selected) {
                            Swal.showValidationMessage('Você precisa selecionar uma opção!');
                        }
        
                        const $inputValue = jQuery(`#input-${$selected.val()}`);
        
                        if (!$inputValue.val().length) {
                            Swal.showValidationMessage('Você precisa definir um valor para continuar.');
                        }
        
                        return {
                            'tipo': $selected.val(),
                            'valor': $inputValue.val()
                        };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {

                        let prazoConfirmacao = result.value;
                        exibeCarregamentoSorteioToggle('#reenvia-email-participante-sorteado-'+idPart+'-gif');
                        reenvia_email(idPart, postId, 'vencedor', prazoConfirmacao, data);
                    } 
                });
            }
        });
    });

    $s('.conf-todos').change(function(event) {

        const data = event.target.getAttribute('data-data');
        const escopo = `#sorteados_data_${data}`;

        let idsEle = buscaTogglesCheck(escopo);

        idsEle.arrNoChecked.forEach(id => {
            $s('#'+id).bootstrapToggle('on');
        });
    });


    $s('#divulgar-resultado').change(function(event) {

        exibeCarregamentoSorteioToggle('#divulgar-resultado-gif');

        let postId = $s('#post_ID').val();
        let data = {
            action: 'exibir_lista_pagina',
            postId: postId,
            opcao: event.target.checked
        }; 
        $s.post(ajaxurl, data, function(response){ 
            exibeCarregamentoSorteioToggle('#divulgar-resultado-gif');
            if(response.res == true){
                toastr.info(response.msg);
            } 
        });
    });

}

function exibeMsg(msg, tipo, tempo){
    Swal.fire({
        position: "center",
        icon: tipo,
        title: msg,
        showConfirmButton: false,
        timer: tempo
    });
}

function exibeCarregamentoSorteio(){
    let img = window.location.origin+"/wp-content/themes/intranet/img/gif/aguarde.gif";
    let html = '<center><img src="'+img+'" alt="Sorteando... Aguarde!"></center>';
    Swal.fire({
        position: "center",
        title: html,
        showConfirmButton: false
    });
}

function exibeCarregamentoSorteioToggle(id){
    const elemento = document.querySelector(id);
    elemento.classList.toggle('is-active');
}

function enviaEmailListaSorteados(participantesSelecionados, prazoConfirmacao){

    let data = {
        action: 'retorna_lista_sorteados',
        selecionados: participantesSelecionados,
        tipo_prazo: prazoConfirmacao.tipo,
        prazo: prazoConfirmacao.valor
    };    
        
    $s.post(ajaxurl, data);

    Swal.fire({
        title: 'Envio em andamento',
        text: 'Os E-mails de notificação estão sendo enviados aos participantes selecionados.',
        iconHtml: '<span class="dashicons dashicons-email-alt2"></span>',
        showCancelButton: false,
        confirmButtonText: 'Fechar',
        allowOutsideClick: false,
        customClass: {
            popup: 'popup-notificar-sorteados sem-borda',
        }
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.reload();
        }
    });
}

function realizarSorteio(tipo, data_sorteios = null, qtd_sortear = null, $target = null, tipo_sorteio = null, premio = ''){
    
    $s('.btn-acf-repetidor').prop('disabled', true);
    $s('#btn-resortear').prop('disabled', true);

    exibeCarregamentoSorteio();

    let postId = $s('#post_ID').val();

    //let qtd_sorteio;
    let acao;
    let data;
    let msg;
    
    if(tipo == 1 && (!qtd_sortear || qtd_sortear == null || qtd_sortear == '') ){
        //qtd_sorteio = $s('#acf-field_67eed3e15dcb4').val();
        msg = "Informe a quantidade a ser sorteada, por gentileza!";
    } else if (tipo == 1 && tipo_sorteio != 'periodo' && (data_sorteios || data_sorteios != null || data_sorteios != '')) {
        //qtd_sorteio = $s('#qtd-resorteada').val();
        msg = "Informe a data a ser sorteada, por gentileza!";
    } else if (tipo == 2 && (!qtd_sortear || qtd_sortear == null || qtd_sortear == '')){
        //qtd_sorteio = $s('#qtd-resorteada').val();
        msg = "Informe a quantidade a ser resorteada, por gentileza!";
    } else if (tipo == 2 && tipo_sorteio != 'periodo' && (data_sorteios || data_sorteios != null || data_sorteios != '')) {
        //qtd_sorteio = $s('#qtd-resorteada').val();
        msg = "Informe a data a ser resorteada, por gentileza!";
    }

    acao = 'sortear';
    data = {
        action: acao,
        idPost: postId,
        qtdSorteio: qtd_sortear,
        tipo: tipo,
        data_selecionada_sorteio: data_sorteios,
        premio: premio
    };

    if(!qtd_sortear || qtd_sortear == null || qtd_sortear == '') { 
        exibeMsg(msg, "info", 3000);
        $s('.btn-acf-repetidor').prop('disabled', false);
        $s('#btn-resortear').prop('disabled', false);
        
    } else if(tipo_sorteio != 'periodo' && (!data_sorteios || data_sorteios == null || data_sorteios == '')) { 
        exibeMsg(msg, "info", 3000);
        $s('.btn-acf-repetidor').prop('disabled', false);
        $s('#btn-resortear').prop('disabled', false);
    } else {

        $s.post(ajaxurl, data, function(response){ 
            if(response.res == true){
                $s(`#${response.target} .inside .conteudo-lista`).html(response.html);
                exibeMsg(response.msg, "success", 4000);

                setTimeout(function() {
                    $s('.toggle-checkbox').bootstrapToggle();
                    inicializaPagina();
                    monitoraToggles();
                    $s('.btn-acf-repetidor').prop('disabled', false);
                    $s('#btn-resortear').prop('disabled', false);
                    if (tipo == 2) {
                        $s("#qtd-resorteada").val('');
                    }

                    marcarSorteiosRelizados();

                }, 500);

                $s('div.conteudo-lista').filter(function () {
                    return $s(this).attr('data-data') === response.data;
                }).replaceWith(response.html);

                $s(`div.tab-inscritos[data-data="${response.data}"]`).replaceWith(response.htmlInscritos);

                $s(`div.tab-inscritos[data-data="${response.data}"] .tabela-participantes-sorteados`).DataTable().destroy();

                $s(`div.tab-inscritos[data-data="${response.data}"] .tabela-participantes-sorteados`).DataTable({
                    pageLength: 10,
                    ordering: false,
                    lengthChange: false,
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.11.3/i18n/pt_br.json'
                    }
                });
                $s(`span[data-ele="${response.data}"]`).text(`${response.total}`);

            } else {
                exibeMsg(response.msg, "info", 5000);
                $s('.btn-acf-repetidor').prop('disabled', false);
                $s('#btn-resortear').prop('disabled', false);
            }

        });

        
    }
    return false;
}

function reenvia_email(idPart, postId, tipoEmail, prazoConfirmacao, dataSorteada = null){
    let data = {
        action: 'envia_email_sorteio',
        idPart: idPart,
        postId: postId,
        tipoEmail: tipoEmail,
        data_sorteada: dataSorteada,
        tipo_prazo: prazoConfirmacao.tipo,
        prazo: prazoConfirmacao.valor
    };    
        
    $s.post(ajaxurl, data, function(response){ 
        exibeCarregamentoSorteioToggle('#reenvia-email-participante-sorteado-'+idPart+'-gif');
        setTimeout(function() {
            window.location.reload();
        }, 500);
    });
}

function copiaEmail(idTxt, opcao){
    
    if(opcao == 1){
        idTxt = 'email-inst-'+idTxt;
    } else if(opcao == 2){
        idTxt = 'email-sec-'+idTxt;
    }
    
    let elemento = document.getElementById(idTxt).innerHTML;

    navigator.clipboard.writeText(elemento).then(() => {
        toastr.success("<b>"+elemento+"</b> copiado para a área de transferência.");
    }).catch(err => {
        console.error('Falha ao copiar o Email: ', err);
    });
    
}

function registraDadosToggles(id, opcao, confirmacao, classToggleGif){
    let data = {
        action: 'confirmacoes_sorteio',
        idSorteado: id,
        opcao: opcao,
        confirmacao: confirmacao
    };    
        
    $s.post(ajaxurl, data, function(response){ 
        if(response.res == true){
            exibeCarregamentoSorteioToggle(classToggleGif+'-gif');
            toastr.success(response.msg);
        }
    });
}

function buscaTogglesCheck(escopo = ''){
    let idsEle = [];
    let arrChecked = [];
    let arrNoChecked = [];
    let qtdChecksConfPresenca = 0;
    document.querySelectorAll(`${escopo} .toggle`).forEach(elemento => {  // Itera sobre cada elemento da lista
        let id = elemento.children[0].id
        idsEle.push(id);
        let idEle = id.split('-')[0]+'-'+id.split('-')[1];
        if(idEle == 'conf-presenca'){
            qtdChecksConfPresenca++;
            if($s('#'+id).prop('checked') == true){
                arrChecked.push(id);
            }
            if($s('#'+id).prop('checked') == false){
                arrNoChecked.push(id);
            }
        }
    });
    return {
        ids: idsEle, 
        arrChecked: arrChecked, 
        arrNoChecked: arrNoChecked, 
        qtdConfPres: qtdChecksConfPresenca
    }; // Retorna um array com todos os idsEle;
}

function alteraToggleConfTodos(opcao){
    if(opcao == 1){
        $s('#conf-todos').bootstrapToggle('on', true);
    } else if(opcao == 2){
        $s('#conf-todos').bootstrapToggle('off', true);
    }
}

function monitoraToggles(){
    let idsEle = [];
    
    idsEle = buscaTogglesCheck();
    idsEle.ids.forEach(id => {
        $s('#'+id).change(function(event) {
            if(id != 'conf-todos' && id != 'divulgar-resultado'){
                let idToggle = id.split('-')[0]+'-'+id.split('-')[1];
                if(idToggle == 'conf-presenca'){
                    if(event.target.checked == false){
                        if( idsEle.arrChecked.length == idsEle.qtdConfPres){
                            alteraToggleConfTodos(2);
                        }
                        let indice = idsEle.arrChecked.indexOf(id);
                        idsEle.arrChecked.splice(indice, 1);
                    }
                    if(event.target.checked == true){
                        if($s('#'+id).prop('checked') == true){
                            idsEle.arrChecked.push(id);
                        }
                        if( idsEle.arrChecked.length == idsEle.qtdConfPres){
                            alteraToggleConfTodos(1);
                        } 
                    }
                }
                exibeCarregamentoSorteioToggle('#'+id+'-gif');
                let numId = id.split('-')[2];
                let opcao = id.split('-')[1];
                registraDadosToggles(numId, opcao, event.target.checked, '#'+id);
            } 
        });
    });
}

function excluiParticipanteSorteado(id, dataSelecionada){
    let postId = $s('#lista-participantes-sorteados').data('post');
    let data = {
        action: 'remove_participante_sorteado',
        postId: postId,
        idPart: id,
        date: dataSelecionada
    };    
        
    $s.post(ajaxurl, data, function(response){ 
        if(response.res == true){
            qtdSorteados--;
            exibeCarregamentoSorteioToggle('#remove-participante-sorteado-'+id+'-gif');
            $s(".sorteado-"+id).fadeOut('slow');
            setTimeout(function() {
                $s(`#${response.target} .card-body .conteudo-lista`).html(response.html);
                $s('.toggle-checkbox').bootstrapToggle();

                inicializaPagina();
                monitoraToggles();

                setTimeout(function () {
                    marcarSorteiosRelizados();
                }, 500);

            }, 500);
            toastr.success(response.msg);
            if(qtdSorteados == 0){
                $s('.conteudo-lista').html('Nenhum participante inscrito até o momento');
            }
        }
    });
}

function removerParticipanteSorteado(idParticipante, nomePaticipante, data, premio = null){

    let dataFormatada = data.split(' ');
    dataFormatada = `${dataFormatada[0].split('-').reverse().join('/')} - ${dataFormatada[1]}`;
    let tipo_sorteio = jQuery('div[data-name="tipo_evento"] select').val();

    let html = `Deseja remover o participante<br><strong> ${nomePaticipante} </strong><br>da lista dos sorteados do dia <strong>${dataFormatada}</strong>?`;

    if ( tipo_sorteio === 'periodo' ) {
        html = `Deseja remover o participante<br><strong> ${nomePaticipante} </strong><br>da lista dos sorteados?`;
    }

    if(tipo_sorteio == 'premio'){
        html = `Deseja remover o participante<br><strong> ${nomePaticipante} </strong><br>da lista dos sorteados do prêmio <strong>${premio}</strong>?`;
    }

    Swal.fire({
        title: "Atenção",
        html: html,
        icon: "error",
        showDenyButton: true,
        confirmButtonText: "SIM",
        denyButtonText: "NÃO",
        customClass: {
            popup: 'popup-notificar-sorteados',
        }
        }).then((result) => {
        if (result.isConfirmed) {
            exibeCarregamentoSorteioToggle('#remove-participante-sorteado-'+idParticipante+'-gif');
            excluiParticipanteSorteado(idParticipante, data);
        } 
    });
}

function aplicar_sancao(participantesSelecionados, dias, dataFim) {
    jQuery.ajax({
        url: ajaxurl, // no admin já existe essa global
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'aplicar_sancao',
            participantes: participantesSelecionados,
            dias: dias,
            dataFim: dataFim
        },
        beforeSend: function() {
            Swal.fire({
                title: 'Aguarde um instante...',
                text: 'Estamos salvando os dados dos participantes...',
                iconHtml: '<span class="dashicons dashicons-warning"></span>',
                customClass: {
                    popup: 'popup-notificar-sorteados sem-borda',
                },
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        },
        success: function(response) {
            Swal.fire({
                icon: 'success',
                title: 'Sanção aplicada com sucesso!',
                text: 'Sanção aplicada até: ' + dataFim, // pega do parâmetro
                showConfirmButton: false,
                timer: 3000
            }).then(() => {
                let eventoSelecionado = jQuery('#post_id_select').val();
                let nomeParticipante = jQuery('#participante').val();

                sessionStorage.setItem('eventoSelecionado', eventoSelecionado);
                sessionStorage.setItem('nomeParticipante', nomeParticipante);

                location.reload();
            });
        },
        error: function(xhr, status, error) {
            Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'Falha na requisição',
                text: error
            });
        }
    });
}

jQuery(document).ready(function($) {   
    
    $('[data-toggle="tooltip"]').tooltip();

    
    $('.tabela-participantes-sorteados').DataTable({
      pageLength: 10,
      ordering: false,
      lengthChange: false,
      language: {
        url: 'https://cdn.datatables.net/plug-ins/1.11.3/i18n/pt_br.json'
      }
    });

    $('#btn-resortear').click(function() {
        const dataSelecionada = $('#data-resorteada').val();
        const qtdSelecionada = $('#qtd-resorteada').val();
        let tipo_sorteio = jQuery('div[data-name="tipo_evento"] select').val();

        if (tipo_sorteio === 'premio') {

            const premio = $('#data-resorteada option:selected').text();
            realizarSorteio(2, dataSelecionada, qtdSelecionada, false, null, premio);

        } else if (tipo_sorteio === 'periodo') {

            realizarSorteio(2, dataSelecionada, qtdSelecionada, null, tipo_sorteio);
            
        } else {
            realizarSorteio(2, dataSelecionada, qtdSelecionada, false);
        }
    });

    $('.btn-acf-repetidor').click(function() {
        // Desativa todos os botões
        $('.btn-acf-repetidor').prop('disabled', true);

        let tipo_sorteio = jQuery('div[data-name="tipo_evento"] select').val();
        var $linha = $(this).closest('.acf-row');
        var data = $linha.find('[data-name="data"] input').val();
        var numero = $linha.find('[data-name="num_sorteio"] input').val();
        var target = $(this);
        
        if (tipo_sorteio == 'premio') {
            // Verifica se existe o campo "premio" e se tem valor
            var premioField = $linha.find('[data-name="premio"] input');
            var premio = '';

            if (premioField.length && premioField.val().trim() !== '') {
                premio = premioField.val();
            }

            realizarSorteio(1, data, numero, target, null, premio);

        } else if (tipo_sorteio == 'periodo') {

            var data = $linha.find('[data-name="data_sorteio"] input').val();
            var numero = $linha.find('[data-name="num_sorteio"] input').val();

            realizarSorteio(1, data, numero, target, tipo_sorteio);

        } else {

            realizarSorteio(1, data, numero, target);
        }

    });

    inicializaPagina();
    monitoraToggles();

    // Confirmar remoção de linha do repetidor
    $(document).on('click', '.acf-repeater .acf-icon.-minus', function(e) {
        e.preventDefault(); // Impede a remoção imediata

        let tipo_evento = jQuery('div[data-name="tipo_evento"] select').val();
        let title;
        let text;

        if (tipo_evento === 'premio') {
            title = 'Remover prêmio?';
            text = 'Tem certeza que deseja remover este prêmio do sorteio? Esta ação não poderá ser desfeita.';
        } else {
            title = 'Remover data?';
            text = 'Tem certeza que deseja remover esta data de sorteio? Esta ação não poderá ser desfeita.';
        } 

        const $botao = $(this);

        Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, remover',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Usar a API do ACF para remover corretamente a linha
                const repeater = acf.getClosestField($botao);
                const row = $botao.closest('.acf-row');

                if (repeater && row.length) {
                    repeater.remove(row);
                }
            }
        });
    });

    $('.acf-field-date-picker input.hasDatepicker').mask('00/00/0000');
    $('.acf-field-time-picker input.hasDatepicker').mask('00:00');

    // Clique no botão "Enviar" dentro do modal
    $(document).on('click', '.modal.show .btn-enviar', function(e) {
        e.preventDefault();

        var $modal = $(this).closest('.modal');

        // 1) Editor dentro do modal
        var $editorEl = $modal.find('.editorEmail').first();

        // 2) Instância do Quill
        var quill = $editorEl.data('quill');
        if (!quill) {
            console.warn('Quill não inicializado para este modal.');
            return;
        }

        // 3) Conteúdo do editor
        var conteudoEmail = quill.root.innerHTML;

        // Remove imagens de emoji inseridas pelo Quill
        conteudoEmail = conteudoEmail.replace(/<img[^>]*role="img"[^>]*>/g, function(match){
            var alt = match.match(/alt="([^"]*)"/);
            return alt ? alt[1] : '';
        });

        // Verifica se está vazio
        if (quill.getText().trim().length === 0) {
            Swal.fire({
                title: "Atenção",
                text: "O conteúdo do e-mail não pode estar vazio.",
                iconHtml: '<span class="dashicons dashicons-warning"></span>',
                customClass: {
                    popup: 'popup-notificar-sorteados',
                }
            });
            return;
        }

        // Post ID
        var postID = $('[data-toggle="modal"][data-target="#' + $modal.attr('id') + '"]').data('postid');
        var responsavel = $('[data-toggle="modal"][data-target="#' + $modal.attr('id') + '"]').data('responsavel');

        // Radio selecionado
        var opcao = $modal.find('input[name="opcao_envio"]:checked').val();
        if (!opcao) {            
            Swal.fire({
                title: "Atenção",
                text: "Selecione uma opção antes de enviar.",
                iconHtml: '<span class="dashicons dashicons-warning"></span>',
                customClass: {
                    popup: 'popup-notificar-sorteados',
                }
            });
            return;
        }

        // Identificador
        var dataRef = $modal.data('data') || $modal.attr('id').replace('modal_', '');

        // Participantes
        var selecionados = [];
        if (opcao === 'selecionados') {
            $('#sorteados_data_' + dataRef)
                .find('.check-item:checked:not(:disabled)')
                .each(function () {
                    selecionados.push($(this).val());
                });

            if (!selecionados.length) {
                Swal.fire({
                    title: "Atenção",
                    text: "Você precisa selecionar ao menos um participante na listagem para usar esta opção.",
                    iconHtml: '<span class="dashicons dashicons-warning"></span>',
                    customClass: {
                        popup: 'popup-notificar-sorteados',
                    }
                });
                return;
            }
        }

        // Arquivo
        var fileInput = $modal.find('.input-anexo')[0];
        var file = fileInput ? fileInput.files[0] : null;

        // Usando FormData
        var formData = new FormData();
        formData.append('action', 'enviar_instrucoes');
        formData.append('opcao', opcao);
        formData.append('data', dataRef);
        formData.append('post_id', postID);
        formData.append('responsavel', responsavel);
        formData.append('conteudo_email', conteudoEmail);

        if (file) {
            formData.append('anexo', file);
        }

        selecionados.forEach(function(v, i) {
            formData.append('participantes[' + i + ']', v);
        });

        Swal.fire({
            title: 'Aguarde um instante...',
            text: 'Estamos enviando os e-mails para os selecionados!',
            iconHtml: '<span class="dashicons dashicons-warning"></span>',
            customClass: {
                    popup: 'popup-notificar-sorteados',
            },
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading(); // Mostra spinner
            }
        });

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: formData,
            processData: false, // obrigatório p/ FormData
            contentType: false, // obrigatório p/ FormData
            beforeSend: function () {
                // Timer para verificar demora (15s)
                demoraTimeout = setTimeout(function () {
                    Swal.fire({
                        title: 'Envio em andamento',
                        text: 'Os E-mails de instruções estão sendo enviados aos participantes selecionados.',
                        iconHtml: '<span class="dashicons dashicons-email-alt2"></span>',
                        showCancelButton: false,
                        confirmButtonText: 'Fechar',
                        allowOutsideClick: false,
                        customClass: {
                            popup: 'popup-notificar-sorteados',
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();
                        }
                    });
                }, 15000);
            },
            success: function (response) {
                clearTimeout(demoraTimeout); // cancela o alerta de demora se já não tiver disparado

                // Fecha modal
                $modal.modal('hide');

                // Limpa campo de anexo e radios
                $modal.find('.input-anexo').val('');
                $modal.find('input[name="opcao_envio"]').prop('checked', false);

                // Mostra alerta de sucesso
                Swal.fire({
                    icon: 'success',
                    title: 'E-mails enviados com sucesso!',
                    text: 'Seu e-mail com as instruções do evento foi enviado.',
                    willClose: () => {
                        location.reload(); // Atualiza a página após fechar o alerta
                    }
                });
            },
            error: function (xhr, status, error) {
                clearTimeout(demoraTimeout); // cancela o alerta de demora

                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: 'Ocorreu um problema ao enviar as instruções.'
                });
                console.error('Erro:', error);
            }
        });


    });

});

jQuery(function($){

    marcarSorteiosRelizados();
    adicionarContagemInscritos();

    var $radioParticipantesConfirmados = $(document).find('.radio-todos');
    var $radioParticipantesSelecionados = $(document).find('.radio-selecionados');
    var $radioParticipantesGeral = $(document).find('.radio-geral');
    var $btnRequerConfirmacao = $('div[data-name="confirm_presen"] input[type="checkbox"]');

    /**
    * Verifica se o evento requer confirmação de presença e ajusta a visualização
    * e ajusta a visualização do modal de instruções.
    */
    if ( !$btnRequerConfirmacao.is(':checked') ) {
        $radioParticipantesConfirmados.addClass('d-none');
        $radioParticipantesGeral.removeClass('d-none');
        $radioParticipantesConfirmados.find('.custom-control-input').prop('checked', false);
        $radioParticipantesGeral.find('.custom-control-input').prop('checked', true);   
    }
    
    //Evento para controlar a opção de selecionar todos os sorteados
    $(document).on('change', '.check-all', function () {
        var $tabela = $(this).closest('table');
        $tabela.find('.check-item:visible:not(:disabled)')
            .prop('checked', $(this).prop('checked'))
            .trigger('change');
    });

    //Controla as ações do campo de "Requer confirmação de presença".
    $btnRequerConfirmacao.on('change', function () {
        const isChecked = $(this).is(':checked');
        const postId = $('#post_ID').val();

        if (!isChecked) {
            $('.btn-notificar-sorteados').prop('disabled', true);
            $(document).find('.check-contato').addClass('d-none');
            $(document).find('.check-presenca').addClass('d-none');
            $(document).find('.reevia-email-sorteado ').addClass('d-none');
            $(document).find('.tit-histo').addClass('d-none');
            $(document).find('.cont-histo').addClass('d-none');

            //Ajusta as opções do modal de envio de instruções
            $radioParticipantesConfirmados.addClass('d-none');
            $radioParticipantesGeral.removeClass('d-none');
            $radioParticipantesConfirmados.find('.custom-control-input').prop('checked', false);
            $radioParticipantesGeral.find('.custom-control-input').prop('checked', true);

        } else {
            $(document).find('.check-contato').removeClass('d-none');
            $(document).find('.check-presenca').removeClass('d-none');
            $(document).find('.reevia-email-sorteado ').removeClass('d-none');
            $(document).find('.tit-histo').removeClass('d-none');
            $(document).find('.cont-histo').removeClass('d-none');

            //Ajusta as opções do modal de envio de instruções
            $radioParticipantesConfirmados.removeClass('d-none');
            $radioParticipantesGeral.addClass('d-none');
            $radioParticipantesConfirmados.find('.custom-control-input').prop('checked', true);
        }

        $('.check-item').prop('checked', false).trigger('change');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'atualizar_confirm_presen',
                post_id: postId,
                valor: isChecked ? 1 : 0,
            },
            success: function(response) {
                if (response.success) {
                    console.log('Valor atualizado com sucesso:', response.data);
                } else {
                    console.warn('Houve um erro ao atualizar o valor:', response.data);
                }
            },
            error: function() {
                console.error('Falha na requisição AJAX');
            }
        });
    });

    /*
    * Evento para as ações individuais dos checkboxs,
    * controla também as ações de ativar e desativar o de notificar os sorteados
    */
    $(document).on('change', '.check-item', function() {

        var $tabela = $(this).closest('table');
        var $btnNotificarSorteados = $tabela.closest('.conteudo-lista').find('.accordion-buttons .btn-notificar-sorteados');
        const $btnRequerConfirmacao = $('div[data-name="confirm_presen"]').find('input[type="checkbox"]');

        // conta apenas os itens ativos
        var totalItensSelecionados = $tabela.find('.check-item:checked:not(:disabled)').length;
        var totalItensHabilitados = $tabela.find('.check-item:not(:disabled)').length;

        // só compara habilitados
        let selecionarTudo = (totalItensSelecionados === totalItensHabilitados);

        $tabela.find('.check-all').prop('checked', selecionarTudo);

        if ($btnRequerConfirmacao.is(':checked')) {
            $btnNotificarSorteados.prop('disabled', (totalItensSelecionados == 0));
        }
    });

    //Controla o comportamento do formulário de definição do prazo de validade do link de confirmação
    $(document).on('change', '.popup-notificar-sorteados #formOpcao .form-check-input', function () {
        const tipo = $(this).val();
        $('.popup-notificar-sorteados #formOpcao .form-control').hide();
        $(`.popup-notificar-sorteados #formOpcao #input-${tipo}`).show();
    });

    //Evento que controla as ações do botão de notificar sorteados
    $(document).on('click', '.btn-notificar-sorteados', function (e) {
        e.preventDefault();

        var $tabela = $(this).closest('.conteudo-lista').find('table');
        var participantesSelecionados = $tabela.find('.check-item:checked').map(function() {
            return $(this).val();
        })
        .get();

        Swal.fire({
            html: renderHtmlFormPrazoConfirmacao(),
            showCancelButton: true,
            confirmButtonText: 'Enviar',
            cancelButtonText: 'Fechar',
            reverseButtons: true,
            customClass: {
                popup: 'popup-notificar-sorteados sem-borda p-4',
            },
            preConfirm: () => {
                const $selected = jQuery('input[name="tipo"]:checked');

                if (!$selected) {
                    Swal.showValidationMessage('Você precisa selecionar uma opção!');
                }

                const $inputValue = jQuery(`#input-${$selected.val()}`);

                if (!$inputValue.val().length) {
                    Swal.showValidationMessage('Você precisa definir um valor para continuar.');
                }

                return {
                    'tipo': $selected.val(),
                    'valor': $inputValue.val()
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                let prazoConfirmacao = result.value;
                enviaEmailListaSorteados(participantesSelecionados, prazoConfirmacao);
            }
        });
    });

    $(document).on('click', '.btn-aplicar-sancao', function (e) {
        e.preventDefault();

        var $tabela = $(this).closest('.conteudo-lista').find('table');
        var participantesSelecionados = $tabela.find('.check-item:checked').map(function() {
            return $(this).val();
        }).get();

        // Verifica se há pelo menos um participante selecionado
        if (participantesSelecionados.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Atenção',
                text: 'Selecione ao menos um participante para aplicar a sanção.'
            });
            return; // Interrompe a execução
        }

        Swal.fire({
            title: 'Aplicar sanção',
            text: 'Esta ação irá impedir que os participantes selecionados se inscrevam em novos sorteios pelo período que você escolher. Deseja aplicar a sanção?',
            iconHtml: '<span class="dashicons dashicons-warning"></span>',
            input: 'number',
            inputLabel: 'Quantidade de dias:',
            inputValue: 30,
            inputAttributes: {
                min: 1,
                step: 1
            },
            allowOutsideClick: false,
            allowEscapeKey: false,
            showCancelButton: true,
            confirmButtonText: 'Confirmar',
            cancelButtonText: 'Cancelar',
            customClass: {
                popup: 'popup-notificar-sorteados sem-borda',
            },
            preConfirm: (value) => {
                if (!value || value <= 0) {
                    Swal.showValidationMessage('Informe um número válido de dias');
                    return false;
                }
                return value;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                let dias = result.value;

                // Calcula a data final da sanção
                let dataFim = new Date();
                dataFim.setDate(dataFim.getDate() + parseInt(dias));

                // Formata a data (DD/MM/YYYY)
                let dataFimFormatada = dataFim.toLocaleDateString('pt-BR');

                aplicar_sancao(participantesSelecionados, dias, dataFimFormatada);
            }
        });
    });

});

jQuery(document).on('click', '.btn-debloqueio', function(e) {
    e.preventDefault();

    var $btn = jQuery(this);
    var sancaoId = $btn.data('sancao-id');
    var $statusSpan = $btn.closest('tr').find('.check-presenca .valor-status span');

    // Primeiro modal: confirmação
    Swal.fire({
        title: 'Atenção',
        text: 'Deseja permitir que este usuário volte a se inscrever e concorrer a sorteios?',
        iconHtml: '<span class="dashicons dashicons-warning"></span>',
        showCancelButton: true,
        confirmButtonText: 'Sim',
        cancelButtonText: 'Cancelar',
        customClass: {
            popup: 'popup-notificar-sorteados sem-borda',
        }
    }).then((result) => {
        if (result.isConfirmed) {

            // Modal de carregamento
            Swal.fire({
                title: 'Aguarde um instante...',
                text: 'Estamos permitindo a inscrição deste usuário...',
                iconHtml: '<span class="dashicons dashicons-warning"></span>',
                allowOutsideClick: false,
                allowEscapeKey: false,
                customClass: {
                    popup: 'popup-notificar-sorteados sem-borda',
                },
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Chamada Ajax
            jQuery.ajax({
                url: ajaxurl, // já existe no admin
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'debloquear_usuario',
                    sancao_id: sancaoId
                },
                success: function(response) {
                    Swal.close();

                    if(response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Participante desbloqueado com sucesso!',
                            showConfirmButton: false,
                            timer: 2000
                        });

                        // Atualiza o status na linha da tabela
                        $statusSpan.html('<span class="dest-azul">NÃO</span>');

                        // Remove o botão de desbloqueio
                        $btn.remove();

                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: response.data || 'Não foi possível desbloquear o usuário'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Falha na requisição',
                        text: error
                    });
                }
            });
        }
    });
});


// Ao carregar a página
jQuery(document).ready(function($){
    let eventoSelecionado = sessionStorage.getItem('eventoSelecionado');
    let nomeParticipante = sessionStorage.getItem('nomeParticipante');

    if(eventoSelecionado) {
        $('#post_id_select').val(eventoSelecionado).trigger('change'); // select2
    }
    if(nomeParticipante) {
        $('#participante').val(nomeParticipante);
    }

    // Força a busca automaticamente
    if(eventoSelecionado || nomeParticipante) {
        $('#buscar-participantes').trigger('click');
    }

    // Limpa sessionStorage
    sessionStorage.removeItem('eventoSelecionado');
    sessionStorage.removeItem('nomeParticipante');
});

/*
* Função para desativar o botão de sortear das linhas em que já houve sorteio.
*/
function marcarSorteiosRelizados() {
    jQuery('.btn-acf-repetidor').each(function(){

        let tipo_sorteio = jQuery('div[data-name="tipo_evento"] select').val();
        var $btn = jQuery(this);
        var $linha = $btn.closest('.acf-row');
        var data_hora = $linha.find('[data-name="data"] input').val();
        const seletor = (tipo_sorteio == 'periodo') ? '.conteudo-lista' : `[data-data="${data_hora}"]`;
        let $inputs = $linha.find('input:visible:not(.input-alt)');
        var sorteio_realizado = jQuery(document)
            .find(`.accordion-card ${seletor} table tbody .sorteado-item`)
            .filter(function () {
                return jQuery(this).css('display') !== 'none' && !jQuery(this).is('[hidden]');
            });

        if (sorteio_realizado.length > 0) {
            $btn.prop('disabled', true).html(`
                <span class="dashicons dashicons-yes-alt mr-2"></span>Sorteio realizado
            `).text();
            
            $inputs.prop('readonly', true).css('pointer-events', 'none');
        } else {
            $btn.prop('disabled', false).html('Sortear').text();
            $inputs.prop('readonly', false).css('pointer-events', 'auto');
        }
    });
};

/*
* Adiciona a quantidade de inscritos na linha do sorteio
*/
function adicionarContagemInscritos() {

    jQuery('.btn-acf-repetidor').each(function(){

        var $btn = jQuery(this);
        var $linha = $btn.closest('.acf-row');
        let tipo_sorteio = jQuery('div[data-name="tipo_evento"] select').val();
        var data_hora = $linha.find('[data-name="data"] input').val();
        var quantidade_inscritos = 0;

        if (tipo_sorteio != 'periodo') {
            quantidade_inscritos = jQuery(document).find(`.tab-inscritos[data-data="${data_hora}"] table tbody tr`).length;

        } else {
            quantidade_inscritos = jQuery('#accordion .card-inscritos').data('count');
        }

        $linha.find('.contador_qtd_inscritos .acf-input').text(quantidade_inscritos);
        
    });

}

function renderHtmlFormPrazoConfirmacao(sorteado = null) {
    const mensagem = sorteado
        ? `Defina o prazo para que o(a) sorteado(a) <strong>${sorteado}</strong> confirme a presença pelo link enviado no e-mail.`
        : 'Defina o prazo para que os sorteados confirmem a presença pelo link enviado no e-mail.';
    
    return`
    <h4>Prazo para confirmação de presença</h4>
    <br>
    <h6>${mensagem}</h6>
    <form id="formOpcao" class="container text-left mt-5">
        <div class="form-group">
            <div class="form-check form-check-inline align-items-center">
                <input class="form-check-input" type="radio" name="tipo" id="radioHoras" value="horas" checked>
                <label class="form-check-label mr-2" for="radioHoras" style="width:67px">Horas</label>
                <input type="time" id="input-horas" class="form-control form-control-sm" style="width:220px" value="01:00">
            </div>
        </div>
        <div class="form-group">
            <div class="form-check form-check-inline align-items-center">
                <input class="form-check-input" type="radio" name="tipo" id="radioDias" value="dias">
                <label class="form-check-label mr-2" for="radioDias" style="width:67px">Dias</label>
                <input type="number" id="input-dias" class="form-control form-control-sm" style="width:220px; display:none;" min="1" step="1" value="1">
            </div>
        </div>
    </form>`
}

jQuery(function($) {

    // Select do ACF dentro do data-name="tipo_evento"
    const selector = 'div[data-name="tipo_evento"] select';

    $(document).on('mousedown', selector, function(e) {

        const inscritos = $('.tabela-participantes-sorteados tbody tr');

        if (inscritos.length > 0) {
            toastr.error("Não é permitido alterar o Tipo de Sorteio, pois já existem inscrições registradas.");
            
            return false;
        }

    });
});