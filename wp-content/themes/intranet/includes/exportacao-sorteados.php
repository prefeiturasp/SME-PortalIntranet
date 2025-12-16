<?php
use Classes\Lib\SimpleXLSXGenExp;

add_action('wp_ajax_exportar_sorteados_excel', 'handle_exportar_sorteados_excel');
function handle_exportar_sorteados_excel() {

    $post_id = absint($_POST['post_id']);
    $tipo_sorteio = get_field( 'tipo_evento', $post_id );
    $requer_confirmacao_presenca = boolval( get_field( 'confirm_presen', $post_id ) );

    try {
        check_ajax_referer('exportar_sorteados_nonce', '_ajax_nonce');

        if (!current_user_can('edit_posts')) {
            throw new Exception('Permissão negada');
        }

        $filtro = $_POST['filtro'];
        if (!$post_id) {
            throw new Exception('ID do post inválido');
        }

        if (!class_exists('\Classes\Lib\SimpleXLSXGenExp')) {
            throw new Exception('Biblioteca SimpleXLSXGenExp não carregada');
        }

        $tags = wp_get_post_tags($post_id);
        $local = '';
        if ($tags && !is_wp_error($tags)) {
            $local = ', ' . implode(', ', wp_list_pluck($tags, 'name'));
        }

        $tipo_evento = get_field('tipo_evento', $post_id);
        if ($tipo_evento == 'premio') {
            $datasEvento = get_field('evento_premios', $post_id);
        } elseif ($tipo_evento == 'periodo') {
            $datasEvento = [];
        } else {
            $datasEvento = get_field('evento_datas', $post_id);
        }

        $dataSorteioUnica = get_field('data_sorteio', $post_id);
        $valor_direto    = ($filtro === 'confirmados') ? '1' : '0';

        $abas = [];

        if (is_array($datasEvento) && count($datasEvento) > 0) {
            usort($datasEvento, function($a, $b) {
                return strtotime($a['data']) - strtotime($b['data']);
            });

            foreach ($datasEvento as $evento) {
                try {
                    // 'data' já vem com data e hora no mesmo campo
                    $premio = $evento['premio'] ?? '';
                    $dt = new DateTime($evento['data'], new DateTimeZone('America/Sao_Paulo')); // Data Evento
                    $ds = new DateTime($evento['data_sorteio'], new DateTimeZone('America/Sao_Paulo')); // Data Sorteio

                    $titulo    = !empty($premio) ? $premio : $dt->format('d/m/Y H\hi');
                    $tituloAba = str_replace(['/', '\\', ':', '*', '?', '[', ']'], '-', $titulo);

                    // Formato para consulta no banco
                    $dataBruta = $dt->format('Y-m-d H:i:s');

                    $abaData = gerar_aba_sorteados($post_id, $local, $ds->format('d/m/Y'), $dataBruta, $valor_direto, $premio);
                    $abas[$tituloAba] = $abaData;

                } catch (Exception $e) {
                    continue; // se algum registro estiver errado, ignora
                }
            }
        } else {
            if (!empty($dataSorteioUnica)) {
                $dt        = new DateTime($dataSorteioUnica, new DateTimeZone('America/Sao_Paulo'));
                $titulo    = $dt->format('d/m/Y');
                $tituloAba = str_replace(['/', '\\', ':', '*', '?', '[', ']'], '-', $titulo);
                $abaData   = gerar_aba_sorteados($post_id, $local, $titulo, null, $valor_direto);
                $abas[$tituloAba] = $abaData;

            } else if ( $tipo_sorteio === 'periodo' ) {

                $info_periodo_evento = get_field( 'evento_periodo', $post_id );
                $data_sorteio    = $info_periodo_evento['data_sorteio'];
                $abaData   = gerar_aba_sorteados($post_id, null, $data_sorteio, null, $valor_direto);
                $abas['Sorteados'] = $abaData;

            } else {
                $abas['Sorteados'] = gerar_aba_sorteados($post_id, $local, null, null, $valor_direto);
            }
        }

        $xlsx = new SimpleXLSXGenExp();

        foreach ($abas as $titulo => $conteudo) {
            $xlsx->addSheet($conteudo, $titulo);

            if($premio || !$requer_confirmacao_presenca){
                $xlsx->setColWidth(1, 8);   // Sorteado
                $xlsx->setColWidth(2, 25);  // Nome Completo
                $xlsx->setColWidth(3, 20);  // E-mail Institucional
                $xlsx->setColWidth(4, 15);  // E-mail c/ Instruções
                $xlsx->setColWidth(5, 20);  // E-mail Secundário
                $xlsx->setColWidth(6, 15);  // Telefone Celular
                $xlsx->setColWidth(7, 15);  // Telefone Comercial
                $xlsx->setColWidth(8, 14); // CPF
                $xlsx->setColWidth(9, 14); // DRE/SME
                $xlsx->setColWidth(10, 15); // Cargo Atual
                $xlsx->setColWidth(11, 15); // Escola/Setor
                $xlsx->setColWidth(12, 20); // Disciplina/Estágio

                $xlsx->mergeCells('A1:L2');
            } else {
                $xlsx->setColWidth(1, 8);   // Sorteado
                $xlsx->setColWidth(2, 25);  // Nome Completo
                $xlsx->setColWidth(3, 20);  // E-mail Institucional
                $xlsx->setColWidth(4, 10);  // Contato
                $xlsx->setColWidth(5, 20);  // Conf. Presença
                $xlsx->setColWidth(6, 15);  // E-mail c/ Instruções
                $xlsx->setColWidth(7, 20);  // E-mail Secundário
                $xlsx->setColWidth(8, 15);  // Telefone Celular
                $xlsx->setColWidth(9, 15);  // Telefone Comercial
                $xlsx->setColWidth(10, 14); // CPF
                $xlsx->setColWidth(11, 14); // DRE/SME
                $xlsx->setColWidth(12, 15); // Cargo Atual
                $xlsx->setColWidth(13, 15); // Escola/Setor
                $xlsx->setColWidth(14, 20); // Disciplina/Estágio

                $xlsx->mergeCells('A1:N2');
            }
        }

        $xlsx->downloadAs("relatorio_sorteados_" . date('d_m_y_H_i_s') . ".xlsx");
        exit;

    } catch (Exception $e) {
        error_log('Erro na exportação: ' . $e->getMessage());
        status_header(500);
        wp_send_json_error(['message' => $e->getMessage()]);
    }
}

function gerar_aba_sorteados($post_id, $local, $dataSorteio, $data_evento = null, $valor_direto = '0', $premio = null) {
    global $wpdb;

    $sorteados = [];
    $data_evento_form = '';
    $tipo_evento = get_field( 'tipo_evento', $post_id );
    $titulo = html_entity_decode(get_the_title($post_id), ENT_QUOTES, 'UTF-8');
    $infoCabecalho = 'Participantes Sorteados para o evento: <b>' . $titulo . $local;
    $requer_confirmacao_presenca = boolval( get_field( 'confirm_presen', $post_id ) );

    if ($data_evento) {
        $data_evento_form = (new DateTime($data_evento, new DateTimeZone('America/Sao_Paulo')))
            ->format('d/m/Y H\hi');
    }

    if($premio){
        $infoCabecalho = 'Participantes sorteados para o prêmio: <b>' . $premio . $local;
    }

    if ( $tipo_evento === 'periodo' ) {
        $info_periodo_evento = get_field( 'evento_periodo', $post_id );
        $infoCabecalho = "Participantes sorteados para o evento {$titulo} do Período: {$info_periodo_evento['descricao']}<b>";   
    }

    $infoCabecalho .= $dataSorteio ? ' - Data do Sorteio: ' . $dataSorteio : '';
    
    if(!$premio){
        $infoCabecalho .= $data_evento_form ? ' - Data do Evento: ' . $data_evento_form : '';
    }
    $dt = new DateTime('now', new DateTimeZone('America/Sao_Paulo'));
    $infoCabecalho .= ' | Extraído em ' . $dt->format('d/m/Y - H:i');

    if($premio){
        $sorteados[] = [
            '<style font-size="12" bgcolor="#b5b3f6" align="center" valign="center"><middle><center>' . $infoCabecalho . '</center></middle></style>',
            '', '', '', '', '', '', '', ''
        ];
    } else {
        $sorteados[] = [
            '<style font-size="12" bgcolor="#b5b3f6" align="center" valign="center"><middle><center>' . $infoCabecalho . '</center></middle></style>',
            '', '', '', '', '', '', '', '', '', ''
        ];
    }

    // Consulta SQL ajustada para DATETIME
    $campos = implode(', ', [
        'cpf',
        'nome_completo',
        'email_institucional',
        'fez_contato',
        'confirmou_presenca',
        'prazo_confirmacao',
        'enviou_email_instrucoes',
        'email_secundario',
        'celular',
        'telefone_comercial',
        'dre',
        'cargo_principal',
        'unidade_setor',
        'disciplina',
        'programa_estagio',
        'historico_emails',
    ]);

    $sql  = "SELECT $campos 
            FROM {$wpdb->prefix}inscricoes 
            WHERE post_id = %d 
            AND sorteado = 1";

    $params = [$post_id];

    // Se valor direto exige confirmação de presença
    if ($valor_direto == '1') {
        $sql .= " AND confirmou_presenca = 1";
    }

    // Se tem data definida
    if ($data_evento) {
        $sql .= " AND data_sorteada = %s";
        $params[] = $data_evento;
    }

    $sql .= " ORDER BY data_hora_sorteado ASC";

    $query = $wpdb->prepare($sql, ...$params);

    $sorteados[] = ['', '', '', '', '', '', '', '', '', '']; // linha em branco

    if($premio || !$requer_confirmacao_presenca){
        $sorteados[] = [
            '<style bgcolor="#652a96" color="#FFFFFF">Sorteado</style>',
            '<style bgcolor="#652a96" color="#FFFFFF">Nome Completo</style>',
            '<style bgcolor="#652a96" color="#FFFFFF">E-mail Institucional</style>',
            '<style bgcolor="#652a96" color="#FFFFFF">E-MAIL C/ INSTRUÇÕES</style>',
            '<style bgcolor="#652a96" color="#FFFFFF">E-mail Secundário</style>',
            '<style bgcolor="#652a96" color="#FFFFFF">Telefone Celular</style>',
            '<style bgcolor="#652a96" color="#FFFFFF">Telefone Comercial</style>',
            '<style bgcolor="#652a96" color="#FFFFFF">CPF</style>',
            '<style bgcolor="#652a96" color="#FFFFFF">DRE/SME</style>',
            '<style bgcolor="#652a96" color="#FFFFFF">Cargo Atual</style>',
            '<style bgcolor="#652a96" color="#FFFFFF">Escola/Setor</style>',
            '<style bgcolor="#652a96" color="#FFFFFF">Disciplina/Estágio</style>',
        ];
    } else {
        $sorteados[] = [
            '<style bgcolor="#652a96" color="#FFFFFF">Sorteado</style>',
            '<style bgcolor="#652a96" color="#FFFFFF">Nome Completo</style>',
            '<style bgcolor="#652a96" color="#FFFFFF">E-mail Institucional</style>',
            '<style bgcolor="#652a96" color="#FFFFFF">CONTATO</style>',
            '<style bgcolor="#652a96" color="#FFFFFF">CONF. PRESENÇA</style>',
            '<style bgcolor="#652a96" color="#FFFFFF">E-MAIL C/ INSTRUÇÕES</style>',
            '<style bgcolor="#652a96" color="#FFFFFF">E-mail Secundário</style>',
            '<style bgcolor="#652a96" color="#FFFFFF">Telefone Celular</style>',
            '<style bgcolor="#652a96" color="#FFFFFF">Telefone Comercial</style>',
            '<style bgcolor="#652a96" color="#FFFFFF">CPF</style>',
            '<style bgcolor="#652a96" color="#FFFFFF">DRE/SME</style>',
            '<style bgcolor="#652a96" color="#FFFFFF">Cargo Atual</style>',
            '<style bgcolor="#652a96" color="#FFFFFF">Escola/Setor</style>',
            '<style bgcolor="#652a96" color="#FFFFFF">Disciplina/Estágio</style>',
        ];
    }

    $programas_estagio = [
        '1' => 'Aprender sem limite',
        '2' => 'Parceiros da aprendizagem',
        '3' => 'Diversos',
    ];

    $results = $wpdb->get_results($query);

    if (!empty($results)) {
        $i = 1;
        foreach ($results as $result) {

            $array_historico = json_decode( $result->historico_emails );
            $fez_contato = boolval( $array_historico->vencedor->enviado );

            if (!empty($result->programa_estagio)) {
                $result->cargo_principal = 'Estagiário(a)';
                $result->disciplina = $programas_estagio[$result->programa_estagio] ?? ' - ';
            }

            if ( $requer_confirmacao_presenca ) {
                $presenca = $result->confirmou_presenca;
                
                if ($presenca === '0') {
                    $prazo_confirmacao = $result->prazo_confirmacao;
                    $presenca = '<center>AINDA NÃO RESPONDEU</center>';

                    if ( $prazo_confirmacao ) {
                        $data_validar = new DateTime( $prazo_confirmacao );
                        $agora = new \DateTime('now', new DateTimeZone('America/Sao_Paulo'));

                        if ( $agora > $data_validar ) {
                            $presenca = '<center>PRAZO EXPIRADO</center>';
                        }
                    }
                    
                } elseif ($presenca === '1') {
                    $presenca = '<center>SIM</center>';
                } else if ($presenca === '2') {
                    $presenca = '<center>NÃO, CANCELOU</center>';
                }
            }

            if($premio || !$requer_confirmacao_presenca){
                $sorteados[] = [
                    '<center>' . $i++ . '°</center>',
                    $result->nome_completo ?: ' - ',
                    $result->email_institucional ?: ' - ',
                    !empty($result->enviou_email_instrucoes) ? '<center>SIM</center>' : '<center>NÃO</center>',
                    $result->email_secundario ?: ' - ',
                    !empty($result->celular) ? formatarTelefone($result->celular) : ' - ',
                    !empty($result->telefone_comercial) ? formatarTelefone($result->telefone_comercial) : ' - ',
                    !empty($result->cpf) ? formatarCpfMasked($result->cpf) : ' - ',
                    $result->dre ?: ' - ',
                    $result->cargo_principal ?: ' - ',
                    $result->unidade_setor ?: ' - ',
                    $result->disciplina ?: ' - '
                ];
            } else {
                    $sorteados[] = [
                    '<center>' . $i++ . '°</center>',
                    $result->nome_completo ?: ' - ',
                    $result->email_institucional ?: ' - ',
                    $fez_contato ? '<center>SIM</center>' : '<center>NÃO</center>',
                    $presenca,
                    !empty($result->enviou_email_instrucoes) ? '<center>SIM</center>' : '<center>NÃO</center>',
                    $result->email_secundario ?: ' - ',
                    !empty($result->celular) ? formatarTelefone($result->celular) : ' - ',
                    !empty($result->telefone_comercial) ? formatarTelefone($result->telefone_comercial) : ' - ',
                    !empty($result->cpf) ? formatarCpfMasked($result->cpf) : ' - ',
                    $result->dre ?: ' - ',
                    $result->cargo_principal ?: ' - ',
                    $result->unidade_setor ?: ' - ',
                    $result->disciplina ?: ' - '
                ];
            }
        }
    }

    return $sorteados;
}

/*
function formatarCpfMasked($cpf) {
    $numeros = preg_replace('/[^0-9]/', '', $cpf);
    if (strlen($numeros) !== 11) return $cpf;
    return substr($numeros, 0, 3) . '.***.***-' . substr($numeros, 9, 2);
}

function formatarTelefone($telefone) {
    $numeros = preg_replace('/[^0-9]/', '', $telefone);
    if (strlen($numeros) < 10 || strlen($numeros) > 11) return $telefone;
    $ddd = substr($numeros, 0, 2);
    $numero = substr($numeros, 2);
    return strlen($numero) === 9
        ? sprintf('(%s) %s-%s', $ddd, substr($numero, 0, 5), substr($numero, 5))
        : sprintf('(%s) %s-%s', $ddd, substr($numero, 0, 4), substr($numero, 4));
}*/