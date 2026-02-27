<?php
/**
* Plugin Name: CoreSSO Integração API
* Plugin URI: https://www.spassu.com.br/
* Description: Integração do Login do WordPress com o CoreSSO.
* Version: 1.4
* Author: Spassu
* Author URI: https://www.spassu.com.br/
**/

if (!defined('ABSPATH')) exit;

/** === 1) REGISTRO DE OPÇÕES E CAMPOS === */
add_action('admin_init', function () {

    register_setting(
        'email_validator_settings_group',
        'email_validator_settings',
        [
            'type'              => 'array',
            'sanitize_callback' => 'evs_sanitize_settings',
            'default'           => [
                'patterns'       => [],
                'domains'        => [],
                'allowSubdomain' => true,
            ],
        ]
    );

    add_settings_section(
        'evs_main_section',
        'Configurações de Validação de E-mail',
        function () {
            echo '<p>Defina os <strong>padrões</strong> e os <strong>domínios monitorados</strong>.</p>';
        },
        'email_validator_settings_page'
    );

    add_settings_field(
        'evs_patterns',
        'Padrões (um por linha)',
        function () {
            $opts = get_option('email_validator_settings', []);
            $patterns = isset($opts['patterns']) && is_array($opts['patterns']) ? $opts['patterns'] : [];
            $value = implode("\n", $patterns);

            echo '<textarea name="email_validator_settings[patterns]" rows="8" class="large-text code" placeholder="emef&#10;emei&#10;indir">'
                 . esc_textarea($value) .
                 '</textarea>';
            echo '<p class="description">Um termo por linha.</p>';
        },
        'email_validator_settings_page',
        'evs_main_section'
    );

    add_settings_field(
        'evs_domains',
        'Domínios monitorados (um por linha)',
        function () {
            $opts = get_option('email_validator_settings', []);
            $domains = isset($opts['domains']) && is_array($opts['domains']) ? $opts['domains'] : [];
            $value = implode("\n", $domains);

            echo '<textarea name="email_validator_settings[domains]" rows="8" class="large-text code" placeholder="sme.prefeitura.sp.gov.br&#10;edu.sme.prefeitura.sp.gov.br">'
                 . esc_textarea($value) .
                 '</textarea>';
            echo '<p class="description">Um domínio por linha.</p>';
        },
        'email_validator_settings_page',
        'evs_main_section'
    );

    add_settings_field(
        'evs_allow_subdomain',
        'Aceitar subdomínios',
        function () {
            $opts = get_option('email_validator_settings', []);
            $allow = !empty($opts['allowSubdomain']);

            echo '<label><input type="checkbox" name="email_validator_settings[allowSubdomain]" value="1" '
                . checked($allow, true, false)
                . '> Aceitar subdomínios</label>';
        },
        'email_validator_settings_page',
        'evs_main_section'
    );
});


/** Sanitização */
function evs_sanitize_settings($input) {

    $out = [
        'patterns'       => [],
        'domains'        => [],
        'allowSubdomain' => true,
    ];

    if (!empty($input['patterns'])) {
        $raw   = is_array($input['patterns']) ? implode("\n", $input['patterns']) : (string)$input['patterns'];
        $lines = preg_split('/\R+/', $raw);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line !== '') {
                $out['patterns'][] = $line;
            }
        }
    }

    if (!empty($input['domains'])) {
        $raw   = is_array($input['domains']) ? implode("\n", $input['domains']) : (string)$input['domains'];
        $lines = preg_split('/\R+/', $raw);
        foreach ($lines as $line) {
            $line = strtolower(trim($line));
            if ($line !== '') {
                $out['domains'][] = $line;
            }
        }
    }

    $out['allowSubdomain'] = !empty($input['allowSubdomain']);

    return $out;
}


/** === 2) PÁGINA DE MENU === */
add_action('admin_menu', function () {
    add_options_page(
        'Validação de E-mail',
        'Validação de E-mail',
        'manage_options',
        'email-validator-settings',
        function () {

            if (!current_user_can('manage_options')) {
                return;
            }

            echo '<div class="wrap"><h1>Validação de E-mail</h1>';

            settings_errors();

            echo '<form method="post" action="options.php">';

            settings_fields('email_validator_settings_group');

            do_settings_sections('email_validator_settings_page');

            submit_button('Salvar alterações');

            echo '</form></div>';
        }
    );
});


/** === 3) Função de validação — usa opções === */
function email_validate_patterns_in_monitored_domains_php7($email) {
    $email = trim((string)$email);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return false;

    $opts = get_option('email_validator_settings', []);
    $patterns         = isset($opts['patterns']) && is_array($opts['patterns']) ? $opts['patterns'] : [];
    $monitoredDomains = isset($opts['domains']) && is_array($opts['domains']) ? $opts['domains'] : [];
    $allowSubdomain   = isset($opts['allowSubdomain']) ? (bool)$opts['allowSubdomain'] : true;

    $parts = explode('@', $email, 2);
    if (count($parts) < 2) return false;
    $domain = strtolower($parts[1]);

    $isMonitored = false;
    foreach ($monitoredDomains as $d) {
        $d = strtolower(trim((string)$d));
        if ($d === '') continue;
        if ($allowSubdomain) {
            if ($domain === $d || evs_ends_with_php7($domain, '.' . $d)) { $isMonitored = true; break; }
        } else {
            if ($domain === $d) { $isMonitored = true; break; }
        }
    }

    if (!$isMonitored) return false;
    if (empty($patterns)) return false;

    foreach ($patterns as $p) {
        $p = (string)$p;
        if ($p === '') continue;
        if (stripos($email, $p) !== false) return true;
    }
    return false;
}

function evs_ends_with_php7($haystack, $needle) {
    $haystack = (string)$haystack;
    $needle   = (string)$needle;
    if ($needle === '') return true;
    $lenHay = strlen($haystack);
    $lenNee = strlen($needle);
    if ($lenNee > $lenHay) return false;
    return substr($haystack, $lenHay - $lenNee, $lenNee) === $needle;
}


// Substituir a autenticacao do WordPress
add_filter( 'authenticate', 'demo_auth', 10, 3 );

function demo_auth( $user, $username, $password ) {
    // Verifica se o usuario e senha foram preenchidos
    if($username == '' || $password == '') return;

    global $patterns, $monitoredDomains;

    // URL da API
    $api_url = getenv('SMEINTEGRACAO_API_URL') . '/api/v1/autenticacao';
    $api_token = getenv('SMEINTEGRACAO_API_TOKEN');

    // Conversao do body para JSON
    $body = wp_json_encode( array(
        "login" => $username,
        "senha" => $password,
    ) );

    $response = wp_remote_post( $api_url ,
            array(
                'headers' => array( 
                    'x-api-eol-key' => $api_token, // Chave da API
                    'Content-Type'=> 'application/json-patch+json'
                ),
                'body' => $body, // Body da requisicao
                'timeout' => 30,
            ));

    if (is_wp_error($response)) {
        // Trata o erro, por exemplo, registrando uma mensagem no log.
        error_log('Erro na requisição: ' . $response->get_error_message());
        
        $login_page = home_url();	
		wp_redirect($login_page . '?request=noresponse');
		exit;
	
    }

    $user = json_decode($response['body']);



    if( $response['response']['code']  != 200 ) {
        // Caso nao encontre o usuario retorna o erro na pagina
        $user = new WP_Error( 'denied', __("ERRO: Usuário/senha incorretos") );

    } else if( $response['response']['code'] == 200 ) {
        //echo $user->codigoRf;
        
        // Verifica se tem o codigo RF e busca os dados do usuario
        if($user->codigoRf){
            
            $rf = $user->codigoRf;
            
            $countRf = strlen($rf);

            if($countRf == 20){
                $usuario = $rf;
                $api_url =  getenv('SMEINTEGRACAO_API_URL') . '/api/escolas/unidades-parceiras';
                $api_token = getenv('SMEINTEGRACAO_API_TOKEN');
                $response = wp_remote_post( $api_url, array(
                    'method'      => 'POST',                    
                    'headers' => array( 
                        'x-api-eol-key' => $api_token,
                        'Content-Type' => 'application/json-patch+json'
                    ),
                    'body' => '['.$rf.']',
                    'timeout' => 30,
                    )
                );
                
                if ( is_wp_error( $response ) ) {

                    // Trata o erro, por exemplo, registrando uma mensagem no log.
                    error_log('Erro na requisição: ' . $response->get_error_message());
                            
                    $login_page = home_url();	
                    wp_redirect($login_page . '?request=noresponse');
                    exit;
                    
                } else {
                    $user = json_decode($response['body']);                     
                    if(!$user){
                        echo $rf;
                        $api_url =  getenv('SMEINTEGRACAO_API_URL') . '/api/AutenticacaoSgp/' . $rf . '/dados';
                        $api_token = getenv('SMEINTEGRACAO_API_TOKEN');
                        $response = wp_remote_get( $api_url ,
                            array( 
                                'headers' => array( 
                                    'x-api-eol-key' => $api_token,							
                                ),
                                'timeout' => 30,
                            )
                        );

                        if (is_wp_error($response)) {
                            // Trata o erro, por exemplo, registrando uma mensagem no log.
                            error_log('Erro na requisição: ' . $response->get_error_message());
                            
                            $login_page = home_url();	
                            wp_redirect($login_page . '?request=noresponse');
                            exit;
                        
                        }
    
                        $user = json_decode($response['body']);
                    }
                }
            } else {
                $api_url = getenv('SMEINTEGRACAO_API_URL') . '/api/AutenticacaoSgp/' . $user->codigoRf . '/dados';
                $api_token = getenv('SMEINTEGRACAO_API_TOKEN');
                $response = wp_remote_get( $api_url ,
                    array( 
                        'headers' => array( 
                            'x-api-eol-key' => $api_token,							
                        ),
                        'timeout' => 30,
                    )
                );

                if (is_wp_error($response)) {
                    // Trata o erro, por exemplo, registrando uma mensagem no log.
                    error_log('Erro na requisição: ' . $response->get_error_message());
                    
                    $login_page = home_url();	
                    wp_redirect($login_page . '?request=noresponse');
                    exit;
                
                }

                $user = json_decode($response['body']); 
            }
                       
        }

        /*
        var_dump(email_validate_patterns_in_monitored_domains_php7(
            'ceiacuriati@sme.prefeitura.sp.gov.br',
            $patterns,
            $monitoredDomains
        ));
        
        echo "<pre>";
        print_r($user);
        echo "</pre>";
        exit;
        */

        if($user->email){
            $email = $user->email;
        } elseif(is_array($user)) {
            $email = $user[0]->email;
        } else {
            $email = $rf . "@sme.prefeitura.sp.gov.br";
        }
        
        // Buscar todos os usuários com 'rf' do usuario que passou no login
        $args = array(
            'meta_key'     => 'rf',
            'meta_value'   => $username,
            'meta_compare' => '='
        );

        $user_query = new WP_User_Query($args);
        $all_users = $user_query->get_results();

        $api_id_map = [];

        // Agrupar usuários por valor de api_user_id
        foreach ($all_users as $user) {
            $api_id = get_user_meta($user->ID, 'rf', true);
            if (!$api_id) continue;

            if (!isset($api_id_map[$api_id])) {
                $api_id_map[$api_id] = [];
            }

            $api_id_map[$api_id][] = $user->ID;
        }

        // Filtrar apenas os grupos duplicados
        foreach ($api_id_map as $api_id => $user_ids) {
            if (empty($user_ids)) {
                continue;
            }

            $users = [];

            foreach ($user_ids as $user_id) {
                $user = get_userdata($user_id);
                if (!$user) continue;

                $last_login = get_user_meta($user_id, 'wp_last_login', true);
                $registered = strtotime($user->user_registered);
                $score = $last_login ? intval($last_login) : $registered;

                $users[] = [
                    'ID' => $user_id,
                    'email' => $user->user_email,
                    'last_login' => $last_login,
                    'registered' => $registered,
                    'score' => $score,
                ];
            }

            // Ordenar por score (mais recente primeiro)
            usort($users, fn($a, $b) => $b['score'] <=> $a['score']);

            $user_to_keep = array_shift($users); // mesmo se houver apenas 1 usuário

            // Sempre atualiza o email/nickname se estiver diferente do CoreSSO
            $novo_email = sanitize_email($email);
            if (is_email($novo_email) && $user_to_keep['email'] !== $novo_email) {
                wp_update_user([
                    'ID'         => $user_to_keep['ID'],
                    'user_email' => $novo_email,
                    'nickname'   => $novo_email,
                ]);
            }

            // Se houver duplicados, salva os que devem ser excluídos
            if (count($users) > 0) {
                update_option('duplicados_para_excluir_' . $user_to_keep['ID'], $users);
            }
        }        
        
        //exit;
        
        // Verifica se o usuario ja esta cadastrado no WordPress
        $userobj = new WP_User();
        $user_wp = $userobj->get_data_by( 'email', $email ); // Does not return a WP_User object :(
        if($user_wp->ID != 0){
            $user_wp = new WP_User($user_wp->ID); // Attempt to load up the user with that ID
        }
        
        // Verifica se o usuario esta com um email temporario cadastrado
        // email temporario corresponde a 'rf + @sme.prefeitura.sp.gov.br'
        if( $user_wp->ID == 0) {
            $email = $rf . "@sme.prefeitura.sp.gov.br";
            $userobj = new WP_User();
            $user_wp = $userobj->get_data_by( 'email', $email ); // Does not return a WP_User object :(
            $user_wp = new WP_User($user_wp->ID); // Attempt to load up the user with that ID

            $args = array(
                'ID'         => $user_wp->ID,
                'user_email' => esc_attr( $user->email )
            );
            wp_update_user( $args );
        }


        if($rf && $countRf == 7){

            $api_url = getenv('SMEINTEGRACAO_API_URL') . '/api/Intranet/CarregarPerfisPorLogin/' . $user->codigoRf;
            $api_token = getenv('SMEINTEGRACAO_API_TOKEN');
            $response = wp_remote_get( $api_url ,
                array( 
                    'headers' => array( 
                        'x-api-eol-key' => $api_token,							
                    ),
                    'timeout' => 30,
                )
            );

            if (is_wp_error($response)) {
                // Trata o erro, por exemplo, registrando uma mensagem no log.
                error_log('Erro na requisição: ' . $response->get_error_message());
                
                $login_page = home_url();	
                wp_redirect($login_page . '?request=noresponse');
                exit;
            
            }

            $userInfo = json_decode($response['body']);

            $cargo = $userInfo->cargos[0]->nome;
            $cargoSobre = $userInfo->cargosSobrePosto[0]->nome;
            $areaAtuacao = implode(", ", $userInfo->areasAtuacao);
            $local = $userInfo->unidadeLotacao->nomeUnidade;
            $localSobre = $userInfo->unidadeExercicio->nomeUnidade;
            
            if($user_wp->ID != 0){
                update_user_meta($user_wp->ID, "cargo_principal", $cargo);
                update_user_meta($user_wp->ID, "cargo_sobre", $cargoSobre);
                update_user_meta($user_wp->ID, "area_atuacao", $areaAtuacao);
                update_user_meta($user_wp->ID, "local", $local);
                update_user_meta($user_wp->ID, "local_sobre", $localSobre);
            }

        }

        // Se nao estiver cadastrado faz a criacao do usuario
        if( $user_wp->ID == 0 ) {
             
            // Caso nao queira adicionar o usuario no WordPress
            // descomente a linha abaixo
            //$user_wp = new WP_Error( 'denied', __("ERROR: Not a valid user for this system") );

            // Recebe o nome completo do usuario            
            $name = $user->nome;            

            if($user->codigo){
                $codigo = $user->codigo;
            } elseif(is_array($user)) {
                $codigo = $user[0]->codigo;
            }

            if($user->email){
                $email = $user->email;
            } elseif(is_array($user)) {
                $email = $user[0]->email;
            } else {
                $email = $rf . "@sme.prefeitura.sp.gov.br";
            }

            $verifica = email_validate_patterns_in_monitored_domains_php7(
                $email,
                $patterns,
                $monitoredDomains
            );

            if ($verifica) {
                $email = $rf . "@sme.prefeitura.sp.gov.br";
            }

            if($user->nome){
                $nome = $user->nome;
            } elseif(is_array($user)) {
                $nome = $user[0]->nome;
            }

            if($codigo){

                $userdata = array( 'user_email' => $email,
                                    'user_login' => $rf,
                                    'first_name' => $nome,                            
                                );
                $new_user_id = wp_insert_user( $userdata ); // Um novo usuario sera criado
                update_user_meta($new_user_id, "rf", $codigo);
                update_user_meta($new_user_id, "parceira", 1);

            } else {
                // Recebe o CPF
                $cpf = $user->cpf;

                // Divide o nome em Nome e Sobrenome
                $parts = explode(" ", $name);
                if(count($parts) > 1) {
                    $firstname = array_shift($parts);
                    $lastname = implode(" ", $parts);
                } else {
                    $firstname = $name;
                    $lastname = " ";
                }

                $userdata = array( 'user_email' =>$email,
                                    'user_login' =>$username,
                                    'first_name' => $firstname,
                                    'last_name' => $lastname,                                
                                );
                $new_user_id = wp_insert_user( $userdata ); // Um novo usuario sera criado
                update_user_meta($new_user_id, "rf", $username);
                if(strlen($username) != 6){
                    update_user_meta($new_user_id, "cpf", $cpf);
                    if($cargo)
                        update_user_meta($new_user_id, "cargo_principal", $cargo);
    
                    if($cargoSobre)
                        update_user_meta($new_user_id, "cargo_sobre", $cargoSobre);
    
                    if($areaAtuacao)
                        update_user_meta($new_user_id, "area_atuacao", $areaAtuacao);
    
                    if($local)
                        update_user_meta($new_user_id, "local", $local);
    
                    if($localSobre)
                        update_user_meta($new_user_id, "local_sobre", $localSobre);
                }

                if(strlen($username) == 11 || strlen($username) == 6){
                    update_user_meta($new_user_id, "parceira", 1);
                }
            }

            
            
            // Carregar as novas informações do usuário
            $user_wp = new WP_User ($new_user_id);
            
        } 

    }

    if(!$user_wp){

        // Verifique se o campo personalizado 'cpf_user' está definido como o nome de usuário
        $args = array(
            'meta_key'     => 'cpf_user',
            'meta_value'   => $username,
            'meta_compare' => '='
        );

        $user_query = new WP_User_Query($args);
        $users = $user_query->get_results();

        // Se houver um usuário com o campo personalizado correspondente, tente autenticá-lo
        if (!empty($users)) {
            $user = $users[0];
            // Tenta autenticar o usuário
            $user_wp = wp_authenticate_username_password(null, $user->user_login, $password);
        } else {
            $user_wp = wp_authenticate_username_password(null, $username, $password);
        }
        
    }

    // Comente esta linha se você deseja recorrer a autenticacao do WordPress
    // Util para momentos em que o servico externo esta offline
    //remove_action( 'authenticate', 'wp_authenticate_username_password', 20, 3 );
    //remove_action( 'authenticate', 'wp_authenticate_email_password', 20, 3 );

    return $user_wp;
}

// Ação para excluir usuários duplicados após o login
add_action('wp_login', function($login, $user) {
    $duplicados = get_option('duplicados_para_excluir_' . $user->ID);
    if ($duplicados && is_array($duplicados)) {

        if (!function_exists('wp_delete_user')) {
            require_once ABSPATH . 'wp-admin/includes/user.php';
        }

        foreach ($duplicados as $dup_id) {
            if ($dup_id != $user->ID) {
                wp_delete_user($dup_id['ID'], $user->ID); // transfere conteúdo (opcional)
            }
        }

        delete_option('duplicados_para_excluir_' . $user->ID);
    }
}, 10, 2);


#########################################################################################
// Criacao do shortcode de login
//function intranet_add_login_shortcode() {
	//add_shortcode( 'intranet-login-form', 'intranet_login_form_shortcode' );
//}

// funcao callbacl do shortcode
function intranet_login_form_shortcode() {
	
	// Se ja estiver conectado
    if (is_user_logged_in() && !is_admin()):
        echo "<h2>Você já está conectado!</h2>";
    else:
    // Inclui o formulario de login
    ?>
    	<div class='wp_login_form'>
			<?php
                // Mensagem de erro exibida na tela
				$page_showing = basename($_SERVER['REQUEST_URI']);

				if (strpos($page_showing, 'failed') !== false) {
					echo '<p class="error-msg"><strong>ERRO:</strong> Usuário e/ou senha inválidos.</p>';
				} elseif (strpos($page_showing, 'blank') !== false ) {
					echo '<p class="error-msg"><strong>ERRO:</strong> Usuário e/ou senha estão vazios.</p>';
				}
			
                $args = array(
                'redirect' => home_url(), // Apos login redireciona para a home
                'id_username' => 'user', // ID no input de usuario
                'id_password' => 'pass', // ID no input da senha
                );
				
                wp_login_form( $args ); // Inclui o formulario de login
                
            ?>

		</div>
<?php
    endif;
}

// Carrega a funcao do shortcode
//add_action( 'init', 'intranet_add_login_shortcode' );


#####################################################################################

// Direcionar o usuario da pagina de login do WordPress para uma pagina de login customizada
function goto_login_page() {
	global $page_id;
	$login_page = home_url();
	$page = basename($_SERVER['REQUEST_URI']);

	if( $page == "wp-login.php" && $_SERVER['REQUEST_METHOD'] == 'GET') {
		wp_redirect($login_page);
		exit;
	}
}
// Funcao desabilitada no momento, para habilitar descomente a linha abaixo
//add_action('init','goto_login_page');

// Se nao autenticar o usuario redireciona para o login novamente
// icluindo o parametro GET na URL
function login_failed() {
	global $page_id;
	$login_page = home_url();
	wp_redirect( $login_page . '?login=failed' );
	exit;
}
// Verifica se nao esta na pagina de login do WordPress
if( $pagenow == 'wp-login.php' && isset($_POST['login_page']) ){
	add_action( 'wp_login_failed', 'login_failed' );
}

// Se usuario/senha estiver vazio redireciona para o login novamente
// icluindo o parametro GET na URL
function blank_username_password( $user, $username, $password ) {
	global $page_id;
	$login_page = home_url();
	if( $username == "" || $password == "" ) {
		wp_redirect( $login_page . "?login=blank" );
		exit;
	}
}
// Verifica se nao esta na pagina de login do WordPress
if( $pagenow == 'wp-login.php' && isset($_POST['login_page']) ){
	add_filter( 'authenticate', 'blank_username_password', 1, 3);
}

// Se for acionado a funcao de Logout (sair) redireciona o usuario para a pagina de login
function logout_page() {
	global $page_id;
	$login_page = home_url();
	wp_redirect( $login_page . "?login=false" );
	exit;
}
add_action('wp_logout', 'logout_page');

// Inclui um input oculto no formulario de login personalizado
// Para que seja validado o usuario via API e nao pelo WordPress
add_filter('login_form_middle','my_added_login_field');
function my_added_login_field(){
     //Output your HTML
     $additional_field = '<div class="login-custom-field-wrapper"">
        <input type="hidden" value="1" name="login_page"></label>
     </div>';

     return $additional_field;
}

// Verifica se esta na pagina de Login do WordPress
// para validar o usuario pelo WordPress e NAO pela API
add_action( 'login_init', 'wpse8170_login_init' );
function wpse8170_login_init() {
	global $pagenow;
	if( $pagenow == 'wp-login.php' && !isset($_POST['login_page']) ){
		remove_filter( 'authenticate', 'demo_auth' );
		remove_filter( 'authenticate', 'blank_username_password');
	}    
}