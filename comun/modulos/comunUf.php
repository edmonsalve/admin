<?php 
	require_once('../includes/initSistema.php');
	
	$conexionDB = new DB_MySQL;
	$conexionDB->conectar(DB_COMUN, DB_SERVER, DB_USER, DB_PASSWD );
    
	$dBase  	  		= DB_COMUN;
    $tablaDB	  		= "comun_tabUf";
	$IdCampo	  		= "id";
	$phpFile 			= "comunUf";
	$orderBy			= "id";
    
    $tabMaster          =  TAB_MASTER;
	$indMaster          = 'id';
	
	$campoId = '';
    $DESC    ='DESC';
	$_id   = '';
    $_ene = 0;
    $_feb = 0;
    $_mar = 0;
    $_abr = 0;
    $_may = 0;
    $_jun = 0;
    $_jul = 0;
    $_ago = 0;
    $_sep = 0;
    $_oct = 0;
    $_nov = 0;
    $_dic = 0;
    
	
	if (isset($_POST['id'])) { require_once(PATH_INCLUDES.'tab@guardar.php'); } 
	
	if (isset($_GET['ope'])) { $operacion = $_GET['ope']; }  else { $operacion =''; }
	
    switch ($operacion) {
		case 'Update': 	$campoId  = $_GET['IdRegistro'];  
						$consulta = "SELECT *   FROM `$tablaDB` WHERE `$IdCampo` = $campoId ";
						$salida   = $conexionDB->consulta($consulta);
						$row = mysql_fetch_array($salida);
						$_id  = $row ['id'];
                        $_ene = $row ['uf_01'];
                        $_feb = $row ['uf_02'];
                        $_mar = $row ['uf_03'];
                        $_abr = $row ['uf_04'];
                        $_may = $row ['uf_05'];
                        $_jun = $row ['uf_06'];
                        $_jul = $row ['uf_07'];
                        $_ago = $row ['uf_08'];
                        $_sep = $row ['uf_09'];
                        $_oct = $row ['uf_10'];
                        $_nov = $row ['uf_11'];
                        $_dic = $row ['uf_12'];
                       	break;
						
		case 'Delete': 	$locationFile = $_SERVER['SCRIPT_NAME'];
						$errorMensaje = "ERROR_borrarTabUf";
						require_once(PATH_INCLUDES.'tab@borrar.php');  
						break;
	}

	/* :::::::::::::::::::::::::::::::::::::::::::: INICIO Lee Grilla ::::::::::::::::::::::::::::::::::::: */
	$titulos[1] = array("tit" => $sistema_txt->GetDefinition('aaaa'),    "ancho" => "span-8 centro enc_mensajes");
    
    $fila[1] = array("campo" => "$IdCampo",   "ancho" => "span-3", "pre" => 'AÑO ', "post" => "", "utf8" => true);
   
   
	require_once(PATH_INCLUDES.'tab@lee.php');
	/* :::::::::::::::::::::::::::::::::::::::::::: FIN Lee Grilla :::::::::::::::::::::::::::::::::::::::: */
	
	
	$contenido=new plantilla("comunUf");
	$contenido->asigna_variables(
			array(
			"lang"  => $sistema_txt->GetDefinition('XMLLang'),
			"barra_pie_mensajes"  => date('d-m-Y'),
			"lateral"			  => $lateral,
            "topbar"		      => $topbar,
			"H2Sistema"			  => $sistema_txt->GetDefinition('H2Sistema'),
            "H2Titulo"		      => $sistema_txt->GetDefinition('H2Uf'),
            "TInforme"		      => $sistema_txt->GetDefinition('TINFUf'),
            "salir"		          => $sistema_txt->GetDefinition('salir'),
            "buscarpor"		      => $sistema_txt->GetDefinition('buscarpor'),
            "dBase"               => $dBase,
            "tablaDB"             => $tablaDB,
			"phpFile"		 	  => $phpFile,
			"IdCampo"		 	  => $IdCampo,
            "campoOrd"            => $orderBy,
			
			"id"          => $sistema_txt->GetDefinition('id'),
            "ene"         => $sistema_txt->GetDefinition('ene'),
            "feb"         => $sistema_txt->GetDefinition('feb'),
            "mar"         => $sistema_txt->GetDefinition('mar'),
            "abr"         => $sistema_txt->GetDefinition('abr'),
            "may"         => $sistema_txt->GetDefinition('may'),
            "jun"         => $sistema_txt->GetDefinition('jun'),
            "jul"         => $sistema_txt->GetDefinition('jul'),
            "ago"         => $sistema_txt->GetDefinition('ago'),
            "sep"         => $sistema_txt->GetDefinition('sep'),
            "oct"         => $sistema_txt->GetDefinition('oct'),
            "nov"         => $sistema_txt->GetDefinition('nov'),
            "dic"         => $sistema_txt->GetDefinition('dic'),
            "uf"          => $sistema_txt->GetDefinition('uf'),
            "borrar"      => $sistema_txt->GetDefinition('borrar'),
			"enviar"      => $sistema_txt->GetDefinition('enviar'),
			"limpiar"     => $sistema_txt->GetDefinition('limpiar'),
			"tablaHTML"	  => $tablaHTML,

			"campoId"  => $campoId,   
		    "_ene"     => $_ene,
            "_feb"     => $_feb,
            "_mar"     => $_mar,
            "_abr"     => $_abr,
            "_may"     => $_may,
            "_jun"     => $_jun,
            "_jul"     => $_jul,
            "_ago"     => $_ago,
            "_sep"     => $_sep,
            "_oct"     => $_oct,
            "_nov"     => $_nov,
            "_dic"     => $_dic
            
            ));
			
	echo $contenido->muestra();
?>
