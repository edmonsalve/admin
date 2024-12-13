<?php 
    date_default_timezone_set('America/Santiago');
    
	//$location 	= "@admin/index.php";
	$location 	= "index_main.php";
	
    $fecha		= date("Y-m-d");
    $hora		= date("h:i:s");
        
    $_user = ltrim($_POST['user-id']);
	$_pass = ltrim($_POST['user-pw']);
	
	if(trim($_user) == '' or trim($_pass) == '') { header("Location: index.php"); }
    
    $ipShare =  '';
    $ipProxy =  '';
    $ipAddre =  $_SERVER['REMOTE_ADDR'];
 
    
	session_start(); 
    session_name($_user);
    $id_session = session_id();
    
    require_once('defines/variables_path.php');
    
	require_once(PATH_DEFINES . 'variables.php');
	require_once(PATH_CLASSES . 'Class.DB_MySQLi.php'); 
	
    $db = DB_CLIENTE;   
    
	$conexionReD = new DB_MySQLi;
	$conexionReD->conectar($db, DB_SERVER, DB_USER, DB_PASSWD ); 
 	
	foreach  ( $_POST as $variable => $valor ){
			   $_POST [ $variable ] = str_replace ( "'" , '"' , $_POST [ $variable ]);
	}
    

    //----------------------------------------------------------------------------------------------------
	$consulta = "SELECT * FROM `adm_users` WHERE username = '$_user' ";
	$salida   = $conexionReD->consulta($consulta);	
   
	if 	(mysqli_num_rows($salida) != 0) {   
		$row     = mysqli_fetch_array($salida);
		$_dbpw   = $row['password'];
		
		// echo "<br>pase ".sha1($_pass)." == $_dbpw ".DB_CLIENTE."  ".DB_SERVER." ".DB_PASSWD;	
		if (sha1($_pass) == $_dbpw) { 
			$_SESSION['idUser']  	= $_user;
			$_SESSION['usuario']	= $row['nombre'];
			$_SESSION['tipo']  		= $row['tipo'];
            $_SESSION['foto']  		= $row['foto'];
            $_SESSION['id_usr']     = $row['username'];  
            $_SESSION['usrMuni']	= $row['muni'];
            $_SESSION['usrEduc']	= $row['educ'];
            $_SESSION['usrSalud']	= $row['salud'];
            $_SESSION['usrAdmin']	= $row['admin'];
            
	        header("Location: $location"); 
		} else {
			header("Location: index.php");
		}
	} else {
		header("Location: index.php");
	}
	
?>