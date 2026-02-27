<?php

require_once('../../wp-load.php');

// Verifica se a requisição é do tipo POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Recebe os dados do código numérico e chave de validação
    $rf = isset($_POST['rf']) ? $_POST['rf'] : null;
    $chave_acesso = isset($_POST['chave_acesso']) ? $_POST['chave_acesso'] : null;

    // Verifica se ambos os campos foram fornecidos
    if ($rf !== null && $chave_acesso !== null) {
        
        // Realiza a validação do código e da chave (substitua esta lógica pela sua lógica específica)
        if (validarCodigoChave($rf, $chave_acesso)) {
            // Código e chave válidos
            //echo "Validação bem-sucedida!";

            //echo $rf;
            $api_url = getenv('SMEINTEGRACAO_API_URL') . '/api/AutenticacaoSgp/' . $rf . '/dados';
            $api_token = getenv('SMEINTEGRACAO_API_TOKEN');
            $response = wp_remote_get( $api_url ,
                array( 
                    'headers' => array( 
                        'x-api-eol-key' => $api_token,							
                    )
                )
            );

            if($response['response']['code'] == 204){
                header('HTTP/1.1 400 Bad Request');
                echo "RF inválido";
            } else {

                $user = json_decode($response['body']);

                if($user->email){
                    $email = $user->email;
                } else {
                    $email = $rf . "@sme.prefeitura.sp.gov.br";
                }

                // Verifica se o usuario ja esta cadastrado no WordPress
                $userobj = new WP_User();
                $user_wp = $userobj->get_data_by( 'email', $email ); // Does not return a WP_User object :(
                $user_wp = new WP_User($user_wp->ID); // Attempt to load up the user with that ID

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

                // Se o usuario existar atualiza as informacoes sobre ele
                if($rf && $user_wp->ID != 0){

                    $api_url = getenv('SMEINTEGRACAO_API_URL') . '/api/Intranet/CarregarPerfisPorLogin/' . $user->codigoRf;
                    $api_token = getenv('SMEINTEGRACAO_API_TOKEN');
                    $response = wp_remote_get( $api_url ,
                        array( 
                            'headers' => array( 
                                'x-api-eol-key' => $api_token,							
                            )
                        )
                    );
        
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

                    if($user->nome){
                        $nome = $user->nome;
                    } elseif(is_array($user)) {
                        $nome = $user[0]->nome;
                    }

                    if($codigo){

                        $userdata = array( 'user_email' => $email,
                                            'user_login' => $email,
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
                                            'user_login' =>$email,
                                            'first_name' => $firstname,
                                            'last_name' => $lastname,                                
                                        );
                        $new_user_id = wp_insert_user( $userdata ); // Um novo usuario sera criado

                        $api_url = getenv('SMEINTEGRACAO_API_URL') . '/api/Intranet/CarregarPerfisPorLogin/' . $rf;
                        $api_token = getenv('SMEINTEGRACAO_API_TOKEN');
                        $response = wp_remote_get( $api_url ,
                            array( 
                                'headers' => array( 
                                    'x-api-eol-key' => $api_token,							
                                )
                            )
                        );
            
                        $userInfo = json_decode($response['body']);
            
                        $cargo = $userInfo->cargos[0]->nome;
                        $cargoSobre = $userInfo->cargosSobrePosto[0]->nome;
                        $areaAtuacao = implode(", ", $userInfo->areasAtuacao);
                        $local = $userInfo->unidadeLotacao->nomeUnidade;
                        $localSobre = $userInfo->unidadeExercicio->nomeUnidade;
                        
                        update_user_meta($new_user_id, "rf", $rf);
                        
                        if(strlen($rf) != 6){
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

                if($user_wp->ID){
                    $user_id = $user_wp->ID;
                    // Tamanho do token em bytes
                    $tamanho_token = 16;

                    // Gera uma sequência de bytes aleatórios
                    $bytes_aleatorios = random_bytes($tamanho_token);

                    // Converte a sequência de bytes para uma representação hexadecimal
                    $token = bin2hex($bytes_aleatorios);

                    $campo = 'chave_temp';

                    // Verifica se o valor já existe antes de adicionar ou atualizar
                    if (get_user_meta($user_id, $campo, true)) {
                        update_user_meta($user_id, $campo, $token);
                    } else {

                        // Adiciona o campo de metadados ao registrar um novo usuário
                        add_user_meta($user_id, 'chave_temp', $token);                       

                    }

                    // Retorna o token no formato JSON
                    $resposta = array('token' => $token);

                    // Configura os cabeçalhos para indicar que o conteúdo é JSON
                    header('Content-Type: application/json');

                    // Retorna a resposta como JSON
                    echo json_encode($resposta);
                }
               
            }

            
        } else {
            // Código ou chave inválidos            
            echo "Código ou chave inválidos!";
        }

    } else {
        // Um ou ambos os campos não foram fornecidos
        header('HTTP/1.1 400 Bad Request');
        echo "Por favor, forneça tanto o código numérico quanto a chave de validação.";
        
    }

} else {
    // Se a requisição não for do tipo POST, retorna um erro
    header('HTTP/1.1 405 Method Not Allowed');
    echo "Método não permitido. Utilize o método POST. " . $_SERVER['REQUEST_METHOD'];
}

// Função de exemplo para validar o código e a chave (substitua pela sua lógica)
function validarCodigoChave($codigo, $chave) {
    // Lógica de validação aqui
    // Retorne true se a validação for bem-sucedida, false caso contrário
    // Este é apenas um exemplo simples
    $api_token = getenv('INTRANET_API_TOKEN');
    return ($codigo && $chave == $api_token);
}