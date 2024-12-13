<?php 
    if(isset($_GET['estado'])) { $_estado = ' (su clave esta caducada) '; } else { $_estado = ''; }
    $error = '';
	session_start();
	if (!isset($_SESSION['idUser'])) { header("Location:/index.php"); }
    
    if (!defined('SISTEMA_ID')) { define('SISTEMA_ID', 0); }

	/* :::::::::::::::::::::::::::: Elimina Icono Sistema Lateral ::::::::::::::::::::::::: */
    if (isset($_SESSION['iconosis'])) { unset($_SESSION['iconosis']); }

    //--------------------------------------------------------------
    require_once('../defines/variables_path.php');
	require_once(PATH_DEFINES . 'variables.php');

	require_once('controlAcceso.php');
	if (!PERMITE_ACCESO) { header("Location:main.php"); }

	require_once(PATH_CLASSES . 'Class.Context.php');
	require_once(PATH_CLASSES . 'Class.Plantilla.php');
	require_once(PATH_CLASSES . 'Class.DB_MySQLi.php');

	$pg_text = new Context();
	$pg_text->init();

	require_once(PATH_LANGUAGE . 'spanish.php');
	require_once(PATH_INCLUDES . 'menuReD-m.php');
	require_once(PATH_INCLUDES . 'userInformacion.php');
	require_once(PATH_INCLUDES . 'crypt.php');

    $tituloNavegador = TITULO_NAVEGADOR;
    //---------------------------------------------------------------


    $conexionDB = new DB_MySQLi;
	$conexionDB->conectar(DB_CLIENTE, DB_SERVER, DB_USER, DB_PASSWD );

    $licencia 	= $_SESSION['licencia'];
	$rut      	= $_SESSION['rutempleador'];
	$serie    	= $_SESSION['keynumber'];

	$user 		= $_SESSION['idUser'];
	$nombre		= $_SESSION['usuario'];

    $consulta = 'SELECT * FROM `adm_users` WHERE username = "'.$user.'" ';
	$salida   = $conexionDB->consulta($consulta);
    $row      = mysqli_fetch_array($salida);
    $_dbpw    = $row['password'];
    
    $fecha_actual = date("Y-m-d");
    $vencimiento  = date("Y-m-d",strtotime($fecha_actual."+ 6 month")); 

	require_once(PATH_INCLUDES . 'top.php');

    if(isset($_POST['antigua'])) {

        $_passANT   = ltrim($_POST['antigua']);
        $_passNEW   = ltrim($_POST['nueva']);
        $_passRENEW = ltrim($_POST['renueva']);

        if (sha1($_passANT) == $_dbpw) {
            if ((trim($_passNEW) != '') and (strlen(trim($_passNEW)) >= 6) and ($_passNEW == $_passRENEW)) {
                $_passNewCod = sha1($_passNEW);

                $consulta = "UPDATE `adm_users` SET `password` = '$_passNewCod', caducidad = '$vencimiento' WHERE username = '$usrID' ";
                $conexionDB->consulta($consulta);
                
                
                header("Location: ../index.php"); 
            } else {
                $error = 'Error, minimo 6 caracteres y/o claves iguales. <br>';
            }
        } else {
            session_unset();
        	session_destroy();
        	header("Location: ../index.php");
        }



        $mifoto_nombre     = $_FILES['mifoto']['name'];
        $mifoto_tipo       = $_FILES['mifoto']['type'];
        $mifoto_size       = $_FILES['mifoto']['size'];
        $mifoto_tmp        = $_FILES['mifoto']['tmp_name'];
        $mifoto_error      = $_FILES['mifoto']['error'];

        if (trim($mifoto_nombre) != '') {
            switch ($mifoto_tipo) {
                case 'image/jpeg': $foto_ext = '.jpg'; break;
                case 'image/png':  $foto_ext = '.png'; break;
            }
            $nombreFile = $usrID.$foto_ext;
            move_uploaded_file($mifoto_tmp, PATH_FOTOS . $nombreFile);
            $consulta = "UPDATE `adm_users` SET `foto` = '$nombreFile' WHERE username = '$usrID' ";
            $conexionDB->consulta($consulta);

                
            session_unset();
        	session_destroy();
            header("Location: ../index.php");
        }
    }

    $contenido=new plantilla("userdata");
	$contenido->asigna_variables(
					array(
                    "topbar"		=> $topbar,
                    "bottombar"		=> $bottombar,
					"titulo"		=> "Cambio de Clave y Fotograf&iacute;a  $_estado",
					"lateral"		=> $lateral,

					"error"		    => $error,

					"enviar"     	=> 'enviar',
					"serie"			=> $serie ,
					));

	echo $contenido->muestra();
?>
