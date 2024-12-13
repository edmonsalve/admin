<?php
	session_start(); 
    $id_session = session_id();

	session_unset();
	session_destroy();
	header("Location: index.php");
?>
