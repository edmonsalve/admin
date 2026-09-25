<?php
    require_once('../includes/initSistema.php');

    $DB_DCODE = 'adm_dCode';
    $conexionDB = new DB_MySQLi;
    $conexionDB->conectar($DB_DCODE, DB_SERVER, DB_USER, DB_PASSWD);

    /*
     * La ficha se invoca desde editarPost(), que entrega IdRegistro por POST.
     * Al guardar, se reutiliza el guardador común, restringido a adm_sistemas.
     */
    $IdRegistro = $_POST['IdRegistro'] ?? 'new';
    $mensajeError = '';

    if (isset($_POST['guardarSistema'])) {
        $idAnterior = $_POST['idAnterior'] ?? 'new';
        $idSistema = trim($_POST['id'] ?? '');

        if (!ctype_digit($idSistema) || (int) $idSistema < 1 || (int) $idSistema > 999) {
            $mensajeError = 'El ID debe ser un número entre 1 y 999.';
        } elseif ($idAnterior !== 'new' && $idSistema !== (string) $idAnterior) {
            $mensajeError = 'El ID de un sistema existente no se puede modificar.';
        } else {
            $campos = ['nombre', 'estado', 'area', 'ruta', 'icono', 'btn', 'prefijoTablas', 'dbase', 'version', 'fechaDesarrollo', 'descripcion'];
            $datos = [];
            foreach ($campos as $campo) {
                $datos[$campo] = trim((string) ($_POST[$campo] ?? ''));
            }

            if ($datos['nombre'] === '') {
                $mensajeError = 'El nombre del sistema es obligatorio.';
            } elseif (!in_array($datos['estado'], ['S', 'N'], true) || !in_array($datos['area'], ['A', 'E', 'M', 'S'], true)) {
                $mensajeError = 'El área o estado informado no es válido.';
            } else {
                try {
                    $pdo = new PDO(
                        'mysql:host=' . DB_SERVER . ';dbname=' . $DB_DCODE . ';charset=utf8mb4',
                        DB_USER,
                        DB_PASSWD,
                        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                    );

                    if ($idAnterior === 'new') {
                        $sql = 'INSERT INTO adm_sistemas (id, ' . implode(', ', $campos) . ') VALUES (?' . str_repeat(', ?', count($campos)) . ')';
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute(array_merge([(int) $idSistema], array_values($datos)));
                    } else {
                        $asignaciones = implode(', ', array_map(static fn($campo) => "$campo = ?", $campos));
                        $stmt = $pdo->prepare("UPDATE adm_sistemas SET $asignaciones WHERE id = ?");
                        $stmt->execute(array_merge(array_values($datos), [(int) $idAnterior]));
                    }

                    header('Location: sistemas_ficha.php?IdRegistro=' . rawurlencode($idSistema));
                    exit;
                } catch (PDOException $e) {
                    $mensajeError = 'No fue posible guardar el sistema. Verifique que el ID no esté repetido.';
                }
            }
        }

        $IdRegistro = $idAnterior;
    }

    if (isset($_GET['IdRegistro'])) {
        $IdRegistro = $_GET['IdRegistro'];
    }

    if ($IdRegistro !== 'new' && !ctype_digit((string) $IdRegistro)) {
        header('Location: sistemas.php');
        exit;
    }

    $_id            = '';
    $_idAnterior    = $IdRegistro;
    $_nombre        = '';
    $_area          = '';
    $_ruta          = '';
    $_dbase         = '';
    $_prefijoTablas = '';
    $_icono         = '';
    $_btn           = '';
    $_version       = '';
    $_fechaDesarrollo = '';
    $_descripcion   = '';
    $_estado        = 'S';

    if ($IdRegistro !== 'new') {
        $consulta = "SELECT id, nombre, area, ruta, dbase, prefijoTablas, icono, btn, version, fechaDesarrollo, descripcion, estado
                       FROM `$DB_DCODE`.adm_sistemas
                       WHERE id = " . (int) $IdRegistro . "
                       LIMIT 1";
        $salida = $conexionDB->consulta($consulta);
        $row = mysqli_fetch_assoc($salida);

        if (!$row) {
            header('Location: sistemas.php');
            exit;
        }

        $_id            = $row['id'];
        $_idAnterior    = $_id;
        $_nombre        = $row['nombre'] ?? '';
        $_area          = $row['area'] ?? '';
        $_ruta          = $row['ruta'] ?? '';
        $_dbase         = $row['dbase'] ?? '';
        $_prefijoTablas = $row['prefijoTablas'] ?? '';
        $_icono         = $row['icono'] ?? '';
        $_btn           = $row['btn'] ?? '';
        $_version       = $row['version'] ?? '';
        $_fechaDesarrollo = $row['fechaDesarrollo'] ?? '';
        $_descripcion   = $row['descripcion'] ?? '';
        $_estado        = $row['estado'] ?? 'S';
    }

    $html = static function ($value): string {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    };

    $urlIcono = $_icono !== '' ? '/iconos/' . rawurlencode(basename($_icono)) : '';
    $urlBoton = $_btn !== '' ? '/btns/' . rawurlencode(basename($_btn)) : '';

    $areas = ['A' => 'Administración', 'E' => 'Educación', 'M' => 'Municipal', 'S' => 'Salud'];
    $optionAreas = '';
    foreach ($areas as $codigo => $nombreArea) {
        $selected = ($_area === $codigo) ? ' selected' : '';
        $optionAreas .= '<option value="' . $codigo . '"' . $selected . '>' . $nombreArea . '</option>';
    }

    $contenido = new plantilla('sistemas_ficha');
    $contenido->asigna_variables([
        'lang'             => $sistema_txt->GetDefinition('XMLLang'),
        'iconoNavegador'   => PATH_ICO . ICONO_NAVEGADOR,
        'topbar'           => $topbar,
        'barraLateral'     => $barraLateral,
        'headerMenu'       => $headerMenu,
        'H2Sistema'        => $sistema_txt->GetDefinition('H2Sistema'),
        'H2Titulo'         => 'Mantenedor de sistemas',
        'mensajeError'     => $html($mensajeError),
        'idReadonly'       => ($IdRegistro === 'new') ? '' : 'readonly',
        'mediaSistema'     => ($_id === '') ? 'none' : 'grid',
        'mensajeMedia'     => ($_id === '') ? 'Guarde primero el sistema para habilitar la carga de imágenes.' : '',
        '_id'               => $html($_id),
        '_idAnterior'       => $html($_idAnterior),
        '_nombre'           => $html($_nombre),
        '_ruta'             => $html($_ruta),
        '_dbase'            => $html($_dbase),
        '_prefijoTablas'    => $html($_prefijoTablas),
        '_icono'            => $html($_icono),
        '_btn'              => $html($_btn),
        '_version'          => $html($_version),
        '_fechaDesarrollo'  => $html($_fechaDesarrollo),
        '_descripcion'      => $html($_descripcion),
        'urlIcono'         => $html($urlIcono),
        'urlBoton'         => $html($urlBoton),
        'mostrarIcono'     => ($urlIcono === '') ? 'none' : 'block',
        'mostrarBoton'     => ($urlBoton === '') ? 'none' : 'block',
        'optionAreas'       => $optionAreas,
        'estadoActivo'      => ($_estado === 'S') ? 'selected' : '',
        'estadoInactivo'    => ($_estado === 'N') ? 'selected' : '',
    ]);

    echo $contenido->muestra();
?>
