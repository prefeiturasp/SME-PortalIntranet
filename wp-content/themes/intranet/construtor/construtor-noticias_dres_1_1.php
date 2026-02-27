<div class="container">
    <div class="row">
        <div class="col-sm-12 p-0">
				<?php
					global $wpdb;

                    $blocoColunas = get_sub_field('colunas');
                    $blocoNoticias = get_sub_field('quantidade');
					
					// Pegar todos os sites da rede exceto o site principal (id 1)
					//$blogs = $wpdb->get_results("SELECT blog_id FROM {$wpdb->blogs} WHERE site_id = '{$wpdb->siteid}' AND spam = '0' AND deleted = '0' AND archived = '0' AND blog_id != 1 ");

					// Selecao manual de blogs
					$blogs = array();
					$blogs[] = array('blog_id' => 8); // DRE
					$blogs[] = array('blog_id' => 9); // DRE
                    $blogs[] = array('blog_id' => 10); // DRE
					$blogs[] = array('blog_id' => 11); // DRE
                    $blogs[] = array('blog_id' => 12); // DRE
					$blogs[] = array('blog_id' => 13); // DRE
                    $blogs[] = array('blog_id' => 14); // DRE
					$blogs[] = array('blog_id' => 15); // DRE
                    $blogs[] = array('blog_id' => 16); // DRE
					$blogs[] = array('blog_id' => 17); // DRE
                    $blogs[] = array('blog_id' => 18); // DRE
					$blogs[] = array('blog_id' => 19); // DRE
                    $blogs[] = array('blog_id' => 20); // DRE

					//echo "<pre>";
					//print_r($blogs);
					//echo "</pre>";

					// New empty arrays
					$blog_ids;
					$blogusers;
					$blogusers_ids;

					// Only save blog id numbers into the new array, also save all blogusers in network
					foreach ( $blogs as $bloggers ) {
					$blog_ids[] = $bloggers["blog_id"];
					$blogusers[] = get_users( 'blog_id='.$bloggers["blog_id"].'');
					}

					// Save blog user ids in network
					foreach ( $blogusers as $user ) {
					$blogusers_ids[] = $user->user_id;
					}

					// Save latest post from every blog, ordered by date. Add to a array.
					$posts = array();
					foreach ( $blog_ids as $blog_id ) {
                        switch_to_blog( $blog_id );
                        $query = new \WP_Query(
                            array(
                                'post_type'      => 'post',
                                'posts_per_page' => 3,
                                'orderby'        => 'date',
                                'order'          => 'DESC',
                                'ignore_sticky_posts' => 1
                            )
                        );
                        while ( $query->have_posts() ) {
                            $query->next_post();
                            $query->post->blog_id = $blog_id;
                            $posts[] = $query->post;
                        }
                        restore_current_blog();
					}

					// Sort array of posts by date
					usort($posts, 'sort_objects_by_date');
                    					

                    echo "<div class='container lista-noticias'>";
                    echo "<div class='row'>";

                    echo '<div class="col-sm-12 lista-noticias-titulo aaa"><p>Notícias das Diretorias Regionais de Ensino (DREs)</p></div>';

                        # Our main loop now sorted and unique authors.
                        //global $post;
                        $count = 1;
                        foreach( $posts as $post ) :

                            $post_title = get_the_title();

                            if($count <= $blocoNoticias):

                                # Get meta data depending on context i.e use switch_to_blog()                               
                                switch_to_blog($post->blog_id);
                                setup_postdata($post);

                                if(get_post_thumbnail_id($id)):
                                    $attachment_image     = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'default-image' );
                                    $attachment_image_url = $attachment_image[0];
                                    $thumbnail_id = get_post_thumbnail_id( $post->ID );
                                    $alt = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true); 
                                    $postlink             = get_permalink();
                                    $bloglink             = get_bloginfo('url');
                                    $blogname             = get_bloginfo('name');
                                    $blogname = str_replace("Diretoria Regional de Educação", "DRE", $blogname);
                                    
                                    echo "<div class='col-sm-12 col-md-6 col-lg-" . $blocoColunas . " lista-noticia'>";

                                        echo "<a href='" . $postlink . "'>";
                                            echo "<img src='" . $attachment_image_url .  "' alt='" . $alt . "'>";
                                        echo "</a>";

                                        echo "<a href='" . $bloglink . "' class='blog-link'>";
                                            echo "<p>" . $blogname . "</p>";
                                        echo "</a>";

                                        echo "<a href='" . $postlink . "'>";
                                            echo "<p>" . $post_title . "</p>";
                                        echo "</a>";
                                        
                                    echo "</div>";
                                    
                                endif;
                                restore_current_blog();                                
                            
                                $count++;
                                # Do something wihth the data now here! ...
                            endif;

                        endforeach;
                        wp_reset_postdata();
                    
                    echo "</div>"; // row
                    echo "</div>"; // container
                    

				?>				
		</div>
    </div>
</div>