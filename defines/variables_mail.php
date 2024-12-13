<?php 
    if (!defined('E_HOST')) { 
		//::::::::::::::::::::: CUENTA E-MAIL ::::::::::::::::::::::: //
		define('E_HOST',		'mail.red-m.cl');
		
		define('E_MAILER',		'smtp');  
		define('E_PORT',		'587');  
 		define('E_SMTP_SECURE',	'tls');  
		define('E_SMTP_AUTH',	'true');   
		  
		define('E_USER_NAME',	'soporte@red-m.cl');   		 
		define('E_PASSWORD',	'kcm64%VI-9');  
		define('E_FROM',		'soporte@red-m.cl');   
		define('E_FROM_NAME',	'Soporte RED-M / dCode'); 
	
		define('E_PIEFIRMA',"<hr><img src='".RUTA_ABSOLUTA."images/pieCorreo2.jpg'><br>
							<p>1. Cuidemos el Medio Ambiente, no imprima este correo electrónico si no es necesario.<br>
							2. De esta manera ahorras agua, energía y recursos forestales.<br>
							3. Si necesitas imprimir este correo, no olvide reciclarlo. Porque el papel es el soporte natural, renovable y reciclable.<p>"); 
     } 

?>