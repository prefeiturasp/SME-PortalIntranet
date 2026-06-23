<?php
namespace EnviaEmailOportunidade\classes;

class Envia_Emails_Oportunidades_SME {

    public $idInscricoes;
    public $idOportunidade;
    public $etapa;

    public function __construct($idInscricoes, $idOportunidade, $etapa) {
        $this->idInscricoes = $idInscricoes;
        $this->idOportunidade = $idOportunidade;
        $this->etapa = $etapa;
    }

    public function enviar_emails_conforme_etapa() {
        
        switch ($this->etapa) {
            case 'convocado_teste':
            case 'entrevista_agendada':
                $this->enviarEmailIndividuais();
                break;
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

    private function enviarEmailIndividuais() {        
        // Lógica para enviar email individual
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
        $link = get_home_url() . '/minhas-oportunidades/';
        $logo = get_template_directory_uri() . '/includes/oportunidades/template-parts/assets/img/logo.png';
        $iconeSeta = get_template_directory_uri() . '/includes/oportunidades/template-parts/assets/img/icon-seta.svg';
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
    
}