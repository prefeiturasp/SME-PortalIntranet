<?php

extract( $args );

$titulo_oportunidade = esc_html( $titulo_oportunidade );
$link_oportunidades = esc_url( $link_oportunidades );
$texto_inicial = $texto_inicial ?? '';
$texto_pos_titulo = $texto_pos_titulo ?? '';
$texto_pre_link = $texto_pre_link ?? '';
$mensagem = $mensagem ?? '';
$mensagem_html = $mensagem ?? '';
$mensagem = trim(
    preg_replace('/\xC2\xA0/', '',
        strip_tags($mensagem)
    )
);
$prazo_confirmacao = $prazo_confirmacao ?? '';
$logo = get_template_directory_uri() . '/includes/oportunidades/template-parts/assets/img/logo.png';
$iconeSeta = get_template_directory_uri() . '/includes/oportunidades/template-parts/assets/img/seta-azul.png';
$iconeAviso = get_template_directory_uri() . '/includes/oportunidades/template-parts/assets/img/icone-aviso.png';
$iconeMensagem = get_template_directory_uri() . '/includes/oportunidades/template-parts/assets/img/icone-mensagem.png';
$iconeCalendario = get_template_directory_uri() . '/includes/oportunidades/template-parts/assets/img/icone-calendario.png';
?>

<html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
            .container { background-color: #ffffff; padding: 20px; border: 1px solid #ddd; max-width: 700px; margin: auto; }
            .dado { margin: 10px 0 20px 0; }
            .espaco { padding-top: 25px; }
            .destaque-azul { background-color: #F6FAFD; color: #0331CD; padding: 36px; font-weight: 600; font-size: 1rem; display: inline-block; border: 1px solid #dde5ff; border-radius: 10px; margin-bottom: 30px; }
            .destaque{  background-color: #F6FAFD; padding: 36px 20px; display: inline-block; border: 1px solid #dde5ff; border-radius: 10px; margin-bottom: 30px; }
            a:active {color: blue;font-weight: 500;}
        </style>
    </head>
    <body>
        <div class='container'>

            <?php if ( isset( $texto_inicial ) && !empty( $texto_inicial ) ) : ?>
                <?= $texto_inicial; ?>
            <?php endif; ?>
            
            <?php if ( isset( $titulo_oportunidade ) && !empty( $titulo_oportunidade ) ) : ?> 
                <div class='espaco'><span class="destaque-azul"><?= $titulo_oportunidade; ?></span></div>
            <?php endif; ?>

            <?php if ( isset( $texto_pos_titulo ) && !empty( $texto_pos_titulo ) ) : ?>
                <?= $texto_pos_titulo; ?>
            <?php endif; ?>

            <?php if ( isset( $prazo_confirmacao ) && !empty( $prazo_confirmacao ) ) : ?>
                <div class='espaco'>
                    <span class="destaque" style="min-width:94%">

                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="width:100%;">
                            <tr>                            
                                <td style="vertical-align: top; padding-right: 20px; width: 24px;">
                                    <img src="<?= $iconeCalendario; ?>" alt="Ícone de prazo"
                                    style="display:block; border:0; outline:none;">
                                </td>
                                
                                <td style="vertical-align: top;">
                                    <h3 style="color: #020202; font-weight: 600;">Prazo para confirmação:</h3>
                                    <p>Você tem até <span style="color: #0331CD; font-weight: 600;"><?= $prazo_confirmacao; ?></span> para confirmar seu interesse.</p>
                                </td>                            
                            </tr>
                        </table>
                                            
                    </span>
                </div>
            <?php endif; ?>

            <?php if ( isset( $mensagem ) && !empty( $mensagem ) ) : ?>
                <div class='espaco'>
                    <span class="destaque" style="min-width:94%">

                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="width:100%;">
                            <tr>                            
                                <td style="vertical-align: top; padding-right: 20px; width: 24px;">
                                    <img src="<?= $iconeMensagem; ?>" alt="Ícone de aviso"
                                    style="display:block; border:0; outline:none;">
                                </td>
                                
                                <td style="vertical-align: top;">
                                    <h3 style="color: #0331CD; font-weight: 600;">Orientações Complementares:</h3>
                                    <?= $mensagem_html; ?>
                                </td>                            
                            </tr>
                        </table>
                                            
                    </span>
                </div>
            <?php endif; ?>

            <?php if ( isset( $texto_pre_link ) && !empty( $texto_pre_link ) ) : ?>
                <div class='dado'>
                    <?= $texto_pre_link; ?>
                </div>
            <?php endif; ?>
            
            <?php if ( isset( $link_oportunidades ) && !empty( $link_oportunidades ) ) : ?>
                <p>
                    <a href="<?= $link_oportunidades; ?>" style="color: #FFFFFF; font-weight: 600; background: #0331CD; padding: 20px; display: inline-block; text-decoration: none; border-radius: 7px; margin-top: 30px; margin-bottom: 30px;"><img src="<?= $iconeSeta; ?>" alt="Ícone de oportunidades" style="vertical-align: middle"> Acessar minhas oportunidades</a>
                </p>            
            <?php endif; ?>
            
            <p>Atenciosamente, <br><strong>Equipe do Portal de Oportunidades SME</strong></p>

            <div class='espaco'>
                <span class="destaque">

                    <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="width:100%;">
                        <tr>                            
                            <td style="vertical-align: top; padding-right: 20px; width: 24px;">
                                <img src="<?= $iconeAviso; ?>" alt="Ícone de aviso"
                                style="display:block; border:0; outline:none;">
                            </td>
                            
                            <td style="vertical-align: top;">
                            
                                <span style="color: #0331CD; font-weight: 600;">Importante:</span> Este é um e-mail enviado automaticamente pelo Portal de Oportunidades SME e não recebe respostas.<br><br>
                                Em caso de novas comunicações sobre sua candidatura, elas serão encaminhadas por este mesmo canal.

                            </td>                            
                        </tr>
                    </table>
                                        
                </span>
            </div>
            
            <img src="<?= $logo; ?>" width="120" alt="Logo SME">
            <br>
        </br>
    </body>
</html>