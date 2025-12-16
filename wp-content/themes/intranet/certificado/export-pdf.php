<?php
$username = 'usr_certificados';
$password = 'WgpxCufo';

try {
	$conn = new PDO('mysql:host=10.50.1.27;dbname=sme_certificados', $username, $password);
	$stmt = $conn->prepare('SELECT arquivo, num_homolog_curso FROM tb_arquivo_certificado WHERE id = :id');
	$stmt->execute(array('id' => $_GET['id']));

	$result = $stmt->fetchAll();

	if ( count($result) ) {
		
		//print_r($result);
		
		foreach($result as $row) :			
			
			$decoded = gzdecode(base64_decode($row['arquivo']));
			$file = $row['num_homolog_curso'] . '.pdf';
			file_put_contents($file, $decoded);

			if (file_exists($file)) {
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename="'.basename($file).'"');
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Length: ' . filesize($file));
				readfile($file);
				unlink($file);
				exit;
			}

		endforeach;
		
	} else {
		echo "Nenhum resultado encontrado.";
	}
} catch(PDOException $e) {
	echo 'ERROR: ' . $e->getMessage();
}
