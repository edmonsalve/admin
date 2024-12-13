<?php 
	require_once('../includes/initSistema.php');
	
	$conexionDB = new DB_MySQL;
	$conexionDB->conectar(DB_COMUN, DB_SERVER, DB_USER, DB_PASSWD );
    
	$dBase  	  		= DB_COMUN;
    $tablaDB	  		= "comun_tabDeptos";
	$IdCampo	  		= "id";
	$phpFile 			= "comunDeptos";
	$orderBy			= "departamento";
    
    $tabMaster          =  TAB_MASTER;
	$indMaster          = 'id';
	
	$campoId 		= 'new';
	$_departamento  = '';
    $_responsable   = '';
    $_email         = '';
    $_programa      = '';
    $_folioSOC      = '';
    
   
	
	if (isset($_POST['departamento'])) { require_once(PATH_INCLUDES.'tab@guardar.php'); }  
	
	if (isset($_GET['ope'])) { $operacion = $_GET['ope']; }  else { $operacion =''; }
	switch ($operacion) {
		case 'Update': 	$campoId  = $_GET['IdRegistro'];  
						$consulta = "SELECT *   FROM `$tablaDB` WHERE `$IdCampo` = $campoId ";
						$salida   = $conexionDB->consulta($consulta);
						$row = mysql_fetch_array($salida);
						$_departamento = $row ['departamento'];
                        $_responsable  = $row ['responsable'];
                        $_email        = $row ['email'];
                        $_programa     = $row ['programa'];
                        $_folioSOC     = $row ['folioSOC'];
                        
                        
						break;
						
		case 'Delete': 	$locationFile = $_SERVER['SCRIPT_NAME'];
						$errorMensaje = "ERROR_borrarTabDeptos";
						require_once(PATH_INCLUDES.'tab@borrar2.php');  
						break;
	}

	/* :::::::::::::::::::::::::::::::::::::::::::: INICIO Lee Grilla ::::::::::::::::::::::::::::::::::::: */
	$titulos[1] = array("tit" => $sistema_txt->GetDefinition('id'), "ancho" => "span-1 enc_mensajes");
	$titulos[2] = array("tit" => $sistema_txt->GetDefinition('departamento'),    "ancho" => "span-7 enc_mensajes last");
	
	$fila[1] = array("campo" => "$IdCampo",   "ancho" => "span-1");
	$fila[2] = array("campo" => "$orderBy",   "ancho" => "span-7 last");
   
	require_once(PATH_INCLUDES.'tab@lee.php');
	/* :::::::::::::::::::::::::::::::::::::::::::: FIN Lee Grilla :::::::::::::::::::::::::::::::::::::::: */
	if ($_programa == 1) { $_varSi = 'selected = "selected" '; $_varNo  = '';  } else{ $_varSi  = ''; $_varNo  = 'selected = "selected" ';  }
        
    
	$contenido=new plantilla("comunDeptos");
	$contenido->asigna_variables(
			array(
			"lang"  => $sistema_txt->GetDefinition('XMLLang'),
			"barra_pie_mensajes"  => date('d-m-Y'),
			"lateral"			  => $lateral,
            "topbar"		      => $topbar,
			"H2Sistema"			  => $sistema_txt->GetDefinition('H2Sistema'),
            "H2Titulo"		      => $sistema_txt->GetDefinition('H2Deptos'),
            "TInforme"		      => $sistema_txt->GetDefinition('TINFDeptos'),
            "salir"		          => $sistema_txt->GetDefinition('salir'),
            "buscarpor"		      => $sistema_txt->GetDefinition('buscarpor'),
            "dBase"               => $dBase,
            "tablaDB"             => $tablaDB,
			"phpFile"		 	  => $phpFile,
			"IdCampo"		 	  => $IdCampo,
            "campoOrd"            => $orderBy,
			
			"IdCenCtos"    => $sistema_txt->GetDefinition('IdCenCtos'),
			"departamento" => $sistema_txt->GetDefinition('departamento'),
            "responsable"  => $sistema_txt->GetDefinition('responsable'),
            "email"        => $sistema_txt->GetDefinition('email'),
            "programa"     => $sistema_txt->GetDefinition('programa'),
            "folioSOC"     => $sistema_txt->GetDefinition('folioSOC'),
                        
            "borrar"       => $sistema_txt->GetDefinition('borrar'),
			"enviar"       => $sistema_txt->GetDefinition('enviar'),
			"limpiar"      => $sistema_txt->GetDefinition('limpiar'),
			"tablaHTML"	   => $tablaHTML,

			"campoId"	    => $campoId,   
			"_departamento" => $_departamento,
            "_responsable"  => $_responsable,
            "_email"        => $_email,
            "_programa"     => $_programa,
            "_folioSOC"     => $_folioSOC,
            "_varSi"        => $_varSi,
            "_varNo"        => $_varNo
            ));
			
	echo $contenido->muestra();
?>
