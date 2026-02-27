<?php
use Classes\Lib\SimpleXLSXGenExp;

add_action('wp_ajax_exportar_cortesias_excel', 'handle_exportar_cortesias_excel');
function handle_exportar_cortesias_excel() {

    $post_id = absint($_POST['post_id']);
    $tipo_evento = get_field( 'tipo_evento', $post_id );
    $requer_confirmacao_presenca = boolval( get_field( 'confirm_presen', $post_id ) );

    try {
        check_ajax_referer('exportar_cortesias_nonce', '_ajax_nonce');

        if (!current_user_can('edit_posts')) {
            throw new Exception('Permissão negada');
        }

        if (!$post_id) {
            throw new Exception('ID do post inválido');
        }

        if (!class_exists('\Classes\Lib\SimpleXLSXGenExp')) {
            throw new Exception('Biblioteca SimpleXLSXGenExp não carregada');
        }

        $tags  = wp_get_post_tags($post_id);
        $local = '';
        if ($tags && !is_wp_error($tags)) {
            $local = ', ' . implode(', ', wp_list_pluck($tags, 'name'));
        }

        if ($tipo_evento == 'premio') {
            $datasEvento = get_field('evento_premios', $post_id);
        } else {
            $datasEvento = get_field('evento_datas', $post_id);
        }

        $dataEventoUnica  = get_field('data_evento', $post_id);
        $abas             = [];

        if (is_array($datasEvento) && count($datasEvento) > 0) {

            usort($datasEvento, function($a, $b) {
                return strtotime($a['data']) - strtotime($b['data']);
            });

            foreach ($datasEvento as $evento) {
                try {
                    // agora 'data' já é data+hora no mesmo campo
                    $premio = $evento['premio'] ?? '';
                    $dt       = new DateTime($evento['data'], new DateTimeZone('America/Sao_Paulo')); // Data Evento
                    $ds       = new DateTime($evento['data_sorteio'], new DateTimeZone('America/Sao_Paulo')); // Data Sorteio
                    $titulo   = !empty($premio) ? $premio : $dt->format('d/m/Y H\hi');
                    $tituloAba = str_replace(['/', '\\', ':', '*', '?', '[', ']'], '-', $titulo);

                    // data bruta no formato Y-m-d H:i:s para consulta
                    $dataBruta = $dt->format('Y-m-d H:i:s');

                    $abaData = gerar_aba_inscritos_corte($post_id, $local, $ds->format('d/m/Y'), $dataBruta, $premio);
                    $abas[$tituloAba] = $abaData;

                } catch (Exception $e) {
                    continue; // pula se algo estiver errado
                }
            }
        } elseif (!empty($dataEventoUnica)) {
            // Campo antigo só de data
            $dt        = new DateTime($dataEventoUnica, new DateTimeZone('America/Sao_Paulo'));
            $titulo    = $dt->format('d/m/Y');
            $tituloAba = str_replace(['/', '\\', ':', '*', '?', '[', ']'], '-', $titulo);
            $abaData   = gerar_aba_inscritos_corte($post_id, $local, $titulo, null);
            $abas[$tituloAba] = $abaData;

        } elseif ( $tipo_evento === 'periodo' ) {

            $info_periodo_evento = get_field( 'evento_periodo', $post_id );
            $titulo    = $info_periodo_evento['data_sorteio'];
            $abaData   = gerar_aba_inscritos_corte($post_id, $local, $titulo, null);
            $abas['Inscritos'] = $abaData;
        }
        
        $xlsx = new SimpleXLSXGenExp();

        foreach ($abas as $titulo => $conteudo) {
            $xlsx->addSheet($conteudo, $titulo);

            if ($requer_confirmacao_presenca) {
                $xlsx->setColWidth(1, 25);
                $xlsx->setColWidth(2, 20);
                $xlsx->setColWidth(3, 20);
                $xlsx->setColWidth(4, 10);  // Contato
                $xlsx->setColWidth(5, 20);  // Conf. Presença
                $xlsx->setColWidth(6, 15);
                $xlsx->setColWidth(7, 15);
                $xlsx->setColWidth(8, 15);
                $xlsx->setColWidth(9, 15);
                $xlsx->setColWidth(10, 15);
                $xlsx->setColWidth(11, 14);
                $xlsx->setColWidth(12, 14);
                $xlsx->setColWidth(13, 15);
                $xlsx->setColWidth(14, 15);
                $xlsx->setColWidth(15, 20);

                $xlsx->mergeCells('A1:O2');
            } else {
                $xlsx->setColWidth(1, 25);
                $xlsx->setColWidth(2, 20);
                $xlsx->setColWidth(3, 20);
                $xlsx->setColWidth(4, 15);
                $xlsx->setColWidth(5, 15);
                $xlsx->setColWidth(6, 15);
                $xlsx->setColWidth(7, 15);
                $xlsx->setColWidth(8, 15);
                $xlsx->setColWidth(9, 14);
                $xlsx->setColWidth(10, 14);
                $xlsx->setColWidth(11, 15);
                $xlsx->setColWidth(12, 15);
                $xlsx->setColWidth(13, 20);

                $xlsx->mergeCells('A1:M2');
            }
        }

        $xlsx->downloadAs("relatorio_inscritos_" . date('d_m_y_H_i_s') . ".xlsx");
        exit;

    } catch (Exception $e) {
        error_log('Erro na exportação: ' . $e->getMessage());
        status_header(500);
        wp_send_json_error(['message' => $e->getMessage()]);
    }
}

function gerar_aba_inscritos_corte($post_id, $local, $dataSorteio, $data_evento = null, $premio = null) {
    global $wpdb;

    $inscritos = [];
    $tipo_evento = get_field( 'tipo_evento', $post_id );
    $titulo = html_entity_decode(get_the_title($post_id), ENT_QUOTES, 'UTF-8');
    $requer_confirmacao_presenca = boolval( get_field( 'confirm_presen', $post_id ) );

    if($premio){

        $infoCabecalho = 'Participantes Inscritos para o prêmio:' . $premio;

    } elseif ( $tipo_evento === 'periodo' ) {

        $info_periodo_evento = get_field( 'evento_periodo', $post_id );
        $infoCabecalho = "Participantes Inscritos para o período: {$info_periodo_evento['descricao']}";

    } else {
        $data_formatada = date('d/m/Y', strtotime($data_evento));
		$hora_formatada = obter_hora_formatada($data_evento);

        $infoCabecalho = 'Participantes Inscritos para o evento do dia ' . $data_formatada . ' às ' . $hora_formatada;
    }

    $dt = new DateTime('now', new DateTimeZone('America/Sao_Paulo'));
    $infoCabecalho .= $dataSorteio ? ' - Data do Sorteio: ' . $dataSorteio : '';
    $infoCabecalho .= ' | Extraído em ' . $dt->format('d/m/Y - H:i');

    if ($requer_confirmacao_presenca) {
        $inscritos[] = [
            '<style font-size="12" bgcolor="#ABBFE3" align="center" valign="center"><middle><center>' . $infoCabecalho . '</center></middle></style>',
            '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''
        ];
    } else {
        $inscritos[] = [
            '<style font-size="12" bgcolor="#ABBFE3" align="center" valign="center"><middle><center>' . $infoCabecalho . '</center></middle></style>',
            '', '', '', '', '', '', '', '', '', '', '', '', ''
        ];
    }

    // Verifica se existe o campo evento_datas para saber se é estrutura nova
    $usa_join = get_field('evento_datas', $post_id);

    if ($usa_join || $data_evento) {
        // Estrutura nova com JOIN e múltiplas datas
        $query = $wpdb->prepare("
            SELECT i." . implode(', i.', [
                'cpf', 'nome_completo', 'email_institucional', 'email_secundario',
                'celular', 'telefone_comercial', 'dre', 'cargo_principal',
                'unidade_setor', 'disciplina', 'programa_estagio', 'enviou_email_instrucoes', 'qtd', 'fez_contato', 'tipo_contato', 'confirmou_presenca', 'prazo_confirmacao', 'historico_emails'
            ]) . "
            FROM {$wpdb->prefix}cortesias_inscricoes i
            JOIN {$wpdb->prefix}cortesias_acf_datas d ON d.id = i.acf_id
            WHERE i.post_id = %d
            AND d.data_evento = %s 
            ORDER BY i.data_inscricao ASC
        ", $post_id, $data_evento);

        
    } else {
        // Estrutura antiga (sem JOIN), ignora $data_evento pois só tem uma
        $query = $wpdb->prepare("
            SELECT " . implode(', ', [
                'cpf', 'nome_completo', 'email_institucional', 'email_secundario',
                'celular', 'telefone_comercial', 'dre', 'cargo_principal',
                'unidade_setor', 'disciplina', 'programa_estagio','enviou_email_instrucoes', 'qtd', 'fez_contato', 'tipo_contato', 'confirmou_presenca', 'prazo_confirmacao', 'historico_emails'
            ]) . "
            FROM {$wpdb->prefix}cortesias_inscricoes
            WHERE post_id = %d 
            ORDER BY data_inscricao ASC
        ", $post_id);        
    }
    
    $results = $wpdb->get_results($query);

    if ($requer_confirmacao_presenca) {
        $inscritos[] = ['', '', '', '', '', '', '', '', '', '', '', '','', '', '']; // linha em branco

        $inscritos[] = [
            '<style bgcolor="#2A4A8B" color="#FFFFFF">Nome Completo</style>',
            '<style bgcolor="#2A4A8B" color="#FFFFFF">E-mail Institucional</style>',
            '<style bgcolor="#2A4A8B" color="#FFFFFF">E-mail Secundário</style>',
            '<style bgcolor="#2A4A8B" color="#FFFFFF">Notificado</style>',
            '<style bgcolor="#2A4A8B" color="#FFFFFF">Conf. Presença</style>',
            '<style bgcolor="#2A4A8B" color="#FFFFFF">Qtde de Cortesias</style>',
            '<style bgcolor="#2A4A8B" color="#FFFFFF">E-mail c/ Instruções</style>',
            '<style bgcolor="#2A4A8B" color="#FFFFFF">Contatado por</style>',
            '<style bgcolor="#2A4A8B" color="#FFFFFF">Telefone Celular</style>',
            '<style bgcolor="#2A4A8B" color="#FFFFFF">Telefone Comercial</style>',
            '<style bgcolor="#2A4A8B" color="#FFFFFF">CPF</style>',
            '<style bgcolor="#2A4A8B" color="#FFFFFF">DRE/SME</style>',
            '<style bgcolor="#2A4A8B" color="#FFFFFF">Cargo Atual</style>',
            '<style bgcolor="#2A4A8B" color="#FFFFFF">Escola/Setor</style>',
            '<style bgcolor="#2A4A8B" color="#FFFFFF">Disciplina/Estágio</style>',
        ];
    } else {

        $inscritos[] = ['', '', '', '', '', '', '', '', '', '', '', '']; // linha em branco

        $inscritos[] = [
            '<style bgcolor="#2A4A8B" color="#FFFFFF">Nome Completo</style>',
            '<style bgcolor="#2A4A8B" color="#FFFFFF">E-mail Institucional</style>',
            '<style bgcolor="#2A4A8B" color="#FFFFFF">E-mail Secundário</style>',
            '<style bgcolor="#2A4A8B" color="#FFFFFF">Qtde de Cortesias</style>',
            '<style bgcolor="#2A4A8B" color="#FFFFFF">E-mail c/ Instruções</style>',
            '<style bgcolor="#2A4A8B" color="#FFFFFF">Contatado por</style>',
            '<style bgcolor="#2A4A8B" color="#FFFFFF">Telefone Celular</style>',
            '<style bgcolor="#2A4A8B" color="#FFFFFF">Telefone Comercial</style>',
            '<style bgcolor="#2A4A8B" color="#FFFFFF">CPF</style>',
            '<style bgcolor="#2A4A8B" color="#FFFFFF">DRE/SME</style>',
            '<style bgcolor="#2A4A8B" color="#FFFFFF">Cargo Atual</style>',
            '<style bgcolor="#2A4A8B" color="#FFFFFF">Escola/Setor</style>',
            '<style bgcolor="#2A4A8B" color="#FFFFFF">Disciplina/Estágio</style>',
        ];
    }

    $programas_estagio = [
        '1' => 'Aprender sem limite',
        '2' => 'Parceiros da aprendizagem',
        '3' => 'Diversos',
    ];

    $tipos_contato = [
        '1' => 'Telefone',
        '2' => 'E-mail',
        '3' => 'WhatsApp',
    ];

    if ($results) {
        foreach ($results as $r) {
            if (!empty($r->programa_estagio)) {
                $r->cargo_principal = 'Estagiário(a)';
                $r->disciplina = $programas_estagio[$r->programa_estagio] ?? ' - ';
            }

            if (!empty($r->tipo_contato)) {
                $r->tipo_contato = $tipos_contato[$r->tipo_contato] ?? ' - ';
            }

            if ($requer_confirmacao_presenca) {

                $array_historico = json_decode( $r->historico_emails );
                $fez_contato = boolval( $array_historico->vencedor->enviado );

                $presenca = $r->confirmou_presenca;
                
                if ($presenca === '0') {
                    $prazo_confirmacao = $r->prazo_confirmacao;
                    $presenca = '<center>AINDA NÃO RESPONDEU</center>';

                    if ( $prazo_confirmacao ) {
                        $tz = new DateTimeZone('America/Sao_Paulo');

                        $data_validar = new DateTime($prazo_confirmacao, $tz);
                        $agora = new DateTime('now', $tz);

                        if ( $agora > $data_validar ) {
                            $presenca = '<center>PRAZO EXPIRADO</center>';
                        }
                    }
                    
                } elseif ($presenca === '1') {
                    $presenca = '<center>SIM</center>';
                } else if ($presenca === '2') {
                    $presenca = '<center>NÃO, CANCELOU</center>';
                }

                $inscritos[] = [
                    $r->nome_completo ?: ' - ',
                    $r->email_institucional ?: ' - ',
                    $r->email_secundario ?: ' - ',
                    $fez_contato ? '<center>SIM</center>' : '<center>NÃO</center>',
                    $presenca,
                    $r->qtd ?: ' - ',
                    !empty($r->enviou_email_instrucoes) ? '<center>SIM</center>' : '<center>NÃO</center>',
                    !empty($r->tipo_contato) ? $r->tipo_contato : ' - ',
                    !empty($r->celular) ? formatarTelefone($r->celular) : ' - ',
                    !empty($r->telefone_comercial) ? formatarTelefone($r->telefone_comercial) : ' - ',
                    !empty($r->cpf) ? formatarCpfMasked($r->cpf) : ' - ',
                    $r->dre ?: ' - ',
                    $r->cargo_principal ?: ' - ',
                    $r->unidade_setor ?: ' - ',
                    $r->disciplina ?: ' - ',
                ];

            } else {
                $inscritos[] = [
                    $r->nome_completo ?: ' - ',
                    $r->email_institucional ?: ' - ',
                    $r->email_secundario ?: ' - ',
                    $r->qtd ?: ' - ',
                    !empty($r->enviou_email_instrucoes) ? '<center>SIM</center>' : '<center>NÃO</center>',
                    !empty($r->tipo_contato) ? $r->tipo_contato : ' - ',
                    !empty($r->celular) ? formatarTelefone($r->celular) : ' - ',
                    !empty($r->telefone_comercial) ? formatarTelefone($r->telefone_comercial) : ' - ',
                    !empty($r->cpf) ? formatarCpfMasked($r->cpf) : ' - ',
                    $r->dre ?: ' - ',
                    $r->cargo_principal ?: ' - ',
                    $r->unidade_setor ?: ' - ',
                    $r->disciplina ?: ' - ',
                ];
            }

        }
    }

    return $inscritos;
}

if ( ! function_exists( 'formatarCpfMasked' ) ) {
    function formatarCpfMasked($cpf) {
        $numeros = preg_replace('/[^0-9]/', '', $cpf);
        if (strlen($numeros) !== 11) {
            return $cpf; // retorna como está se não tiver 11 dígitos
        }
        return substr($numeros, 0, 3) . '.' .
            substr($numeros, 3, 3) . '.' .
            substr($numeros, 6, 3) . '-' .
            substr($numeros, 9, 2);
    }
}

if ( ! function_exists( 'formatarTelefone' ) ) {

    function formatarTelefone($telefone) {
        $numeros = preg_replace('/[^0-9]/', '', $telefone);
        if (strlen($numeros) < 10 || strlen($numeros) > 11) return $telefone;
        $ddd = substr($numeros, 0, 2);
        $numero = substr($numeros, 2);
        return strlen($numero) === 9
            ? sprintf('(%s) %s-%s', $ddd, substr($numero, 0, 5), substr($numero, 5))
            : sprintf('(%s) %s-%s', $ddd, substr($numero, 0, 4), substr($numero, 4));
    }
}