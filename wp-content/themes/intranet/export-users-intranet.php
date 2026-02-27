<?php
//require_once('../../../pluggable.php');
require_once("../../../wp-load.php");
$date = date('d_m_y_h_i_s');
$fileName = $date . '_usuarios_portal.csv';
 
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header('Content-Description: File Transfer');
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename={$fileName}");
header("Expires: 0");
header("Pragma: public");

$fh = @fopen( 'php://output', 'w' );

$headerDisplayed = false;

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
			break;
		default:
			return $funcao;
	endswitch;
}

foreach($blogusers as $user){
	$user_meta = get_userdata($user->id);
	$user_roles = $user_meta->roles;
	$setor = get_field('setor', 'user_'. $user->id );
	$grupos = get_field('grupo', 'user_'. $user->id );
	$grupoTitle = '';
	$i = 0;
	if($grupos && $grupos != '' && $grupos[0] != ''){
		foreach($grupos as $grupo){
			$title = preg_replace("/&([a-z])[a-z]+;/i", "$1", htmlentities(trim( get_the_title($grupo) )));
			if($i == 0){				
				$grupoTitle .= $title;
			} else {
				$grupoTitle .= ', ' . $title;
			}
			$i++;
		}				
	}

	$usuarios[] = array(
		'id' => $user->id,
		'login' => $user->user_login,
		'email' => $user->user_email,
		'funcao' => convertFunc($user_roles[0]),
		'grupo' => $grupoTitle,
		'setor' => $setor
	);

}

foreach ( $usuarios as $data ) {
    // Add a header row if it hasn't been added yet
    if ( !$headerDisplayed ) {
        // Use the keys from $data as the titles
        fputcsv($fh, array_keys($data));
        $headerDisplayed = true;
    }
 
    // Put the data into the stream
    fputcsv($fh, $data);
}
// Close the file
fclose($fh);
// Make sure nothing else is sent, our file is done
exit;