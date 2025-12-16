<?php
define( 'WP_USE_THEMES', false ); // Don't load theme support functionality
require( './wp-load.php' );
$user = wp_get_current_user();
$rf = get_field('rf', 'user_' . get_current_user_id());
$rg = get_field('rg', 'user_' . get_current_user_id());
$cpf = get_field('cpf', 'user_' . get_current_user_id());
$cargo = get_field('cargo', 'user_' . get_current_user_id());
if($cargo == 'Outro')
    $cargo = get_field('cargo_outro', 'user_' . get_current_user_id());
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cartão do Educador</title>
</head>
<body>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&display=swap');
        .content{
            max-width: 1170px;
            display: block;
            margin: 0 auto;
            position: relative;
            font-family: 'Open Sans', sans-serif;
        }

        .user-card-info {
            max-width: 44%;
            margin-left: 3%;
            position: absolute;
            top: 66%;
            display: block;
            left: 0;
            right: 0;
            color: #24B5BC;
            font-size: 16px;
        }

        .user-card-info span {
            color: #767882;
            font-weight: bold;
            background: #F1F4FE;
            padding: 1px 2px;
        }

        .info-line-one,
        .info-line-two{
            margin-bottom: 10px;
        }

        .d-flex{
            display: flex;
        }

        .justify-content-between{
            justify-content: space-between;
        }

        @page {
            size: A4;
        }

        @media print {
            html, body {
                width: 210mm;
                height: 297mm;
            }
            body {
                -webkit-print-color-adjust: exact;
            }
            .user-card-info{
                font-size: 10px;
                top: 68%;
            }

            .info-line-one,
            .info-line-two{
                margin-bottom: 5px;
            }
        }
    </style>
    <div class="content">        
        <img src="cartao-educador.png" alt="" srcset="" style="max-width: 100%">

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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.8/jquery.mask.min.js" integrity="sha512-hAJgR+pK6+s492clbGlnrRnt2J1CJK6kZ82FZy08tm6XG2Xl/ex9oVZLE6Krz+W+Iv4Gsr8U2mGMdh0ckRH61Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $('.cpf span').mask('000.000.000-00');
        window.print();
    </script>
</body>
</html>