<?php

$servername = getenv('WORDPRESS_DB_HOST');
$username = getenv('WORDPRESS_DB_USER');
$password = getenv('WORDPRESS_DB_PASSWORD');

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";


$headers = apache_request_headers();        
foreach ($headers as $header => $value) {
 echo "<pre>";
 echo "$header : $value";
 echo "</pre>";
}

?>
