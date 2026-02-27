<?php
    use Shuchkin\SimpleXLSX;

    //ini_set('error_reporting', E_ALL);
    //ini_set('display_errors', true);
    
    require_once __DIR__.'/src/SimpleXLSX.php';
?>

<div class="wrap">
<h2>Atualizar usuários</h2>
<form method="post" enctype="multipart/form-data">    
    <label for="file">Faça o upload do arquivo em formato XLSX</label><br>
    <input type="file" name="file" id="file" /><br><br>
    <input type="submit" class="button" value="Enviar" />
</form>
</div>

<?php

if (isset($_FILES['file'])) {
    echo '<h1>Resultados da atualização</h1>';
    if ($xlsx = SimpleXLSX::parse($_FILES['file']['tmp_name']) ) {
        
        $api_url = 'https://hom-smeintegracaoapi.sme.prefeitura.sp.gov.br/api/AutenticacaoSgp/AlterarEmail';
        $usuarios = $xlsx->rows();
        echo "<table class='wp-list-table widefat fixed striped table-view-list posts' cellspacing='0'>";
            echo "<thead>";
            echo "<tr>";
            echo "<th class='manage-column column-title'><strong>RF/EOL</strong></th>";
            echo "<th class='manage-column column-title'><strong>E-mail</strong></th>";
            echo "<th class='manage-column column-title'><strong>Status</strong></th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";

            foreach($usuarios as $usuario){  

                $usuarioEol = $usuario[1];
                $email = $usuario[2];
                
                $response = wp_remote_post( $api_url, array(
                    'method'      => 'POST',                    
                    'headers' => array( 
                        'x-api-eol-key' => 'fe8c65abfac596a39c40b8d88302cb7341c8ec99',
                    ),
                    'body' => array("Usuario" => "$usuarioEol","Email" => "$email"),
                    )
                );
                
                if ( is_wp_error( $response ) ) {
                    $error_message = $response->get_error_message();
                    echo "<tr>";
                    echo "<td>" . $usuario[1] . "</td>";
                    echo "<td>" . $usuario[2] . "</td>";
                    echo "<td>Ocorreu um erro: $error_message</td>";
                    echo "</tr>";
                } else {
                    
                    $user = json_decode($response);

                    if($response['response']['code'] == 601){
                        echo "<tr>";
                            echo "<td>" . $usuario[1] . "</td>";
                            echo "<td>" . $usuario[2] . "</td>";
                            echo "<td>" . $response['body'] . "</td>";
                        echo "</tr>";
                        //echo "Usuario: " . $usuario[0] . " - RF/EOL: " . $usuario[2] . " - " . $response['body'] . " <br>";
                    } elseif($response['response']['code'] == 200){
                        echo "<tr>";
                            echo "<td>" . $usuario[1] . "</td>";
                            echo "<td>" . $usuario[2] . "</td>";
                            echo "<td>Usuário atualizado com sucesso!</td>";
                        echo "</tr>";
                        //echo "Usuario: " . $usuario[0] . " - RF/EOL: " . $usuario[2] . " - Usuário cadastrado com sucesso <br>";
                    } else {
                        echo "<tr>";
                            echo "<td>" . $usuario[1] . "</td>";
                            echo "<td>" . $usuario[2] . "</td>";
                            echo "<td>Ocorreu um erro, verifique os dados e tente novamente.</td>";
                        echo "</tr>";
                        //echo "Usuario: " . $usuario[0] . " - RF/EOL: " . $usuario[2] . " - Ocorreu um erro, verifique os dados e tente novamente. <br>";
                    }                    
                    
                }
                
            }
            echo "</tbody>";
        echo "</table>";
     

    } else {
        echo SimpleXLSX::parseError();
    }
    
}