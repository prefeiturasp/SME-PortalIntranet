<?php

namespace Classes\Usuarios;


class CamposAdicionais
{

	public function __construct()
	{
		// Add e Salvando o campo personalizado
		//add_action('show_user_profile', array($this, 'fb_add_custom_user_profile_fields'));
		//add_action('edit_user_profile', array($this, 'fb_add_custom_user_profile_fields'));
		//add_action('personal_options_update', array($this, 'fb_save_custom_user_profile_fields'));
		//add_action('edit_user_profile_update', array($this, 'fb_save_custom_user_profile_fields'));

		add_filter('manage_users_columns', array($this, 'exibe_cols'));
		add_filter('manage_users_custom_column', array($this, 'cols_content'), 10, 3);
		add_filter('manage_users_sortable_columns', array($this, 'cols_sort'));
		//add_filter('request', array($this, 'orderby'));

		//add_action('restrict_manage_users', array($this, 'my_restrict_manage_posts'));

		//$this->exibeTodosUsuarios();

		add_action( 'restrict_manage_users', array($this,'add_setor_filter' ));

		add_filter( 'pre_get_users', array($this,'filter_users_by_setor' ));


	}

	public function exibeTodosUsuarios(){
		global $wpdb;
		$wp_user_search = $wpdb->get_results("SELECT ID, display_name FROM $wpdb->users ORDER BY ID");
		$todos_usuarios_id = [];

		foreach ( $wp_user_search as $userid ) {
			$user_id       = (int) $userid->ID;
			$user_login    = stripslashes($userid->user_login);
			$display_name  = stripslashes($userid->display_name);
			$todos_usuarios_id[] = $user_id;
		}
		return $todos_usuarios_id;

	}

	public function getUsersSetorUnique($users){

		$setor_unico = [];
		foreach ($users as $user_id){
			$setor_unico[] = get_user_meta($user_id, 'setor', true);
		}

		return array_unique($setor_unico, SORT_REGULAR);


	}

	function add_setor_filter() {

		if ( isset( $_GET[ 'setor' ]) && $_GET[ 'setor' ][0] !== '0') {
			$section = $_GET[ 'setor' ];
			$section = !empty( $section[ 0 ] ) ? $section[ 0 ] : $section[ 1 ];
		} else {
			$section = -1;
		}

		$users = $this->exibeTodosUsuarios();

		$setores_unicos = $this->getUsersSetorUnique($users);

		echo ' <select name="setor[]" style="float:none;">';
		echo'<option value="0" selected="selected">Todos os setores</option>';

		foreach ($setores_unicos as $setor){

			$selected = $setor == $section ? ' selected="selected"' : '';

			if ($setor) {
				echo '<option value="' . $setor . '"' . $selected . '>' . $setor . '</option>';
			}
		}
		echo '</select>';
		echo '<input type="submit" class="button" value="Filtrar">';
	}


	function filter_users_by_setor( $query ) {
		global $pagenow;

		if ( is_admin() &&
			'users.php' == $pagenow &&
			isset( $_GET[ 'setor' ] ) &&
			is_array( $_GET[ 'setor' ] )
		) {
			$section = $_GET[ 'setor' ];

			if ($section[ 0 ] !== "0") {

				$section = !empty($section[0]) ? $section[0] : $section[1];
				$meta_query = array(
					array(
						'key' => 'setor',
						'value' => $section
					)
				);
				$query->set('meta_key', 'setor');
				$query->set('meta_query', $meta_query);
			}
		}
	}


	// Funções necessária para exibir o filtro de categorias nos produtos no Dashboard
	public function my_restrict_manage_posts(){

		global $typenow;
		$taxonomy = $this->taxonomy; // taxonomia personalizada = categorias
		//if ($typenow == $this->cptSlug) { // custom post type = link
		$filters = array($taxonomy);

		foreach ($filters as $tax_slug) {
			//$tax_obj = get_taxonomy($tax_slug);
			//$tax_name = $tax_obj->labels->name;
			$terms = get_terms($tax_slug);
			echo "<select name='$tax_slug' id='$tax_slug' class='postform'>";
			echo "<option value=''>Ver todas as categorias</option>";
			foreach ($terms as $term) {
				echo '<option value=' . $term->slug, $_GET[$tax_slug] == $term->slug ? ' selected="selected"' : '', '>' . $term->name . ' (' . $term->count . ')</option>';
			}
			echo "</select>";
		}
		//}
	}

	function orderby($vars)
	{
		if (is_admin()) {
			if (isset($vars['orderby']) && $vars['orderby'] == 'setor') {
				$vars = array_merge( $vars, array(
					'meta_key' => 'setor',
					'orderby' => 'meta_value'
				) );
				//$vars['orderby'] = 'menu_order';
			}


		}
		return $vars;
	}

	public function cols_sort($cols)
	{
		$cols['setor'] = 'setor';
		return $cols;
	}


	public function fb_add_custom_user_profile_fields($user)
	{
		?>
        <h3><?php _e('Informações adicionais dos usuários', 'your_textdomain'); ?></h3>

        <table class="form-table">
            <tr>
                <th>
                    <label for="setor"><?php _e('Setor', 'your_textdomain'); ?>
                    </label></th>
                <td>
                    <input type="text" name="setor" id="setor"
                           value="<?php echo esc_attr(get_the_author_meta('setor', $user->ID)); ?>"
                           class="regular-text"/><br/>
                    <span class="description"><?php _e('Por favor entre com o setor do usuário.', 'your_textdomain'); ?></span>
                </td>
            </tr>
        </table>
	<?php }

	public function fb_save_custom_user_profile_fields($user_id)
	{

		if (!current_user_can('edit_user', $user_id))
			return FALSE;

		update_user_meta($user_id, 'setor', $_POST['setor']);
	}

	
	//Exibindo as colunas no Dashboard
	public function exibe_cols($columns)
	{
		//$columns['posts'] = 'Notícias';
		//$columns['pages'] = 'Páginas';
		//$columns['setor'] = 'Setor';
		$columns['grupos'] = 'Grupos';
		return $columns;
	}

	//Exibindo as informações correspondentes de cada coluna
	public function cols_content($val, $column_name, $user_id)
	{
		$user_setor = get_user_meta($user_id, 'setor', true);

		switch ($column_name) {
			case 'setor' :
				if ($user_setor){
					return "<p><strong>$user_setor</strong></p>";
				}else{
					return "<p>Nenhum Setor Cadastrado</p>";
				}
			case 'pages' :

					global $wpdb;
	
					$where = get_posts_by_author_sql('page', true, $user_id);
					$count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts $where" );
					if($count > 0){
						$number = "<span class='pages-count'><a href='" . admin_url('edit.php?post_type=page&author=' . $user_id) . "'>" . $count . "</a></span>";
					} else {
						$number = "<span class='pages-count'>" . $count . "</span>";
					}
					return $number;
	
			case 'grupos' :
					// pega o grupo que o usuario pertence
					$usergrupos = get_field('grupo', 'user_' . $user_id);
	
					$returngrupos = '';
	
					if($usergrupos && $usergrupos != ''){
						$b = 0;
						foreach($usergrupos as $usergrupo){
							if($b == 0){
								$returngrupos .= "<a href='" . admin_url('users.php?grupo_id=' . $usergrupo) . "'>" . get_the_title($usergrupo) . "</a>";
							} else {
								$returngrupos .= ", <a href='" . admin_url('users.php?grupo_id=' . $usergrupo) . "'>" . get_the_title($usergrupo) . "</a>";
							}
							$b++;				
						}
	
						//print_r($variable);
						return $returngrupos;
					} else {
						return "-";
					}
			case 'login_count' :				
			
				if ( get_user_meta( $user_id, 'wp_login_count', true ) !== '' ) {
					$login_count = get_user_meta( $user_id, 'wp_login_count', true );
	
					return "<strong>$login_count</strong>";
				}
				else {
					return __( 'Nenhum login registrado.' );
				}
				
			case 'last_login' :
				if ( get_user_meta( $user_id, 'wp_last_login', true ) !== '' ) {
					//$the_login_date = human_time_diff($last_login);
					$last_login = get_user_meta( $user_id, 'wp_last_login', true );
					$the_login_date = date('M j, Y h:i a', $last_login);
	
	
					return "<strong>$the_login_date</strong>";
				}
				else {
					return __( 'Nenhum registro' );
				}
			default:
		}

	}

}

new CamposAdicionais();