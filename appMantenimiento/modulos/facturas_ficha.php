<?php
require_once('../includes/initSistema.php');

$DB_DCODE = 'adm_dCode';
$conexionDB = new DB_MySQLi;
$conexionDB->conectar($DB_DCODE, DB_SERVER, DB_USER, DB_PASSWD);
$html = static function ($valor): string { return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); };
$aFechaInput = static function ($valor): string {
    $valor = (string) $valor;
    return preg_match('/^\d{8}$/', $valor) ? substr($valor, 0, 4) . '-' . substr($valor, 4, 2) . '-' . substr($valor, 6, 2) : '';
};
$aFechaNumero = static function ($valor): ?int {
    if ($valor === '') { return null; }
    $fecha = DateTime::createFromFormat('Y-m-d', $valor);
    return $fecha && $fecha->format('Y-m-d') === $valor ? (int) str_replace('-', '', $valor) : null;
};

$idFactura = $_POST['id'] ?? $_GET['IdRegistro'] ?? 'new';
$datos = array(
    'nroFactura' => '', 'fechaFactura' => '', 'rutCliente' => '', 'idFacturaReferencia' => '',
    'montoFactura' => '', 'tipoDocumento' => 'FACTURA', 'estadoFactura' => 'P', 'fechaPago' => '',
);
$mensajeError = '';
$mensajeExito = isset($_GET['guardado']) ? 'Factura guardada correctamente.' : '';
$archivoFactura = $_FILES['archivoFactura'] ?? null;
$archivoInformeTecnico = $_FILES['archivoInformeTecnico'] ?? null;
$subirArchivoFactura = false;
$mimeArchivoFactura = '';
$subirArchivoInformeTecnico = false;
$mimeArchivoInformeTecnico = '';

$opcionesClientes = '<option value="">Seleccione un cliente</option>';
$salidaClientes = $conexionDB->consulta("SELECT rut, cliente FROM `$DB_DCODE`.adm_clientes WHERE estadoCliente = 'ACTIVO' ORDER BY cliente");
$clientes = array();
while ($cliente = mysqli_fetch_assoc($salidaClientes)) {
    $rut = preg_replace('/\D.*/', '', (string) ($cliente['rut'] ?? ''));
    if ($rut === '') { continue; }
    $clientes[$rut] = $cliente['cliente'];
}

$opcionesFacturasReferencia = '<option value="">Seleccione la factura afectada</option>';
$salidaFacturasReferencia = $conexionDB->consulta("SELECT f.id, f.nroFactura, f.rutCliente, c.cliente
    FROM `$DB_DCODE`.adm_facturas f
    LEFT JOIN `$DB_DCODE`.adm_clientes c ON CAST(SUBSTRING_INDEX(c.rut, '-', 1) AS UNSIGNED) = f.rutCliente
    WHERE f.tipoDocumento = 'FACTURA' AND f.estadoFactura <> 'A'
    ORDER BY f.fechaFactura DESC, f.nroFactura DESC");
$facturasReferencia = array();
while ($facturaReferencia = mysqli_fetch_assoc($salidaFacturasReferencia)) {
    $facturasReferencia[(int) $facturaReferencia['id']] = $facturaReferencia;
}

if (isset($_POST['guardarFactura'])) {
    foreach (array_keys($datos) as $campo) { $datos[$campo] = trim((string) ($_POST[$campo] ?? '')); }
    $fechaFactura = $aFechaNumero($datos['fechaFactura']);
    $fechaPago = $aFechaNumero($datos['fechaPago']);
    $idFacturaReferencia = $datos['idFacturaReferencia'] === '' ? null : (ctype_digit($datos['idFacturaReferencia']) ? (int) $datos['idFacturaReferencia'] : -1);
    if ($datos['tipoDocumento'] === 'FACTURA') { $idFacturaReferencia = null; }
    if ($archivoFactura && $archivoFactura['error'] === UPLOAD_ERR_OK) {
        $mimeArchivoFactura = (new finfo(FILEINFO_MIME_TYPE))->file($archivoFactura['tmp_name']);
        $subirArchivoFactura = true;
    }
    if ($archivoInformeTecnico && $archivoInformeTecnico['error'] === UPLOAD_ERR_OK) {
        $mimeArchivoInformeTecnico = (new finfo(FILEINFO_MIME_TYPE))->file($archivoInformeTecnico['tmp_name']);
        $subirArchivoInformeTecnico = true;
    }
    if (!ctype_digit($datos['nroFactura']) || (int) $datos['nroFactura'] < 1) {
        $mensajeError = 'El número de factura debe ser positivo.';
    } elseif (!isset($clientes[$datos['rutCliente']])) {
        $mensajeError = 'Debe seleccionar un cliente válido.';
    } elseif ($fechaFactura === null) {
        $mensajeError = 'La fecha de emisión es obligatoria y debe ser válida.';
    } elseif (!ctype_digit($datos['montoFactura']) || (int) $datos['montoFactura'] < 0) {
        $mensajeError = 'El monto debe ser un número entero igual o mayor que cero.';
    } elseif (!in_array($datos['tipoDocumento'], array('FACTURA', 'NOTA_CREDITO'), true)) {
        $mensajeError = 'El tipo de documento no es válido.';
    } elseif ($datos['tipoDocumento'] === 'NOTA_CREDITO' && $idFacturaReferencia === null) {
        $mensajeError = 'Debe indicar la factura afectada por la nota de crédito.';
    } elseif ($idFacturaReferencia === -1) {
        $mensajeError = 'La factura afectada no es válida.';
    } elseif (!in_array($datos['estadoFactura'], array('P', 'C', 'A'), true)) {
        $mensajeError = 'El estado de la factura no es válido.';
    } elseif ($idFactura !== 'new' && !ctype_digit((string) $idFactura)) {
        $mensajeError = 'El identificador de factura no es válido.';
    } elseif (empty($_SESSION['save'])) {
        $mensajeError = 'No dispone de permisos para guardar facturas.';
    } elseif ($archivoFactura && $archivoFactura['error'] !== UPLOAD_ERR_NO_FILE && $archivoFactura['error'] !== UPLOAD_ERR_OK) {
        $mensajeError = 'No fue posible recibir el PDF de la factura.';
    } elseif ($subirArchivoFactura && ($archivoFactura['size'] > 8 * 1024 * 1024 || $mimeArchivoFactura !== 'application/pdf')) {
        $mensajeError = 'El PDF de la factura debe tener un tamaño máximo de 8 MB.';
    } elseif ($archivoInformeTecnico && $archivoInformeTecnico['error'] !== UPLOAD_ERR_NO_FILE && $archivoInformeTecnico['error'] !== UPLOAD_ERR_OK) {
        $mensajeError = 'No fue posible recibir el informe técnico.';
    } elseif ($subirArchivoInformeTecnico && ($archivoInformeTecnico['size'] > 8 * 1024 * 1024 || $mimeArchivoInformeTecnico !== 'application/pdf')) {
        $mensajeError = 'El informe técnico debe ser un archivo PDF de hasta 8 MB.';
    }
    if ($mensajeError === '') {
        try {
            $pdo = new PDO('mysql:host=' . DB_SERVER . ';dbname=' . $DB_DCODE . ';charset=latin1', DB_USER, DB_PASSWD, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
            $idValidar = $idFactura === 'new' ? 0 : (int) $idFactura;
            $duplicada = $pdo->prepare('SELECT COUNT(*) FROM adm_facturas WHERE nroFactura = ? AND rutCliente = ? AND tipoDocumento = ? AND id <> ?');
            $duplicada->execute(array((int) $datos['nroFactura'], (int) $datos['rutCliente'], $datos['tipoDocumento'], $idValidar));
            if ((int) $duplicada->fetchColumn() > 0) { throw new RuntimeException('Ya existe una factura con ese número para el cliente seleccionado.'); }

            if ($idFactura !== 'new') {
                $referenciasExistentes = $pdo->prepare("SELECT COUNT(*) FROM adm_facturas
                    WHERE tipoDocumento = 'NOTA_CREDITO' AND idFacturaReferencia = ?");
                $referenciasExistentes->execute(array($idValidar));
                $tieneNotasCredito = (int) $referenciasExistentes->fetchColumn() > 0;
                if ($tieneNotasCredito) {
                    $documentoActual = $pdo->prepare('SELECT rutCliente FROM adm_facturas WHERE id = ?');
                    $documentoActual->execute(array($idValidar));
                    $rutActual = (int) $documentoActual->fetchColumn();
                    if ($datos['tipoDocumento'] !== 'FACTURA' || $rutActual !== (int) $datos['rutCliente']) {
                        throw new RuntimeException('No puede cambiar el tipo ni el cliente de una factura que tiene notas de crédito asociadas.');
                    }
                }
            }

            if ($datos['tipoDocumento'] === 'NOTA_CREDITO') {
                $facturaAfectada = $pdo->prepare("SELECT id, montoFactura FROM adm_facturas
                    WHERE id = ? AND rutCliente = ? AND tipoDocumento = 'FACTURA' AND estadoFactura <> 'A'");
                $facturaAfectada->execute(array($idFacturaReferencia, (int) $datos['rutCliente']));
                $facturaAfectada = $facturaAfectada->fetch(PDO::FETCH_ASSOC);
                if (!$facturaAfectada || $idFacturaReferencia === $idValidar) {
                    throw new RuntimeException('La factura afectada debe ser una factura vigente del mismo cliente.');
                }
                $creditosAplicados = $pdo->prepare("SELECT COALESCE(SUM(montoFactura), 0) FROM adm_facturas
                    WHERE tipoDocumento = 'NOTA_CREDITO' AND idFacturaReferencia = ?
                    AND estadoFactura <> 'A' AND id <> ?");
                $creditosAplicados->execute(array($idFacturaReferencia, $idValidar));
                if ($datos['estadoFactura'] !== 'A'
                    && ((int) $creditosAplicados->fetchColumn() + (int) $datos['montoFactura'] > (int) $facturaAfectada['montoFactura'])
                ) {
                    throw new RuntimeException('El monto de las notas de crédito no puede superar el total de la factura afectada.');
                }
            } elseif ($idFactura !== 'new') {
                if (!empty($tieneNotasCredito)) {
                    $montoCreditos = $pdo->prepare("SELECT COALESCE(SUM(montoFactura), 0) FROM adm_facturas
                        WHERE tipoDocumento = 'NOTA_CREDITO' AND idFacturaReferencia = ? AND estadoFactura <> 'A'");
                    $montoCreditos->execute(array($idValidar));
                    if ((int) $datos['montoFactura'] < (int) $montoCreditos->fetchColumn()) {
                        throw new RuntimeException('No puede reducir el monto de una factura por debajo de sus notas de crédito vigentes.');
                    }
                }
            }

            $valores = array(
                (int) $datos['nroFactura'], $fechaFactura, (int) $datos['rutCliente'], $idFacturaReferencia,
                (int) $datos['montoFactura'], $datos['tipoDocumento'], $datos['estadoFactura'], $fechaPago,
            );
            if ($idFactura === 'new') {
                $idFactura = (int) $pdo->query('SELECT COALESCE(MAX(id), 0) + 1 FROM adm_facturas')->fetchColumn();
                $stmt = $pdo->prepare('INSERT INTO adm_facturas (id, nroFactura, fechaFactura, rutCliente, idFacturaReferencia, montoFactura, tipoDocumento, estadoFactura, fechaPago) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
                $stmt->execute(array_merge(array($idFactura), $valores));
            } else {
                $stmt = $pdo->prepare('UPDATE adm_facturas SET nroFactura = ?, fechaFactura = ?, rutCliente = ?, idFacturaReferencia = ?, montoFactura = ?, tipoDocumento = ?, estadoFactura = ?, fechaPago = ? WHERE id = ?');
                $stmt->execute(array_merge($valores, array((int) $idFactura)));
            }

            if ($subirArchivoFactura || $subirArchivoInformeTecnico) {
                $carpetaFacturas = rtrim(ALMACEN_AUX, '/') . '/facturas/';
                if (!is_dir($carpetaFacturas) || !is_writable($carpetaFacturas)) {
                    throw new RuntimeException('La carpeta para adjuntos de documentos no está disponible.');
                }

                $adjuntos = array();
                if ($subirArchivoFactura) {
                    $adjuntos[] = array(
                        'archivo' => $archivoFactura, 'mimeTipo' => $mimeArchivoFactura,
                        'tabla' => 'adm_facturas_archivos', 'prefijo' => 'documento_', 'descripcion' => 'el PDF de la factura',
                    );
                }
                if ($subirArchivoInformeTecnico) {
                    $adjuntos[] = array(
                        'archivo' => $archivoInformeTecnico, 'mimeTipo' => $mimeArchivoInformeTecnico,
                        'tabla' => 'adm_facturas_informes_tecnicos', 'prefijo' => 'informe_tecnico_', 'descripcion' => 'el informe técnico',
                    );
                }

                $adjuntosGuardados = array();
                try {
                    foreach ($adjuntos as $indice => $adjunto) {
                        $nombreArchivo = $adjunto['prefijo'] . (int) $idFactura . '_' . bin2hex(random_bytes(12)) . '.pdf';
                        $rutaArchivo = $carpetaFacturas . $nombreArchivo;
                        if (!move_uploaded_file($adjunto['archivo']['tmp_name'], $rutaArchivo)) {
                            throw new RuntimeException('No fue posible almacenar ' . $adjunto['descripcion'] . '.');
                        }
                        $adjuntos[$indice]['nombreArchivo'] = $nombreArchivo;
                        $adjuntos[$indice]['rutaArchivo'] = $rutaArchivo;
                        $adjuntosGuardados[] = $adjuntos[$indice];
                    }

                    $pdo->beginTransaction();
                    foreach ($adjuntos as $indice => $adjunto) {
                        $consultaAnterior = $pdo->prepare('SELECT archivo FROM ' . $adjunto['tabla'] . ' WHERE idFactura = ?');
                        $consultaAnterior->execute(array((int) $idFactura));
                        $adjuntos[$indice]['archivoAnterior'] = $consultaAnterior->fetchColumn();
                        $eliminarAnterior = $pdo->prepare('DELETE FROM ' . $adjunto['tabla'] . ' WHERE idFactura = ?');
                        $eliminarAnterior->execute(array((int) $idFactura));
                        $guardarArchivo = $pdo->prepare('INSERT INTO ' . $adjunto['tabla'] . ' (idFactura, archivo, nombreOriginal, mimeTipo, tamano) VALUES (?, ?, ?, ?, ?)');
                        $guardarArchivo->execute(array(
                            (int) $idFactura,
                            $adjunto['nombreArchivo'],
                            mb_substr(basename((string) $adjunto['archivo']['name']), 0, 255),
                            $adjunto['mimeTipo'],
                            (int) $adjunto['archivo']['size'],
                        ));
                    }
                    $pdo->commit();
                    foreach ($adjuntos as $adjunto) {
                        if ($adjunto['archivoAnterior'] && is_file($carpetaFacturas . basename($adjunto['archivoAnterior']))) {
                            @unlink($carpetaFacturas . basename($adjunto['archivoAnterior']));
                        }
                    }
                } catch (Throwable $e) {
                    if ($pdo->inTransaction()) {
                        $pdo->rollBack();
                    }
                    foreach ($adjuntosGuardados as $adjuntoGuardado) {
                        @unlink($adjuntoGuardado['rutaArchivo']);
                    }
                    if ($e instanceof RuntimeException) { throw $e; }
                    throw new RuntimeException('No fue posible asociar los adjuntos a la factura.');
                }
            }
            header('Location: facturas_ficha.php?IdRegistro=' . rawurlencode((string) $idFactura) . '&guardado=1');
            exit;
        } catch (RuntimeException $e) {
            $mensajeError = $e->getMessage();
        } catch (Throwable $e) {
            $mensajeError = 'No fue posible guardar la factura. Intente nuevamente.';
        }
    }
} elseif ($idFactura !== 'new') {
    if (!ctype_digit((string) $idFactura)) { header('Location: facturas.php'); exit; }
    $salida = $conexionDB->consulta("SELECT * FROM `$DB_DCODE`.adm_facturas WHERE id = " . (int) $idFactura . ' LIMIT 1');
    $registro = mysqli_fetch_assoc($salida);
    if (!$registro) { header('Location: facturas.php'); exit; }
    foreach (array_keys($datos) as $campo) { $datos[$campo] = $registro[$campo] ?? ''; }
    $datos['fechaFactura'] = $aFechaInput($datos['fechaFactura']);
    $datos['fechaPago'] = $aFechaInput($datos['fechaPago']);
}
foreach ($clientes as $rut => $cliente) {
    $seleccionado = (string) $rut === (string) $datos['rutCliente'] ? ' selected' : '';
    $opcionesClientes .= '<option value="' . $html($rut) . '"' . $seleccionado . '>' . $html($cliente) . '</option>';
}
foreach ($facturasReferencia as $idReferencia => $facturaReferencia) {
    if ((int) $idReferencia === (int) $idFactura) { continue; }
    $seleccionado = (string) $idReferencia === (string) $datos['idFacturaReferencia'] ? ' selected' : '';
    $nombreCliente = $facturaReferencia['cliente'] ?: ('RUT ' . $facturaReferencia['rutCliente']);
    $opcionesFacturasReferencia .= '<option value="' . (int) $idReferencia . '" data-cliente="' . (int) $facturaReferencia['rutCliente'] . '"' . $seleccionado . '>'
        . 'Factura ' . $html($facturaReferencia['nroFactura']) . ' - ' . $html($nombreCliente) . '</option>';
}

$pdfFacturaHTML = '<span class="factura-pdf__vacio">Sin PDF adjunto.</span>';
$informeTecnicoHTML = '<span class="factura-pdf__vacio">Sin informe técnico adjunto.</span>';
if ($idFactura !== 'new' && ctype_digit((string) $idFactura)) {
    $consultaArchivo = $conexionDB->consulta("SELECT idArchivoFactura, nombreOriginal FROM `$DB_DCODE`.adm_facturas_archivos WHERE idFactura = " . (int) $idFactura . ' LIMIT 1');
    $archivoRegistrado = mysqli_fetch_assoc($consultaArchivo);
    if ($archivoRegistrado) {
        $pdfFacturaHTML = '<a class="factura-pdf__enlace" href="facturas_archivo.php?id=' . (int) $archivoRegistrado['idArchivoFactura'] . '" target="_blank" rel="noopener">Ver PDF: ' . $html($archivoRegistrado['nombreOriginal']) . '</a>';
    }
    $consultaInformeTecnico = $conexionDB->consulta("SELECT idInformeTecnico, nombreOriginal FROM `$DB_DCODE`.adm_facturas_informes_tecnicos WHERE idFactura = " . (int) $idFactura . ' LIMIT 1');
    $informeTecnicoRegistrado = mysqli_fetch_assoc($consultaInformeTecnico);
    if ($informeTecnicoRegistrado) {
        $informeTecnicoHTML = '<a class="factura-pdf__enlace" href="facturas_informe_tecnico.php?id=' . (int) $informeTecnicoRegistrado['idInformeTecnico'] . '" target="_blank" rel="noopener">Ver informe: ' . $html($informeTecnicoRegistrado['nombreOriginal']) . '</a>';
    }
}

$contenido = new plantilla('facturas_ficha');
$contenido->asigna_variables(array(
    'lang' => $sistema_txt->GetDefinition('XMLLang'), 'icono' => PATH_ICO . ICONO_NAVEGADOR, 'topbar' => $topbar, 'barraLateral' => $barraLateral, 'headerMenu' => $headerMenu,
    'H2Sistema' => $sistema_txt->GetDefinition('H2Sistema'), 'H2Titulo' => 'Mantenedor de facturas clientes',
    'id' => $html($idFactura), 'nroFactura' => $html($datos['nroFactura']), 'fechaFactura' => $html($datos['fechaFactura']), 'opcionesClientes' => $opcionesClientes,
    'montoFactura' => $html($datos['montoFactura']), 'fechaPago' => $html($datos['fechaPago']), 'mensajeError' => $html($mensajeError), 'mensajeExito' => $html($mensajeExito),
    'estadoP' => $datos['estadoFactura'] === 'P' ? 'selected' : '', 'estadoC' => $datos['estadoFactura'] === 'C' ? 'selected' : '', 'estadoA' => $datos['estadoFactura'] === 'A' ? 'selected' : '',
    'tipoFactura' => $datos['tipoDocumento'] === 'FACTURA' ? 'selected' : '', 'tipoNotaCredito' => $datos['tipoDocumento'] === 'NOTA_CREDITO' ? 'selected' : '',
    'opcionesFacturasReferencia' => $opcionesFacturasReferencia, 'pdfFacturaHTML' => $pdfFacturaHTML, 'informeTecnicoHTML' => $informeTecnicoHTML,
));
echo $contenido->muestra();
?>
