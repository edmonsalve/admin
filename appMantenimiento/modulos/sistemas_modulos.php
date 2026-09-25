<?php
    require_once('../includes/initSistema.php');

    $DB_DCODE = 'adm_dCode';
    // grillaPaginacion3 usa esta variable para construir los enlaces AJAX.
    $moduloPHP = 'sistemas_modulos';
    $conexionDB = new DB_MySQLi;
    $conexionDB->conectar($DB_DCODE, DB_SERVER, DB_USER, DB_PASSWD);

    $tiposModulo = [];
    $consultaTiposModulo = $conexionDB->consulta("SELECT tipo FROM adm_modulos WHERE TRIM(tipo) <> '' UNION SELECT tipoGrupo FROM adm_estrucMenu ORDER BY tipo");
    while ($tipoModulo = mysqli_fetch_assoc($consultaTiposModulo)) {
        $tiposModulo[] = $tipoModulo['tipo'];
    }

    $input = json_decode(file_get_contents('php://input'), true) ?: [];
    $auxiliares = $input['auxiliares'] ?? [];
    $arrayCampos = [];
    $arrayValores = [];
    foreach ($auxiliares as $filtro) {
        $idAux = $filtro['idaux'] ?? '';
        if ($idAux !== '') {
            $arrayCampos[$idAux] = $filtro['campo'] ?? '';
            $arrayValores[$idAux] = $filtro['valor'] ?? '';
        }
    }

    $idSistema = $_GET['idSistema'] ?? ($arrayValores['aux5'] ?? ($_POST['idSistema'] ?? ''));
    if (!ctype_digit((string) $idSistema)) {
        header('Location: sistemas.php');
        exit;
    }
    $idSistema = (int) $idSistema;

    $respuestaPost = static function (string $url): void {
        header('Location: ' . $url);
        exit;
    };

    if (isset($_POST['accion']) && $_POST['accion'] === 'estado') {
        $idModulo = $_POST['idModulo'] ?? '';
        $estado = $_POST['estado'] ?? '';
        if (ctype_digit((string) $idModulo) && in_array($estado, ['0', '1'], true)) {
            $pdo = new PDO(
                'mysql:host=' . DB_SERVER . ';dbname=' . $DB_DCODE . ';charset=utf8mb4',
                DB_USER,
                DB_PASSWD,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            $stmt = $pdo->prepare('UPDATE adm_modulos SET estado = ? WHERE id = ? AND idsistema = ?');
            $stmt->execute([$estado, (int) $idModulo, $idSistema]);
        }
        $respuestaPost('sistemas_modulos.php?idSistema=' . $idSistema);
    }

    $mensajeError = '';
    if (isset($_POST['accion']) && $_POST['accion'] === 'guardar') {
        $idModulo = $_POST['idModulo'] ?? 'new';
        $campos = ['modulo', 'detalle', 'tipo', 'ordenICO', 'php', 'parametros', 'txtIco', 'icon', 'target', 'estado', 'sistemaVer', 'movil'];
        $datos = [];
        foreach ($campos as $campo) {
            $datos[$campo] = trim((string) ($_POST[$campo] ?? ''));
        }

        if ($datos['modulo'] === '' || $datos['php'] === '' || $datos['tipo'] === '') {
            $mensajeError = 'Módulo, tipo y archivo PHP son obligatorios.';
        } elseif (!preg_match('/^[A-Za-z0-9]{1,5}$/', $datos['tipo']) || !ctype_digit($datos['ordenICO']) || !in_array($datos['target'], ['T', 'B'], true) || !in_array($datos['estado'], ['0', '1'], true) || !in_array($datos['sistemaVer'], ['1', '2'], true) || !in_array($datos['movil'], ['0', '1', '2'], true)) {
            $mensajeError = 'Revise tipo, orden de ícono, destino, estado, versión y disponibilidad móvil.';
        } else {
            try {
                $pdo = new PDO(
                    'mysql:host=' . DB_SERVER . ';dbname=' . $DB_DCODE . ';charset=utf8mb4',
                    DB_USER,
                    DB_PASSWD,
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );

                if ($idModulo === 'new') {
                    $base = $idSistema * 1000;
                    $consultaId = $pdo->prepare('SELECT COALESCE(MAX(id), ?) + 1 FROM adm_modulos WHERE id >= ? AND id < ?');
                    $consultaId->execute([$base, $base, $base + 1000]);
                    $nuevoId = (int) $consultaId->fetchColumn();
                    $insertar = $pdo->prepare('INSERT INTO adm_modulos (id, idModulo, idsistema, modulo, detalle, estado, tipo, ordenICO, php, parametros, txtIco, icon, target, sistemaVer, movil) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
                    $insertar->execute([$nuevoId, $nuevoId, $idSistema, $datos['modulo'], $datos['detalle'], (int) $datos['estado'], $datos['tipo'], (int) $datos['ordenICO'], $datos['php'], $datos['parametros'], $datos['txtIco'], $datos['icon'], $datos['target'], (int) $datos['sistemaVer'], (int) $datos['movil']]);
                } elseif (ctype_digit((string) $idModulo)) {
                    $actualizar = $pdo->prepare('UPDATE adm_modulos SET modulo = ?, detalle = ?, tipo = ?, ordenICO = ?, php = ?, parametros = ?, txtIco = ?, icon = ?, target = ?, estado = ?, sistemaVer = ?, movil = ? WHERE id = ? AND idsistema = ?');
                    $actualizar->execute([$datos['modulo'], $datos['detalle'], $datos['tipo'], (int) $datos['ordenICO'], $datos['php'], $datos['parametros'], $datos['txtIco'], $datos['icon'], $datos['target'], (int) $datos['estado'], (int) $datos['sistemaVer'], (int) $datos['movil'], (int) $idModulo, $idSistema]);
                } else {
                    throw new RuntimeException('Módulo inválido.');
                }
                $respuestaPost('sistemas_modulos.php?idSistema=' . $idSistema);
            } catch (Throwable $e) {
                $mensajeError = 'No fue posible guardar el módulo. Revise los datos ingresados.';
            }
        }
    }

    $consultaSistema = $conexionDB->consulta("SELECT id, nombre FROM adm_sistemas WHERE id = $idSistema LIMIT 1");
    $sistema = mysqli_fetch_assoc($consultaSistema);
    if (!$sistema) {
        $respuestaPost('sistemas.php');
    }

    $idModuloEdicion = $_GET['idModulo'] ?? ($_POST['idModulo'] ?? '');
    $esNuevo = $idModuloEdicion === 'new';
    $datosModulo = ['id' => '', 'modulo' => '', 'detalle' => '', 'tipo' => '', 'ordenICO' => '0', 'php' => '', 'parametros' => '', 'txtIco' => '', 'icon' => '', 'target' => 'T', 'estado' => '1', 'sistemaVer' => '2', 'movil' => '2'];
    $mostrarFicha = $esNuevo;
    if (!$esNuevo && ctype_digit((string) $idModuloEdicion)) {
        $consultaModulo = $conexionDB->consulta('SELECT * FROM adm_modulos WHERE id = ' . (int) $idModuloEdicion . ' AND idsistema = ' . $idSistema . ' LIMIT 1');
        $filaModulo = mysqli_fetch_assoc($consultaModulo);
        if ($filaModulo) {
            $datosModulo = $filaModulo;
            $mostrarFicha = true;
        }
    }
    if ($mensajeError !== '') {
        foreach (array_keys($datosModulo) as $campo) {
            if (isset($_POST[$campo])) {
                $datosModulo[$campo] = $_POST[$campo];
            }
        }
        $mostrarFicha = true;
    }

    $llaveFiltroTipo = 'sistemas_modulos_tipo_' . $idSistema;
    if (($arrayValores['aux1'] ?? null) === '__TODOS__') {
        $_SESSION[$llaveFiltroTipo] = '';
        unset($arrayCampos['aux1'], $arrayValores['aux1']);
    } elseif (isset($arrayValores['aux1'])) {
        $_SESSION[$llaveFiltroTipo] = $arrayValores['aux1'];
    }
    $tipoFiltro = $_SESSION[$llaveFiltroTipo] ?? '';
    if (!preg_match('/^[A-Za-z0-9]{1,5}$/', (string) $tipoFiltro)) {
        $tipoFiltro = '';
    }

    $_pag = $input['pag'] ?? 1;
    $_filas = $input['filas'] ?? PAGINACION;
    $_ordenarPor = $input['ordenarPor'] ?? 'm.tipo, m.modulo';
    $_sentido = strtoupper($input['sentido'] ?? 'ASC') === 'DESC' ? 'DESC' : 'ASC';
    $ordenarPorOptions = ['m.tipo, m.modulo', 'm.modulo', 'm.php', 'm.id'];
    if (!in_array($_ordenarPor, $ordenarPorOptions, true)) {
        $_ordenarPor = 'm.tipo, m.modulo';
    }
    foreach ($ordenarPorOptions as $indice => $campo) {
        $_ordSel[$indice] = $_ordenarPor === $campo ? 'selected' : '';
    }

    $arrayCriterios[1] = ['campo' => 'L,m.modulo', 'descripcion' => 'Módulo'];
    $arrayCriterios[2] = ['campo' => 'L,m.detalle', 'descripcion' => 'Detalle'];
    $arrayCriterios[3] = ['campo' => 'L,m.php', 'descripcion' => 'Archivo PHP'];
    $criterio = $input['iguala'] ?? '';
    $buscarpor = $input['buscarpor'] ?? 'L,m.modulo';
    $input['filas'] = $_filas;
    $input['sentido'] = $_sentido;
    include(PATH_INCLUDES . '@grillaSentidoFilas.php');
    include(PATH_INCLUDES . '@grillaCriterios.php');

    $html = static function ($valor): string {
        return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    };

    $opcionesTipo = '<option value="__TODOS__"' . ($tipoFiltro === '' ? ' selected' : '') . '>Todos los tipos</option>';
    $opcionesTipoFicha = '<option value="">Seleccione tipo</option>';
    $titulosTipo = [];
    $consultaGrupos = $conexionDB->consulta('SELECT tipoGrupo, tituloGrupo FROM adm_estrucMenu');
    while ($grupo = mysqli_fetch_assoc($consultaGrupos)) {
        $titulosTipo[$grupo['tipoGrupo']] = $grupo['tituloGrupo'];
    }
    foreach ($tiposModulo as $codigo) {
        $texto = $codigo . (isset($titulosTipo[$codigo]) ? ' - ' . $titulosTipo[$codigo] : '');
        $seleccionFiltro = $tipoFiltro === $codigo ? ' selected' : '';
        $seleccionFicha = $datosModulo['tipo'] === $codigo ? ' selected' : '';
        $opcionesTipo .= '<option value="' . $html($codigo) . '"' . $seleccionFiltro . '>' . $html($texto) . '</option>';
        $opcionesTipoFicha .= '<option value="' . $html($codigo) . '"' . $seleccionFicha . '>' . $html($texto) . '</option>';
    }

    $tablaDatos = [
        'consulta' => "SELECT m.id, m.idsistema, m.modulo, m.detalle, m.tipo, COALESCE(e.tituloGrupo, 'SIN DEFINIR') AS tipoDescripcion, m.ordenICO, m.php, m.icon, m.target, m.estado, m.movil, IF(m.estado = 0, 'LightYellow', '') AS colorFila FROM `$DB_DCODE`.adm_modulos m LEFT JOIN `$DB_DCODE`.adm_estrucMenu e ON e.tipoGrupo = m.tipo WHERE m.idsistema = $idSistema" . ($tipoFiltro !== '' ? " AND m.tipo = '" . $conexionDB->escapaDatos($tipoFiltro) . "'" : ''),
        'ordenarPor' => $_ordenarPor . ' ' . $_sentido,
        'columnas' => [
            ['campo' => 'colorFila'],
            ['campo' => 'id', 'ancho' => '2', 'titulo' => 'ID', 'alin' => 'R'],
            ['campo' => 'modulo', 'ancho' => '6', 'titulo' => 'Módulo'],
            ['campo' => 'detalle', 'ancho' => '5', 'titulo' => 'Detalle'],
            ['campo' => 'tipo', 'ancho' => '1', 'titulo' => 'Tipo', 'alin' => 'C'],
            ['campo' => 'php', 'ancho' => '4', 'titulo' => 'Archivo PHP'],
            ['campo' => 'ordenICO', 'ancho' => '1', 'titulo' => 'Ord.', 'alin' => 'R'],
            ['campo' => 'icon', 'ancho' => '3', 'titulo' => 'Ícono'],
            ['campo' => 'target', 'ancho' => '1', 'titulo' => 'Dest.', 'alin' => 'C'],
            ['campo' => 'estado', 'ancho' => '1', 'titulo' => 'Estado', 'alin' => 'C'],
        ],
        'accionesG1' => [
            ['funcion' => 'editarModuloSistema', 'icono' => 'btn_editar.png', 'titulo' => 'Editar módulo', 'parametros' => 'id,idsistema', 'paramEstaticos' => '', 'modPHP' => ''],
            ['funcion' => 'cambiarEstadoModulo', 'icono' => 'btn_cancel.png', 'titulo' => 'Activar o desactivar módulo', 'parametros' => 'id,idsistema,estado', 'paramEstaticos' => '', 'modPHP' => ''],
        ],
        'setupTabla' => [
            'funcionBusqueda' => 'filtrarJSON', 'conPaginacion' => true, 'lineasPorPagina' => $_filas,
            'soloConFiltro' => false, 'conFiltro' => true, 'ignorarWhere' => false,
            'accionesGrupo1' => true, 'anchoAccGrupo1' => '2', 'tituloAccGrupo1' => 'Acc.',
            'accionesGrupo2' => false, 'grillaPeq' => false, 'colorEncabezado' => '', 'manuscrito' => false,
        ],
    ];

    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_SERVER['CONTENT_TYPE']) && str_contains($_SERVER['CONTENT_TYPE'], 'application/json'));
    if ($isAjax) {
        header('Content-Type: text/html; charset=UTF-8');
        ob_start();
        $_async = 1;
        include(PATH_INCLUDES . 'grillaLeeRes3.php');
        echo ob_get_clean();
        exit;
    }
    $_async = 0;
    include(PATH_INCLUDES . 'grillaLeeRes3.php');

    $contenido = new plantilla('sistemas_modulos');
    $contenido->asigna_variables([
        'lang' => $sistema_txt->GetDefinition('XMLLang'), 'icono' => PATH_ICO . ICONO_NAVEGADOR,
        'topbar' => $topbar, 'barraLateral' => $barraLateral, 'headerMenu' => $headerMenu,
        'H2Sistema' => $sistema_txt->GetDefinition('H2Sistema'), 'H2Titulo' => 'Mantenedor de módulos',
        'idSistema' => $idSistema, 'nombreSistema' => $html($sistema['nombre']), 'moduloPHP' => 'sistemas_modulos',
        'pagina' => $_pag, 'iguala' => $html($criterio), 'htmlCriterios' => $htmlCriterios,
        'opcionesTipo' => $opcionesTipo, 'opcionesTipoFicha' => $opcionesTipoFicha,
        'grillaHTMLTit' => $encabezadoHTML, 'grillaHTML' => $filasHTML, 'paginacionHTML' => $paginacionHTML,
        'sentASC' => $sentASC, 'sentDESC' => $sentDESC, '_ord0' => $_ordSel[0], '_ord1' => $_ordSel[1], '_ord2' => $_ordSel[2], '_ord3' => $_ordSel[3],
        'fl8' => $fl8, 'fl10' => $fl10, 'fl12' => $fl12, 'fl15' => $fl15, 'fl20' => $fl20,
        'mostrarFicha' => $mostrarFicha ? 'block' : 'none', 'idModulo' => $html($datosModulo['id'] ?? ''),
        'modulo' => $html($datosModulo['modulo']), 'detalle' => $html($datosModulo['detalle']), 'php' => $html($datosModulo['php']), 'parametros' => $html($datosModulo['parametros']),
        'ordenICO' => $html($datosModulo['ordenICO']), 'txtIco' => $html($datosModulo['txtIco']), 'icon' => $html($datosModulo['icon']),
        'targetT' => $datosModulo['target'] === 'T' ? 'selected' : '', 'targetB' => $datosModulo['target'] === 'B' ? 'selected' : '',
        'estadoActivo' => (string) $datosModulo['estado'] === '1' ? 'selected' : '', 'estadoInactivo' => (string) $datosModulo['estado'] === '0' ? 'selected' : '',
        'versionActual' => (string) $datosModulo['sistemaVer'] === '2' ? 'selected' : '', 'versionAntigua' => (string) $datosModulo['sistemaVer'] === '1' ? 'selected' : '',
        'movilNo' => (string) $datosModulo['movil'] === '0' ? 'selected' : '', 'movilSi' => (string) $datosModulo['movil'] === '2' ? 'selected' : '',
        'mensajeError' => $html($mensajeError), '_async' => $_async,
    ]);
    echo $contenido->muestra();
?>
