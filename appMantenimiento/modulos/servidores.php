<?php
    if (isset($_GET['mod'])) { $idModuloIco = $_GET['mod']; } else { $idModuloIco = 0; }
    require_once('../includes/initSistema.php');

    $DB_DCODE = 'adm_dCode';
    $filename = str_replace(__DIR__ . '/', '', __FILE__);
    $moduloPHP = str_replace('.php', '', $filename);

    $conexionDB = new DB_MySQLi;
    $conexionDB->conectar($DB_DCODE, DB_SERVER, DB_USER, DB_PASSWD);

    $html = static function ($valor): string {
        return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    };

    // Clientes disponibles para el selector y para validar altas/modificaciones.
    $clientes = [];
    $salidaClientes = $conexionDB->consulta("SELECT idCliente, cliente FROM `$DB_DCODE`.adm_clientes ORDER BY cliente");
    while ($cliente = mysqli_fetch_assoc($salidaClientes)) {
        $clientes[(string) $cliente['idCliente']] = $cliente['cliente'];
    }

    $camposServidor = [
        'idCliente', 'nombreServidor', 'ipLocal', 'ipPublica', 'url', 'urlApiCliente',
        'phpVer', 'prefijoBD', 'puertoSQL', 'usrSQL', 'passSQL', 'usrSSH', 'puertoSSH',
        'passSSH', 'llavePrivada', 'tunelSSH', 'puertoHTTP',
    ];
    $camposPuerto = ['puertoSQL', 'puertoSSH', 'puertoHTTP'];
    $mensajeError = '';
    $mensajeExito = isset($_GET['guardado']) ? 'Servidor guardado correctamente.' : '';
    $operacion = $_GET['ope'] ?? '';
    $idRegistro = $_GET['IdRegistro'] ?? 'new';
    $datosServidor = array_fill_keys($camposServidor, '');
    $datosServidor['tunelSSH'] = 'N';
    $idServidor = '';

    $directorioLlaves = '/var/www/html/admin/llavePriv';
    $llavesPrivadas = [];
    if (is_dir($directorioLlaves)) {
        foreach (scandir($directorioLlaves) ?: [] as $nombreLlave) {
            if ($nombreLlave === '.' || $nombreLlave === '..' || str_ends_with($nombreLlave, '.pub')) { continue; }
            if (preg_match('/^[A-Za-z0-9._-]{1,50}$/', $nombreLlave) && is_file($directorioLlaves . '/' . $nombreLlave)) {
                $llavesPrivadas[] = $nombreLlave;
            }
        }
    }

    if (isset($_POST['guardarServidor'])) {
        $idRegistro = $_POST['idServidor'] ?? 'new';
        $operacion = $idRegistro === 'new' ? 'Add' : 'Update';
        $idServidor = (ctype_digit((string) $idRegistro) || $idRegistro === 'new') ? $idRegistro : '';

        foreach ($camposServidor as $campo) {
            $datosServidor[$campo] = trim((string) ($_POST[$campo] ?? ''));
        }

        if (!isset($clientes[$datosServidor['idCliente']])) {
            $mensajeError = 'Debe seleccionar un cliente válido.';
        } elseif ($datosServidor['nombreServidor'] === '') {
            $mensajeError = 'El nombre del servidor es obligatorio.';
        } elseif (!in_array($datosServidor['tunelSSH'], ['S', 'N'], true)) {
            $mensajeError = 'La opción de túnel SSH no es válida.';
        } elseif ($datosServidor['llavePrivada'] !== '' && !in_array($datosServidor['llavePrivada'], $llavesPrivadas, true)) {
            $mensajeError = 'La llave privada seleccionada no está disponible.';
        } elseif ($datosServidor['tunelSSH'] === 'S' && ($datosServidor['usrSSH'] === '' || $datosServidor['llavePrivada'] === '')) {
            $mensajeError = 'Para usar túnel SSH debe indicar usuario y llave privada.';
        } else {
            foreach ($camposPuerto as $campo) {
                if ($datosServidor[$campo] !== '' &&
                    (!ctype_digit($datosServidor[$campo]) || (int) $datosServidor[$campo] < 1 || (int) $datosServidor[$campo] > 65535)) {
                    $mensajeError = 'Los puertos deben ser números entre 1 y 65535.';
                    break;
                }
            }
        }

        if ($mensajeError === '') {
            // Los puertos son opcionales; al no informarlos se persisten como NULL,
            // no como una cadena vacía que MySQL estricto rechaza en columnas numéricas.
            $valoresPersistir = $datosServidor;
            foreach ($camposPuerto as $campo) {
                $valoresPersistir[$campo] = $datosServidor[$campo] === '' ? null : (int) $datosServidor[$campo];
            }

            try {
                $pdo = new PDO(
                    'mysql:host=' . DB_SERVER . ';dbname=' . $DB_DCODE . ';charset=latin1',
                    DB_USER,
                    DB_PASSWD,
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );

                if ($idRegistro === 'new') {
                    // La tabla no tiene AUTO_INCREMENT: se asigna el siguiente identificador disponible.
                    $idServidor = (int) $pdo->query('SELECT COALESCE(MAX(idServidor), 0) + 1 FROM adm_servidores')->fetchColumn();
                    $columnas = array_merge(['idServidor'], $camposServidor);
                    $marcadores = implode(', ', array_fill(0, count($columnas), '?'));
                    $stmt = $pdo->prepare('INSERT INTO adm_servidores (' . implode(', ', $columnas) . ') VALUES (' . $marcadores . ')');
                    $stmt->execute(array_merge([$idServidor], array_values($valoresPersistir)));
                } elseif (ctype_digit((string) $idRegistro)) {
                    $idServidor = (int) $idRegistro;
                    $asignaciones = implode(', ', array_map(static fn($campo) => "$campo = ?", $camposServidor));
                    $stmt = $pdo->prepare("UPDATE adm_servidores SET $asignaciones WHERE idServidor = ?");
                    $stmt->execute(array_merge(array_values($valoresPersistir), [$idServidor]));
                } else {
                    throw new RuntimeException('Identificador de servidor inválido.');
                }

                header('Location: servidores.php?IdRegistro=' . rawurlencode((string) $idServidor) . '&ope=Update&guardado=1');
                exit;
            } catch (Throwable $e) {
                $mensajeError = 'No fue posible guardar el servidor. Intente nuevamente.';
            }
        }
    }

    if ($mensajeError === '' && $operacion === 'Update') {
        if (!ctype_digit((string) $idRegistro)) {
            header('Location: servidores.php');
            exit;
        }

        $idServidor = (int) $idRegistro;
        $salidaServidor = $conexionDB->consulta(
            "SELECT " . implode(', ', $camposServidor) . " FROM `$DB_DCODE`.adm_servidores WHERE idServidor = $idServidor LIMIT 1"
        );
        $servidor = mysqli_fetch_assoc($salidaServidor);
        if (!$servidor) {
            header('Location: servidores.php');
            exit;
        }
        foreach ($camposServidor as $campo) {
            $datosServidor[$campo] = $servidor[$campo] ?? '';
        }
    }

    if ($operacion === 'Add') {
        $idServidor = 'new';
    }

    $opcionesClientes = '<option value="">Seleccione un cliente</option>';
    foreach ($clientes as $idCliente => $cliente) {
        $selected = ((string) $datosServidor['idCliente'] === (string) $idCliente) ? ' selected' : '';
        $opcionesClientes .= '<option value="' . $html($idCliente) . '"' . $selected . '>' . $html($cliente) . '</option>';
    }
    $opcionesLlaves = '<option value="">Seleccione una llave privada</option>';
    foreach ($llavesPrivadas as $llavePrivada) {
        $seleccionada = $datosServidor['llavePrivada'] === $llavePrivada ? ' selected' : '';
        $opcionesLlaves .= '<option value="' . $html($llavePrivada) . '"' . $seleccionada . '>' . $html($llavePrivada) . '</option>';
    }

    // Datos de la grilla: se reciben por JSON en las actualizaciones asíncronas.
    $input = json_decode(file_get_contents('php://input'), true);
    if (!is_array($input)) { $input = []; }
    $arrayCampos = [];
    $arrayValores = [];
    $recibioAuxiliares = array_key_exists('auxiliares', $input);
    foreach (($input['auxiliares'] ?? []) as $filtro) {
        if (!is_array($filtro) || ($filtro['idaux'] ?? '') !== 'aux1' ||
            ($filtro['campo'] ?? '') !== 's.idCliente') {
            continue;
        }
        $idClienteFiltro = (string) ($filtro['valor'] ?? '');
        if (ctype_digit($idClienteFiltro) && isset($clientes[$idClienteFiltro])) {
            $arrayCampos['aux1'] = 's.idCliente';
            $arrayValores['aux1'] = $idClienteFiltro;
        }
    }
    $_pag = (int) ($input['pag'] ?? 1);
    if ($_pag < 1) { $_pag = 1; }
    $_filas = (int) ($input['filas'] ?? PAGINACION);
    if (!in_array($_filas, [8, 10, 12, 15, 20], true)) { $_filas = PAGINACION; }
    $_sentido = strtoupper((string) ($input['sentido'] ?? 'ASC')) === 'DESC' ? 'DESC' : 'ASC';
    $ordenarPorOptions = ['s.nombreServidor', 'c.cliente', 's.ipPublica', 's.prefijoBD'];
    $_ordenarPor = (string) ($input['ordenarPor'] ?? $ordenarPorOptions[0]);
    if (!in_array($_ordenarPor, $ordenarPorOptions, true)) { $_ordenarPor = $ordenarPorOptions[0]; }
    foreach ($ordenarPorOptions as $indice => $campo) {
        $_ordSel[$indice] = $_ordenarPor === $campo ? 'selected' : '';
    }
    foreach ([8, 10, 12, 15, 20] as $fila) {
        ${'fl' . $fila} = $_filas === $fila ? 'selected' : '';
    }
    $sentASC = $_sentido === 'ASC' ? 'selected' : '';
    $sentDESC = $_sentido === 'DESC' ? 'selected' : '';

    $filtroCliente = (string) ($arrayValores['aux1'] ?? ($recibioAuxiliares ? '' : ($_SESSION['aux1Valor'] ?? '')));
    if ($filtroCliente !== '' && isset($clientes[$filtroCliente])) {
        $arrayCampos['aux1'] = 's.idCliente';
        $arrayValores['aux1'] = $filtroCliente;
    } else {
        $filtroCliente = '';
    }
    $opcionesClientesFiltro = '<option value="">Todos los clientes</option>';
    foreach ($clientes as $idClienteFiltro => $clienteFiltro) {
        $seleccionado = (string) $idClienteFiltro === $filtroCliente ? ' selected' : '';
        $opcionesClientesFiltro .= '<option value="' . $html($idClienteFiltro) . '"' . $seleccionado . '>' . $html($clienteFiltro) . '</option>';
    }
    $criterio = (string) ($input['iguala'] ?? '');
    $buscarpor = 'servidores_busqueda';

    $tablaDB = 'adm_servidores';
    $IdCampo = 'idServidor';
    $tablaDatos = [
        'consulta' => "SELECT s.idServidor, s.nombreServidor, c.cliente, s.ipLocal, s.ipPublica, s.url, s.phpVer, s.prefijoBD, s.puertoSQL
                        FROM `$DB_DCODE`.adm_servidores s
                        LEFT JOIN `$DB_DCODE`.adm_clientes c ON c.idCliente = s.idCliente",
        'ordenarPor' => "$_ordenarPor $_sentido",
        'columnas' => [
            ['campo' => 'colorFila'],
            ['campo' => 'nombreServidor', 'ancho' => '5', 'titulo' => 'Servidor', 'alin' => 'L'],
            ['campo' => 'cliente', 'ancho' => '5', 'titulo' => 'Cliente', 'alin' => 'L'],
            ['campo' => 'ipPublica', 'ancho' => '3', 'titulo' => 'IP pública', 'alin' => 'L'],
            ['campo' => 'url', 'ancho' => '4', 'titulo' => 'URL', 'alin' => 'L'],
            ['campo' => 'phpVer', 'ancho' => '2', 'titulo' => 'PHP', 'alin' => 'C'],
            ['campo' => 'prefijoBD', 'ancho' => '2', 'titulo' => 'Prefijo BD', 'alin' => 'L'],
            ['campo' => 'puertoSQL', 'ancho' => '2', 'titulo' => 'P. SQL', 'alin' => 'R'],
        ],
        'accionesG1' => [
            ['funcion' => 'abrirServidor', 'icono' => 'btn_editar.png', 'titulo' => 'Editar', 'parametros' => 'idServidor', 'paramEstaticos' => '', 'modPHP' => ''],
        ],
        'setupTabla' => [
            'funcionBusqueda' => 'filtrarJSON', 'conPaginacion' => true, 'lineasPorPagina' => $_filas,
            'camposBusquedaCalculada' => [
                'servidores_busqueda' => "CONCAT_WS(' ', s.nombreServidor, c.cliente, s.url)",
            ],
            'soloConFiltro' => false, 'conFiltro' => true, 'ignorarWhere' => false,
            'accionesGrupo1' => true, 'anchoAccGrupo1' => '1', 'tituloAccGrupo1' => 'Acc.',
            'accionesGrupo2' => false, 'anchoAccGrupo2' => '0', 'tituloAccGrupo2' => '',
            'grillaPeq' => false, 'colorEncabezado' => '', 'manuscrito' => false, 'verConsulta' => false,
        ],
    ];

    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) ||
        (isset($_SERVER['CONTENT_TYPE']) && str_contains($_SERVER['CONTENT_TYPE'], 'application/json'));
    if ($isAjax) {
        header('Content-Type: text/html; charset=UTF-8');
        $_async = 1;
        include(PATH_INCLUDES . 'grillaLeeRes3.php');
        exit;
    }

    $_async = 0;
    include(PATH_INCLUDES . 'grillaLeeRes3.php');
    $contenido = new plantilla('servidores');
    $contenido->asigna_variables([
        'lang' => $sistema_txt->GetDefinition('XMLLang'), 'icono' => PATH_ICO . ICONO_NAVEGADOR,
        'topbar' => $topbar, 'barraLateral' => $barraLateral, 'headerMenu' => $headerMenu,
        'moduloPHP' => $moduloPHP, 'H2Sistema' => $sistema_txt->GetDefinition('H2Sistema'),
        'H2Titulo' => 'Mantenedor de servidores',
        'grillaHTMLTit' => $encabezadoHTML, 'grillaHTML' => $filasHTML,
        'paginacionHTML' => $paginacionHTML, 'pagina' => $_pag, 'iguala' => $html($criterio),
        '_async' => $_async, '_ord0' => $_ordSel[0], '_ord1' => $_ordSel[1], '_ord2' => $_ordSel[2], '_ord3' => $_ordSel[3],
        'sentASC' => $sentASC, 'sentDESC' => $sentDESC, 'fl8' => $fl8, 'fl10' => $fl10, 'fl12' => $fl12, 'fl15' => $fl15, 'fl20' => $fl20,
        'mostrarFormulario' => in_array($operacion, ['Add', 'Update'], true) ? 'flex' : 'none',
        'tituloFormulario' => $operacion === 'Add' ? 'Nuevo servidor' : 'Editar servidor',
        'idServidor' => $html($idServidor), 'opcionesClientes' => $opcionesClientes,
        'opcionesClientesFiltro' => $opcionesClientesFiltro,
        'mensajeError' => $html($mensajeError), 'mensajeExito' => $html($mensajeExito),
        '_nombreServidor' => $html($datosServidor['nombreServidor']), '_ipLocal' => $html($datosServidor['ipLocal']),
        '_ipPublica' => $html($datosServidor['ipPublica']), '_url' => $html($datosServidor['url']),
        '_urlApiCliente' => $html($datosServidor['urlApiCliente']), '_phpVer' => $html($datosServidor['phpVer']),
        '_prefijoBD' => $html($datosServidor['prefijoBD']), '_puertoSQL' => $html($datosServidor['puertoSQL']),
        '_usrSQL' => $html($datosServidor['usrSQL']), '_passSQL' => $html($datosServidor['passSQL']),
        '_usrSSH' => $html($datosServidor['usrSSH']), '_puertoSSH' => $html($datosServidor['puertoSSH']),
        '_passSSH' => $html($datosServidor['passSSH']), '_puertoHTTP' => $html($datosServidor['puertoHTTP']),
        'opcionesLlaves' => $opcionesLlaves,
        'tunelSSHSi' => $datosServidor['tunelSSH'] === 'S' ? 'selected' : '',
        'tunelSSHNo' => $datosServidor['tunelSSH'] === 'N' ? 'selected' : '',
    ]);
    echo $contenido->muestra();
?>
