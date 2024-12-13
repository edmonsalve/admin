<?php 
    require_once("../../includes/crypt.php");
    require_once('../../defines/variables_path.php');
	require_once('../includes/initSistema.php');

    if (isset($_GET['sisId'])) {
        $idSistema = $_GET['sisId'];
        $where  = "WHERE idsistema = $idSistema";
        $serial = $_GET['serial']; 
        } else {      
        $where     = "";
        $idSistema = 0;
    } 
    
    /* :::::::::::::::::::::::::::::: Conexion Base ReD-M  ::::::::::::::::::::::::::::::::::::: */
	$conexionDB = new DB_MySQLi;
	$conexionDB->conectar(DB_SISTEMA, DB_SERVER, DB_USER, DB_PASSWD );
    
    
    $serial   = $_GET['serial'];
    $consulta = "SELECT *   FROM `db_@`.`licencias`
                            WHERE `key` = '$serial'";
                            
	$salida   = $conexionDB->consulta($consulta);
    $row      = mysqli_fetch_array($salida);
    
    $url       = $row['url'];
    $puertoSQL = $row['puertoSQL'];
    $pass      = $row['passSQL'];

    // define('REMOTE_BASE', 'red_@admin');
    define('REMOTE_SERVER', $url.":".$puertoSQL );
    define('REMOTE_USER',  'root');
    define('REMOTE_PASSWD', $pass);
 
    $sistemasHTML = "";
    $consulta  = "SELECT * FROM `db_@admin`.`adm_sistemas`";
    $salida    = $conexionDB->consulta($consulta);
    while ($row = mysqli_fetch_array($salida)) {
        $sistemaTxt = $row['nombre'];
        $sistemaId  = $row['id'];
        
        if ($idSistema == $sistemaId) { $select = "selected='selected'"; } else { $select=""; }
        $sistemasHTML .= "<option value='$sistemaId' $select>$sistemaTxt</option>";
    }
    
    
    
    /* :::::::::::::::::::::::::::::: Conexion Base Remota  ::::::::::::::::::::::::::::::::::::: */
	$conexionDBRemota = new DB_MySQLi;
	$conexionDBRemota->conectar(REMOTE_BASE, REMOTE_SERVER, REMOTE_USER, REMOTE_PASSWD );
    
    
    
	/* :::::::::::::::::::::::::::::: Lee Modulos Base ReD-M  ::::::::::::::::::::::::::::::::::::: */
    $consulta = "SELECT * FROM `adm_modulos` 
                                $where
                                ORDER BY id";
    
    $salida   = $conexionDB->consulta($consulta);
    
    $contador = 1;                            
    $modulosHTML  = '<div class="span-11 last" style="float:none; height:435px; overflow:auto; ">'; 
    while ($row = mysqli_fetch_array($salida)) {
        if (($contador % 2) == 1) { $clase='fila_impar'; } else { $clase='fila_par'; } 
		$contador++;
        
        $idModulo   = $row['id'];
        $idSistema  = $row['idsistema'];
        $modulo     = $row['modulo'];
        $php        = $row['php'];
        $parametros = $row['parametros'];
        $tipo       = $row['tipo'];
        $target     = $row['target'];
        $estado     = $row['estado'];
        $sistemaVer = $row['sistemaVer'];
        $movil      = $row['movil'];
        
        $color = "white";

        $consultaModulo = "SELECT * FROM `adm_modulos` 
                                    WHERE id = $idModulo";
                                    
        
        $salidaMod = $conexionDBRemota->consulta($consultaModulo);
        if (mysqli_num_rows($salidaMod) == 1) { $color = "GreenYellow"; } else { $color = "RoyalBlue"; }
                                    
        $modulosHTML .= "<div class='span-10 $clase'  >";
        $modulosHTML .= "
                <div class='span-8' >$tipo - $php</div>
                <div class='span-2 derecha last' style='background-color:$color;' >$idModulo</div>
        ";
        $modulosHTML .= "</div>";
        
	}
    $modulosHTML .= "</div>";
	
    
    
    
    /* :::::::::::::::::::::::::::::: Lee Modulos Base Remota  ::::::::::::::::::::::::::::::::::::: */
    $consulta = "SELECT * FROM `adm_modulos` 
                                $where
                                ORDER BY id";
    
    $salida   = $conexionDBRemota->consulta($consulta);
    
    $contador = 1; 
	$modulosCliHTML  = '<div class="span-11" style="float:none; height:435px; overflow:auto; ">'; 
    while ($row = mysql_fetch_array($salida)) {
        if (($contador % 2) == 1) { $clase='fila_impar'; } else { $clase='fila_par'; } 
		$contador++;
        
        $idModulo   = $row['id'];
        $idSistema  = $row['idsistema'];
        $modulo     = $row['modulo'];
        $php        = $row['php'];
        $parametros = $row['parametros'];
        $tipo       = $row['tipo'];
        $target     = $row['target'];
        $estado     = $row['estado'];
        $sistemaVer = $row['sistemaVer'];
        $movil      = $row['movil'];
        $color = "white";
        

        $consultaModulo = "SELECT * FROM `adm_modulos` 
                                    WHERE id = $idModulo";
                                    
        
        $salidaMod = $conexionDB->consulta($consultaModulo);
        if (mysql_num_rows($salidaMod) == 1) { $color = "GreenYellow"; } else { $color = "Red"; }
                                    
        $modulosCliHTML .= "<div class='span-10 $clase' >";
        $modulosCliHTML .= "
                <div class='span-8' >$tipo - $php</div>
                <div class='span-2 derecha last' style='background-color:$color;' >$idModulo</div>
        ";
        $modulosCliHTML .= "</div>";
        
	}
    $modulosCliHTML .= "</div>";
    
	$contenido=new plantilla("clientes_modulos");
	$contenido->asigna_variables(
			array(
			"lang"  => $sistema_txt->GetDefinition('XMLLang'),
			"barra_pie_mensajes"  => date('d-m-Y'),
            "topbar"		      => $topbar,
			"lateral"			  => $lateral,
			"H2Sistema"			  => $sistema_txt->GetDefinition('H2Sistema'),
			"H2Modulo"	          => 'Chequea Modulos',
			
			"cerrarSesion"        => $sistema_txt->GetDefinition('cerrarSesion'),
			"buscarpor"        	  => $sistema_txt->GetDefinition('buscarpor'),
			"borrar"        	  => $sistema_txt->GetDefinition('borrar'),
            "salir"        	      => $red_text->GetDefinition('salir'),
            
			"modulosHTML"       => $modulosHTML,
            "modulosCliHTML"    => $modulosCliHTML,
			"sistemasHTML"      => $sistemasHTML,
            
            "serial"            => $serial,
			));
			
	echo $contenido->muestra();
?>
