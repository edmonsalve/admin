<?php 
	session_start();

	// :::::::::::::::::::::: VARIABLES GENERALES  ::::::::::::::::::::::: //
	require_once('../../defines/variables_path.php');
	require_once(PATH_DEFINES . '/variables.php');

	// ::::::::::::::::::::::: VARIABLES SISTEMA  :::::::::::::::::::::::: //
	require_once('../includes/variablesSistema.php');
	require_once(PATH_CLASSES . 'Class.Context.php');
	require_once(PATH_CLASSES . 'Class.DB_MySQLi.php');

	// ::::::::::::: Diccionario General y de Sistema :::::::::::::::::::: //
	$dg_txt = new Context();
	$dg_txt->init();

	$sistema_txt = new Context();
	$sistema_txt->init();
    require_once(PATH_INCLUDES . 'top.php');
	// require_once(PATH_INCLUDES . 'barraLateral.php');
  
    require_once(PATH_INCLUDES . 'funciones.php');
	require_once(PATH_CLASSES_SISTEMA .  'Class.Plantilla.php');
	require_once(PATH_LANGUAGE . 'spanish.php');
	require_once(PATH_LANGUAGE_SISTEMA . 'spanish.php');
	
    $DB_PERSON   = DB_PERSON;
    $DB_COMUN    = DB_COMUN;
    $DB_ADMIN    = DB_ADMIN;  
    $DB_CLIENTE  = DB_CLIENTE; 
    $USER_CADUC  = USER_CADUC;  

	$mes   = date('m');
    $ano   = date('Y');
    $hoy   = date('Y-m-d');
    $icono = 'ico_clave.png';
    $usrID = $_SESSION['idUser']; 

	$conexionDB = new DB_MySQLi;
	$conexionDB->conectar($DB_COMUN, DB_SERVER, DB_USER, DB_PASSWD );

	$_claveUno = "";
	$_claveDos = "";

    $consulta = "SELECT * FROM `$DB_CLIENTE`.`adm_users` WHERE username = '$usrID'"; 
    $salida   = $conexionDB->consulta($consulta);     
    $row 	  = mysqli_fetch_array($salida);

    $user 	  = $row['username'];
    $name 	  = $row['nombre'];
    $mail 	  = $row['email'];
    $pass     = $row['password'];

    $mensajeClave   = "Nueva Contraseña debe tener este formato... <br><b>Entre 8 y 16 carácteres, contener: letras mayúsculas y minúsculas, un número y al menos un carácter especial</b>";
    $fondoMensaje   = "info";

    $_pass     = $pass;

    if(isset($_GET['estado'])) {
        $_titulo = "Clave Caducada: ";
    } else {
        $_titulo = "Cambiar Clave: ";
    }


	$contenido=new plantilla("cambioClave");
	$contenido->asigna_variables(
			array(
			'lang'  			  	=> $sistema_txt->GetDefinition('XMLLang'),
			"icono"	  		      	=> PATH_ICO.ICONO_NAVEGADOR, 

			"moduloPHP"				=> $moduloPHP,
			
			"topbar"		      	=> $topbar,
			"barraLateral"		  	=> $barraLateral,
            "dispFormPerson"        => $dispFormPerson,
            "origen"                => $origen,
		
			"H2Sistema"			  	=> $sistema_txt->GetDefinition('H2Sistema'),
            'H2Titulo'		        => 'Actualización de contraseña',
						
			"topbar"		      	=> $topbar,
            "barraLateral"		  	=> $barraLateral,
            "salir"		          	=> $dg_txt->GetDefinition('salir'),
            "filtrarpor"		  	=> $dg_txt->GetDefinition('filtrarpor'),
			'cerrarSesion'        	=> $dg_txt->GetDefinition('cerrarSesion'),
			'guardar'        	  	=> $dg_txt->GetDefinition('guardar'),
			'borrar'        	  	=> $dg_txt->GetDefinition('borrar'),
            'salir'        	      	=> $dg_txt->GetDefinition('salir'),

            "nombre"     	 		=> $dg_txt->GetDefinition('nombre'), 
            "username"     	 		=> $dg_txt->GetDefinition('username'),
            "email"                 => $dg_txt->GetDefinition('email'), 
			'headerMenu'		  	=> $headerMenu,
			
            "mensajeClave"          => $mensajeClave, 
            "fondoMensaje"          => $fondoMensaje,  
            "_pass"                 => $pass,
            "_user"        		    => $user,
            "_name"        		    => $name,
            "_mail"        		    => $mail,
  
			));
			
	echo $contenido->muestra();
?>
