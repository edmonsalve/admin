<?php
require_once('../includes/initSistema.php');

$DB_DCODE = 'adm_dCode';
$tiposCambio = array(
    'MEJORA' => 'Mejora',
    'CORRECCION' => 'Corrección de error',
    'NUEVO' => 'Nueva funcionalidad',
    'TECNICO' => 'Cambio técnico',
);
$tiposAccion = array(
    'AVANCE' => 'Avance',
    'CORRECCION' => 'Corrección',
    'VALIDACION' => 'Validación',
    'DESPLIEGUE' => 'Despliegue',
    'OTRO' => 'Otro',
);
$estadosEntrada = array(
    'ABIERTO' => 'Abierto',
    'PAUSADO' => 'Pausado',
    'CERRADO' => 'Cerrado',
    'DESCARTADO' => 'Descartado',
);
$prioridadesEntrada = array(
    'URGENTE' => 'Urgente',
    'ALTO' => 'Alto',
    'MEDIO' => 'Medio',
    'BAJO' => 'Bajo',
);

// Helpers used for all text emitted in the template and date filters.
$html = static function ($valor): string {
    return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};
$fechaValida = static function (string $fecha): bool {
    $fechaObjeto = DateTime::createFromFormat('Y-m-d', $fecha);
    return $fechaObjeto && $fechaObjeto->format('Y-m-d') === $fecha;
};

$pdo = new PDO(
    'mysql:host=' . DB_SERVER . ';dbname=' . $DB_DCODE . ';charset=utf8mb4',
    DB_USER,
    DB_PASSWD,
    array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
);

// Los responsables de la bitácora deben ser usuarios técnicos autorizados.
$usuariosResponsables = $pdo->query("SELECT username, nombre FROM adm_users WHERE estado = 'A' AND tipoUser IN ('D', 'T') AND TRIM(nombre) <> '' ORDER BY nombre")
    ->fetchAll(PDO::FETCH_ASSOC);
$responsablesAutorizados = array();
foreach ($usuariosResponsables as $usuarioResponsable) {
    $responsablesAutorizados[(string) $usuarioResponsable['nombre']] = true;
}

// Catalogues used by the form and filter bar.
$sistemas = $pdo->query("SELECT id, nombre FROM adm_sistemas WHERE area = 'M' ORDER BY nombre")
    ->fetchAll(PDO::FETCH_ASSOC);
$sistemasPorId = array();
foreach ($sistemas as $sistema) {
    $sistemasPorId[(int) $sistema['id']] = $sistema;
}
$modulos = $pdo->query("SELECT id, idsistema, modulo
    FROM adm_modulos
    WHERE idsistema IN (SELECT id FROM adm_sistemas WHERE area = 'M')
    ORDER BY idsistema, modulo")
    ->fetchAll(PDO::FETCH_ASSOC);
$modulosPorId = array();
foreach ($modulos as $modulo) {
    $modulosPorId[(int) $modulo['id']] = $modulo;
}

$registro = array(
    'idBitacora' => '',
    'fechaCambio' => date('Y-m-d'),
    'idSistema' => '',
    'idModulo' => '',
    'tipoCambio' => 'MEJORA',
    'estado' => 'ABIERTO',
    'prioridad' => 'MEDIO',
    'version' => '',
    'titulo' => '',
    'detalle' => '',
    'responsable' => trim((string) ($_SESSION['nomUsuario'] ?? $_SESSION['idUser'] ?? '')),
);
if (!isset($responsablesAutorizados[$registro['responsable']])) {
    $registro['responsable'] = '';
}
$mensajeError = '';
$mensajeExito = isset($_GET['guardado'])
    ? 'El registro fue guardado correctamente.'
    : '';

if (isset($_POST['guardarBitacora'])) {
    foreach (array_keys($registro) as $campo) {
        if ($campo !== 'idBitacora') {
            $registro[$campo] = trim((string) ($_POST[$campo] ?? ''));
        }
    }
    $registro['idBitacora'] = trim((string) ($_POST['idBitacora'] ?? ''));
    $idSistema = $registro['idSistema'] === ''
        ? null
        : (ctype_digit($registro['idSistema']) ? (int) $registro['idSistema'] : -1);
    $idModulo = $registro['idModulo'] === ''
        ? null
        : (ctype_digit($registro['idModulo']) ? (int) $registro['idModulo'] : -1);

    if (!$fechaValida($registro['fechaCambio'])) {
        $mensajeError = 'La fecha del cambio no es válida.';
    } elseif (!array_key_exists($registro['tipoCambio'], $tiposCambio)) {
        $mensajeError = 'Seleccione un tipo de cambio válido.';
    } elseif (!array_key_exists($registro['estado'], $estadosEntrada)) {
        $mensajeError = 'Seleccione un estado válido.';
    } elseif (!array_key_exists($registro['prioridad'], $prioridadesEntrada)) {
        $mensajeError = 'Seleccione una prioridad válida.';
    } elseif ($idSistema !== null && !isset($sistemasPorId[$idSistema])) {
        $mensajeError = 'Seleccione un sistema municipal válido.';
    } elseif ($idModulo !== null && ($idSistema === null || !isset($modulosPorId[$idModulo]) || (int) $modulosPorId[$idModulo]['idsistema'] !== $idSistema)) {
        $mensajeError = 'El módulo seleccionado no corresponde al sistema indicado.';
    } elseif (mb_strlen($registro['titulo']) < 3 || mb_strlen($registro['titulo']) > 160) {
        $mensajeError = 'El título debe tener entre 3 y 160 caracteres.';
    } elseif (mb_strlen($registro['detalle']) < 10) {
        $mensajeError = 'El detalle debe describir el cambio en al menos 10 caracteres.';
    } elseif (!isset($responsablesAutorizados[$registro['responsable']])) {
        $mensajeError = 'Seleccione un responsable autorizado.';
    } elseif (mb_strlen($registro['detalle']) > 10000 || mb_strlen($registro['version']) > 40 || mb_strlen($registro['responsable']) > 100) {
        $mensajeError = 'Revise la extensión de los datos ingresados.';
    } elseif ($registro['idBitacora'] !== '' && !ctype_digit($registro['idBitacora'])) {
        $mensajeError = 'El registro de bitácora no es válido.';
    }

    if ($mensajeError === '') {
        $idRegistroGuardado = 0;
        if ($registro['idBitacora'] === '') {
            $sentencia = $pdo->prepare('INSERT INTO adm_bitacora_desarrollo
                (fechaCambio, idSistema, idModulo, tipoCambio, estado, prioridad, version, titulo, detalle, responsable)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $sentencia->execute(array(
                $registro['fechaCambio'], $idSistema, $idModulo, $registro['tipoCambio'],
                $registro['estado'], $registro['prioridad'], $registro['version'], $registro['titulo'],
                $registro['detalle'], $registro['responsable'],
            ));
            $idRegistroGuardado = (int) $pdo->lastInsertId();
        } else {
            $idRegistroGuardado = (int) $registro['idBitacora'];
            $sentencia = $pdo->prepare('UPDATE adm_bitacora_desarrollo
                SET fechaCambio = ?, idSistema = ?, idModulo = ?, tipoCambio = ?, estado = ?, prioridad = ?,
                    version = ?, titulo = ?, detalle = ?, responsable = ?
                WHERE idBitacora = ?');
            $sentencia->execute(array(
                $registro['fechaCambio'], $idSistema, $idModulo, $registro['tipoCambio'],
                $registro['estado'], $registro['prioridad'], $registro['version'], $registro['titulo'],
                $registro['detalle'], $registro['responsable'], (int) $registro['idBitacora'],
            ));
            if ($sentencia->rowCount() === 0) {
                $existe = $pdo->prepare('SELECT 1 FROM adm_bitacora_desarrollo WHERE idBitacora = ?');
                $existe->execute(array((int) $registro['idBitacora']));
                if (!$existe->fetchColumn()) {
                    $mensajeError = 'El registro que intenta actualizar ya no existe.';
                }
            }
        }
        if ($mensajeError === '') {
            header('Location: bitacora_desarrollo.php?id=' . $idRegistroGuardado . '&guardado=1');
            exit;
        }
    }
} elseif (isset($_GET['id']) && $_GET['id'] !== '') {
    if (!ctype_digit((string) $_GET['id'])) {
        header('Location: bitacora_desarrollo.php');
        exit;
    }
    $consultaRegistro = $pdo->prepare('SELECT idBitacora, fechaCambio, idSistema, idModulo, tipoCambio, estado, prioridad, version, titulo, detalle, responsable FROM adm_bitacora_desarrollo WHERE idBitacora = ?');
    $consultaRegistro->execute(array((int) $_GET['id']));
    $registroBD = $consultaRegistro->fetch(PDO::FETCH_ASSOC);
    if (!$registroBD) {
        header('Location: bitacora_desarrollo.php');
        exit;
    }
    $registro = array_merge($registro, $registroBD);
}

$datosAccion = array(
    'fechaAccion' => date('Y-m-d'),
    'tipoAccion' => 'AVANCE',
    'detalleAccion' => '',
    'responsableAccion' => trim((string) ($_SESSION['nomUsuario'] ?? $_SESSION['idUser'] ?? '')),
);
if (!isset($responsablesAutorizados[$datosAccion['responsableAccion']])) {
    $datosAccion['responsableAccion'] = '';
}
$mensajeAccion = isset($_GET['accionGuardada']) ? 'La acción fue agregada al hilo.' : '';
if (isset($_POST['guardarAccion'])) {
    foreach (array_keys($datosAccion) as $campo) {
        $datosAccion[$campo] = trim((string) ($_POST[$campo] ?? ''));
    }
    $idEntradaAccion = $_POST['idBitacora'] ?? '';
    if (!ctype_digit((string) $idEntradaAccion) || (int) $idEntradaAccion !== (int) $registro['idBitacora']) {
        $mensajeAccion = 'La entrada seleccionada no es válida.';
    } elseif (!$fechaValida($datosAccion['fechaAccion'])) {
        $mensajeAccion = 'La fecha de la acción no es válida.';
    } elseif (!array_key_exists($datosAccion['tipoAccion'], $tiposAccion)) {
        $mensajeAccion = 'Seleccione un tipo de acción válido.';
    } elseif (mb_strlen($datosAccion['detalleAccion']) < 3 || mb_strlen($datosAccion['detalleAccion']) > 4000) {
        $mensajeAccion = 'El detalle de la acción debe tener entre 3 y 4.000 caracteres.';
    } elseif (!isset($responsablesAutorizados[$datosAccion['responsableAccion']])) {
        $mensajeAccion = 'Seleccione un responsable autorizado.';
    } else {
        $guardarAccion = $pdo->prepare('INSERT INTO adm_bitacora_desarrollo_acciones (idBitacora, fechaAccion, tipoAccion, detalle, responsable) VALUES (?, ?, ?, ?, ?)');
        $guardarAccion->execute(array((int) $idEntradaAccion, $datosAccion['fechaAccion'], $datosAccion['tipoAccion'], $datosAccion['detalleAccion'], $datosAccion['responsableAccion']));
        $idAccionCreada = (int) $pdo->lastInsertId();
        $archivoAccion = $_FILES['adjuntoAccion'] ?? null;
        if ($archivoAccion && $archivoAccion['error'] !== UPLOAD_ERR_NO_FILE && $archivoAccion['error'] !== UPLOAD_ERR_OK) {
            $mensajeAccion = 'No fue posible recibir el adjunto de la acción.';
        } elseif ($archivoAccion && $archivoAccion['error'] === UPLOAD_ERR_OK && $archivoAccion['size'] > 8 * 1024 * 1024) {
            $mensajeAccion = 'El adjunto de la acción no puede superar los 8 MB.';
        } elseif ($archivoAccion && $archivoAccion['error'] === UPLOAD_ERR_OK) {
            $mime = (new finfo(FILEINFO_MIME_TYPE))->file($archivoAccion['tmp_name']);
            $permitidos = array(
                'application/pdf' => 'pdf',
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/webp' => 'webp',
                'text/plain' => 'txt',
                'application/sql' => 'sql',
                'text/x-sql' => 'sql',
                'application/x-sql' => 'sql',
            );
            if (!isset($permitidos[$mime])) {
                $mensajeAccion = 'El adjunto de la acción debe ser PDF, imagen, TXT o SQL.';
            } else {
                $nombre = 'accion_' . $idAccionCreada . '_' . bin2hex(random_bytes(10)) . '.' . $permitidos[$mime];
                $ruta = rtrim(ALMACEN_AUX, '/') . '/bitacora_desarrollo/' . $nombre;
                if (move_uploaded_file($archivoAccion['tmp_name'], $ruta)) {
                    $adjuntar = $pdo->prepare('INSERT INTO adm_bitacora_desarrollo_acciones_archivos (idAccion, archivo, nombreOriginal, mimeTipo, tamano) VALUES (?, ?, ?, ?, ?)');
                    $adjuntar->execute(array($idAccionCreada, $nombre, mb_substr(basename($archivoAccion['name']), 0, 255), $mime, (int) $archivoAccion['size']));
                } else {
                    $mensajeAccion = 'No fue posible almacenar el adjunto de la acción.';
                }
            }
        }
        if ($mensajeAccion === '') {
            header('Location: bitacora_desarrollo.php?id=' . (int) $idEntradaAccion . '&accionGuardada=1');
            exit;
        }
    }
}

$imagenesRegistro = array();
if ($registro['idBitacora'] !== '') {
    $consultaImagenesRegistro = $pdo->prepare('SELECT idImagen, archivo, nombreOriginal, mimeTipo, tamano FROM adm_bitacora_desarrollo_imagenes WHERE idBitacora = ? ORDER BY idImagen');
    $consultaImagenesRegistro->execute(array((int) $registro['idBitacora']));
    $imagenesRegistro = $consultaImagenesRegistro->fetchAll(PDO::FETCH_ASSOC);
}
$imagenesIniciales = array();
foreach ($imagenesRegistro as $imagen) {
    $imagenesIniciales[] = array(
        'idImagen' => (int) $imagen['idImagen'],
        'nombre' => $imagen['nombreOriginal'],
        'mimeTipo' => $imagen['mimeTipo'],
        'tamano' => (int) $imagen['tamano'],
        'url' => 'bitacora_desarrollo_imagen.php?id=' . (int) $imagen['idImagen'],
    );
}
$accionesHilo = array();
if ($registro['idBitacora'] !== '') {
    $consultaAcciones = $pdo->prepare('SELECT idAccion, fechaAccion, tipoAccion, detalle, responsable FROM adm_bitacora_desarrollo_acciones WHERE idBitacora = ? ORDER BY fechaAccion DESC, idAccion DESC');
    $consultaAcciones->execute(array((int) $registro['idBitacora']));
    $accionesHilo = $consultaAcciones->fetchAll(PDO::FETCH_ASSOC);
}
$archivosPorAccion = array();
if (count($accionesHilo) > 0) {
    $idsAcciones = array_map(static fn($accion): int => (int) $accion['idAccion'], $accionesHilo);
    $marcadoresAcciones = implode(',', array_fill(0, count($idsAcciones), '?'));
    $consultaArchivosAccion = $pdo->prepare("SELECT idArchivo, idAccion, nombreOriginal, mimeTipo FROM adm_bitacora_desarrollo_acciones_archivos WHERE idAccion IN ($marcadoresAcciones)");
    $consultaArchivosAccion->execute($idsAcciones);
    while ($archivoAccion = $consultaArchivosAccion->fetch(PDO::FETCH_ASSOC)) {
        $archivosPorAccion[(int) $archivoAccion['idAccion']][] = $archivoAccion;
    }
}

$filtroSistema = isset($_GET['sistema'])
    && ctype_digit((string) $_GET['sistema'])
    && isset($sistemasPorId[(int) $_GET['sistema']])
    ? (int) $_GET['sistema']
    : '';
$filtroTipo = isset($_GET['tipo']) && array_key_exists($_GET['tipo'], $tiposCambio)
    ? $_GET['tipo']
    : '';
$filtroResponsable = isset($_GET['responsableFiltro'])
    ? mb_substr(trim((string) $_GET['responsableFiltro']), 0, 100)
    : '';
if ($filtroResponsable !== '' && !isset($responsablesAutorizados[$filtroResponsable])) {
    $filtroResponsable = '';
}
$filtroEstado = !isset($_GET['estadoFiltro'])
    ? 'ABIERTO'
    : (array_key_exists($_GET['estadoFiltro'], $estadosEntrada) ? $_GET['estadoFiltro'] : '');
$filtrosPrioridad = array(
    'MEDIA_ALTA' => array('ALTO', 'MEDIO'),
    'ALTA' => array('URGENTE', 'ALTO'),
    'MEDIA' => array('MEDIO'),
    'BAJA' => array('BAJO'),
    'TODOS' => array(),
);
$filtroPrioridad = isset($_GET['prioridadFiltro'])
    && array_key_exists($_GET['prioridadFiltro'], $filtrosPrioridad)
    ? $_GET['prioridadFiltro']
    : 'MEDIA_ALTA';
$ordenFecha = isset($_GET['ordenFecha']) && in_array($_GET['ordenFecha'], array('ASC', 'PRIORIDAD'), true)
    ? $_GET['ordenFecha']
    : 'DESC';

// Indicadores generales de la bitácora; no dependen de los filtros del listado.
$resumenTickets = $pdo->query(
    "SELECT
        SUM(CASE WHEN estado = 'ABIERTO' THEN 1 ELSE 0 END) AS abiertos,
        SUM(CASE WHEN estado = 'PAUSADO' THEN 1 ELSE 0 END) AS pausados,
        SUM(CASE WHEN estado = 'ABIERTO' AND fechaCambio < CURDATE() - INTERVAL 10 DAY THEN 1 ELSE 0 END) AS atrasados,
        SUM(CASE WHEN estado = 'CERRADO' THEN 1 ELSE 0 END) AS cerrados
     FROM adm_bitacora_desarrollo"
)->fetch(PDO::FETCH_ASSOC) ?: array();
$ticketsAbiertos = (int) ($resumenTickets['abiertos'] ?? 0);
$ticketsPausados = (int) ($resumenTickets['pausados'] ?? 0);
$ticketsAtrasados = (int) ($resumenTickets['atrasados'] ?? 0);
$ticketsCerrados = (int) ($resumenTickets['cerrados'] ?? 0);

$condiciones = array();
$parametros = array();
if ($filtroSistema !== '') {
    $condiciones[] = 'b.idSistema = ?';
    $parametros[] = $filtroSistema;
}
if ($filtroTipo !== '') {
    $condiciones[] = 'b.tipoCambio = ?';
    $parametros[] = $filtroTipo;
}
if ($filtroResponsable !== '') {
    $condiciones[] = 'b.responsable = ?';
    $parametros[] = $filtroResponsable;
}
if ($filtroEstado !== '') {
    $condiciones[] = 'b.estado = ?';
    $parametros[] = $filtroEstado;
}
if ($filtroPrioridad !== 'TODOS') {
    $prioridadesFiltradas = $filtrosPrioridad[$filtroPrioridad];
    $marcadoresPrioridad = implode(',', array_fill(0, count($prioridadesFiltradas), '?'));
    $condiciones[] = 'b.prioridad IN (' . $marcadoresPrioridad . ')';
    foreach ($prioridadesFiltradas as $prioridadFiltrada) {
        $parametros[] = $prioridadFiltrada;
    }
}
$ordenHistorial = $ordenFecha === 'PRIORIDAD'
    ? "CASE b.prioridad
            WHEN 'URGENTE' THEN 1
            WHEN 'ALTO' THEN 2
            WHEN 'MEDIO' THEN 3
            WHEN 'BAJO' THEN 4
            ELSE 5
        END ASC, b.fechaCambio DESC, b.idBitacora DESC"
    : 'b.fechaCambio ' . $ordenFecha . ', b.idBitacora ' . $ordenFecha;
$sqlHistorial = "SELECT b.idBitacora, b.fechaCambio, b.tipoCambio, b.estado, b.prioridad, b.version,
        b.titulo, b.detalle, b.responsable, s.nombre AS sistema, m.modulo
    FROM adm_bitacora_desarrollo b
    LEFT JOIN adm_sistemas s ON s.id = b.idSistema
    LEFT JOIN adm_modulos m ON m.id = b.idModulo
    " . (count($condiciones) ? 'WHERE ' . implode(' AND ', $condiciones) : '') . '
    ORDER BY ' . $ordenHistorial . ' LIMIT 200';
$consultaHistorial = $pdo->prepare($sqlHistorial);
$consultaHistorial->execute($parametros);
$historial = $consultaHistorial->fetchAll(PDO::FETCH_ASSOC);
$imagenesPorBitacora = array();
if (count($historial) > 0) {
    $idsHistorial = array_map(static fn($fila): int => (int) $fila['idBitacora'], $historial);
    $marcadores = implode(',', array_fill(0, count($idsHistorial), '?'));
    $consultaImagenes = $pdo->prepare("SELECT idImagen, idBitacora
        FROM adm_bitacora_desarrollo_imagenes
        WHERE idBitacora IN ($marcadores)
        ORDER BY idImagen");
    $consultaImagenes->execute($idsHistorial);
    while ($imagen = $consultaImagenes->fetch(PDO::FETCH_ASSOC)) {
        $imagenesPorBitacora[(int) $imagen['idBitacora']][] = (int) $imagen['idImagen'];
    }
}

$opcionesSistemas = '<option value="">Proyecto municipal (general)</option>';
$opcionesFiltroSistemas = '<option value="">Todos los sistemas</option>';
foreach ($sistemas as $sistema) {
    $seleccionRegistro = (string) $registro['idSistema'] === (string) $sistema['id'] ? ' selected' : '';
    $seleccionFiltro = (string) $filtroSistema === (string) $sistema['id'] ? ' selected' : '';
    $opcionesSistemas .= '<option value="' . (int) $sistema['id'] . '"' . $seleccionRegistro . '>' . $html($sistema['nombre']) . '</option>';
    $opcionesFiltroSistemas .= '<option value="' . (int) $sistema['id'] . '"' . $seleccionFiltro . '>' . $html($sistema['nombre']) . '</option>';
}
$opcionesModulos = '<option value="">Cambio general del sistema</option>';
foreach ($modulos as $modulo) {
    $seleccion = (string) $registro['idModulo'] === (string) $modulo['id'] ? ' selected' : '';
    $opcionesModulos .= '<option value="' . (int) $modulo['id'] . '" data-sistema="' . (int) $modulo['idsistema'] . '"' . $seleccion . '>' . $html($modulo['modulo']) . '</option>';
}
$opcionesTipoRegistro = '';
$opcionesTipoFiltro = '<option value="">Todos los tipos</option>';
foreach ($tiposCambio as $codigo => $nombre) {
    $opcionesTipoRegistro .= '<option value="' . $codigo . '"' . ($registro['tipoCambio'] === $codigo ? ' selected' : '') . '>' . $nombre . '</option>';
    $opcionesTipoFiltro .= '<option value="' . $codigo . '"' . ($filtroTipo === $codigo ? ' selected' : '') . '>' . $nombre . '</option>';
}
$opcionesResponsables = '<option value="">Seleccione responsable</option>';
$opcionesFiltroResponsables = '<option value="">Todos los responsables</option>';
foreach ($usuariosResponsables as $usuarioResponsable) {
    $responsableFiltro = (string) $usuarioResponsable['nombre'];
    $seleccionado = $filtroResponsable === $responsableFiltro ? ' selected' : '';
    $opcionesFiltroResponsables .= '<option value="' . $html($responsableFiltro) . '"' . $seleccionado . '>' . $html($responsableFiltro) . '</option>';
    $opcionesResponsables .= '<option value="' . $html($responsableFiltro) . '"' . ($registro['responsable'] === $responsableFiltro ? ' selected' : '') . '>' . $html($responsableFiltro) . '</option>';
}
$opcionesResponsablesAccion = '<option value="">Seleccione responsable</option>';
foreach ($usuariosResponsables as $usuarioResponsable) {
    $nombreResponsable = (string) $usuarioResponsable['nombre'];
    $opcionesResponsablesAccion .= '<option value="' . $html($nombreResponsable) . '"' . ($datosAccion['responsableAccion'] === $nombreResponsable ? ' selected' : '') . '>' . $html($nombreResponsable) . '</option>';
}
$opcionesEstado = '';
$opcionesEstadoFiltro = '<option value="">Todos los estados</option>';
foreach ($estadosEntrada as $codigo => $nombre) {
    $seleccionado = $registro['estado'] === $codigo ? ' selected' : '';
    $opcionesEstado .= '<option value="' . $codigo . '"' . $seleccionado . '>'
        . $nombre . '</option>';
    $opcionesEstadoFiltro .= '<option value="' . $codigo . '"'
        . ($filtroEstado === $codigo ? ' selected' : '') . '>' . $nombre . '</option>';
}
$nombresFiltroPrioridad = array(
    'MEDIA_ALTA' => 'Media/Alta',
    'ALTA' => 'Alta',
    'MEDIA' => 'Media',
    'BAJA' => 'Baja',
    'TODOS' => 'Todos',
);
$opcionesPrioridadFiltro = '';
foreach ($nombresFiltroPrioridad as $codigo => $nombre) {
    $opcionesPrioridadFiltro .= '<option value="' . $codigo . '"'
        . ($filtroPrioridad === $codigo ? ' selected' : '') . '>' . $nombre . '</option>';
}
$opcionesOrdenFecha = '<option value="DESC"' . ($ordenFecha === 'DESC' ? ' selected' : '') . '>Fecha descendente</option>'
    . '<option value="ASC"' . ($ordenFecha === 'ASC' ? ' selected' : '') . '>Fecha ascendente</option>'
    . '<option value="PRIORIDAD"' . ($ordenFecha === 'PRIORIDAD' ? ' selected' : '') . '>Prioridad: Urgente → Alta → Media → Baja</option>';

$opcionesPrioridad = '';
foreach ($prioridadesEntrada as $codigo => $nombre) {
    $seleccionado = $registro['prioridad'] === $codigo ? ' selected' : '';
    $opcionesPrioridad .= '<option value="' . $codigo . '"' . $seleccionado . '>'
        . $nombre . '</option>';
}
$opcionesTipoAccion = '';
foreach ($tiposAccion as $codigo => $nombre) {
    $opcionesTipoAccion .= '<option value="' . $codigo . '"' . ($datosAccion['tipoAccion'] === $codigo ? ' selected' : '') . '>' . $nombre . '</option>';
}

$filasHistorial = '';
$fechaLimiteAntiguedad = (new DateTimeImmutable('today'))->modify('-10 days');
foreach ($historial as $fila) {
    $fechaRegistro = DateTimeImmutable::createFromFormat('!Y-m-d', $fila['fechaCambio']);
    $claseAntiguedad = $fila['estado'] === 'ABIERTO'
        && $fechaRegistro && $fechaRegistro < $fechaLimiteAntiguedad
        ? ' bitacora-item--antiguo'
        : '';
    $alcance = $fila['sistema'] ?: 'Proyecto municipal';
    if ($fila['modulo']) {
        $alcance .= ' / ' . $fila['modulo'];
    }

    $version = $fila['version'] !== ''
        ? '<span class="bitacora-version">' . $html($fila['version']) . '</span>'
        : '';
    $estado = '<span class="bitacora-estado bitacora-estado--'
        . strtolower($fila['estado']) . '">'
        . $html($estadosEntrada[$fila['estado']]) . '</span>';
    $prioridad = '<span class="bitacora-prioridad bitacora-prioridad--'
        . strtolower($fila['prioridad']) . '">'
        . $html($prioridadesEntrada[$fila['prioridad']]) . '</span>';
    $cantidadAdjuntos = count($imagenesPorBitacora[(int) $fila['idBitacora']] ?? array());
    $adjuntosResumen = $cantidadAdjuntos > 0
        ? '<span class="bitacora-adjuntos-resumen">' . $cantidadAdjuntos . ' adjunto'
            . ($cantidadAdjuntos === 1 ? '' : 's') . '</span>'
        : '';

    $filasHistorial .= '<article class="bitacora-item' . $claseAntiguedad . '">'
        . '<div class="bitacora-item__fecha">' . $html(date('d-m-Y', strtotime($fila['fechaCambio']))) . '</div>'
        . '<div class="bitacora-item__contenido">'
        . '<div class="bitacora-item__meta">'
        . '<span class="bitacora-tipo bitacora-tipo--' . strtolower($fila['tipoCambio']) . '">'
        . $html($tiposCambio[$fila['tipoCambio']]) . '</span>' . $prioridad . $estado . $version
        . '<span>' . $html($alcance) . '</span></div>'
        . '<h4>' . $html($fila['titulo']) . '</h4>'
        . '<p>' . nl2br($html($fila['detalle'])) . '</p>' . $adjuntosResumen
        . '<small>Responsable: ' . $html($fila['responsable']) . '</small>'
        . '<div class="bitacora-item__acciones"><a class="boton boton--editar" href="bitacora_desarrollo.php?id='
        . (int) $fila['idBitacora'] . '">Abrir</a>'
        . '<a class="boton bitacora-boton-pdf" href="bitacora_desarrollo_pdf.php?id='
        . (int) $fila['idBitacora'] . '" target="_blank" rel="noopener">PDF</a></div></div></article>';
}
if ($filasHistorial === '') {
    $filasHistorial = '<p class="bitacora-vacio">No hay registros que coincidan con los filtros seleccionados.</p>';
}
$hiloAccionesHTML = '';
foreach ($accionesHilo as $accion) {
    $adjuntosAccion = '';
    $archivosAccion = $archivosPorAccion[(int) $accion['idAccion']] ?? array();
    foreach ($archivosAccion as $archivo) {
        $adjuntosAccion .= '<span class="bitacora-adjunto-accion__grupo">'
            . '<button type="button" class="bitacora-adjunto-accion"'
            . ' data-adjunto-url="bitacora_desarrollo_accion_archivo.php?id=' . (int) $archivo['idArchivo'] . '"'
            . ' data-adjunto-mime="' . $html($archivo['mimeTipo']) . '">'
            . 'Ver adjunto: ' . $html($archivo['nombreOriginal']) . '</button>'
            . '<button type="button" class="bitacora-adjunto-accion--eliminar"'
            . ' data-eliminar-adjunto-accion="' . (int) $archivo['idArchivo'] . '"'
            . ' aria-label="Eliminar adjunto ' . $html($archivo['nombreOriginal']) . '">Eliminar</button>'
            . '</span>';
    }
    $cantidadAdjuntos = count($archivosAccion);
    $etiquetaAdjuntos = $cantidadAdjuntos > 0
        ? '<span class="bitacora-hilo__adjuntos">' . $cantidadAdjuntos . ' adjunto'
            . ($cantidadAdjuntos === 1 ? '' : 's') . '</span>'
        : '';
    $hiloAccionesHTML .= '<article class="bitacora-hilo__item">'
        . '<div class="bitacora-hilo__fecha">' . $html(date('d-m-Y', strtotime($accion['fechaAccion']))) . '</div>'
        . '<div class="bitacora-hilo__contenido"><div>'
        . '<span class="bitacora-tipo bitacora-tipo--accion">'
        . $html($tiposAccion[$accion['tipoAccion']]) . '</span>' . $etiquetaAdjuntos . '</div>'
        . '<p>' . nl2br($html($accion['detalle'])) . '</p>'
        . '<div class="bitacora-hilo__archivos">' . $adjuntosAccion . '</div>'
        . '<div class="bitacora-hilo__pie"><small>' . $html($accion['responsable']) . '</small>'
        . '<button type="button" class="bitacora-accion--eliminar" data-eliminar-accion="'
        . (int) $accion['idAccion'] . '">Eliminar acción</button></div></div></article>';
}
if ($hiloAccionesHTML === '') {
    $hiloAccionesHTML = '<p class="bitacora-vacio">Aún no se han registrado acciones para esta entrada.</p>';
}

$contenido = new plantilla('bitacora_desarrollo');
$contenido->asigna_variables(array(
    'lang' => $sistema_txt->GetDefinition('XMLLang'),
    'topbar' => $topbar, 'barraLateral' => $barraLateral, 'headerMenu' => $headerMenu,
    'H2Sistema' => $sistema_txt->GetDefinition('H2Sistema'),
    'idBitacora' => $html($registro['idBitacora']), 'fechaCambio' => $html($registro['fechaCambio']),
    'opcionesSistemas' => $opcionesSistemas, 'opcionesModulos' => $opcionesModulos,
    'opcionesTipoRegistro' => $opcionesTipoRegistro, 'version' => $html($registro['version']),
    'opcionesEstado' => $opcionesEstado, 'opcionesPrioridad' => $opcionesPrioridad,
    'titulo' => $html($registro['titulo']), 'detalle' => $html($registro['detalle']), 'responsable' => $html($registro['responsable']),
    'mensajeError' => $html($mensajeError), 'mensajeExito' => $html($mensajeExito),
    'opcionesFiltroSistemas' => $opcionesFiltroSistemas, 'opcionesTipoFiltro' => $opcionesTipoFiltro,
    'opcionesEstadoFiltro' => $opcionesEstadoFiltro, 'opcionesPrioridadFiltro' => $opcionesPrioridadFiltro, 'opcionesOrdenFecha' => $opcionesOrdenFecha, 'opcionesFiltroResponsables' => $opcionesFiltroResponsables,
    'opcionesResponsables' => $opcionesResponsables, 'opcionesResponsablesAccion' => $opcionesResponsablesAccion,
    'filasHistorial' => $filasHistorial, 'hiloAccionesHTML' => $hiloAccionesHTML,
    'ticketsAbiertos' => $ticketsAbiertos, 'ticketsPausados' => $ticketsPausados, 'ticketsAtrasados' => $ticketsAtrasados, 'ticketsCerrados' => $ticketsCerrados,
    'idBitacoraImagenes' => (int) $registro['idBitacora'],
    'mostrarZonaAdjuntos' => $registro['idBitacora'] === '' ? 'none' : 'block',
    'mostrarAvisoAdjuntos' => $registro['idBitacora'] === '' ? 'block' : 'none',
    'mostrarAcciones' => $registro['idBitacora'] === '' ? 'none' : 'block',
    'imagenesInicialesJSON' => json_encode($imagenesIniciales, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT),
    'opcionesTipoAccion' => $opcionesTipoAccion, 'fechaAccion' => $html($datosAccion['fechaAccion']),
    'detalleAccion' => $html($datosAccion['detalleAccion']), 'responsableAccion' => $html($datosAccion['responsableAccion']),
    'mensajeAccion' => $html($mensajeAccion),
));
echo $contenido->muestra();
?>
