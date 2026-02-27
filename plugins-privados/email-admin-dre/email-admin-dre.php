<?php

/*
Plugin Name: Emails Admin DREs
Plugin URI: http://educacao.sme.prefeitura.sp.gov.br
Description: Envio de emails para admins para notícias e páginas pendentes.
Version: 1.0
Author: AMcom
Author URI: https://www.amcom.com.br
*/

function post_unpublished( $new_status, $old_status, $post ) {
    if ( $new_status == 'pending' ) {
        
        if ( ! $post_type = get_post_type_object( $post->post_type ) )
        return;

        //if($post_type->labels->singular_name != 'Noticia' || $post_type->labels->singular_name != 'Página')
        //return;
        
        $emailto = array();
        
        $adminUsers = get_users('role=Administrator'); // Uuarios do tipo admin  
       
        foreach ($adminUsers as $user) {
            $emailto[] = $user->user_email;
        }

        // usuarios que nao receberao email
        $removeUser = array('teste.teste@teste.com', 'ollyver.ottoboni@amcom.com.br', 'ollyverottoboni@gmail.com');

        $emailto = array_diff($emailto, $removeUser);
       
        // Assunto do email"
        $subject = 'Uma ' . $post_type->labels->singular_name . ' foi editada no portal.';

        //Link para editar
        $link = get_edit_post_link( $post->ID );
        $link = str_replace('&amp;' , '&', $link);

        // Corpo do email
        $message = 'A ' . $post_type->labels->singular_name . ' "' . get_the_title($post->ID) . '"' . " foi editado no portal.\nPara visualizar as alterações acesse: " . get_permalink( $post->ID ) . "\nPara publicar acesse: " . $link;

        //foreach($emailto as $email){
            //$message .= "\n" . $email;
        //}

        $emailto2 = array('felipe.almeida@amcom.com.br');
        // evia o email
        wp_mail( $emailto, $subject, $message );
    }
}
add_action( 'transition_post_status', 'post_unpublished', 100, 3 );