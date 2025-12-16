<?php
namespace EnviaEmailSme\classes;
class Envia_Emails_Sorteio_SME {

    public $idInscrito;
    public $userId;
    public $idEvento;
    public $tipoEnvio;
    public $mensagem;
    public $anexo;
    public $tipo_sorteio;

    public function __construct($idInscrito = null, $userId = null, $idEvento, $tipoEnvio, $mensagem = null, $anexo = null) {
        add_filter('wp_mail_content_type', array($this, 'set_html_content_type'));
        $this->idInscrito = $idInscrito;
        $this->userId = $userId;
        $this->idEvento = $idEvento;
        $this->tipoEnvio = $tipoEnvio;
        $this->tipo_sorteio = get_field( 'tipo_evento', $idEvento );
        $this->mensagem = $mensagem;
        $this->anexo = $anexo;
        $this->envia_email_sme();
        remove_filter('wp_mail_content_type', array($this, 'set_html_content_type'));
    }

    public function set_html_content_type() {
        return 'text/html';
    }

    public function envia_email_sme() {

        $tipoEnvio = sanitize_text_field($this->tipoEnvio);
        $mensagem = $this->mensagem;
        $anexo = $this->anexo;
        $temaEmail = "";
        
        // Monta o E-mail em template HTML
        switch ($tipoEnvio) {
            case 'confirmacao':

                $assunto = 'Inscrição para o sorteio, confirmada!';
                // busca os dados na tabela
                $incrito = $this->retornaDadosDB($tipoEnvio);
                // Envia o e-mail
                $emailInstitucional = $incrito[0]["email_institucional"];
                $emailSecundario = $incrito[0]["email_secundario"];
                $postID = $incrito[0]["post_id"];
                $inscricaoID = $incrito[0]["id"];

                global $wpdb;
                $tabela = $wpdb->prefix . 'inscricao_datas';
                $datasInscricoes = $wpdb->get_results("SELECT data_evento FROM $tabela WHERE inscricao_id = $inscricaoID", ARRAY_A);
                $datasEvento = get_field('evento_datas', $postID);

                $resultado = '';

                if (is_array($datasInscricoes) && is_array($datasEvento)) {
                    foreach ($datasInscricoes as $inscricao) {
                        $dataEventoInscricao = $inscricao['data_evento'] ?? null;
                        if (!$dataEventoInscricao) continue;

                        foreach ($datasEvento as $evento) {
                            if (isset($evento['data']) && $evento['data'] === $dataEventoInscricao) {
                                // Formata as datas para o formato desejado
                                $dtEvento = \DateTime::createFromFormat('Y-m-d H:i:s', $evento['data']);
                                $dtSorteio = \DateTime::createFromFormat('Y-m-d', $evento['data_sorteio']);

                                if ($dtEvento && $dtSorteio) {
                                    $hora = $dtEvento->format('H');
                                    $minuto = (int) $dtEvento->format('i');

                                    if ($minuto === 0) {
                                        $horaFormatada = "{$hora}h";
                                    } else {
                                        $horaFormatada = sprintf("%sh%02d", $hora, $minuto);
                                    }

                                    $linha  = '<span class="destaque">Data do Evento:</span> ' 
                                            . $dtEvento->format('d/m/Y') 
                                            . ' às ' . $horaFormatada;
                                    $linha .= ' | <span class="destaque">Data do Sorteio:</span> ' 
                                            . $dtSorteio->format('d/m/Y');

                                    $resultado .= $linha . '<br>';
                                }
                                break; // achou o evento, sai do foreach interno
                            }
                        }
                    }

                    $resultado = rtrim($resultado, '<br>');
                    $dataSorteio = $resultado;
                } else {
                    // Data do sorteio
                    $dataSorteio = get_post_meta( $postID, 'data_sorteio', true );
                    $dateTime = strtotime($dataSorteio);
                    $dataSorteio = '<span class="destaque">Data do Sorteio:</span> ' . $this->converte_data_sorteio($dateTime);
                }

                
                
                $temaEmail = file_get_contents( DIR_ENVIA_EMAIL_SME . '/src/templates/tema-email-confirmacao.html');
                $temaEmail = str_replace('{NOME-INGRESSO}', $incrito[0]["post_title"], $temaEmail);
                $temaEmail = str_replace('{DATA-SORTEIO}', $dataSorteio, $temaEmail);
                $temaEmail = str_replace('{LINK-CANCELAR}', get_site_url()."/index.php/beneficios/sorteios/cancela-inscricao-sorteio/?ni=".base64_encode($incrito[0]["id"]), $temaEmail);
                $temaEmail = str_replace('{LINK-LOGO}', URL_ENVIA_EMAIL_SME . '/src/templates/assets/img/logo.png', $temaEmail);

                // Define o cabeçalho para e-mail HTML
                $headers = array('Content-Type: text/html; charset=UTF-8');
                
                // Envia o e-mail
                //wp_mail($emailInstitucional, $assunto, $temaEmail, $headers);

                if (isset($emailSecundario)){
                   //wp_mail($emailSecundario, $assunto, $temaEmail, $headers);
                }

                $this->registra_historico_e_envia_email($incrito[0]["id"], $incrito[0]["post_id"], $tipoEnvio, $emailInstitucional, $emailSecundario, $assunto, $temaEmail);
            break;
            case 'cancelamento':
                $assunto = 'Inscrição para o sorteio, cancelada!';
                $incrito = $this->retornaDadosDB($tipoEnvio);
                $emailInstitucional = $incrito[0]["email_institucional"];
                $emailSecundario = $incrito[0]["email_secundario"];

                $temaEmail = file_get_contents( DIR_ENVIA_EMAIL_SME . '/src/templates/tema-email-cancelamento.html');
                $temaEmail = str_replace('{NOME-INGRESSO}', $incrito[0]["post_title"], $temaEmail);
                $temaEmail = str_replace('{TEXTO-DATAS}', $mensagem, $temaEmail);
                $temaEmail = str_replace('{LINK-LOGO}', URL_ENVIA_EMAIL_SME . '/src/templates/assets/img/logo.png', $temaEmail);

                // Define o cabeçalho para e-mail HTML
                $headers = array('Content-Type: text/html; charset=UTF-8');

                // Envia o e-mail
                wp_mail($emailInstitucional, $assunto, $temaEmail, $headers);

                if (isset($emailSecundario)){
                   wp_mail($emailSecundario, $assunto, $temaEmail, $headers);
                }

            break;
            case 'vencedor':
                $assunto = 'Parabéns, você foi sorteado(a)!';

                $incrito = $this->retornaDadosDB($tipoEnvio);
                $emailInstitucional = $incrito[0]["email_institucional"];
                $emailSecundario = $incrito[0]["email_secundario"];

                $o_que = get_post_meta( $this->idEvento, 'o_que', true );
                
                if ( $this->tipo_sorteio === 'periodo' ) {
                    $info_periodo_evento = get_field( 'evento_periodo',$this->idEvento );
                    $dataEvento = "<strong>Período:</strong> {$info_periodo_evento['descricao']}";
                    $aviso_utilizacao = " <div class='espaco'>
                        <strong>Importante:</strong>
                        <p>Você poderá utilizar o seu ingresso durante o período descrito acima. Entretanto, sua participação é válida uma única vez.</p>
                    </div>";
                } else {
                    $dataEvento = $incrito[0]['data_sorteada'];
                    $dataEvento = '<strong>Data:</strong> ' . $this->converte_data_sorteio_com_hora($dataEvento);
                    $hora_evento = $this->obter_informacoes_data_evento($this->idEvento, $incrito[0]['data_sorteada']);
                    $horaTime = strtotime($hora_evento);
                    $aviso_utilizacao = '';
                }

                $generoBusca = get_field('genero_taxo', $this->idEvento);
                $genero = $generoBusca->name;
                $duracao = get_post_meta( $this->idEvento, 'duracao', true );
                $class_indicativa = get_post_meta( $this->idEvento, 'class_indicativa', true );
                $local = get_term(get_post_meta( $this->idEvento, 'local', true ));
                $local_outros = get_post_meta( $this->idEvento, 'local_outros', true );
                $endereco = get_post_meta( $this->idEvento, 'endereco', true );

                $temaEmail = file_get_contents( DIR_ENVIA_EMAIL_SME . '/src/templates/tema-email-vencedor.html');
                $temaEmail = str_replace('{NOME-INGRESSO}', $incrito[0]["post_title"], $temaEmail);
                $temaEmail = str_replace('{O-QUE}', $o_que, $temaEmail);
                $temaEmail = str_replace('{DATA-HORA-EVENTO}', $dataEvento, $temaEmail);
                if($genero){
                    $temaEmail = str_replace('{GENERO}', '<strong>Gênero:</strong> ' . $genero, $temaEmail);
                } else {
                    $temaEmail = str_replace('{GENERO}', '', $temaEmail);
                }
                if($duracao){
                    $temaEmail = str_replace('{DURACAO}', '<strong>Duração:</strong> ' . $duracao, $temaEmail);
                } else {
                    $temaEmail = str_replace('{DURACAO}', '', $temaEmail);
                }
                if($class_indicativa){
                    $temaEmail = str_replace('{CLASSIFICACAO}', '<strong>Classificação Indicativa:</strong> ' . $class_indicativa, $temaEmail);
                } else {
                    $temaEmail = str_replace('{CLASSIFICACAO}', '', $temaEmail);
                }
                if($local->name){
                    $temaEmail = str_replace('{LOCAL-EVENTO}', '<strong>Local:</strong> ' . $local->name, $temaEmail);
                } else {
                    $temaEmail = str_replace('{LOCAL-EVENTO}', '', $temaEmail);
                }
                if($endereco){
                    $temaEmail = str_replace('{ENDERECO-EVENTO}', '<strong>Endereço:</strong> ' . $endereco, $temaEmail);
                } else {
                    $temaEmail = str_replace('{ENDERECO-EVENTO}', '', $temaEmail);
                }                
                $temaEmail = str_replace('{LOCAL-OUTROS}', $local_outros, $temaEmail);
                $temaEmail = str_replace('{AVISO-PERIODO-UTILIZACAO}', $aviso_utilizacao, $temaEmail);
                $temaEmail = str_replace('{LINK-CONFIRMACAO}', get_site_url()."/index.php/beneficios/sorteios/confirma-inscricao-sorteio/?ni=".base64_encode($incrito[0]["id"])."&ne=".base64_encode($incrito[0]["post_title"]), $temaEmail);
                $temaEmail = str_replace('{LINK-LOGO}', URL_ENVIA_EMAIL_SME . '/src/templates/assets/img/logo.png', $temaEmail);
                
                $this->registra_historico_e_envia_email($incrito[0]["id"], $incrito[0]["post_id"], $tipoEnvio, $emailInstitucional, $emailSecundario, $assunto, $temaEmail);
                
            break;
            case 'instrucoes':
                $assunto = 'Instruções para o evento!';
                $incrito = $this->retornaDadosDB($tipoEnvio);
                $emailInstitucional = $incrito[0]["email_institucional"];
                $emailSecundario = $incrito[0]["email_secundario"];

                $temaEmail = file_get_contents( DIR_ENVIA_EMAIL_SME . '/src/templates/tema-email-instrucoes.html');
                //$temaEmail = str_replace('{NOME-INGRESSO}', $incrito[0]["post_title"], $temaEmail);
                $temaEmail = str_replace('{MENSAGEM}', $mensagem, $temaEmail);
                $temaEmail = str_replace('{LINK-LOGO}', URL_ENVIA_EMAIL_SME . '/src/templates/assets/img/logo.png', $temaEmail);

                // Define o cabeçalho para e-mail HTML
                $headers = array('Content-Type: text/html; charset=UTF-8');

                // Envia o e-mail
                if ($anexo) {
                    wp_mail($emailInstitucional, $assunto, $temaEmail, $headers, array($anexo));

                    if (isset($emailSecundario)){
                        wp_mail($emailSecundario, $assunto, $temaEmail, $headers, array($anexo));
                    }
                } else {
                    wp_mail($emailInstitucional, $assunto, $temaEmail, $headers);

                    if (isset($emailSecundario)){
                        wp_mail($emailSecundario, $assunto, $temaEmail, $headers);
                    }
                }

                $this->registra_historico_e_envia_email($incrito[0]["id"], $incrito[0]["post_id"], $tipoEnvio, $emailInstitucional, $emailSecundario, $assunto, $temaEmail);
                
                global $wpdb;
                $tbInscritos = $wpdb->prefix . 'inscricoes';

                // Atualiza a coluna para indicar que o email foi enviado
                $wpdb->update(
                    $tbInscritos,
                    array('enviou_email_instrucoes' => 1),
                    array('id' => $this->idInscrito),
                    array('%d'),
                    array('%d')
                );

            break;
            case 'desistencia':
                $assunto = 'Confirmação de cancelamento de inscrição!';
                $incrito = $this->retornaDadosDB($tipoEnvio);
                $emailInstitucional = $incrito[0]["email_institucional"];
                $emailSecundario = $incrito[0]["email_secundario"];

                
                $inscricao_id = $incrito[0]["id"];
                $chave = bin2hex(random_bytes(32));
                $agora = current_time('mysql');               
                $agora_timestamp = current_time('timestamp');
                $expira_em = date('Y-m-d H:i:s', $agora_timestamp + 86400);


                // Desativa links anteriores para essa inscrição
                global $wpdb;
                $wpdb->update(
                    'int_inscricao_cancelamento',
                    ['status' => 0],
                    [
                        'inscricao_id' => $inscricao_id,
                        'status' => 1
                    ]
                );

                // Insere novo link
                $wpdb->insert('int_inscricao_cancelamento', [
                    'inscricao_id' => $inscricao_id,
                    'chave' => $chave,
                    'criado_em' => $agora,
                    'expira_em' => $expira_em,
                    'status' => 1,
                ]);

                $dados = json_encode([
                    'id' => $inscricao_id,
                    'chave' => $chave
                ]);

                $chave = base64_encode($dados);
                
                $temaEmail = file_get_contents( DIR_ENVIA_EMAIL_SME . '/src/templates/tema-email-desistencia.html');
                $temaEmail = str_replace('{NOME-INGRESSO}', $incrito[0]["post_title"], $temaEmail);
                $temaEmail = str_replace('{LINK-CANCELAR}', get_site_url()."/index.php/beneficios/sorteios/cancela-inscricao-sorteio/?ni=" . $chave, $temaEmail);
                $temaEmail = str_replace('{LINK-LOGO}', URL_ENVIA_EMAIL_SME . '/src/templates/assets/img/logo.png', $temaEmail);

                // Define o cabeçalho para e-mail HTML
                $headers = array('Content-Type: text/html; charset=UTF-8');

                // Envia o e-mail
                wp_mail($emailInstitucional, $assunto, $temaEmail, $headers);

                if (isset($emailSecundario)){
                    wp_mail($emailSecundario, $assunto, $temaEmail, $headers);
                }

            break;
        }
    }

    public function converte_data_sorteio($dateTime) {

        $dataFormatada = date('l, d \d\e F \d\e Y', $dateTime);
    
        $diasSemana = ['Monday'=>'segunda-feira','Tuesday'=>'terça-feira','Wednesday'=>'quarta-feira','Thursday'=>'quinta-feira','Friday'=>'sexta-feira','Saturday'=>'sábado','Sunday'=>'domingo'];
        $meses = ['January'=>'janeiro','February'=>'fevereiro','March'=>'março','April'=>'abril','May'=>'maio','June'=>'junho','July'=>'julho','August'=>'agosto','September'=>'setembro','October'=>'outubro','November'=>'novembro','December'=>'dezembro'];
    
        $dataFormatada = str_replace(array_keys($diasSemana),array_values($diasSemana),$dataFormatada);
        $dataFormatada = str_replace(array_keys($meses),array_values($meses),$dataFormatada);

        return $dataFormatada;
    }

    public function converte_data_sorteio_com_hora($dataInput) {
        // Garante que temos um objeto DateTime
        if ($dataInput instanceof \DateTime) {
            $dateTime = $dataInput;
        } else {
            $dateTime = new \DateTime($dataInput);
        }

        // Usa o timestamp no formato esperado pela função original
        $dataTraduzida = $this->converte_data_sorteio($dateTime->getTimestamp());

        // Formata a hora no padrão desejado
        $hora = $dateTime->format('H');
        $minuto = (int) $dateTime->format('i');

        if ($minuto === 0) {
            $horaFormatada = "{$hora}h";
        } else {
            $horaFormatada = sprintf("%sh%02d", $hora, $minuto);
        }

        return $dataTraduzida . ' às ' . $horaFormatada;
    }

    public function obter_informacoes_data_evento(int $post_id, string $data) {
        
        $datas = get_field('evento_datas', $post_id);

        return reset(array_filter($datas, function ($item) use ($data) {
            return $item['data'] == $data;
        }));
    }

    public function retornaDadosDB($tipoEnvio){

        global $wpdb;

        $tbInscritos = $wpdb->prefix . 'inscricoes';
        $tbPosts = $wpdb->prefix . 'posts';
        $tbPostMeta = $wpdb->prefix . 'postmeta';

        switch($tipoEnvio){
            case 'confirmacao':
                return $wpdb->get_results("
                    SELECT DISTINCT(i.id), i.post_id, i.nome_completo, i.cpf, i.email_institucional, i.email_secundario, p.post_title
                    FROM $tbInscritos as i
                    INNER JOIN $tbPosts as p ON i.post_id = p.ID
                    INNER JOIN $tbPostMeta as pm ON p.ID = pm.post_id
                    WHERE i.id = $this->idInscrito AND i.post_id = $this->idEvento"
                , ARRAY_A);
            break;
            case 'cancelamento':
                return $wpdb->get_results("
                    SELECT i.id, i.post_id, i.email_institucional, i.email_secundario, p.post_title FROM $tbInscritos as i
                    INNER JOIN $tbPosts as p ON i.post_id = p.ID
                    WHERE i.user_id = $this->userId AND i.post_id = $this->idEvento"
                , ARRAY_A);
            break;
            case 'vencedor':
                return $wpdb->get_results("
                    SELECT DISTINCT(i.id), i.user_id, i.post_id, i.nome_completo, i.email_institucional, i.email_secundario, i.data_sorteada, p.post_title FROM $tbInscritos as i
                    INNER JOIN $tbPosts as p ON i.post_id = p.ID
                    WHERE i.id = $this->idInscrito AND i.post_id = $this->idEvento"
                , ARRAY_A);
            break;
            case 'instrucoes':
            case 'desistencia':
                return $wpdb->get_results("
                    SELECT i.id, i.post_id, i.email_institucional, i.email_secundario, p.post_title FROM $tbInscritos as i
                    INNER JOIN $tbPosts as p ON i.post_id = p.ID
                    WHERE i.id = $this->idInscrito AND i.post_id = $this->idEvento"
                , ARRAY_A);
            break;
        }
    }

    public static function get_inscrito_by_id($idInscrito){
        global $wpdb;
        $inscrito = $wpdb->get_results("
            SELECT i.id, i.post_id, i.nome_completo, i.cpf, i.email_institucional, i.email_secundario, i.sorteado, i.confirmou_presenca, i.prazo_confirmacao, i.data_sorteada, p.post_title 
            FROM int_inscricoes as i 
            INNER JOIN int_posts as p ON i.post_id = p.ID 
            WHERE i.id = $idInscrito
        ");

        $datas = $wpdb->get_col("
            SELECT data_evento 
            FROM int_inscricao_datas 
            WHERE inscricao_id = $idInscrito
        ");

        if($inscrito && $datas){
            $inscrito[0]->datas = $datas;
            $inscrito[0]->modelo = 'multi';
        } elseif($inscrito) {
            $inscrito[0]->modelo = 'unico';
        }

        return $inscrito;
    }

    public static function remove_inscrito_by_id($idInscrito, $modelo, $datas = [], $chave = '') {
        global $wpdb;
        $tabela_inscricoes = $wpdb->prefix . 'inscricoes';
        $tabela_datas = $wpdb->prefix . 'inscricao_datas';
        $inscrito = self::get_inscrito_by_id($idInscrito);
        $tipo_evento = get_field( 'tipo_evento', $inscrito[0]->post_id );
        $array_premios = obter_array_premios_sorteio( $inscrito[0]->post_id );

        $mensagemDatas = '';
        if($modelo == 'multi' && is_array($datas) && count($datas) >= 1){
            if ( $tipo_evento === 'premio' ) {
                
                $premios = array_map( function( $data ) use ( $array_premios ) {
                    return $array_premios[$data];
                }, $datas );
    
                if(count($premios) > 1) {
                    $mensagemDatas = 'Você não está mais participando do sorteio dos prêmios: <br>'. implode( ',<br>', $premios );
                } else {
                    $mensagemDatas = 'Você não está mais participando do sorteio do prêmio: <br>'. $premios[0];
                }

            } else {

                $datasFormatadas = array_map(function($data) {
                    $dt = new \DateTime($data);
                    $hora = $dt->format('H');
                    $minuto = (int) $dt->format('i');
    
                    if ($minuto === 0) {
                        $horaFormatada = "{$hora}h";
                    } else {
                        $horaFormatada = sprintf("%sh%02d", $hora, $minuto);
                    }
    
                    return $dt->format('d/m/Y') . ' ' . $horaFormatada;
                }, $datas);
    
                if(count($datas) > 1) {
                    $mensagemDatas = 'Você não está mais participando deste sorteio nas datas: <br>'. implode(',<br>', $datasFormatadas);
                } else {
                    $mensagemDatas = 'Você não está mais participando deste sorteio na data: <br>'. $datasFormatadas[0];
                }
            }
            
        } else {
            $mensagemDatas = 'Você não está mais participando deste sorteio.';
        }

        $inscrito = self::get_inscrito_by_id($idInscrito);
        $nomeEvento = $inscrito[0]->post_title;
        $emailInstitucional = $inscrito[0]->email_institucional;
        $emailSecundario = $inscrito[0]->email_secundario;
        $assunto = 'Inscrição para o sorteio, cancelada!';
        $temaEmail = file_get_contents( DIR_ENVIA_EMAIL_SME . '/src/templates/tema-email-cancelamento.html');
        $temaEmail = str_replace('{NOME-INGRESSO}', $nomeEvento, $temaEmail);
        $temaEmail = str_replace('{TEXTO-DATAS}', $mensagemDatas, $temaEmail);
        $temaEmail = str_replace('{LINK-LOGO}', URL_ENVIA_EMAIL_SME . '/src/templates/assets/img/logo.png', $temaEmail);

        $headers = array('Content-Type: text/html; charset=UTF-8');
        $enviaEmail = wp_mail($emailInstitucional, $assunto, $temaEmail, $headers);

        if (isset($emailSecundario)){
            $enviaEmail = wp_mail($emailSecundario, $assunto, $temaEmail, $headers);
        }

        if($modelo == 'multi'){
            if (!is_array($datas)) {
                $datas = [$datas];
            }
            
            foreach($datas as $data){
                $wpdb->delete($tabela_datas, ['inscricao_id' => $idInscrito, 'data_evento' => $data], ['%d', '%s']);
            }

            // Verifica se ainda restam datas
            $restantes = $wpdb->get_var($wpdb->prepare("
                SELECT COUNT(*) FROM {$tabela_datas}
                WHERE inscricao_id = %d
            ", $idInscrito));

            if($restantes == 0){
                $wpdb->delete($tabela_inscricoes, [
                'id' => $idInscrito
                ], ['%d']);
            }

            $res = true;
            
        } else {
            $where = array('id' => $idInscrito);
            $res = $wpdb->delete( $tabela_inscricoes, $where);
        }

        if($chave && $chave != ''){
            $wpdb->update(
                'int_inscricao_cancelamento',
                ['status' => 2],
                ['chave' => $chave]
            );
        }

        return array('res'=>$res, 'mail'=>$enviaEmail);
        
    }

    public static function confirma_presenca_inscrito($idInscrito, $resposta) {
        global $wpdb;
        $data = array('confirmou_presenca' => $resposta);
        $where = array('id' => $idInscrito);
        $res = $wpdb->update( 'int_inscricoes', $data, $where);
        return array('res' => $res);
    }

    public function registra_historico_e_envia_email($idInscrito, $idEvento, $tipoEmail, $emailInstitucional, $emailSecundario = null, $assunto, $temaEmail) {
        global $wpdb;

        date_default_timezone_set('America/Sao_Paulo');
        
        $tabela = $wpdb->prefix . 'inscricoes';
        $arrHistorico = $wpdb->get_results("SELECT historico_emails FROM $tabela WHERE id = $idInscrito AND post_id = $idEvento", ARRAY_A);

        if(isset($arrHistorico[0]['historico_emails']) && $arrHistorico[0]['historico_emails'] != null){
            $arrBase = json_decode($arrHistorico[0]['historico_emails']);
        } else {
            $arrBase = (object) array('confirmacao' => (object) array("enviado"=> '0', "data_hora_envio" => null), 'vencedor' => (object) array("enviado"=> '0', "data_hora_envio" => null), 'instrucoes' => (object) array("enviado"=> '0', "data_hora_envio" => null));
        }

        // Define o cabeçalho para e-mail HTML
        $headers = array('Content-Type: text/html; charset=UTF-8');

        switch ($tipoEmail) {
            case 'confirmacao':
                if($arrBase->confirmacao->enviado == '0'){
                    $arrBase->confirmacao->enviado = '1';
                    $arrBase->confirmacao->data_hora_envio = date('Y-m-d H:i:s');
                    $data = array('historico_emails' => json_encode($arrBase));
                    $where = array('id' => $idInscrito, 'post_id' => $idEvento);
                    
                    $ConfEmailInst = wp_mail($emailInstitucional, $assunto, $temaEmail, $headers);

                    if (isset($emailSecundario)){
                        $ConfEmailSec = wp_mail($emailSecundario, $assunto, $temaEmail, $headers);
                    }

                    if(isset($ConfEmailInst) || isset($ConfEmailSec)){
                        $wpdb->update( $tabela, $data, $where);
                    }
                }
            break;
            case 'vencedor':
                if($arrBase->vencedor->enviado == '0'){
                    $arrBase->vencedor->enviado = '1';
                    $arrBase->vencedor->data_hora_envio = date('Y-m-d H:i:s');
                    $data = array('historico_emails' => json_encode($arrBase));
                    $where = array('id' => $idInscrito, 'post_id' => $idEvento);

                    $ConfEmailInst = wp_mail($emailInstitucional, $assunto, $temaEmail, $headers);

                    if (isset($emailSecundario)){
                        $ConfEmailSec = wp_mail($emailSecundario, $assunto, $temaEmail, $headers);
                    }

                    if(isset($ConfEmailInst) || isset($ConfEmailSec)){
                        $wpdb->update( $tabela, $data, $where);
                    }
                }
            break;
            case 'instrucoes':
                if (!isset($arrBase->instrucoes)) {
                    $arrBase->instrucoes = new \stdClass();
                }

                if (!isset($arrBase->instrucoes->enviado) || $arrBase->instrucoes->enviado == '0' || $arrBase->instrucoes->enviado == '') {
                    $arrBase->instrucoes->enviado = '1';
                    $arrBase->instrucoes->data_hora_envio = date('Y-m-d H:i:s');

                    $data = array('historico_emails' => json_encode($arrBase));
                    $where = array('id' => $idInscrito, 'post_id' => $idEvento);

                    $wpdb->update($tabela, $data, $where);
                }
            break;
        }
    }
}