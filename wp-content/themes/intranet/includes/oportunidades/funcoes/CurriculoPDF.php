<?php

use Dompdf\Dompdf;
use Dompdf\Options;

class CurriculoPDF
{

    public static function init(): void
    {
        add_action(
            'admin_post_download_curriculo_pdf',
            [self::class, 'download']
        );
    }

    public static function download(): void
    {

        if (!current_user_can('edit_posts')) {
            //wp_die('Sem permissão.');
        }

        $user_id = isset($_GET['user_id'])
            ? absint($_GET['user_id'])
            : 0;

        if (!$user_id) {
            wp_die('Usuário inválido.');
        }

        $dados = Curriculo::obter_dados($user_id);
        $html = Curriculo::gerar_html($dados);
        
        $css = '';
        
        // Carrega o CSS específico do PDF
        $css .= file_get_contents(
            get_template_directory() . '/style.css'
        );

        $css .= file_get_contents(
            get_template_directory() . '/css/bootstrap-4-2-1.min.css'
        );        

        $css .= "
            @page {
                margin: 12mm 12mm !important;
            }
            
            .row {
                display: block !important;
                margin: 0 !important;
            }

            .col,
            .col-1,
            .col-2,
            .col-3,
            .col-4,
            .col-5,
            .col-6,
            .col-7,
            .col-8,
            .col-9,
            .col-10,
            .col-11,
            .col-12 {
                display: block !important;
                float: none !important;
                width: 100% !important;
                padding: 0 !important;
            }

            .container,
            .container-fluid {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .curriculo-subsecao .table{
                font-size: 14px;
            }
        ";        

        $html = '
            <style>
                ' . $css . '
            </style>
        ' . $html;

        $options = new Options();
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait', "margin:1cm 2cm 1cm 2cm");
        $dompdf->render();

        $dompdf->stream(
            'curriculo.pdf',
            [
                'Attachment' => true
            ]
        );

        exit;
    }

}

CurriculoPDF::init();