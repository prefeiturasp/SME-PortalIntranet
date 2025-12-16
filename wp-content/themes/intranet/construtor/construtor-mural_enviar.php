<div class="container">
    <div class="row">
        <div class="col-12">
            <form id="mural-enviar" class="a-form-mural" action="<?= get_the_permalink(); ?>" method="POST" enctype="multipart/form-data">
                <div class="row">

                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="nome">Nome*</label>
                            <input type="text" class="form-control" id="nome" name="nome" placeholder="Digite seu nome e sobrenome">
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="nome_ent">Nome da entidade*</label>
                            <input type="text" class="form-control" id="nome_ent" name="nome_ent" placeholder="Ex. Escola, DRE ou coordenações da SME">
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="title">Título para publicação*</label>
                            <input type="text" class="form-control" id="title" name="title" placeholder="Digite seu nome e sobrenome">
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="customFile">Imagem destaque para a publicação*</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="customFile" lang="pt-BR" name="customFile" data-max-size="1024000" accept="image/png, image/gif, image/jpeg">
                                <label class="custom-file-label" for="customFile">Adicione um arquivo para enviar</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="link_sites">Link (Ex.: sites, youtube)</label>
                            <div class="input-group mb-2">
                                <div class="input-group-prepend">
                                <div class="input-group-text">http://</div>
                                </div>
                                <input type="text" class="form-control" name="link_sites" id="link_sites" placeholder="Insira um link">
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="images">Galeria</label>
                            <div class="input-images"></div>
                        </div>
                    </div>

                    <div class="col-12 col-md-12">
                        <div class="form-group">
                            <label for="descricao_publi">Descrição da publicação*</label>
                            <textarea id="descricao_publi" class="mural-textarea" name="descricao_publi" placeholder="Descreva aqui o seu projeto">
                            </textarea>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="auto_publi" id="auto_publi" value="1">
                            <label class="form-check-label" for="auto_publi">Eu autorizo a publicação parcial ou integral da minha mensagem e o armazenamento dos meus dados conforme a Política de Privacidade e Segurança.</label>
                        </div>

                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="auto_compa" id="auto_compa" value="1">
                            <label class="form-check-label" for="auto_compa">Possuo autorização para compartilhamento de imagens de pessoas nas fotos e vídeos publicados.</label>
                        </div>
                    </div>

                    <div class="col-12 mt-4">
                        <div class="form-group d-flex justify-content-end">
                            <div class="itens-form"></div>
                            <button type="button" class="btn btn-outline-primary mr-3" id="limpar" onclick="calcelMural()">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Enviar publicação</button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>

<?php

if($_POST['title']){
    $new_post = array(
        'post_title'    => $_POST['title'],
        'post_status'   => 'pending',         // Selecione o status
        'post_author'   => get_current_user_id(),
        'post_type' => 'mural-professores',  // Selecione o post type
        'post_content' => $_POST['descricao_publi']
    );
    //salva o novo post e retorna o ID
    $pid = wp_insert_post($new_post);

    if($_POST['nome'] && $_POST['nome'] != '' && $pid){
        $nome = $_POST['nome'];
        update_post_meta($pid, 'nome', $nome);
    }

    if($_POST['nome_ent'] && $_POST['nome_ent'] != '' && $pid){
        $nome_ent = $_POST['nome_ent'];
        update_post_meta($pid, 'nome_entidade', $nome_ent);
    }

    if($_POST['link_sites'] && $_POST['link_sites'] != '' && $pid){
        $link_sites = $_POST['link_sites'];
        update_post_meta($pid, 'link', $link_sites);
    }

    if($_POST['auto_publi'] && $_POST['auto_publi'] != '' && $pid){
        $auto_publi = $_POST['auto_publi'];
        update_post_meta($pid, 'autorizo_a_publicacao', $auto_publi);
    }

    if($_POST['auto_compa'] && $_POST['auto_compa'] != '' && $pid){
        $auto_compa = $_POST['auto_compa'];
        update_post_meta($pid, 'possuo_autorizacao', $auto_compa);
    }
}


if ($_FILES["customFile"] && $pid) {
    if ( ! function_exists( 'wp_handle_upload' ) ) {
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
    }

    $upload_overrides = array( 'test_form' => false );
    $movefile = wp_handle_upload( $_FILES["customFile"], $upload_overrides );
     
       
    // $filename should be the path to a file in the upload directory.
    $filename = $movefile['file'];
    
    // The ID of the post this attachment is for.
    $parent_post_id = $pid;
    
    // Check the type of file. We'll use this as the 'post_mime_type'.
    $filetype = wp_check_filetype( basename( $filename ), null );
    
    // Get the path to the upload directory.
    $wp_upload_dir = wp_upload_dir();
    
    // Prepare an array of post data for the attachment.
    $attachment = array(
        'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ), 
        'post_mime_type' => $filetype['type'],
        'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
        'post_content'   => '',
        'post_status'    => 'inherit'
    );
    
    // Insert the attachment.
    $attach_id = wp_insert_attachment( $attachment, $filename, $parent_post_id );
    
    // Generate the metadata for the attachment, and update the database record.
    $attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
    wp_update_attachment_metadata( $attach_id, $attach_data );

    // colocar como Imagem destacada   
    set_post_thumbnail( $pid, $attach_id );
    
}


$galeria = array();


if ($_FILES['images'] && $pid) {

    $files = $_FILES['images'];
    if ( ! function_exists( 'wp_handle_upload' ) ) {
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
    }

    foreach ($files['name'] as $key => $value) {
        if ($files['name'][$key]) {
            $file = array(
                'name'     => $files['name'][$key],
                'type'     => $files['type'][$key],
                'tmp_name' => $files['tmp_name'][$key],
                'error'    => $files['error'][$key],
                'size'     => $files['size'][$key]
            );

            //wp_handle_upload($file);

            $upload_overrides = array( 'test_form' => false );
            $movefile = wp_handle_upload( $file, $upload_overrides );
            
            // $filename should be the path to a file in the upload directory.
            $filename = $movefile['file'];
            
            // The ID of the post this attachment is for.
            $parent_post_id = $pid;
            
            // Check the type of file. We'll use this as the 'post_mime_type'.
            $filetype = wp_check_filetype( basename( $filename ), null );
            
            // Get the path to the upload directory.
            $wp_upload_dir = wp_upload_dir();
            
            // Prepare an array of post data for the attachment.
            $attachment = array(
                'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ), 
                'post_mime_type' => $filetype['type'],
                'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
                'post_content'   => '',
                'post_status'    => 'inherit'
            );
            
            // Insert the attachment.
            $attach_id = wp_insert_attachment( $attachment, $filename, $parent_post_id );
            
            // Generate the metadata for the attachment, and update the database record.
            $attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
            wp_update_attachment_metadata( $attach_id, $attach_data );

            $galeria[] = $attach_id;
        }
    }

    update_field( 'field_6438521f91b99', $galeria , $pid );

}

if($pid){
    echo '<script>window.location.href = "' . get_home_url() .'/index.php/mural-dos-professores/?publicacao=success";</script>';
}