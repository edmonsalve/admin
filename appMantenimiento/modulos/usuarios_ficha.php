<?php
require_once('../includes/initSistema.php');

$DB_DCODE = 'adm_dCode';
$conexionDB = new DB_MySQLi;
$conexionDB->conectar($DB_DCODE, DB_SERVER, DB_USER, DB_PASSWD);

$html = static function ($valor): string { return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); };
$campos = array('nombre', 'tipoUser', 'foto', 'email', 'estado');
$datos = array('nombre' => '', 'tipoUser' => 'T', 'foto' => 'foto.jpg', 'email' => '', 'estado' => 'A');
$usuarioOriginal = $_POST['usuarioOriginal'] ?? $_GET['IdRegistro'] ?? 'new';
$usuario = $usuarioOriginal;
$mensajeError = '';
$mensajeExito = isset($_GET['guardado']) ? 'Usuario guardado correctamente.' : '';

if (isset($_POST['guardarUsuario'])) {
    $usuario = trim((string) ($_POST['username'] ?? ''));
    foreach ($campos as $campo) { $datos[$campo] = trim((string) ($_POST[$campo] ?? '')); }
    $clave = (string) ($_POST['clave'] ?? '');

    if (!preg_match('/^[A-Za-z0-9._-]{3,20}$/', $usuario)) {
        $mensajeError = 'El usuario debe tener entre 3 y 20 caracteres: letras, números, punto, guion o guion bajo.';
    } elseif ($usuarioOriginal !== 'new' && $usuario !== $usuarioOriginal) {
        $mensajeError = 'El nombre de un usuario existente no se puede modificar.';
    } elseif ($datos['nombre'] === '') {
        $mensajeError = 'Debe indicar el nombre del usuario.';
    } elseif ($usuarioOriginal === 'new' && $clave === '') {
        $mensajeError = 'Debe indicar una contraseña para el nuevo usuario.';
    } elseif ($datos['email'] !== '' && !filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
        $mensajeError = 'El correo no es válido.';
    } elseif (!in_array($datos['tipoUser'], array('D', 'T', 'A'), true) || !in_array($datos['estado'], array('A', 'I'), true)) {
        $mensajeError = 'El tipo o estado informado no es válido.';
    } elseif (empty($_SESSION['save'])) {
        $mensajeError = 'No dispone de permisos para guardar usuarios.';
    }

    if ($mensajeError === '') {
        try {
            $pdo = new PDO('mysql:host=' . DB_SERVER . ';dbname=' . $DB_DCODE . ';charset=latin1', DB_USER, DB_PASSWD, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
            if ($usuarioOriginal === 'new') {
                $stmt = $pdo->prepare('INSERT INTO adm_users (username, password, nombre, tipoUser, foto, email, estado) VALUES (?, ?, ?, ?, ?, ?, ?)');
                $stmt->execute(array($usuario, sha1($clave), $datos['nombre'], $datos['tipoUser'], $datos['foto'], $datos['email'], $datos['estado']));
            } else {
                $sql = 'UPDATE adm_users SET nombre=?, tipoUser=?, foto=?, email=?, estado=?';
                $parametros = array($datos['nombre'], $datos['tipoUser'], $datos['foto'], $datos['email'], $datos['estado']);
                if ($clave !== '') { $sql .= ', password=?'; $parametros[] = sha1($clave); }
                $sql .= ' WHERE username=?';
                $parametros[] = $usuarioOriginal;
                $stmt = $pdo->prepare($sql);
                $stmt->execute($parametros);
            }
            header('Location: usuarios_ficha.php?IdRegistro=' . rawurlencode($usuario) . '&guardado=1');
            exit;
        } catch (Throwable $e) {
            $mensajeError = 'No fue posible guardar el usuario. Verifique que el nombre no esté repetido.';
        }
    }
} elseif ($usuarioOriginal !== 'new') {
    $usuario = $usuarioOriginal;
    $usuarioSql = $conexionDB->escapaDatos($usuario);
    $salida = $conexionDB->consulta("SELECT username, nombre, tipoUser, foto, email, estado FROM `$DB_DCODE`.adm_users WHERE username = '$usuarioSql' LIMIT 1");
    $registro = mysqli_fetch_assoc($salida);
    if (!$registro) { header('Location: usuarios.php'); exit; }
    foreach ($campos as $campo) { $datos[$campo] = $registro[$campo] ?? $datos[$campo]; }
}

$sel = static function ($valor, $actual): string { return $valor === $actual ? 'selected' : ''; };
$contenido = new plantilla('usuarios_ficha');
$contenido->asigna_variables(array(
    'lang' => $sistema_txt->GetDefinition('XMLLang'), 'icono' => PATH_ICO . ICONO_NAVEGADOR,
    'topbar' => $topbar, 'barraLateral' => $barraLateral, 'headerMenu' => $headerMenu,
    'H2Sistema' => $sistema_txt->GetDefinition('H2Sistema'), 'H2Titulo' => $usuarioOriginal === 'new' ? 'Nuevo usuario' : 'Editar usuario',
    'usuarioOriginal' => $html($usuarioOriginal), 'username' => $html($usuario), 'usernameReadonly' => $usuarioOriginal === 'new' ? '' : 'readonly',
    'nombre' => $html($datos['nombre']), 'foto' => $html($datos['foto']), 'email' => $html($datos['email']),
    'mensajeError' => $html($mensajeError), 'mensajeExito' => $html($mensajeExito),
    'tipoD' => $sel('D', $datos['tipoUser']), 'tipoT' => $sel('T', $datos['tipoUser']), 'tipoA' => $sel('A', $datos['tipoUser']),
    'estadoA' => $sel('A', $datos['estado']), 'estadoI' => $sel('I', $datos['estado']),
));
echo $contenido->muestra();
?>
