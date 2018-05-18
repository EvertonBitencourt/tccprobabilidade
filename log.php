<pre>
<?php
	$dados = $_GET["dados"];


	file_put_contents("log.txt", $dados . PHP_EOL, FILE_APPEND);
	echo file_get_contents("log.txt");
	
?>