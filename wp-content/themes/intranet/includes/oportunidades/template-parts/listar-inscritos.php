<?php
/**
 * Template para exibição de candidatos inscritos em uma oportunidade
 */

global $wpdb;

// Mapeamento dos status (código => [descrição, classe_css])
$status_map = array(
    'analise_curricular' => array(
        'descricao' => 'Em Análise Curricular',
        'classe' => 'analise-curricular'
    ),
    'nao_avancou_triagem' => array(
        'descricao' => 'Candidatura não avançou na Triagem',
        'classe' => 'triagem-curricular'
    ),
    'convocado_teste' => array(
        'descricao' => 'Convocado para Teste/Avaliação',
        'classe' => 'convocado-teste'
    ),
    'entrevista_agendada' => array(
        'descricao' => 'Entrevista Agendada',
        'classe' => 'entrevista-agendada'
    ),
    'nao_avancou_pos_entrevista' => array(
        'descricao' => 'Candidatura não avançou pós-entrevista',
        'classe' => 'candidatura-nao-avancou'
    ),
    'fase_anuencia' => array(
        'descricao' => 'Em Fase de Anuência',
        'classe' => 'fase-anuencia'
    ),
    'entrega_documentos' => array(
        'descricao' => 'Em Fase de Entrega de Documentos',
        'classe' => 'entrega-documentos'
    ),
    'analise_documental' => array(
        'descricao' => 'Análise Documental + Publicação DOC',
        'classe' => 'analise-documental'
    ),
    'aprovado' => array(
        'descricao' => 'Processo Finalizado - Candidato Aprovado',
        'classe' => 'candidatura-aprovada'
    ),
    'nao_selecionado' => array(
        'descricao' => 'Processo Finalizado - Candidato não selecionado',
        'classe' => 'nao-selecionado'
    )
);

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
    $table_inscricoes = $wpdb->prefix . 'oportunidade_inscricoes';
    $table_banco_talentos = $wpdb->prefix . 'banco_talentos';
    
    $participantes = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT 
                oi.id,
                oi.curriculo_id,
                oi.rf,
                oi.status,
                oi.created_at,
                oi.updated_at,
                bt.nome_completo,
                bt.nome_social,
                bt.email_principal,
                bt.telefone_whatsapp
            FROM {$table_inscricoes} AS oi
            INNER JOIN {$table_banco_talentos} AS bt 
                ON oi.curriculo_id = bt.id
            WHERE oi.oportunidade_id = %d
            ORDER BY oi.created_at ASC",
            $current_post_id
        ),
        ARRAY_A
    );
    
    $total_participantes = count($participantes);
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
                <button type="button" class="btn btn-outline-success" id="btn-comunicar-selecionados">
                    <i class="fa fa-paper-plane" aria-hidden="true"></i> Comunicar Selecionados
                </button>

                <label for="etapa-massa">Alterar etapa do processo seletivo em massa</label>
                <div class="d-flex gap-2">
                    <select id="etapa-massa" class="filtro-select form-control">
                        <option value="">Selecione uma opção</option>
                        <?php foreach ($todos_status_select as $codigo => $descricao): ?>
                            <option value="<?php echo esc_attr($codigo); ?>"><?php echo esc_html($descricao); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn btn-primary btn-aplicar-etapa" onclick="aplicarEtapa()" type="button">Aplicar</button>
                </div>
            </div>

        </div>

    </div>

    <div class="row">
        <div class="col-12">
            <div class="table-responsive">
                <table class="table table-striped tabela-candidatos-inscritos" id="tabela-candidatos">
                    <thead>
                        <tr>
                            <th width="40"><input type="checkbox" id="check-all" class="check-all"></th>
                            <th>Nome completo</th>
                            <th>Situação da Candidatura</th>
                            <th>RF</th>
                            <th>E-mail institucional</th>
                            <th>WhatsApp</th>
                            <th>Currículo</th>
                            <th>Etapa do Processo Seletivo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($participantes)): ?>
                            <?php foreach ($participantes as $participante): 
                                // Pega os dados do status com base no código armazenado no banco
                                $status_codigo = $participante['status']; // Ex: 'analise_curricular'
                                $status_info = isset($status_map[$status_codigo]) ? $status_map[$status_codigo] : $status_map['inscrito'];
                                $status_descricao = $status_info['descricao'];
                                $status_classe = $status_info['classe'];
                            ?>
                                <tr>
                                    <td><input type="checkbox" class="check-sorteados check-item" name="participantes-sorteados[]" value="<?php echo esc_attr($participante['id']); ?>"></td>
                                    <td>
                                        <?php if ($participante['nome_social']): ?>
                                            <span class="nome-candidato"><?php echo esc_html($participante['nome_social']); ?> <br><small>(<?php echo esc_html($participante['nome_completo']); ?>)</small></span><br>
                                        <?php else: ?>
                                            <span class="nome-candidato"><?php echo esc_html($participante['nome_completo']); ?></span><br>
                                        <?php endif; ?>
                                        <span class="card-etapa data-inscricao">
                                            Inscrição Recebida<br>
                                            <span class="data-etapa"><?php echo date('d/m/Y \à\s H:i', strtotime($participante['created_at'])); ?></span>
                                        </span>
                                    </td>
                                    <td data-situacao="<?php echo esc_attr($status_descricao); ?>">
                                        <span class="card-etapa <?php echo esc_attr($status_classe); ?>">
                                            <?php echo esc_html($status_descricao); ?><br>
                                            <span class="data-etapa"><?php echo date('d/m/Y \à\s H:i', strtotime($participante['updated_at'])); ?></span>
                                        </span>
                                    </td>
                                    <td><?php echo esc_html($participante['rf']); ?></td>
                                    <td><?php echo esc_html($participante['email_principal']); ?></td>
                                    <td><?php echo esc_html($participante['telefone_whatsapp']); ?></td>
                                    <td><a href="#"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Ver CV</a></td>
                                    <td><button type="button" class="btn btn-success btn-comunicar-selecionados"><i class="fa fa-paper-plane" aria-hidden="true"></i></button></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">Nenhum candidato inscrito encontrado.</td>
                            </tr>
                        <?php endif; ?>
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    jQuery(document).ready(function($) {
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
            
            // Inicializa o DataTable
            var table = $('#tabela-candidatos').DataTable({
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
            });
            
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
            
            // Selecionar/desmarcar todos os checkboxes
            $('#check-all').on('change', function() {
                $('.check-item').prop('checked', $(this).prop('checked'));
            });
            
            // Atualiza o checkbox "selecionar todos" quando um individual muda
            $(document).on('change', '.check-item', function() {
                if ($('.check-item:checked').length === $('.check-item').length) {
                    $('#check-all').prop('checked', true);
                } else {
                    $('#check-all').prop('checked', false);
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
                    alert('Selecione uma etapa para aplicar');
                    return;
                }
                
                var checkboxes = $('.check-item:checked');
                if (checkboxes.length === 0) {
                    alert('Selecione pelo menos um candidato');
                    return;
                }
                
                var statusDescricao = $('#etapa-massa option:selected').text();
                
                if (confirm('Deseja alterar a etapa de ' + checkboxes.length + ' candidato(s) para "' + statusDescricao + '"?')) {
                    var ids = [];
                    checkboxes.each(function() {
                        ids.push($(this).val());
                    });

                    console.log('Alterar etapa para', ids, novaEtapaCodigo);
                    
                    // Mostra loading
                    var btn = $(this);
                    var originalText = btn.text();
                    btn.text('Aplicando...').prop('disabled', true);
                    
                    // Requisição AJAX
                    $.ajax({
                        url: window.ajaxurl || window.location.href,
                        method: 'POST',
                        data: {
                            action: 'atualizar_etapa_candidatos',
                            ids: ids,
                            etapa_codigo: novaEtapaCodigo,
                            post_id: <?php echo $current_post_id; ?>,
                            nonce: $('#_wpnonce').val() || ''
                        },
                        success: function(response) {
                            if (response.success) {
                                location.reload();
                            } else {
                                alert('Erro ao atualizar: ' + (response.data || 'Erro desconhecido'));
                                btn.text(originalText).prop('disabled', false);
                            }
                        },
                        error: function() {
                            alert('Erro na requisição. Por favor, tente novamente.');
                            btn.text(originalText).prop('disabled', false);
                        }
                    });
                }
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
            
            // Comunicar selecionados
            $('#btn-comunicar-selecionados').on('click', function() {
                var checkboxes = $('.check-item:checked');
                if (checkboxes.length === 0) {
                    alert('Selecione pelo menos um candidato');
                    return;
                }
                
                alert('Função de comunicação será implementada para ' + checkboxes.length + ' candidato(s)');
            });
            
        } else {
            console.log('Nenhum dado para inicializar o DataTable');
        }
    });
</script>