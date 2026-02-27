<?php
    use Shuchkin\SimpleXLSX;    
    require_once __DIR__.'/src/SimpleXLSX.php';
?>

<div class="wrap">
<h2>Cadastrar Assessores</h2>
<form method="post" enctype="multipart/form-data">    
    <label for="file">Faça o upload do arquivo em formato XLSX</label><br>
    <input type="file" name="file" id="file" /><br><br>
    <input type="submit" class="button" value="Enviar" />
</form>
</div>

<?php

if (isset($_FILES['file'])) {
    echo '<h1>Resultados do Cadastro</h1>';
    if ($xlsx = SimpleXLSX::parse($_FILES['file']['tmp_name']) ) {
        
        $usuarios = $xlsx->rows();
        // Loop para cadastrar novos usuários

        foreach ($usuarios as $usuario) {
            // Criar array de dados do usuário

            // Divide o nome em Nome e Sobrenome
            $parts = explode(" ", $usuario[0]);
            if(count($parts) > 1) {
                $firstname = array_shift($parts);
                $lastname = implode(" ", $parts);
            } else {
                $firstname = $name;
                $lastname = " ";
            }

            $userdata = array(
                'user_login' => $usuario[1],
                'user_email' => $usuario[4],
                'user_pass' => $usuario[3],
                'first_name' => $firstname,
                'last_name' => $lastname,
                'role' => 'assessor' // Altere a função do usuário conforme necessário
            );

            // Inserir o usuário no banco de dados
            $user_id = wp_insert_user($userdata);

            // Verificar se houve algum erro ao inserir o usuário
            if (is_wp_error($user_id)) {
                echo 'Nome: ' . $usuario[0] . ' / RF: ' . $usuario[1] . ' - Erro ao cadastrar o usuário: ' . $user_id->get_error_message() . '<br>';
            } else {
                // Atualizar os campos personalizados do usuário
                update_user_meta($user_id, 'rf', $usuario[1]);
                update_user_meta($user_id, 'cargo_principal', $usuario[2]);

                echo 'Nome: ' . $usuario[0] . ' / RF: ' . $usuario[1] . ' - Usuário cadastrado com sucesso! ID: ' . $user_id . '<br>';
            }
        }

    } else {
        echo SimpleXLSX::parseError();
    }
    
}