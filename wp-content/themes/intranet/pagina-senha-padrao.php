<?php
/**
 * Template Name: Senha Padrão
 *
 * Allow users to update their profiles from Frontend.
 *
 
*/
/* Get user info. */
wp_enqueue_script('pagina-login');
global $current_user, $wp_roles;
get_currentuserinfo();

/* Load the registration file. */
require_once( ABSPATH . WPINC . '/registration.php' );
$error = array();    
/* If profile was saved, update profile. */
if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) && $_POST['action'] == 'update-user' ) {

    
    /* Update user information. */
    if ( !empty( $_POST['url'] ) )
       wp_update_user( array ('ID' => $current_user->ID, 'user_url' => esc_attr( $_POST['url'] )));
    if ( !empty( $_POST['email'] ) ){
        if (!is_email(esc_attr( $_POST['email'] )))
            $error[] = __('The Email you entered is not valid.  please try again.', 'profile');
        elseif( email_exists(esc_attr( $_POST['email'] )) && email_exists(esc_attr( $_POST['email'] )) != $current_user->id )
            $error[] = __('This email is already used by another user.  try a different one.', 'profile');
        else{
            $usuario = get_field('rf', 'user_' . $current_user->ID);
            $api_url = 'https://hom-smeintegracaoapi.sme.prefeitura.sp.gov.br/api/AutenticacaoSgp/AlterarEmail';
            $email = $_POST['email'];
            $response = wp_remote_post( $api_url, array(
                'method'      => 'POST',                    
                'headers' => array( 
                        'x-api-eol-key' => 'fe8c65abfac596a39c40b8d88302cb7341c8ec99',                    
                    ),
                'body' => array("Usuario" => "$usuario","Email" => "$email"),
                )
            );
            
            if ( is_wp_error( $response ) ) {
                $error_message = $response->get_error_message();
                echo "Something went wrong: $error_message";
            } else {
                //echo "<pre>";
                //print_r($response);
                //echo "</pre>";
                $response = json_decode($response['body']);
                
            }
            wp_update_user( array ('ID' => $current_user->ID, 'user_email' => esc_attr( $_POST['email'] )));
            echo "Aqui";
        }
    }

    // Nome
    if ( !empty( $_POST['first-name'] ) )
        update_user_meta( $current_user->ID, 'first_name', esc_attr( $_POST['first-name'] ) );

    // Sobrenome
    if ( !empty( $_POST['last-name'] ) )
        update_user_meta($current_user->ID, 'last_name', esc_attr( $_POST['last-name'] ) );

    if ( !empty( $_POST['acf']['field_621f6ccdf288d'] ) )
        update_user_meta( $current_user->ID, 'rf', esc_attr( $_POST['acf']['field_621f6ccdf288d'] ) );
    
    // DRE
    if ( !empty( $_POST['acf']['field_6241fd988f34d'] ) )
        update_user_meta( $current_user->ID, 'dre', esc_attr( $_POST['acf']['field_6241fd988f34d'] ) );
    
    // Cargo
    if ( !empty( $_POST['acf']['field_6241ffb3bf190'] ) )
        update_user_meta( $current_user->ID, 'cargo', esc_attr( $_POST['acf']['field_6241ffb3bf190'] ) );

    // Cargo (Outros)
    if ( !empty( $_POST['acf']['field_624200d68cfd0'] ) )
        update_user_meta( $current_user->ID, 'cargo_outro', esc_attr( $_POST['acf']['field_624200d68cfd0'] ) );

    // Celular
    if ( !empty( $_POST['acf']['field_62420050a8eb3'] ) )
        update_user_meta( $current_user->ID, 'celular', esc_attr( $_POST['acf']['field_62420050a8eb3'] ) );

    // RG
    if ( !empty( $_POST['acf']['field_624ae351630a7'] ) )
        update_user_meta( $current_user->ID, 'rg', esc_attr( $_POST['acf']['field_624ae351630a7'] ) );
    
    
    // These files need to be included as dependencies when on the front end.
    require_once( ABSPATH . 'wp-admin/includes/image.php' );
    require_once( ABSPATH . 'wp-admin/includes/file.php' );
    require_once( ABSPATH . 'wp-admin/includes/media.php' );

    // Let WordPress handle the upload.
    $img_id = media_handle_upload( 'avatar_user', 0 );

    if ( is_wp_error( $img_id ) ) {
        echo "Error";
    } else {
        update_user_meta( $current_user->ID, 'imagem', $img_id );
    }



    /* Redirect so the page will show updated info.*/
  /*I am not Author of this Code- i dont know why but it worked for me after changing below line to if ( count($error) == 0 ){ */
    if ( count($error) == 0 ) {
        //action hook for plugins and extra fields saving
        do_action('edit_user_profile_update', $current_user->ID);
        wp_redirect( get_permalink().'?updated=true' ); exit;
    }       
}

$email = email_exists(esc_attr( 'aa7217757@sme.prefeitura.sp.gov.br' ));
print_r($email);

get_header(); // Loads the header.php template. ?>

<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="profile-title"><?= get_the_title(); ?></h1>
        </div>        
    </div>
</div>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
    <div id="post-<?php the_ID(); ?>">
        <div class="container">
            <div class="profile-form">
                <?php the_content(); ?>
                <?php if ( !is_user_logged_in() ) : ?>
                        <p class="warning">
                            <?php _e('Você precisa estar logado para editar o seu perfil.', 'profile'); ?>
                        </p><!-- .warning -->
                <?php else : ?>
                    <?php if ( count($error) > 0 ) echo '<p class="error">' . implode("<br />", $error) . '</p>'; ?>
                    <form method="post" id="adduser" action="<?php the_permalink(); ?>" enctype='multipart/form-data'>
                        
                        <div class="row">
                            <div class="col-12">
                                <div class="profile-avatar">

                                    <div class="img-profile d-none d-md-flex">
                                        <?php
                                            $parceira = get_field('parceira', 'user_'. $current_user->ID );
                                            $image_id = get_field('imagem', 'user_' . $current_user->ID);
                                            $image_profile = $img_atts = wp_get_attachment_image_src($image_id, 'thumbnail');

                                            //echo "<pre>";
                                            //print_r($image_id);
                                            //echo "</pre>";
                                                                           
                                            if($image_id['sizes']['thumbnail']):
                                        ?>
                                                <img src="<?= $image_id['sizes']['thumbnail']; ?>" alt="Imagem de perfil">
                                        <?php else: ?>
                                            <img src="<?= get_template_directory_uri() . '/img/user-image.jpg'; ?>" alt="Avatar">                                     
                                        <?php endif; ?>
                                    </div>
                                    <div class="name-profile">
                                        <?php
                                            $nome = get_the_author_meta( 'first_name', $current_user->ID );
                                            $sobrenome = get_the_author_meta( 'last_name', $current_user->ID );
                                        ?>
                                        <h3><?= strtolower($nome); ?> <?= strtolower($sobrenome); ?></h3>
                                        
                                        <div class="img-profile profile-mob d-inline-block d-sm-inline-block d-md-none">
                                            <?php
                                                $image_id = get_field('imagem', 'user_' . $current_user->ID);
                                                $image_profile = $img_atts = wp_get_attachment_image_src($image_id, 'thumbnail');
                                                                            
                                                if($image_profile[0]):
                                            ?>
                                                    <img src="<?= $image_profile[0]; ?>" alt="Imagem de perfil">
                                            <?php else: ?>
                                                <img src="<?= get_template_directory_uri() . '/img/user-image.jpg'; ?>" alt="Avatar">                                     
                                            <?php endif; ?>
                                        </div>

                                        <input class="text-input" name="avatar_user" type="file" data-max-size="2048000" accept="image/png, image/gif, image/jpeg" id="avatar_user"/>
                                    </div>
                                </div>

                            </div>

                            <div class="col-12">
                                <hr class='w-100'>
                            </div>
                        </div>

                        <div class="row">

                            <?php if($parceira): ?>

                                <div class="col-12">
                                    <label for="first-name"><?php _e('Nome da Unidade Educacional', 'profile'); ?></label>
                                    <div class="input-disable" id="first-name"><?php the_author_meta( 'first_name', $current_user->ID ); ?></div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="user-rf"><?php _e('Código EOL', 'profile'); ?></label>                               
                                    <div class="input-disable" id="user-rf"><?= get_field('rf', 'user_' . $current_user->ID); ?></div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="email"><?php _e('E-mail *', 'profile'); ?></label>
                                    <div class="input-disable" id="email"><?php the_author_meta( 'user_email', $current_user->ID ); ?></div>
                                </div>

                            <?php else: ?>

                                <div class="col-12 col-md-4">
                                    <label for="first-name"><?php _e('Nome', 'profile'); ?></label>
                                    <input class="text-input form-control" name="first-name" type="text" id="first-name" value="<?php the_author_meta( 'first_name', $current_user->ID ); ?>" />
                                </div>

                                <div class="col-12 col-md-4">
                                    <label for="last-name"><?php _e('Sobrenome', 'profile'); ?></label>
                                    <input class="text-input form-control" name="last-name" type="text" id="last-name" value="<?php the_author_meta( 'last_name', $current_user->ID ); ?>" />
                                </div>

                                <div class="col-12 col-md-4">
                                    <label for="email"><?php _e('E-mail *', 'profile'); ?></label>
                                    <?php
                                        $rf = get_field('rf', 'user_' . $current_user->ID);
                                        $email = $current_user->user_email;
                                        $verifyEmail = explode('@', $email);                                        
                                    ?>
                                    <?php if($rf == $verifyEmail[0]): ?>
                                        <input class="text-input form-control" name="email" type="text" id="email" value="" required />
                                    <?php else: ?>
                                        <input class="text-input form-control" name="email" type="text" id="email" value="<?= $email; ?>" />
                                    <?php endif; ?>
                                </div>

                            <?php endif; ?>

                        </div>

                        <div class="row">

                            <div class="form-group senha-atual col-12 col-md-4">
                                <label for="senha-atual"><span>*</span> Senha atual</label>
                                <input type="password" class="form-control" id="senha-atual" placeholder="Digite sua senha">
                            </div>
                            <div class="form-group senha-nova col-12 col-md-4">
                                <label for="senha-atual"><span>*</span> Nova senha</label>
                                <input type="password" class="form-control" id="senha-nova" placeholder="Nova senha">
                            </div>
                            <div class="form-group senha-repita col-12 col-md-4">
                                <label for="senha-atual"><span>*</span> Confirmação da nova senha</label>
                                <input type="password" class="form-control" id="senha-repita" placeholder="Repita a nova senha">
                            </div>

                        </div>
                    
                        <p class="form-submit text-right">
                            <?php echo $referer; ?>
                            <input name="updateuser" type="submit" id="updateuser" class="submit btn btn-primary" value="<?php _e('Salvar mudanças', 'profile'); ?>" />
                            <?php wp_nonce_field( 'update-user' ) ?>
                            <input name="action" type="hidden" id="action" value="update-user" />
                        </p><!-- .form-submit -->
                    </form><!-- #adduser -->
                <?php endif; ?>
            </div>
        </div><!-- .entry-content -->
    </div><!-- .hentry .post -->
    <?php endwhile; ?>
<?php else: ?>
    <p class="no-data">
        <?php _e('Sorry, no page matched your criteria.', 'profile'); ?>
    </p><!-- .no-data -->
<?php endif; ?>

<!-- Modal -->
<div class="modal fade" id="modalPass" tabindex="-1" role="dialog" aria-labelledby="modalPassLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

        <div class="modal-header">
            <h3 class="modal-title" id="modalPassLabel">Editar senha</h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="modal-body">

            <div class="requisitos">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="ciencia-senha">
                    <label class="form-check-label" for="ciencia-senha">
                        <span>*</span> Estou ciente que a nova senha será aplicada para os outros acessos (Portais e Sistemas) da SME, incluindo o SGP.
                    </label>
                </div>
                <div class="form-group senha-atual">
                    <label for="senha-atual"><span>*</span> Senha atual</label>
                    <input type="password" class="form-control" id="senha-atual" placeholder="Digite sua senha">
                </div>
                <div class="form-group senha-nova">
                    <label for="senha-atual"><span>*</span> Nova senha</label>
                    <input type="password" class="form-control" id="senha-nova" placeholder="Nova senha">
                </div>
                <div class="form-group senha-repita">
                    <label for="senha-atual"><span>*</span> Confirmação da nova senha</label>
                    <input type="password" class="form-control" id="senha-repita" placeholder="Repita a nova senha">
                </div>
                
                <div class="requisitos-texto">
                    <strong>Requisitos de segurança da senha:</strong>
                    <br><br>
                    Uma letra maiúscula<br>
                    Uma letra minúscula<br>
                    As senhas devem ser iguais<br>
                    Não pode conter espaços em branco<br>
                    Não pode conter caracteres acentuados<br>
                    Um número ou símbolo (caractere especial)<br>
                    Deve ter no mínimo 8 e no máximo 12 caracteres
                </div>
                
            </div>
            
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-primary" id="alterPass">Confirmar</button>
        </div>

    </div>
  </div>
</div>

<?php get_footer(); // Loads the footer.php template. ?>

<?php if($_GET['atualizar']): ?>
    <script>
        Swal.fire({
            title: '<strong><u>Seus dados estão incompletos!</u></strong>',
            //icon: 'error',
            html: 'Favor atualizar o e-mail em seu perfil! <br>Fique atento para não haver erro de digitação!',
            imageUrl: '<?= get_template_directory_uri() . "/img/perfil-ilustra.jpg"; ?>',
            showCloseButton: true,            
            focusConfirm: false,
            confirmButtonText: '<i class="fa fa-thumbs-up"></i> Atualize agora',            
        })
    </script>
<?php endif; ?>