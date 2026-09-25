<?php
require_once('../includes/initSistema.php');

$DB_DCODE = 'adm_dCode';
$conexionDB = new DB_MySQLi;
$conexionDB->conectar($DB_DCODE, DB_SERVER, DB_USER, DB_PASSWD);
$html = static function ($valor): string { return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); };
$esAdministrativo = strtoupper(trim((string) ($_SESSION['tipo'] ?? ''))) === 'A';
$campos = array('rut','cliente','estadoCliente','administrador1','emailAdm1','administrador2','emailAdm2','administrador3','emailAdm3','inicioContrato','venctoContrato','montoMensual','contactoAdmin','observContrato','contactoCobranza','emailCobranza','telefonoCobranza','estadoContactoCobranza','contactoCobranza2','emailCobranza2','telefonoCobranza2','estadoContactoCobranza2','contactoCobranza3','emailCobranza3','telefonoCobranza3','estadoContactoCobranza3','licencia','vencimientoLic','estadoLic','tipoLic','m','e','s','c','prefijoBD');
$camposAdministrativo = array('rut','cliente','estadoCliente','inicioContrato','venctoContrato','montoMensual','contactoAdmin','observContrato','contactoCobranza','emailCobranza','telefonoCobranza','estadoContactoCobranza','contactoCobranza2','emailCobranza2','telefonoCobranza2','estadoContactoCobranza2','contactoCobranza3','emailCobranza3','telefonoCobranza3','estadoContactoCobranza3','prefijoBD');
$camposEditables = $esAdministrativo ? $camposAdministrativo : $campos;
$datos = array_fill_keys($campos, '');
$datos = array_merge($datos, array('estadoCliente' => 'ACTIVO', 'estadoContactoCobranza' => 'ACTIVO', 'estadoContactoCobranza2' => 'ACTIVO', 'estadoContactoCobranza3' => 'ACTIVO', 'estadoLic' => 'A', 'tipoLic' => 'C', 'm' => 'S', 'e' => 'N', 's' => 'N', 'c' => 'S'));
$idCliente = $_POST['idCliente'] ?? $_GET['IdRegistro'] ?? 'new';
$mensajeError = '';
$mensajeExito = isset($_GET['guardado']) ? 'Cliente guardado correctamente.' : '';

// The administrative profile can edit a limited subset without altering technical data.
$registroExistente = null;
if ($idCliente !== 'new' && ctype_digit((string) $idCliente)) {
    $salidaExistente = $conexionDB->consulta("SELECT * FROM `$DB_DCODE`.adm_clientes WHERE idCliente = " . (int) $idCliente . ' LIMIT 1');
    $registroExistente = mysqli_fetch_assoc($salidaExistente) ?: null;
    if ($registroExistente) {
        foreach ($campos as $campo) {
            $datos[$campo] = $registroExistente[$campo] ?? $datos[$campo];
        }
        $modulos = (string) ($registroExistente['modulos'] ?? '');
    }
}

// Sólo se muestran sistemas vigentes. Los códigos no vigentes se conservan al guardar.
$sistemasPorArea = array('M' => array(), 'S' => array(), 'E' => array(), 'A' => array());
$idsVigentes = array();
$salidaSistemas = $conexionDB->consulta("SELECT id, nombre, area FROM `$DB_DCODE`.adm_sistemas WHERE estado = 'S' ORDER BY area, nombre");
while ($sistema = mysqli_fetch_assoc($salidaSistemas)) {
    if (isset($sistemasPorArea[$sistema['area']])) {
        $sistemasPorArea[$sistema['area']][] = $sistema;
        $idsVigentes[(string) (int) $sistema['id']] = true;
    }
}

if (isset($_POST['guardarCliente'])) {
    foreach ($camposEditables as $campo) {
        $datos[$campo] = trim((string) ($_POST[$campo] ?? ''));
    }
    if ($esAdministrativo) {
        $modulos = (string) ($registroExistente['modulos'] ?? '');
    } else {
        $seleccionados = $_POST['sistemas'] ?? array();
        $seleccionados = is_array($seleccionados) ? $seleccionados : array();
        $seleccionados = array_values(array_unique(array_filter($seleccionados, static function ($id) use ($idsVigentes): bool { return ctype_digit((string) $id) && isset($idsVigentes[(string) (int) $id]); })));
        sort($seleccionados, SORT_NUMERIC);
        $modulosVigentes = implode('', array_map(static function ($id): string { return str_pad((string) (int) $id, 3, '0', STR_PAD_LEFT); }, $seleccionados));
        $modulosNoVigentes = preg_replace('/[^0-9]/', '', (string) ($_POST['modulosNoVigentes'] ?? ''));
        if (strlen($modulosNoVigentes) % 3 !== 0) { $modulosNoVigentes = ''; }
        $modulos = $modulosVigentes . $modulosNoVigentes;
    }

    if ($datos['cliente'] === '' || $datos['rut'] === '') {
        $mensajeError = 'El RUT y el nombre del cliente son obligatorios.';
    } elseif (!in_array($datos['estadoCliente'], array('ACTIVO', 'INACTIVO'), true)) {
        $mensajeError = 'El estado del cliente no es válido.';
    } elseif ($datos['emailCobranza'] !== '' && !filter_var($datos['emailCobranza'], FILTER_VALIDATE_EMAIL)) {
        $mensajeError = 'El correo del contacto de cobranza 1 no es válido.';
    } elseif ($datos['emailCobranza2'] !== '' && !filter_var($datos['emailCobranza2'], FILTER_VALIDATE_EMAIL)) {
        $mensajeError = 'El correo del contacto de cobranza 2 no es válido.';
    } elseif ($datos['emailCobranza3'] !== '' && !filter_var($datos['emailCobranza3'], FILTER_VALIDATE_EMAIL)) {
        $mensajeError = 'El correo del contacto de cobranza 3 no es válido.';
    } elseif (!in_array($datos['estadoContactoCobranza'], array('ACTIVO', 'INACTIVO'), true)
        || !in_array($datos['estadoContactoCobranza2'], array('ACTIVO', 'INACTIVO'), true)
        || !in_array($datos['estadoContactoCobranza3'], array('ACTIVO', 'INACTIVO'), true)) {
        $mensajeError = 'El estado de un contacto de cobranza no es válido.';
    } elseif ($idCliente !== 'new' && !ctype_digit((string) $idCliente)) {
        $mensajeError = 'El identificador del cliente no es válido.';
    } elseif (empty($_SESSION['save']) && !$esAdministrativo) {
        $mensajeError = 'No dispone de permisos para guardar clientes.';
    }
    if ($mensajeError === '') {
        try {
            $pdo = new PDO('mysql:host=' . DB_SERVER . ';dbname=' . $DB_DCODE . ';charset=latin1', DB_USER, DB_PASSWD, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
            $columnas = array();
            foreach ($pdo->query('SHOW COLUMNS FROM adm_clientes') as $columna) { $columnas[$columna['Field']] = true; }
            $guardar = array();
            foreach ($camposEditables as $campo) {
                if (isset($columnas[$campo])) {
                    $guardar[$campo] = $datos[$campo];
                }
            }
            // HTML date inputs submit an empty value for legacy 0000-00-00 dates.
            // Store NULL instead so an unrelated update, such as the client state, is not rejected.
            foreach (array('inicioContrato', 'venctoContrato', 'vencimientoLic') as $campoFecha) {
                if (array_key_exists($campoFecha, $guardar)
                    && ($guardar[$campoFecha] === '' || $guardar[$campoFecha] === '0000-00-00')) {
                    $guardar[$campoFecha] = null;
                }
            }
            if (!$esAdministrativo && isset($columnas['modulos'])) {
                $guardar['modulos'] = $modulos;
            }
            if ($idCliente === 'new') {
                $nombres = array_keys($guardar);
                $stmt = $pdo->prepare('INSERT INTO adm_clientes (' . implode(', ', $nombres) . ') VALUES (' . implode(', ', array_fill(0, count($nombres), '?')) . ')');
                $stmt->execute(array_values($guardar));
                $idCliente = (string) $pdo->lastInsertId();
            } else {
                $nombres = array_keys($guardar);
                $asignaciones = implode(', ', array_map(static function ($campo): string { return "$campo = ?"; }, $nombres));
                $stmt = $pdo->prepare("UPDATE adm_clientes SET $asignaciones WHERE idCliente = ?");
                $stmt->execute(array_merge(array_values($guardar), array((int) $idCliente)));
            }
            header('Location: clientes_ficha.php?IdRegistro=' . rawurlencode($idCliente) . '&guardado=1');
            exit;
        } catch (Throwable $e) {
            $mensajeError = 'No fue posible guardar el cliente. Verifique los datos e intente nuevamente.';
        }
    }
} elseif ($idCliente !== 'new') {
    if (!ctype_digit((string) $idCliente)) { header('Location: clientes.php'); exit; }
    $salidaCliente = $conexionDB->consulta("SELECT * FROM `$DB_DCODE`.adm_clientes WHERE idCliente = " . (int) $idCliente . ' LIMIT 1');
    $registro = mysqli_fetch_assoc($salidaCliente);
    if (!$registro) { header('Location: clientes.php'); exit; }
    foreach ($campos as $campo) { $datos[$campo] = $registro[$campo] ?? $datos[$campo]; }
    $modulos = (string) ($registro['modulos'] ?? '');
}
$modulos = $modulos ?? '';
$contratados = array(); $modulosNoVigentes = '';
for ($pos = 0; $pos < strlen($modulos); $pos += 3) {
    $codigo = substr($modulos, $pos, 3);
    if (!ctype_digit($codigo)) { continue; }
    $idSistema = (string) (int) $codigo;
    if (isset($idsVigentes[$idSistema])) { $contratados[$idSistema] = true; } else { $modulosNoVigentes .= $codigo; }
}
$nombresArea = array('M' => 'Municipal', 'S' => 'Salud', 'E' => 'Educación', 'A' => 'Administración');
$sistemasHTML = '';
foreach ($sistemasPorArea as $area => $sistemas) {
    $sistemasHTML .= '<fieldset class="clientes-sistemas__grupo"><legend>' . $nombresArea[$area] . '</legend><div class="clientes-sistemas__lista">';
    foreach ($sistemas as $sistema) {
        $idSistema = (string) (int) $sistema['id'];
        $marcado = isset($contratados[$idSistema]) ? ' checked' : '';
        $sistemasHTML .= '<label class="clientes-sistemas__item"><input type="checkbox" name="sistemas[]" value="' . $html($idSistema) . '"' . $marcado . '> <span>(' . $html($sistema['id']) . ') ' . $html($sistema['nombre']) . '</span></label>';
    }
    if (!$sistemas) { $sistemasHTML .= '<span class="clientes-sistemas__vacio">Sin sistemas vigentes.</span>'; }
    $sistemasHTML .= '</div></fieldset>';
}
$seleccion = static function (string $valor, string $actual): string { return $valor === $actual ? 'selected' : ''; };
$contenido = new plantilla('clientes_ficha');
$contenido->asigna_variables(array(
    'lang' => $sistema_txt->GetDefinition('XMLLang'), 'icono' => PATH_ICO . ICONO_NAVEGADOR, 'topbar' => $topbar, 'barraLateral' => $barraLateral, 'headerMenu' => $headerMenu,
    'H2Sistema' => $sistema_txt->GetDefinition('H2Sistema'), 'H2Titulo' => $idCliente === 'new' ? 'Nuevo cliente' : 'Editar cliente',
    'idCliente' => $html($idCliente), 'rut' => $html($datos['rut']), 'cliente' => $html($datos['cliente']), 'prefijoBD' => $html($datos['prefijoBD']),
    'estadoClienteActivo' => $seleccion('ACTIVO', (string) $datos['estadoCliente']), 'estadoClienteInactivo' => $seleccion('INACTIVO', (string) $datos['estadoCliente']),
    'administrador1' => $html($datos['administrador1']), 'emailAdm1' => $html($datos['emailAdm1']), 'administrador2' => $html($datos['administrador2']), 'emailAdm2' => $html($datos['emailAdm2']), 'administrador3' => $html($datos['administrador3']), 'emailAdm3' => $html($datos['emailAdm3']),
    'inicioContrato' => $html($datos['inicioContrato']), 'venctoContrato' => $html($datos['venctoContrato']), 'montoMensual' => $html($datos['montoMensual']), 'contactoAdmin' => $html($datos['contactoAdmin']), 'observContrato' => $html($datos['observContrato']),
    'contactoCobranza' => $html($datos['contactoCobranza']), 'emailCobranza' => $html($datos['emailCobranza']), 'telefonoCobranza' => $html($datos['telefonoCobranza']),
    'estadoContactoCobranzaActivo' => $seleccion('ACTIVO', (string) $datos['estadoContactoCobranza']), 'estadoContactoCobranzaInactivo' => $seleccion('INACTIVO', (string) $datos['estadoContactoCobranza']),
    'contactoCobranza2' => $html($datos['contactoCobranza2']), 'emailCobranza2' => $html($datos['emailCobranza2']), 'telefonoCobranza2' => $html($datos['telefonoCobranza2']),
    'estadoContactoCobranza2Activo' => $seleccion('ACTIVO', (string) $datos['estadoContactoCobranza2']), 'estadoContactoCobranza2Inactivo' => $seleccion('INACTIVO', (string) $datos['estadoContactoCobranza2']),
    'contactoCobranza3' => $html($datos['contactoCobranza3']), 'emailCobranza3' => $html($datos['emailCobranza3']), 'telefonoCobranza3' => $html($datos['telefonoCobranza3']),
    'estadoContactoCobranza3Activo' => $seleccion('ACTIVO', (string) $datos['estadoContactoCobranza3']), 'estadoContactoCobranza3Inactivo' => $seleccion('INACTIVO', (string) $datos['estadoContactoCobranza3']),
    'licencia' => $html($datos['licencia']), 'vencimientoLic' => $html($datos['vencimientoLic']), 'modulos' => $html($modulos), 'modulosNoVigentes' => $html($modulosNoVigentes), 'sistemasHTML' => $sistemasHTML,
    'estadoLicA' => $seleccion('A', (string) $datos['estadoLic']), 'estadoLicD' => $seleccion('D', (string) $datos['estadoLic']), 'tipoLicC' => $seleccion('C', (string) $datos['tipoLic']), 'tipoLicA' => $seleccion('A', (string) $datos['tipoLic']),
    'mSi' => $seleccion('S', (string) $datos['m']), 'mNo' => $seleccion('N', (string) $datos['m']), 'sSi' => $seleccion('S', (string) $datos['s']), 'sNo' => $seleccion('N', (string) $datos['s']), 'eSi' => $seleccion('S', (string) $datos['e']), 'eNo' => $seleccion('N', (string) $datos['e']), 'cSi' => $seleccion('S', (string) $datos['c']), 'cNo' => $seleccion('N', (string) $datos['c']),
    'mensajeError' => $html($mensajeError), 'mensajeExito' => $html($mensajeExito), 'urlLicencia' => ctype_digit((string) $idCliente) ? 'licencia_calculo.php?idCliente=' . rawurlencode((string) $idCliente) : '#',
    'mostrarGestionTecnica' => $esAdministrativo ? 'none' : 'block'
));
echo $contenido->muestra();
?>
