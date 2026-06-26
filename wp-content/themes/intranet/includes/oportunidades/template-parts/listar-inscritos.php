<?php
/**
 * Template para exibição de candidatos inscritos em uma oportunidade
 */

wp_enqueue_script( 'sweetalert-sorteio-js' );

// Lista completa para os selects
$todos_status_select = array(
    'analise_curricular' => 'Em Análise Curricular',
    'nao_avancou_triagem' => 'Candidatura não avançou na Triagem',
    'convocado_teste' => 'Convocado para Teste/Avaliação',
    'entrevista_agendada' => 'Entrevista Agendada',
    'nao_avancou_pos_entrevista' => 'Candidatura não avançou pós-entrevista',
    'fase_anuencia' => 'Em Fase de Anuência',
    'entrega_documentos' => 'Em Fase de Entrega de Documentos',
    'analise_documental' => 'Análise Documental + Publicação DOC',
    'aprovado' => 'Processo Finalizado - Candidato Aprovado',
    'nao_selecionado' => 'Processo Finalizado - Candidato não selecionado'
);

$current_post_id = isset($_GET['post']) ? intval($_GET['post']) : 0;

if ($current_post_id > 0) {

    $participantes = Oportunidade::get_inscricoes( $current_post_id );
    $total_participantes = count( $participantes );

} else {
    $participantes = array();
    $total_participantes = 0;
}
?>

<div class="container-fluid">    
    <div class="meu-layout-superior">
        <div class="row">

            <div class="col-12">
                <p><strong>
                    <?php 
                    printf(
                        _n(
                            '<strong>%s</strong> candidato inscrito',
                            '<strong>%s</strong> candidatos inscritos',
                            $total_participantes
                        ),
                        $total_participantes
                    );
                    ?>
                </strong></p>
            </div>            

            <div class="col-md-5">
                <div class="">
                    <div class="filtros-header">                   
                        
                        <div class="filtros-adicionais">
                            <label for="filtro-situacao">Filtrar por etapa</label>
                            <select id="filtro-situacao" class="filtro-select form-control">
                                <option value="">Todas as etapas</option>
                                <?php foreach ($todos_status_select as $codigo => $descricao): ?>
                                    <option value="<?php echo esc_attr($codigo); ?>"><?php echo esc_html($descricao); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="busca-personalizada">
                            <label for="campo-busca-personalizado">Buscar</label>
                            <input type="text" id="campo-busca-personalizado" 
                                placeholder="Buscar por nome, RF, e-mail..." 
                                class="campo-busca-estilizado form-control">
                            <button class="btn-limpar-busca" type="button" onclick="limparBusca()">✕</button>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-md-2"></div>            

            <div class="col-md-5">

                <button type="button" class="btn btn-outline-primary" id="btn-exportar-excel">
                    <i class="fa fa-file-excel-o" aria-hidden="true"></i> Exportar Excel
                </button>
                <button
                    type="button"
                    class="btn btn-outline-success"
                    id="btn-comunicar-selecionados"
                    title="Para habilitar esta ação, selecione um ou mais candidatos na listagem."
					data-toggle="modal"
					data-target="#modal-comunicar-candidatos"
                    disabled
                    >
                    <i class="fa fa-paper-plane" aria-hidden="true"></i> Comunicar Candidatos
                </button>

                <label for="etapa-massa">
                    Alterar etapa do processo seletivo individual ou em massa
                    <i class="fa fa-info-circle text-info" aria-hidden="true" data-toggle="tooltip" data-placement="right" title="Selecione pelo menos um candidato para habilitar esta opção."></i>
                </label>
                <div class="d-flex gap-2">
                    <select id="etapa-massa" class="filtro-select form-control" disabled>
                        <option value="">Selecione uma opção</option>
                        <?php foreach ($todos_status_select as $codigo => $descricao): ?>
                            <option value="<?php echo esc_attr($codigo); ?>"><?php echo esc_html($descricao); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn btn-primary btn-aplicar-etapa" onclick="aplicarEtapa()" type="button" disabled>Aplicar</button>
                </div>
            </div>

        </div>

    </div>

    <div class="row">
        <div class="col-12">
            <div class="table-responsive">
                <table class="table table-striped tabela-candidatos-inscritos" id="tabela-candidatos">
                    <thead>
                        <tr >
                            <th width="40" class="align-middle"><input type="checkbox" id="check-all" class="check-all"></th>
                            <th class="align-middle">Nome completo</th>
                            <th class="align-middle">Etapa do Processo Seletivo</th>
                            <th class="align-middle">RF</th>
                            <th class="align-middle">E-mail institucional</th>
                            <th class="align-middle">WhatsApp</th>
                            <th class="align-middle">Currículo</th>
                            <th class="align-middle">Ações do Processo Seletivo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($participantes)): ?>
                            <?php get_template_part( 'includes/oportunidades/template-parts/linhas-tabela-inscritos', null, ['participantes' => $participantes] ); ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">Nenhuma inscrição encontrada.</td>
                            </tr>
                        <?php endif; ?>             
                    </tbody>
                </table>
            </div>
        </div>
    </div>

	<div class="modal fade" id="modal-comunicar-candidatos" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
			<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Comunicar Candidatos</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">

				<div class="alert alert-warning" role="alert">
					<i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
					Utilize esta funcionalidade para enviar uma mensagem aos candidatos selecionados na listagem.
					A seleção pode incluir candidatos em diferentes etapas ou status do processo seletivo,
					porém o mesmo conteúdo será enviado para todos os destinatários.
				</div>

				<hr>
                <div class="bloco-conteudo">
                    <label for="conteudo-email">Mensagem <span class="text-danger">*</span></label>
                    <p class="m-0 error-message" style="display: none;"></p>
                    <div class="conteudo-email" name="conteudo_email" id="conteudo-email"></div>
                </div>
				<hr>
				<div class="form-group">
					<label for="anexos-email" class="m-0">Anexar arquivos (PDF, DOC, etc.)</label>
                    <p class="text-secondary mt-0"><i class="fa fa-info-circle" aria-hidden="true"></i> Limite: 5 arquivos de, no máximo, 2MB cada.</p>
					<input
						type="file"
						class="form-control-file anexos-email"
						id="anexos-email" 
						name="anexo"
                        multiple
                        accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
					>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
				<button type="button" class="btn btn-success btn-enviar"><i class="fa fa-paper-plane" aria-hidden="true"></i> Enviar Mensagem</button>
			</div>
			</div>
		</div>
	</div>
</div>

<script>

    function inicializarComponentesTabelaInscritos() {
        // Máscara para celular
        jQuery('.celular-mask').mask('(00) 00000-0000');
    }

    jQuery(document).ready(function($) {

        const $btnComunicarSelecionados = $('#btn-comunicar-selecionados'); // Botão Comunicar selecionados

        inicializarComponentesTabelaInscritos();

        // Configurações do toastr
        toastr.options = {
            positionClass: 'toast-bottom-right',
            timeOut: 3000
        };

        // Evento para copiar texto
        $(document).on('click', '.copiar-texto', function() {

            let texto = $(this).data('texto');
            navigator.clipboard.writeText(texto).then(function() {
                toastr.success('Informação copiada com sucesso!');
            }).catch(function() {
                toastr.error('Não foi possível copiar a informação.');
            });

        });

        // Verifica se a tabela existe e tem dados
        if ($('#tabela-candidatos tbody tr').length > 0 && $('#tabela-candidatos tbody td').length > 0) {
            
            // Função para extrair o texto da situação (ignorando HTML)
            function getSituacaoTexto(cellHtml) {
                // Cria um elemento temporário para manipular o HTML
                var temp = document.createElement('div');
                temp.innerHTML = cellHtml;
                // Encontra o span com a classe card-etapa
                var cardSpan = $(temp).find('.card-etapa').first();
                // Pega o texto, separa por <br> e pega a primeira linha
                var texto = cardSpan.contents().filter(function() {
                    return this.nodeType === 3; // Text nodes only
                }).first().text().trim();
                
                // Se não encontrou texto, tenta outra abordagem
                if (!texto) {
                    texto = cardSpan.text().split('\n')[0].trim();
                }
                
                return texto;
            }
            
            var tableConfig = {
                "language": {
                    "search": "",
                    "searchPlaceholder": "Pesquisar...",
                    "lengthMenu": "Mostrar _MENU_ registros",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ candidatos",
                    "infoEmpty": "Nenhum candidato encontrado",
                    "zeroRecords": "Nenhum candidato corresponde à busca",
                    "paginate": {
                        "first": "«",
                        "last": "»",
                        "next": "›",
                        "previous": "‹"
                    }
                },
                "paging": false,
                "searching": true,
                "ordering": true,
                "info": false,
                "autoWidth": false,
                "dom": 'rt',
                "order": [[1, 'asc']],
                "columnDefs": [
                    {
                        "targets": [0],
                        "orderable": false
                    },
                    {
                        "targets": [2], // Coluna "Situação da Candidatura"
                        "render": function(data, type, row) {
                            if (type === 'display') {
                                return data; // Retorna HTML para exibição
                            }
                            if (type === 'filter' || type === 'sort') {
                                // Para filtro/ordenação, retorna apenas o texto puro
                                return getSituacaoTexto(data);
                            }
                            return data;
                        }
                    }
                ]
            };

            // Verifica se a tabela existe e tem dados REAIS (não a mensagem de "nenhum candidato")
            var $table = $('#tabela-candidatos');
            var $tbody = $table.find('tbody');
            var hasRealData = $tbody.find('tr').length > 0 &&
                $tbody.find('tr:first td').length > 1 &&
                $tbody.find('tr:first td').text().indexOf('Nenhum candidato') === -1;

            if (hasRealData) {

                // Inicializa o DataTable
                var table = $(document).find('#tabela-candidatos').DataTable(tableConfig);
            }

            
            // Mapeamento de códigos para descrições
            var statusDescricaoMap = {
                'analise_curricular': 'Em Análise Curricular',
                'nao_avancou_triagem': 'Candidatura não avançou na Triagem',
                'convocado_teste': 'Convocado para Teste/Avaliação',
                'entrevista_agendada': 'Entrevista Agendada',
                'nao_avancou_pos_entrevista': 'Candidatura não avançou pós-entrevista',
                'fase_anuencia': 'Em Fase de Anuência',
                'entrega_documentos': 'Em Fase de Entrega de Documentos',
                'analise_documental': 'Análise Documental + Publicação DOC',
                'aprovado': 'Processo Finalizado - Candidato Aprovado',
                'nao_selecionado': 'Processo Finalizado - Candidato não selecionado'
            };
            
            // Filtro por situação
            $('#filtro-situacao').on('change', function() {
                var situacaoCodigo = $(this).val();
                
                if (situacaoCodigo === '') {
                    // Limpa o filtro da coluna 2
                    table.columns(2).search('').draw();
                } else {
                    // Pega a descrição correspondente ao código
                    var statusDescricao = statusDescricaoMap[situacaoCodigo] || situacaoCodigo;
                    
                    // Aplica o filtro na coluna 2
                    table.columns(2).search(statusDescricao, false, false).draw();
                }
            });
            
            // Busca personalizada
            var buscaInput = $('#campo-busca-personalizado');
            var limparBtn = $('.btn-limpar-busca');
            
            buscaInput.on('keyup', function() {
                var termo = $(this).val();
                table.search(termo).draw();
                
                if (termo.length > 0) {
                    limparBtn.show();
                    $(this).addClass('busca-ativa');
                } else {
                    limparBtn.hide();
                    $(this).removeClass('busca-ativa');
                }
            });
            
            // Selecionar/desmarcar todos os checkboxes (apenas os não desabilitados)
            $('#check-all').on('change', function() {
                $('.check-item:not(:disabled)').prop('checked', $(this).prop('checked')).trigger('change');
            });

            // Atualiza o checkbox "selecionar todos" quando um individual muda
            $(document).on('change', '.check-item', function() {
                var totalCheckboxes = $('.check-item:not(:disabled)').length;
                var checkedCheckboxes = $('.check-item:not(:disabled):checked').length;
                
                if (checkedCheckboxes === totalCheckboxes) {
                    $('#check-all').prop('checked', true);
                } else {
                    $('#check-all').prop('checked', false);
                }

                if ($('.check-item:checked').length) {
                    $('#etapa-massa').prop('disabled', false);

                    $btnComunicarSelecionados.prop('disabled', false);
                    $btnComunicarSelecionados.attr('title', '');

                } else {
                    $('#etapa-massa').val('');
                    $('#etapa-massa').prop('disabled', true);

                    $btnComunicarSelecionados.prop('disabled', true);
                    $btnComunicarSelecionados.attr('title', 'Para habilitar esta ação, selecione um ou mais candidatos na listagem.');
                }
            });

            $('#etapa-massa').on('change', function() {

                if ($(this).val() == "") {
                    $('.btn-aplicar-etapa').prop('disabled', true);
                } else {
                    $('.btn-aplicar-etapa').prop('disabled', false);
                }
            });
            
            // Função para limpar busca
            window.limparBusca = function() {
                buscaInput.val('');
                $('#filtro-situacao').val('');
                table.search('').columns(2).search('').draw();
                buscaInput.removeClass('busca-ativa');
                limparBtn.hide();
            };
            
            // Função para aplicar etapa em massa
            window.aplicarEtapa = function() {
                var novaEtapaCodigo = $('#etapa-massa').val();
                if (!novaEtapaCodigo) {
                    toastr.error('Selecione uma etapa para aplicar.');
                    return;
                }
                
                var checkboxes = $('.check-item:checked');
                if (checkboxes.length === 0) {
                    toastr.error('Selecione pelo menos um candidato');
                    return;
                }
                
                var statusDescricao = $('#etapa-massa option:selected').text();

                Swal.fire({
                    icon: 'warning',
                    title: 'Atualização de etapa da inscrição',
                    html: `
                        <h6 class=" text-left mt-4 mb-0">Deseja alterar a etapa de <b>${checkboxes.length}</b> candidato(s) para <b>${statusDescricao}</b>?</h6>
                    `,
                    showCancelButton: true,
                    cancelButtonText: 'Cancelar',
                    confirmButtonText: 'Confirmar',
                    confirmButtonColor: '#14447C',
                    reverseButtons: true
                }).then((result) => {

                    if (!result.isConfirmed) {
                        return;
                    }

                    var ids = [];
                    checkboxes.each(function() {
                        ids.push($(this).val());
                    });
                    
                    // Requisição AJAX
                    $.ajax({
                        url: window.ajaxurl || window.location.href,
                        method: 'POST',
                        data: {
                            action: 'atualizar_etapa_candidatos',
                            ids: ids,
                            etapa_codigo: novaEtapaCodigo,
                            post_id: <?php echo $current_post_id; ?>,
                            nonce: '<?php echo wp_create_nonce( 'atualizar_etapa_candidatos' ); ?>'
                        },
                        beforeSend: function () {
                            Swal.fire({
                                text: 'Aplicando as alterações. Aguarde alguns instantes...',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                        },
                        success: function(response) {
                            if (response.success) {

                                Swal.fire({
                                    icon: 'success',
                                    text: 'Etapa do processo atualizada com sucesso para os candidatos selecionados.',
                                    confirmButtonText: 'Fechar',
                                    confirmButtonColor: '#14447C',
                                });

                                table.destroy();
                                $(document).find('#tabela-candidatos tbody').html(response.data.html);
                                $('#check-all').prop('checked', false).trigger('change');
                                $('.btn-aplicar-etapa').prop('disabled', true);

                                table = $(document).find('#tabela-candidatos').DataTable(tableConfig);
                                inicializarComponentesTabelaInscritos();
                                
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Não foi possível completar a ação',
                                    text: response.data.message,
                                    confirmButtonText: 'Fechar',
                                    confirmButtonColor: '#14447C',
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro inesperado',
                                text: 'Ocorreu um erro ao realizar a ação.',
                                confirmButtonText: 'Fechar',
                                confirmButtonColor: '#14447C',
                            });
                        }
                    });

                });
            };
                       
            // Exportar Excel
            $('#btn-exportar-excel').on('click', function() {
                var data = [];
                var headers = [];
                
                $('#tabela-candidatos thead th').each(function(index, th) {
                    if (index !== 0) {
                        headers.push($(th).text().trim());
                    }
                });
                
                $('#tabela-candidatos tbody tr').each(function() {
                    var row = [];
                    $(this).find('td').each(function(index, td) {
                        if (index !== 0) {
                            // Para a coluna de situação, extrai apenas o texto
                            if (index === 2) {
                                var text = getSituacaoTexto($(td).html());
                                row.push(text);
                            } else {
                                var text = $(td).text().trim();
                                row.push(text);
                            }
                        }
                    });
                    if (row.length > 0) {
                        data.push(row);
                    }
                });
                
                console.log('Exportar dados:', headers, data);
                alert('Função de exportação será implementada');
            });

            $(document).on('click', '.btn-comunicar-selecionados', function(){

                var user_id = this.getAttribute('data-id');
                var status = this.getAttribute('data-status');


                Swal.fire({
                    customClass: {
                        popup: 'modal-comunicacao',
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-outline-secondary'
                    },                 
                    html: `
                        <div class="text-left modal-prazo">

                            <h3 class="mb-3">Prazo para Confirmação de Interesse no Processo Seletivo</h3>

                            <p class="mb-3">
                               Defina o prazo para que o(a) candidato(a) confirme o interesse em prosseguir no processo seletivo.
                            </p>

                            <hr>

                            <div class="alert alert-primary" role="alert">
                                O(a) candidato(a) receberá uma solicitação para informar se deseja continuar na etapa atual do processo seletivo até a data definida abaixo.
                            </div>

                            <div class="form-group">
                                <label><strong>Prazo para resposta <span class="text-danger">*</span></strong></label>

                                <div class="row">

                                    <div class="col-8 pr-0">
                                        <input type="number" 
                                            id="prazo" 
                                            placeholder="Ex: 48" 
                                            class="form-control" 
                                            min="1">

                                            <small id="erro-prazo" class="text-danger d-none">
                                                Este campo é obrigatório.
                                            </small>
                                    </div>

                                    <div class="col-4">

                                        <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">

                                            <label class="btn btn-outline-primary active w-50">
                                                <input type="radio" 
                                                    name="tipo_prazo" 
                                                    value="horas" 
                                                    autocomplete="off" 
                                                    checked>
                                                Horas
                                            </label>

                                            <label class="btn btn-outline-primary w-50">
                                                <input type="radio" 
                                                    name="tipo_prazo" 
                                                    value="dias" 
                                                    autocomplete="off">
                                                Dias
                                            </label>

                                        </div>

                                    </div>

                                </div>

                            </div>


                            <div class="form-group">
                                <label>Orientação complementar (opcional)</label>

                                <div id="mensagem"></div>

                            </div>
                            
                            <div class="form-group">
                                <label for="anexos-email-confirm" class="m-0">Anexos (opcional)</label>
                                <p class="text-secondary mt-0"><i class="fa fa-info-circle" aria-hidden="true"></i> Limite: 5 arquivos de, no máximo, 2MB cada.</p>
                                <input
                                    type="file"
                                    class="form-control-file anexos-email"
                                    id="anexos-email-confirm" 
                                    name="anexo"
                                    multiple
                                    accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                                >
                            </div>


                        </div>
                    `,

                    showCancelButton: true,
                    cancelButtonText: 'Cancelar',
                    confirmButtonText: '<i class="fa fa-paper-plane" aria-hidden="true"></i> Enviar',
                    reverseButtons: true,                  
                    focusConfirm: false,


                    didOpen: () => {

                        var editor = document.querySelector('#mensagem');

                        var quill = new Quill(editor, {
                            theme: 'snow',
                            placeholder: 'Digite seu texto...',
                            modules: {
                                toolbar: [
                                    ['bold', 'italic', 'underline'],
                                    [{ list: 'ordered' }, { list: 'bullet' }],
                                    [{ align: [] }],
                                    [{ size: ['small', false, 'large', 'huge'] }],
                                    ['link']
                                ]
                            }
                        });


                        editor.__quill = quill;

                    },


                    preConfirm: () => {

                        let prazo = document.getElementById('prazo');
                        let tipo_prazo = document.querySelector('input[name="tipo_prazo"]:checked').value;

                        let quill = document.querySelector('#mensagem').__quill;
                        let mensagem = quill.root.innerHTML;

                        let anexo = document.getElementById('anexos-email').files;
                        let post_id = document.getElementById('post_ID').value;

                        let erro = document.getElementById('erro-prazo');
                        
                        if (!prazo.value) {

                            prazo.classList.add('is-invalid');
                            erro.classList.remove('d-none');

                            return false;
                        }


                        return {

                            user_id: user_id,
                            status: status,
                            prazo: prazo.value,
                            tipo_prazo: tipo_prazo,
                            mensagem: mensagem,
                            anexo: anexo,
                            post_id: post_id

                        };

                    }


                }).then((result) => {


                    if (result.isConfirmed) {

                        var dados = result.value;

                        var formData = new FormData();

                        formData.append('action', 'comunicar_selecionados');

                        formData.append('user_id', dados.user_id);
                        formData.append('status', dados.status);
                        formData.append('prazo', dados.prazo);
                        formData.append('tipo_prazo', dados.tipo_prazo);
                        formData.append('mensagem', dados.mensagem);
                        formData.append('post_id', dados.post_id);


                        if (dados.anexo) {
                            formData.append('anexo', dados.anexo);
                        }


                        $.ajax({

                            url: ajaxurl,
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,


                            beforeSend: function() {

                                Swal.fire({
                                    icon: 'info',
                                    title: 'Enviando...',
                                    text: 'Aguarde enquanto a comunicação é enviada.',
                                    allowOutsideClick: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });

                            },


                            success: function(response) {

                                console.log(response);


                                if (response.success) {

                                    table.destroy();

                                    $(document)
                                        .find('#tabela-candidatos tbody')
                                        .html(response.data.html);


                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Comunicação enviada com sucesso',
                                        text: response.data.message
                                    });


                                } else {

                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Erro!',
                                        text: response.data.message
                                    });

                                }

                            },


                            error: function(xhr) {

                                console.log(xhr.responseText);

                                Swal.fire({
                                    icon: 'error',
                                    title: 'Erro na requisição!',
                                    text: 'Não foi possível processar a solicitação.'
                                });

                            }

                        });

                    }


                });


            });
            
        } else {
            console.log('Nenhum dado para inicializar o DataTable');
        }

        // Validação da quantidade de arquivos anexados no modal de comunicar candidatos
        $(document).on('change', '.anexos-email', function() {

            const limite = 5;

            if (this.files.length > limite) {
                toastr.error(`Limite de arquivos excedido. Você pode selecionar no máximo ${limite} arquivos.`)
                this.value = '';

                return;
            }

        });

        // Evento de click ao enviar o e-mail de comunicação
		$('#modal-comunicar-candidatos .btn-enviar').on('click', function () {
			
            const $modal = $(this).closest('.modal');
			const inscricoesSelecionadas = getInscricoesSelecionadas();
			const $editor = $modal.find('#conteudo-email').first();
			const instanciaQuill = $editor.data('quill');

			let conteudoEmail = instanciaQuill.root.innerHTML.replace(/<img[^>]*role="img"[^>]*>/g, function(match){
				var alt = match.match(/alt="([^"]*)"/);
				return alt ? alt[1] : '';
			});

            if (instanciaQuill.getText().trim().length === 0) {
                $editor.closest('.bloco-conteudo').find('.error-message').text('Este campo é de preenchimento obrigatório.').show();
                return
            }

            var inputAnexos = $modal.find('#anexos-email')[0];
            var anexos = inputAnexos.files;

            Swal.fire({
                title: 'Aguarde um instante...',
                text: 'Estamos processando o envio dos e-mails.',
                iconHtml: '<span class="dashicons dashicons-warning"></span>',
                customClass: {
                    popup: 'popup-notificar-sorteados',
                },
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            enviarComunicado(inscricoesSelecionadas, conteudoEmail, anexos)
		});

		//Função para enviar o comunicado aos participantes selecionados.
        function enviarComunicado(inscricoes, conteudoEmail, anexos) {

            const formData = new FormData();

            formData.append('action', 'enviar_email_comunicado');
            formData.append('conteudo_email', conteudoEmail);
            formData.append('ids', inscricoes);
            formData.append('nonce', '<?php echo wp_create_nonce( 'enviar_email_comunicado' ); ?>');
            formData.append('post_id', '<?php echo $current_post_id; ?>');

            for (let i = 0; i < anexos.length; i++) {
                formData.append('anexos[]', anexos[i]);
            }

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function () {
                    demoraTimeout = setTimeout(function () {
                        Swal.fire({
                            title: 'Envio em andamento',
                            text: 'Os e-mails estão sendo enviados aos candidatos selecionados.',
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
                success: function(response) {

                    clearTimeout(demoraTimeout);

                    if (!response.success) {

                        Swal.fire({
                            icon: 'error',
                            title: 'Erro!',
                            confirmButtonText: 'Fechar',
                            confirmButtonColor: '#14447C',
                            text: response.data?.message || 'Erro ao enviar comunicado.'
                        });

                        return;
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Comunicação enviada com sucesso!',
                        confirmButtonText: 'Fechar',
                        confirmButtonColor: '#14447C',
                        willClose: () => {
                            location.reload();
                        }
                    });

                },
                error: function (xhr, status, error) {
                    clearTimeout(demoraTimeout); // cancela o alerta de demora

                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: 'Ocorreu um problema ao enviar o comunicado.',
                        confirmButtonText: 'Fechar',
                        confirmButtonColor: '#14447C',
                    });
                    console.error('Erro:', error);
                }
            });
        }
        
        function getInscricoesSelecionadas() {
            const checkboxes = $('.check-item:checked');
            const ids = [];

            checkboxes.each(function() {
                ids.push($(this).val());
            });

            return ids;
        }
    });
</script>