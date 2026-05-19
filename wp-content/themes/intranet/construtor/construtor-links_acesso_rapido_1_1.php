<?php
$visibilidade_bloco = get_sub_field( 'visibilidade_bloco' );
$lista_links = get_sub_field( 'links' );
$perfil_usuario = get_perfil_usuario_logado();

?>

<?php if ( $visibilidade_bloco === 'todos' || $visibilidade_bloco === $perfil_usuario && $lista_links ) : ?>
    <div class="container my-2" id="acesso-rapido">
        <div class="row">

            <?php foreach ( $lista_links as $item ) : ?>
                <div class="col-md-4 mb-4">
                    <a
                        href="<?php echo esc_url( $item['url'] ); ?>"
                        target="<?php echo $item['target'] ? '_blank' : '_self'; ?>"
                        class="custom-card text-center h-100"
                        >

                        <?php if ( isset( $item['titulo'] ) && !empty( $item['titulo'] ) ) : ?>
                            <h5 class="card-title mb-4">
                                <?php echo esc_html( $item['titulo'] ); ?>
                            </h5>
                        <?php endif; ?>
                        
                        <?php if ( isset( $item['icone'] ) && !empty( $item['icone'] ) ) : ?>
                        <div class="card-image mb-4">
                            <img src="<?php echo esc_url( $item['icone'] ); ?>" class="img-fluid">
                        </div>
                        <?php endif; ?>

                        <?php if ( isset( $item['descricao'] ) && !empty( $item['descricao'] ) ) : ?>
                            <?php echo esc_html( $item['descricao'] ); ?>
                        <?php endif; ?>

                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>