<?php
require_once("../../../wp-load.php");
$date = date('d_m_y_h_i_s');
$fileName = $date . '_usuarios_intranet.xlsx';
 
if($_GET['funcao'] == 'all'){
	$blogusers = get_users( array( 'fields' => array( 'id', 'user_login', 'user_email' ) ) );
} else {
	$blogusers = get_users( 
		array( 
			'fields' => array( 'id', 'user_login', 'user_email' ),
			'role__in' => array( $_GET['funcao'] )
		)
	);
}

$usuarios = array();
$usuarios[] = array(
	'<style bgcolor="#8EA9DB">Nome</style>',
	'<style bgcolor="#8EA9DB">RF</style>',
	'<style bgcolor="#8EA9DB">E-mail</style>',
	'<style bgcolor="#8EA9DB">Função</style>',
	'<style bgcolor="#8EA9DB">Novidades Email</style>',
	'<style bgcolor="#8EA9DB">Telefone</style>',
	'<style bgcolor="#8EA9DB">Novidades Whats</style>',
	'<style bgcolor="#8EA9DB">DRE</style>',
	'<style bgcolor="#8EA9DB">Cargo</style>'
);

function convertFunc($funcao){
	switch ($funcao):
		case 'administrator':
			return 'Administrador';
			break;
		case 'contributor':
			return 'Colaborador';
			break;
		case 'editor':
			return 'Editor';
		case 'assessor':
			return 'Assessor';
			break;
		default:
			return $funcao;
	endswitch;
}

foreach($blogusers as $user){
	$user_meta = get_userdata($user->id);
	$user_roles = $user_meta->roles;
	$rf = get_field('rf', 'user_'. $user->id );
	$nov_email = get_field('nov_email', 'user_'. $user->id );
	$telefone = get_field('celular', 'user_'. $user->id );
	$nov_whats = get_field('nov_whats', 'user_'. $user->id );
	$dre = get_field('dre', 'user_'. $user->id );
	$cargo = get_field('cargo', 'user_'. $user->id );
	$nome = get_user_meta( $user->id, 'first_name', true ) . ' ' . get_user_meta( $user->id, 'last_name', true );
	if(!$nome)
		$nome = get_user_meta( $user->id, 'display_name', true );

	$conf_email = $nov_email == 1 ? "Sim" : '-';
	$conf_whats = $nov_whats == 1 ? "Sim" : '-';

	$func = $user_roles[0];
	if($func == '')
		$func = '<center>-</center>';
	
	$usuarios[] = array(
		$nome,
		$rf,
		$user->user_email,
		convertFunc($func),
		$conf_email,
		$telefone,
		$conf_whats,
		$dre,
		$cargo
	);

}

$xlsx = Classes\Lib\SimpleXLSXGenExp::fromArray( $usuarios );
$xlsx->downloadAs($fileName); // or downloadAs('books.xlsx') or $xlsx_content = (string) $xlsx 

exit();