<?php
	// Estas Variables deven venir Predefinidas
    // $Subject = 'Asunto del Mensaje';
	// $body 	= 'Cuerpo del Mensaje';
	// $address = 'destinatario@dominio.cl, d2@d2.cl, ... '
	
	
	use PHPMailer\PHPMailer\PHPMailer;
	require(PATH_EXTERNOS . "vendorMail/autoload.php");
	require(PATH_DEFINES .  "variables_mail.php"); 
	
	$ConfirmReadingTo = "";

	$mail           	= new PHPMailer();
	
	$mail->Host     	= E_HOST;
	$mail->Mailer   	= E_MAILER;
	$mail->Port     	= E_PORT;
	$mail->SMTPSecure	= E_SMTP_SECURE;
	$mail->SMTPAuth 	= E_SMTP_AUTH;

	$mail->Username 	= E_USER_NAME; 
	$mail->Password 	= E_PASSWORD;

	$mail->From 		= E_FROM; 
	$mail->FromName 	= E_FROM_NAME;
	
	$mail->Subject		= $Subject;

	// Destinatarios
	$destinatarios 	= explode(",",$address);
	
	foreach($destinatarios as $ind => $direccion)  { 
		if(trim($direccion) != '') {
			$mail->AddAddress($direccion); 
		}
	}
	
	
	$body .= E_PIEFIRMA;
	
	
	$mail->MsgHTML("<h1>esto es una prueba</h1>"); // file_get_contents('contents.html')

	$mail->Body = $body;
	$mail->IsHTML(true);
	
	$enviado = $mail->Send();
    
	$intentos = 1; 
	while ((!$enviado) && ($intentos < 2)) {
		sleep(3);
		echo $mail->ErrorInfo;
		$enviado = $mail->Send();
		$intentos=$intentos+1;	
	}
?>