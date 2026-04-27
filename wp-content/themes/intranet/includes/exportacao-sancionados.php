<?php
use Classes\Lib\SimpleXLSXGenExp;
/**
 * Exportar Sancionados para Excel
 */

wp_enqueue_script('bootstrap-sorteio-js');
wp_enqueue_style('bootstrap-sorteio-css');
wp_enqueue_style('toastr-sorteio-css');
wp_enqueue_script('toastr-sorteio-js');
add_action('wp_ajax_exportar_sancoes_excel', 'handle_exportar_sancoes_excel');
add_action('wp_ajax_nopriv_exportar_sancoes_excel', 'handle_exportar_sancoes_excel');

function handle_exportar_sancoes_excel() {
    if (!current_user_can('manage_options')) {
        wp_die('Sem permissão.');
    }

    // Inclui a lib
    $lib_file = get_stylesheet_directory() . '/classes/Lib/SimpleXLSXGen.php';
    require_once $lib_file;

    $nome_arquivo = 'participantes_sancionados_' . date('Y-m-d_H-i-s') . '.xlsx';

    $xlsx = new SimpleXLSXGenExp();
    $xlsx->setDefaultFont('Arial', 10);

    // Gera aba
    $aba = gerar_aba_sancoes();
    $xlsx->addSheet($aba, 'Sancionados');

    // Ajustes de layout
    $xlsx->setColWidth(1, 20); // Nome
    $xlsx->setColWidth(2, 20); // Email
    $xlsx->setColWidth(3, 10); // Evento ID
    $xlsx->setColWidth(4, 15); // Tipo da notícia
    $xlsx->setColWidth(5, 30); // Evento
    $xlsx->setColWidth(6, 18); // Data aplicação
    $xlsx->setColWidth(7, 18); // Data validade
    $xlsx->setColWidth(8, 12); // DRE
    $xlsx->setColWidth(9, 20); // Cargo
    $xlsx->setColWidth(10, 20); // Unidade
    $xlsx->setColWidth(11, 20); // Disciplina/Estágio

    // Mescla primeira e segunda linha (A1 até J2)
    $xlsx->mergeCells('A1:k2');


    // Download
    $xlsx->downloadAs($nome_arquivo);
    exit;
}

function gerar_aba_sancoes() {
    global $wpdb;

    $inscritos = [];

    $infoCabecalho = 'Participantes com Aplicação de Sanção';    
    $dt = new DateTime('now', new DateTimeZone('America/Sao_Paulo'));
    $infoCabecalho .= ' | Extraído em ' . $dt->format('d/m/Y - H:i');

    $inscritos[] = [
        '<style font-size="12" bgcolor="#b5b3f6" align="center" valign="center"><middle><center>' . $infoCabecalho . '</center></middle></style>',
        '', '', '', '', '', '', '', ''
    ];

    $query = "
    SELECT 
        s.data_aplicacao,
        s.data_validade,
        s.evento_id,
        p.post_title AS evento_titulo,

        COALESCE(i.nome_completo, ci.nome_completo) AS nome_completo,
        COALESCE(i.email_institucional, ci.email_institucional) AS email_institucional,
        COALESCE(i.dre, ci.dre) AS dre,
        COALESCE(i.cargo_principal, ci.cargo_principal) AS cargo_principal,
        COALESCE(i.unidade_setor, ci.unidade_setor) AS unidade_setor,
        COALESCE(i.disciplina, ci.disciplina) AS disciplina,
        COALESCE(i.programa_estagio, ci.programa_estagio) AS programa_estagio,

        s.tipo_noticia

        FROM {$wpdb->prefix}inscricao_sancoes AS s

        INNER JOIN {$wpdb->posts} AS p 
            ON p.ID = s.evento_id

        LEFT JOIN {$wpdb->prefix}inscricoes AS i 
            ON i.id = s.id_inscricao
            AND s.tipo_noticia = 's'

        LEFT JOIN {$wpdb->prefix}cortesias_inscricoes AS ci 
            ON ci.id = s.id_inscricao
            AND s.tipo_noticia = 'c'

        WHERE s.data_validade >= CURDATE()
        ORDER BY s.data_aplicacao DESC;
    ";
    $results = $wpdb->get_results($query);

    $inscritos[] = ['', '', '', '', '', '', '', '', '']; // linha em branco

    $inscritos[] = [
        '<style bgcolor="#652a96" color="#FFFFFF">Nome Completo</style>',
        '<style bgcolor="#652a96" color="#FFFFFF">E-mail Institucional</style>',
        '<style bgcolor="#652a96" color="#FFFFFF">ID do Evento</style>',
        '<style bgcolor="#652a96" color="#FFFFFF">Tipo da Notícia</style>',
        '<style bgcolor="#652a96" color="#FFFFFF">Nome do Evento</style>',
        '<style bgcolor="#652a96" color="#FFFFFF">Data da Sanção</style>',
        '<style bgcolor="#652a96" color="#FFFFFF">Valida até</style>',
        '<style bgcolor="#652a96" color="#FFFFFF">DRE/SME</style>',
        '<style bgcolor="#652a96" color="#FFFFFF">Cargo Atual</style>',
        '<style bgcolor="#652a96" color="#FFFFFF">Escola/Setor</style>',
        '<style bgcolor="#652a96" color="#FFFFFF">Disciplina/Estágio</style>',
    ];

    $programas_estagio = [
        '1' => 'Aprender sem limite',
        '2' => 'Parceiros da aprendizagem',
        '3' => 'Diversos',
    ];

    if ($results) {
        foreach ($results as $r) {
            if (!empty($r->programa_estagio)) {
                $r->cargo_principal = 'Estagiário(a)';
                $r->disciplina = $programas_estagio[$r->programa_estagio] ?? ' - ';
            }

            $inscritos[] = [
                $r->nome_completo ?: ' - ',
                $r->email_institucional ?: ' - ',
                $r->evento_id ?: ' - ',
                $r->tipo_noticia === 's' ? 'Sorteio' : 'Ordem de Inscrição',
                $r->evento_titulo ?: ' - ',
                $r->data_aplicacao ? date('d/m/Y', strtotime($r->data_aplicacao)) : ' - ',
                $r->data_validade ? date('d/m/Y', strtotime($r->data_validade)) : ' - ',
                $r->dre ?: ' - ',
                $r->cargo_principal ?: ' - ',
                $r->unidade_setor ?: ' - ',
                $r->disciplina ?: ' - ',
            ];
        }
    }

    return $inscritos;
}