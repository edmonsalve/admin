<?php
    // $keynumber    = $_SESSION['keynumber'];
    $nsistema	  = isset($_SESSION['nsistema']) ? $_SESSION['nsistema'] : 0;
    $VERSION_SIS  = VERSION_SIS;

	$_user  	  = $_SESSION['idUser'];
	$_usrAdmin	  = $_SESSION['usrAdmin'];
	$tipoUsuario  = strtoupper(trim((string) ($_SESSION['tipo'] ?? '')));
	$nombreUSR	  = $_SESSION['nomUsuario'];
	$_ip		  = $_SERVER['REMOTE_ADDR'];

	$_codCliente  = COD_CLIENTE; 
	$LINK_SOPORTE = LINK_SOPORTE;
	$LOGO_CLIENTE = LOGO_CLIENTE;	

	$DB_PREFIJO   =  str_replace('_', '', DB_PREFIJO);
	$paginaActual = basename(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH));
	$seccionActiva = 'sistemas';
	if (strpos($paginaActual, 'clientes') === 0 || strpos($paginaActual, 'licencia_calculo') === 0) {
		$seccionActiva = 'clientes';
	} elseif (strpos($paginaActual, 'facturas') === 0) {
		$seccionActiva = 'facturas';
	} elseif (strpos($paginaActual, 'cobranzas') === 0) {
		$seccionActiva = 'cobranzas';
	} elseif (strpos($paginaActual, 'usuarios') === 0) {
		$seccionActiva = 'usuarios';
	} elseif (strpos($paginaActual, 'servidores') === 0) {
		$seccionActiva = 'servidores';
	} elseif (strpos($paginaActual, 'sincronizar') === 0) {
		$seccionActiva = 'sincronizar';
	} elseif (strpos($paginaActual, 'comparar_estructuras') === 0) {
		$seccionActiva = 'comparar_estructuras';
	} elseif (strpos($paginaActual, 'bitacora_desarrollo') === 0) {
		$seccionActiva = 'bitacora';
	}

	// Punto único para agregar los futuros mantenedores de esta aplicación.
	$menuMantenimiento = array(
		'clientes' => array('texto' => 'Clientes', 'url' => '/appMantenimiento/modulos/clientes.php'),
		'facturas' => array('texto' => 'Facturas', 'url' => '/appMantenimiento/modulos/facturas.php'),
		'cobranzas' => array('texto' => 'Cobranzas', 'url' => '/appMantenimiento/modulos/cobranzas.php'),
		'usuarios' => array('texto' => 'Usuarios', 'url' => '/appMantenimiento/modulos/usuarios.php'),
		'sistemas' => array('texto' => 'Sistemas', 'url' => '/appMantenimiento/modulos/sistemas.php'),
		'servidores' => array('texto' => 'Servidores', 'url' => '/appMantenimiento/modulos/servidores.php'),
		'sincronizar' => array('texto' => 'Sincronización', 'url' => '/appMantenimiento/modulos/sincronizar.php'),
		'comparar_estructuras' => array('texto' => 'Comparar estructuras', 'url' => '/appMantenimiento/modulos/comparar_estructuras.php'),
		'bitacora' => array('texto' => 'Bitácora', 'url' => '/appMantenimiento/modulos/bitacora_desarrollo.php')
	);
	$menuPorPerfil = array(
		'D' => array('clientes', 'facturas', 'cobranzas', 'usuarios', 'sistemas', 'servidores', 'sincronizar', 'comparar_estructuras', 'bitacora'),
		'T' => array('sistemas', 'servidores', 'sincronizar', 'comparar_estructuras', 'bitacora'),
		'A' => array('clientes', 'facturas', 'cobranzas'),
	);
	$seccionesPermitidas = $menuPorPerfil[$tipoUsuario] ?? array();
	$menuMantenimientoHtml = '';
	foreach ($menuMantenimiento as $idMenu => $itemMenu) {
		if (!in_array($idMenu, $seccionesPermitidas, true)) { continue; }
		$claseActiva = $idMenu === $seccionActiva ? ' superior__menu-enlace--activo' : '';
		$textoMenu = htmlspecialchars($itemMenu['texto'], ENT_QUOTES, 'UTF-8');
		$urlMenu = htmlspecialchars($itemMenu['url'], ENT_QUOTES, 'UTF-8');
		$menuMantenimientoHtml .= "<a class='superior__menu-enlace$claseActiva' href='$urlMenu'>$textoMenu</a>";
	}
	
	
	$_ndia  = date('d');
	$_naaaa = date('Y');

	$_dia 		= $dias[date('w')];
	$_mes 		= $meses[date('n')];
    $__fecha 	= "$_dia, $_ndia de $_mes $_naaaa";

    // :::::::::::::::::::::::::::::::::::::  Barra superior ::::::::::::::::::::::::::::::::::::::: //
	$nombreUsuarioHtml = htmlspecialchars($nombreUSR, ENT_QUOTES, 'UTF-8');
	$fechaHtml = htmlspecialchars($__fecha, ENT_QUOTES, 'UTF-8');
	$topbar = "
	<header class='header' id='toolsbar'>
		<div class='superior'>
			<div class='superior__izquierda'>
				<a class='superior__marca' href='/appMantenimiento/index_main.php' aria-label='Ir al inicio de Mantenimiento'>
					<img class='superior__logo-principal' src='/images/m_BV.png' alt='dMuni'>
				</a>
				<nav class='superior__menu' aria-label='Módulos de mantenimiento'>$menuMantenimientoHtml</nav>
			</div>
			<div class='superior__derecha'>
				<div class='superior__contexto'>
					<img class='superior__logo-cliente' src='/imagesCli/$LOGO_CLIENTE' alt='Cliente'>
					<span class='superior__usuario'>$nombreUsuarioHtml</span>
					<span class='superior__entorno'>DB:$DB_PREFIJO</span>
					<span class='superior__fecha'>$fechaHtml</span>
				</div>
				<nav class='superior__acciones' aria-label='Acciones de usuario'>
					<a class='superior__accion' href='/appUtilidades/modulos/cambioClave.php' target='_top' title='Configuración de cuenta' aria-label='Configuración de cuenta'>
						<img src='/btns/btn_admin.png' alt=''>
					</a>
					<a class='superior__accion' href='/00_Manual/index.html' target='_blank' title='Manuales' aria-label='Abrir manuales'>
						<img src='/btns/btn_manual.png' alt=''>
					</a>
					<a class='superior__accion' href='$LINK_SOPORTE?usr=$_user&cliente=$_codCliente&tt=tc' target='_blank' title='Soporte' aria-label='Abrir soporte'>
						<img src='/btns/btn_soporte.png' alt=''>
					</a>
					<a class='superior__accion superior__accion--salir' href='/logout.php' target='_top' title='Cerrar sesión' aria-label='Cerrar sesión'>
						<img src='/btns/btn_exit.png' alt=''>
					</a>
				</nav>
			</div>
		</div>
	</header>
	<script src='/appMantenimiento/js/sesion_activa.js'></script>";
?>
