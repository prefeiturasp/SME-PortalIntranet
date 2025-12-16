<?php 
/**
 * Plugin Name: Envio de Emails Personalizados - SME
 * Description: Envia e-mails personalizados.
 * Version: 1.1
 * Author: Jardeon J M Araujo
 */

 defined('ABSPATH') || die('Ops, acesso negado!');
// Dentro do plugin
require_once plugin_dir_path(__FILE__) . 'src/classes/Envia_Emails_Sorteio_SME.php';


 //require_once 'vendor/autoload.php';

 define('URL_ENVIA_EMAIL_SME', WP_PLUGIN_URL . "/" . dirname(plugin_basename(__FILE__)));
 define('DIR_ENVIA_EMAIL_SME', WP_PLUGIN_DIR . "/" . dirname(plugin_basename(__FILE__)));

# É Preciso criar uma página para o cancelamento de inscrição do sorteio no painel admin do wordpress, com o nome "cancela-inscricao-sorteio"
# Voltar a URL padrão da logo SME
# Colocar o email institucional e secundario para receber os emails
 