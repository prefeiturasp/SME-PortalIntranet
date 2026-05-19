<style>
    body {
        margin: 0;
        padding: 24px;
        background-color: #f5f7fa;
        font-family: Arial, Helvetica, sans-serif;
        color: #333333;
    }

    .email-container {
        max-width: 720px;
        margin: 0 auto;
        padding: 30px;
        background-color: #ffffff;
        border: 1px solid #dde3ea;
        border-radius: 8px;
    }

    .email-container p {
        margin: 0;
        line-height: 1.5;
        font-size: 14px;
    }

    .email-container p.link {
        margin: 20px 0;
    }

    .email-container p.mensagem {
        text-align: justify;
    }

    .email-container strong {
        color: #1f2937;
    }

    .email-container a {
        color: #2563eb;
        text-decoration: none;
        word-break: break-word;
    }

    .email-container a:hover {
        text-decoration: underline;
    }
</style>

<div class="email-container">
    
    <?php if ( isset( $mensagem ) && !empty( $mensagem ) ) : ?>
        <p class="mensagem"><?php echo esc_html( $mensagem ); ?></p>
    <?php endif; ?>

    <?php if ( isset( $link ) && !empty( $link ) ) : ?>
        <p class="link">
            <strong>Link da Oportunidade:</strong>
            <a href="<?php echo esc_url( $link ); ?>">
                <?php echo esc_html( $link ); ?>
            </a>
        </p>
    <?php endif; ?>

    <?php if ( isset( $tipo_oportunidade ) && !empty( $tipo_oportunidade ) ) : ?>
        <p>
            <strong>Tipo da Oportunidade:</strong>
            <?php echo esc_html( $tipo_oportunidade ); ?>
        </p>
    <?php endif; ?>

    <?php if ( isset( $nome_gestor ) && !empty( $nome_gestor ) ) : ?>

        <?php if ( isset( $email_gestor ) && !empty( $email_gestor ) ) : ?>
            <p>
                <strong>Gestor Responsável:</strong>
                <?php echo esc_html( $nome_gestor ); ?> -
                <a href="mailto:<?php echo esc_attr( $email_gestor ); ?>">
                    <?php echo esc_html( $email_gestor ); ?>
                </a>
            </p>
        <?php else : ?>
            <p>
                <strong>Gestor Responsável:</strong>
                <?php echo esc_html( $nome_gestor ); ?>
            </p>
        <?php endif; ?>

    <?php endif; ?>
</div>