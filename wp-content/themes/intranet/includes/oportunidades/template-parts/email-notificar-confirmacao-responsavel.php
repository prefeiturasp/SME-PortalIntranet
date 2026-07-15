<?php

extract( $args );

$candidato = Inscricao::obter_curriculo_usuario( $inscricao->user_id );
$etapas_processo = Inscricao::get_etapas_processo();

$emails = array_filter( [$candidato->email_principal, $candidato->email_secundario] );
$emails = implode( ', ', $emails );

$logo = get_template_directory_uri() . '/includes/oportunidades/template-parts/assets/img/logo.png';

?>

<html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
            .container { background-color: #ffffff; padding: 20px; border: 1px solid #ddd; max-width: 700px; margin: auto; }
            .dado { margin: 10px 0 20px 0; }
            a:active {color: blue;font-weight: 500;}
            .bloco-info {margin-bottom: 20px;}
            .bloco-info p {margin: 2px;}
            .bloco-info strong {display: block; padding-bottom: 2px;}
            .bloco-info .texto-cancelou {color: #b81820; font-weight: 700;}
            .bloco-info .texto-confirmou {color: #097e51; font-weight: 700;}
        </style>
    </head>
    <body>
        <div class='container'>

            <div class='dado'>Olá,</div>
            <div class='dado'>O candidato respondeu à solicitação de confirmação de interesse no processo seletivo.</div>

            <div class="bloco-info">
                <strong>Dados do Candidato:</strong>

                <?php
                $nome_completo = $candidato->nome_completo;

                if ( $candidato->nome_social ) {
                    $nome_completo = "{$candidato->nome_social} ({$candidato->nome_completo})";
                }

                if ( $nome_completo ) :
                    ?>
                    <p>Nome Completo: <?php echo esc_html( $nome_completo ); ?></p>
                    <?php
                endif;
                ?>
                <?php if ( $candidato->rf ) : ?>
                    <p>RF: <?php echo esc_html( $candidato->rf ); ?></p>
                <?php endif; ?>
                <?php if ( !empty( $emails ) ) : ?>
                    <p>E-mail(s): <?php echo esc_html( $emails ); ?></p>
                <?php endif; ?>
                <?php if ( $candidato->telefone_whatsapp ) : ?>
                    <p>WhatsApp: <?php echo esc_html( $candidato->telefone_whatsapp ); ?></p>
                <?php endif; ?>
            </div>

            <div class="bloco-info">
                <strong>Dados da Oportunidade:</strong>

                <p>ID da Oportunidade: <?php echo esc_html( $inscricao->oportunidade_id ); ?></p>
                <p>Título da Oportunidade: <?php echo esc_html( get_the_title( $inscricao->oportunidade_id ) ); ?></p>
            </div>

            <div class="bloco-info">
                <strong>Etapa do Processo Seletivo:</strong>

                <p><?php echo esc_html( $etapas_processo[$inscricao->status_confirm]['descricao'] ); ?></p>
            </div>

            <div class="bloco-info">
                <strong>Resposta do Candidato:</strong>

                <?php
                $class = $inscricao->confirmou_presenca == 1 ? 'texto-confirmou' : 'texto-cancelou';
                $label = $inscricao->confirmou_presenca == 1 ? 'Sim' : 'Não'
                ?>

                <p>
                    Confirmou Interesse -
                    <span class="<?php echo esc_html( $class ); ?>"><?php echo esc_html( $label ); ?></span>
                </p>
            </div>
            
            <div class="dado">
                <span>Para visualizar os detalhes da oportunidade e dar continuidade ao processo seletivo, acesse:</span>
                <p>
                    <a href="<?php echo esc_url( get_edit_post_link( $inscricao->oportunidade_id ) ); ?>">
                        <?php echo esc_html( get_edit_post_link( $inscricao->oportunidade_id ) ); ?>
                    </a>
                </p>
            </div>
            
            <p>Atenciosamente, <br><strong>Equipe do Portal de Oportunidades SME</strong></p>
            
            <img src="<?= $logo; ?>" width="120" alt="Logo SME">
            <br>
        </br>
    </body>
</html>