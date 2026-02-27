<?php 
    $imagem = get_sub_field('imagem_de_fundo'); // link
    $user = wp_get_current_user();
    $rf = get_field('rf', 'user_' . get_current_user_id());
    $rg = get_field('rg', 'user_' . get_current_user_id());
    $cpf = get_field('cpf', 'user_' . get_current_user_id());
    $cargo = get_field('cargo', 'user_' . get_current_user_id());
    if($cargo == 'Outro')
        $cargo = get_field('cargo_outro', 'user_' . get_current_user_id());
?>

<div class="user-card" style="background-image: url(<?= $imagem; ?>);">
    <div class="user-card-info">
        <div class="info-line-one d-flex justify-content-between">
            <div class="nome">
                Nome <span><?= $user->data->display_name; ?></span>
            </div>
            <div class="rf">
                RF <span><?= $rf; ?></span>
            </div>
        </div>
        <div class="info-line-two d-flex justify-content-between">
            <div class="rg">
                RG <span><?= $rg; ?></span>
            </div>
            <div class="cpf">
                CPF <span><?= $cpf; ?></span>
            </div>
        </div>
        <div class="info-line-two d-flex justify-content-between">
            <div class="cargo">
                Cargo/Função <span><?= $cargo; ?></span>
            </div>
        </div>
    </div>
</div>
<?php if( $user->data->display_name && $rf && $rg && $cpf && $cargo): ?>    
    <a href="<?= get_home_url();?>/cartao-educador.php" target="_blank" class="btn btn-outline-primary mt-2"><i class="fa fa-download" aria-hidden="true"></i> Download</a>
<?php else: ?>
    <button type="button" class="btn btn-outline-primary mt-2" data-toggle="modal" data-target="#profileModal"><i class="fa fa-download" aria-hidden="true"></i> Download</button>
    <!-- Modal -->
    <div class="modal fade" id="profileModal" tabindex="-1" role="dialog" aria-labelledby="profileModalLabel" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered" role="document">
            <div class="modal-content">
            <div class="modal-header">
                
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <img src="<?= get_template_directory_uri(); ?>/img/atualizar-dados.jpg" />
                <p class="text-center mt-3">“Seus dados estão incompletos!<br>
                Favor atualizá-los em seu perfil!”</p>
                <p class="text-center"><a href="<?= get_home_url();?>/index.php/perfil/" class="btn btn-primary">Atualize agora</a></p>
            </div>
            
            </div>
        </div>
    </div>
<?php endif; ?>