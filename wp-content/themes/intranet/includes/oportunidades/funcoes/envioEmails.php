<?php
namespace EnviaEmailOportunidade\classes;

use WP_Error;

class Envia_Emails_Oportunidades_SME {

    public $idInscricoes;
    public $idOportunidade;
    public $etapa;

    public function __construct($idInscricoes, $idOportunidade, $etapa = null) {
        $this->idInscricoes = $idInscricoes;
        $this->idOportunidade = $idOportunidade;
        $this->etapa = $etapa;
    }

    public function enviar_emails_conforme_etapa() {
        
        switch ($this->etapa) {            
            case 'analise_curricular':
            case 'nao_avancou_triagem':
            case 'nao_avancou_pos_entrevista':
            case 'analise_documental':
            case 'nao_selecionado':
            case 'aprovado':
                $this->enviarEmailAtualizarEtapa();
                break;
            default:
                // Lógica para outros tipos de oportunidades, se necessário
                break;
        }

    }

    public function enviar_email_confirmacao_interesse( string $conteudo_email, string $prazo_confirmacao ) {

        $anexos_processados = $this->processar_anexos_email();

        if (is_wp_error($anexos_processados)) {

            return false;

        }

        // Define o cabeçalho para e-mail HTML
        $headers = [
            'Content-Type: text/html; charset=UTF-8'
        ];

        global $wpdb;

        $tabelaTalentos = $wpdb->prefix . 'banco_talentos';
        $tabelaInscricoes = $wpdb->prefix . 'oportunidade_inscricoes';


        /**
         * ID da inscrição recebido no construtor
         */
        $id_inscricao = absint($this->idInscricoes);

        if (!$id_inscricao) {
            return false;
        }


        /**
         * Busca dados do candidato
         */
        $sql = $wpdb->prepare(
            "
            SELECT 
                t.email_principal,
                t.email_secundario
            FROM {$tabelaInscricoes} i
            INNER JOIN {$tabelaTalentos} t 
                ON t.id = i.curriculo_id
            WHERE i.id = %d
            ",
            $id_inscricao
        );


        $inscricao = $wpdb->get_row($sql, ARRAY_A);


        if (empty($inscricao)) {
            return false;
        }


        /**
         * Monta destinatários
         */
        $destinatarios = [];


        if (!empty($inscricao['email_principal'])) {
            $destinatarios[] = $inscricao['email_principal'];
        }


        if (!empty($inscricao['email_secundario'])) {
            $destinatarios[] = $inscricao['email_secundario'];
        }


        if (empty($destinatarios)) {
            return false;
        }


        /**
         * Dados da oportunidade
         */
        $oportunidade = get_the_title($this->idOportunidade);


        $pagina_minhas_oportunidades = get_field(
            'pagina_minhas_oportunidades',
            'options'
        );


        $link = $pagina_minhas_oportunidades
            ? get_permalink($pagina_minhas_oportunidades->ID)
            : home_url();



        /**
         * Conteúdo padrão do email
         */
        $texto_inicial = '<div class="dado">Olá,</div>';

        $texto_inicial .= '
            <div class="dado">
                Você está participando do processo seletivo da oportunidade:
            </div>
        ';


        $texto_pos_titulo = '
            <div class="dado">
                Sua candidatura recebeu uma atualização e o Gestor responsável solicita que você confirme seu interesse em continuar participando do processo seletivo.
            </div>
        ';


        $texto_pre_link = 'Para consultar os detalhes da atualização e realizar sua confirmação, acesse a área <span style="color: #0331CD; font-weight: 600;">"Minhas Oportunidades"</span> pelo link abaixo:';

        /**
         * Renderiza template do email
         */
        ob_start();

        get_template_part(
            '/includes/oportunidades/template-parts/email-confirmar-interesse',
            null,
            [
                'titulo_oportunidade' => $oportunidade,
                'texto_pre_link'      => $texto_pre_link,
                'link_oportunidades'  => $link,
                'texto_inicial'      => $texto_inicial,
                'texto_pos_titulo'   => $texto_pos_titulo,
                'mensagem'           => $conteudo_email,
                'prazo_confirmacao' => date('d/m/Y \à\s H:i', strtotime($prazo_confirmacao)),
            ]
        );

        $html = ob_get_clean();



        /**
         * Assunto do email
         */
        $assunto = 'Portal de Oportunidades SME | Atualização da sua candidatura';


        /**
         * Envio do email
         */
        $enviado = wp_mail(
            $destinatarios,
            $assunto,
            $html,
            $headers,
            $anexos_processados
        );


        return $enviado;

    }

    private function enviarEmailAtualizarEtapa() {

        // Define o cabeçalho para e-mail HTML
        $headers = array('Content-Type: text/html; charset=UTF-8');

        global $wpdb;

        $tabelaTalentos = $wpdb->prefix . 'banco_talentos';
        $tabelaInscricoes = $wpdb->prefix . 'oportunidade_inscricoes';

        $ids = is_array($this->idInscricoes)
            ? array_map('absint', $this->idInscricoes)
            : [absint($this->idInscricoes)];

        if (empty($ids)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($ids), '%d'));

        $sql = $wpdb->prepare(
            "
            SELECT 
                t.email_principal,
                t.email_secundario
            FROM {$tabelaInscricoes} i
            INNER JOIN {$tabelaTalentos} t 
                ON t.id = i.curriculo_id
            WHERE i.id IN ($placeholders)
            ",
            $ids
        );

        $inscricoes = $wpdb->get_results($sql, ARRAY_A);

        if (empty($inscricoes)) {           
            return;
        }

        $oportunidade = get_the_title($this->idOportunidade);
        $pagina_minhas_oportunidades = get_field( 'pagina_minhas_oportunidades', 'options' );
        $link = $pagina_minhas_oportunidades ? get_permalink( $pagina_minhas_oportunidades->ID ) : home_url();
        $logo = get_template_directory_uri() . '/includes/oportunidades/template-parts/assets/img/logo.png';
        $iconeSeta = get_template_directory_uri() . '/includes/oportunidades/template-parts/assets/img/seta-azul.png';
        $iconeAviso = get_template_directory_uri() . '/includes/oportunidades/template-parts/assets/img/icone-aviso.png';

        $assunto = "Portal de Oportunidades SME | Atualização da sua candidatura";

        

        foreach ($inscricoes as $inscricao) {            

            $email_principal = $inscricao['email_principal'];
            $email_secundario = $inscricao['email_secundario'];

            // Monta destinatários (1 email por inscrição)
            $destinatarios = [];

            if (!empty($email_principal)) {
                $destinatarios[] = $email_principal;
            }

            if (!empty($email_secundario)) {
                $destinatarios[] = $email_secundario;
            }

            // remove duplicados por segurança
            $destinatarios = array_unique($destinatarios);

            if (empty($destinatarios)) {
                continue;
            }

           

            // Template
            $temaEmail = file_get_contents(
                get_template_directory() . '/includes/oportunidades/template-parts/email-atualizar-etapas.php'
            );
           

            $temaEmail = str_replace('{TITULO-OPORTUNIDADE}', $oportunidade, $temaEmail);
            $temaEmail = str_replace('{LINK-OPORTUNIDADES}', $link, $temaEmail);
            $temaEmail = str_replace('{LINK-LOGO}', $logo, $temaEmail);
            $temaEmail = str_replace('{ICONE-SETA}', $iconeSeta, $temaEmail);
            $temaEmail = str_replace('{ICONE-AVISO}', $iconeAviso, $temaEmail);
            
          
            // Um unico envio para os dois destinatários
            wp_mail(
                $destinatarios,
                $assunto,
                $temaEmail,
                $headers
            );
        }

    }

    public function enviar_comunicado_inscritos( string $conteudo_email, array $anexos = [] ) {

        global $wpdb;

        if ( empty( $this->idInscricoes ) ) {

            return new WP_Error(
                'sem_inscricoes',
                'Nenhuma inscrição encontrada.'
            );
        }


        $tabelaTalentos = $wpdb->prefix . 'banco_talentos';
        $tabelaInscricoes = $wpdb->prefix . 'oportunidade_inscricoes';

        $placeholders = implode(',', array_fill(0, count($this->idInscricoes), '%d'));

        $sql = $wpdb->prepare(
            "
            SELECT 
                t.email_principal,
                t.email_secundario
            FROM {$tabelaInscricoes} i
            INNER JOIN {$tabelaTalentos} t 
                ON t.id = i.curriculo_id
            WHERE i.id IN ($placeholders)
            ",
            $this->idInscricoes
        );

        $inscricoes = $wpdb->get_results( $sql, ARRAY_A );

        if ( empty($inscricoes) ) {

            return new WP_Error(
                'sem_candidatos',
                'Nenhum candidato encontrado.'
            );
        }

        $oportunidade = get_the_title( $this->idOportunidade );
        $pagina_minhas_oportunidades = get_field( 'pagina_minhas_oportunidades', 'options' );
        $link = $pagina_minhas_oportunidades ? get_permalink( $pagina_minhas_oportunidades->ID ) : home_url();

        $temaEmail = file_get_contents( get_template_directory() . '/includes/oportunidades/template-parts/email-comunicar-candidatos.php' );
        $logo = get_template_directory_uri() . '/includes/oportunidades/template-parts/assets/img/logo.png';
        $iconeSeta = get_template_directory_uri() . '/includes/oportunidades/template-parts/assets/img/seta-azul.png';
        $iconeAviso = get_template_directory_uri() . '/includes/oportunidades/template-parts/assets/img/icone-aviso.png';
        $iconeMensagem = get_template_directory_uri() . '/includes/oportunidades/template-parts/assets/img/icone-mensagem.png';

        $template = str_replace(
            [
                '{TITULO-OPORTUNIDADE}',
                '{ORIENTACOES-COMPLEMENTARES}',
                '{LINK-OPORTUNIDADES}',
                '{LINK-LOGO}',
                '{ICONE-SETA}',
                '{ICONE-AVISO}',
                '{ICONE-MENSAGEM}'
            ],
            [
                $oportunidade,
                $conteudo_email,
                $link,
                $logo,
                $iconeSeta,
                $iconeAviso,
                $iconeMensagem
            ],
            $temaEmail
        );

        $headers = ['Content-Type: text/html; charset=UTF-8'];
        $assunto = 'Portal de Oportunidades SME | Atualização da sua candidatura';

        $enviados = 0;
        $falhas = [];

        foreach ( $inscricoes as $inscricao ) {

            $destinatarios = array_filter( [$inscricao['email_principal'], $inscricao['email_secundario']] );
            $destinatarios = array_unique( $destinatarios );

            if ( empty( $destinatarios ) ) {
                continue;
            }

            $resultado = wp_mail(
                $destinatarios,
                $assunto,
                $template,
                $headers,
                $anexos
            );


            if ( $resultado ) {
                $enviados++;

            } else {
                $falhas[] = $destinatarios;
            }
        }


        return [
            'enviados' => $enviados,
            'falhas' => $falhas
        ];
    }

    /**
     * Trata arquivos enviados para anexos de e-mail.
     *
     * @param string $campo Nome do campo no formulário.
     * @param int $limite_arquivos Quantidade de arquivos que podem ser anexados.
     * @param int $tamanho_maximo Tamanho máximo do arquivo que pode ser anexado (Em MB).
     *
     * @return array|WP_Error Array com caminhos dos arquivos ou WP_Error.
    */
    public function processar_anexos_email( $campo = 'anexos', int $limite_arquivos = 5, int $tamanho_maximo = 2 ) {

        if ( empty( $_FILES[$campo] ) || empty( $_FILES[$campo]['name'][0] ) ) {
            return [];
        }

        $arquivos = $_FILES[$campo];
        $tamanho_maximo = $tamanho_maximo * 1024 * 1024;

        $extensoes_permitidas = [
            'pdf',
            'doc',
            'docx',
            'xls',
            'xlsx',
            'jpg',
            'jpeg',
            'png'
        ];

        $anexos = [];
        $total = count( $arquivos['name'] );

        if ( $total > $limite_arquivos ) {

            return new WP_Error(
                'limite_anexos',
                'Você pode enviar no máximo 5 arquivos.'
            );
        }

        require_once ABSPATH . 'wp-admin/includes/file.php';

        foreach ( $arquivos['name'] as $indice => $nome ) {

            if ( empty( $nome ) ) {
                continue;
            }

            $tipo = wp_check_filetype( $nome );

            if ( empty( $tipo['ext'] ) || !in_array( strtolower( $tipo['ext'] ), $extensoes_permitidas, true ) ) {

                return new WP_Error(
                    'tipo_invalido',
                    'O arquivo "' . $nome . '" não é permitido.'
                );
            }

            if ( $arquivos['size'][$indice] > $tamanho_maximo ) {

                return new WP_Error(
                    'arquivo_grande',
                    'O arquivo "' . $nome . '" ultrapassa o limite permitido.'
                );
            }

            if ( $arquivos['error'][$indice] !== UPLOAD_ERR_OK ) {

                return new WP_Error(
                    'erro_upload',
                    'Erro ao enviar o arquivo: ' . $nome
                );
            }

            // Monta um array no formato esperado pelo WP
            $arquivo = [
                'name'     => $arquivos['name'][$indice],
                'type'     => $arquivos['type'][$indice],
                'tmp_name' => $arquivos['tmp_name'][$indice],
                'error'    => $arquivos['error'][$indice],
                'size'     => $arquivos['size'][$indice],
            ];

            $upload = wp_handle_upload( $arquivo, ['test_form' => false] );

            if ( isset( $upload['error'] ) ) {

                return new WP_Error(
                    'erro_processamento',
                    $upload['error']
                );
            }

            $anexos[] = $upload['file'];
        }

        return $anexos;
    }
}