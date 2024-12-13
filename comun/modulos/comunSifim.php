<?php 
	require_once('../includes/initSistema.php');
	
	$conexionDB = new DB_MySQL;
	$conexionDB->conectar(DB_Sistema, DB_SERVER, DB_USER, DB_PASSWD );
	
    $dBase  	  		= "red-m_@comun";
	$tablaDB	  		= "comun_tabSifim";
	$IdCampo	  		= "Id";
	$phpFile 			= "comunSifim";
	$orderBy			= "Id";
	
	if (isset($_POST['CodCliente'])) { 
	    require_once(PATH_INCLUDES.'tab@guardar.php'); 
    }  
	
	$campoId  = 1;  
	$consulta = "SELECT *   FROM `$tablaDB` WHERE `$IdCampo` = 1 ";
	$salida   = $conexionDB->consulta($consulta);
	$row = mysql_fetch_array($salida);
	$_CodCliente       = $row ['CodCliente'];
    $_CodArea          = $row ['CodArea'];
    $_ip               = $row ['ip'];
    $_codComuna        = $row ['codComuna'];
    $_webServiceUserDB = $row ['webServiceUserDB'];
    $_webServicePassDB = $row ['webServicePassDB'];
    $_webServiceDB     = $row ['webServiceDB'];
    $_comunDB          = $row ['comunDB'];
    $_comunUser        = $row ['comunUser'];
    $_comunPass        = $row ['comunPass'];
    
    //--if ($_conectadoSifim == 0) { $selectedSiF0 = ' selected="selected" '; } else { $selectedSiF0  = ''; }
    //--if ($_conectadoSifim == 1) { $selectedSiF1 = ' selected="selected" '; } else { $selectedSiF1 = ''; }
        
	$contenido=new plantilla("comunSifim");
	$contenido->asigna_variables(
			array(
			"lang"  => $sistema_txt->GetDefinition('XMLLang'),
			"barra_pie_mensajes"  => date('d-m-Y'),
			"lateral"			  => $lateral,
            "topbar"		      => $topbar,
			"H2Sistema"			  => $sistema_txt->GetDefinition('H2Sistema'),
            "H2Titulo"		      => $sistema_txt->GetDefinition('H2ComunSifim'),
            "TInforme"            => $sistema_txt->GetDefinition('TINFComunSifim'),
            "salir"		          => $sistema_txt->GetDefinition('salir'),
            "buscarpor"		      => $sistema_txt->GetDefinition('buscarpor'),
			
			"Id"             => $sistema_txt->GetDefinition('Id'),
			"borrar"         => $sistema_txt->GetDefinition('borrar'),
			"enviar"         => $sistema_txt->GetDefinition('enviar'),
			"limpiar"        => $sistema_txt->GetDefinition('limpiar'),
            "salir"		     => $sistema_txt->GetDefinition('salir'),
			
            "CodCliente"       => $sistema_txt->GetDefinition('CodCliente'),
            "CodArea"          => $sistema_txt->GetDefinition('CodArea'),
            "ip"               => $sistema_txt->GetDefinition('ip'),
            "codComuna"        => $sistema_txt->GetDefinition('codComuna'),
            "webServiceUserDB" => $sistema_txt->GetDefinition('webServiceUserDB'),
            "webServicePassDB" => $sistema_txt->GetDefinition('webServicePassDB'),
            "webServiceDB"     => $sistema_txt->GetDefinition('webServiceDB'),
            "comunDB"          => $sistema_txt->GetDefinition('comunDB'),
            "comunUser"        => $sistema_txt->GetDefinition('comunUser'),
            "comunPass"        => $sistema_txt->GetDefinition('comunPass'),
            
            "_CodCliente"       => $_CodCliente,
            "_CodArea"          => $_CodArea,
            "_ip"                => $_ip,
            "_codComuna"        => $_codComuna,
            "_webServiceUserDB" => $_webServiceUserDB,
            "_webServicePassDB" => $_webServicePassDB,
            "_webServiceDB"     => $_webServiceDB,
            "_comunDB"          => $_comunDB,
            "_comunUser"        => $_comunUser,
            "_comunPass"        => $_comunPass,
            ));
			
	echo $contenido->muestra();
?>
