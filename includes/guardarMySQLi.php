<?php
/**
 * guardar_mysqli.php
 * Requiere: PHP 8.1+, MySQL 5.7+/MariaDB 10.x, extensión mysqli.
 * Conexión: ajusta host/usuario/pass/dbName.
 */

declare(strict_types=1);

// ====== Parámetros esperados (como en tu script) ======
/*
$guardarBD      = 'nombreBD';           // opcional. Si lo usas, NO incluyas el punto final.
$guardarTabla   = 'mi_tabla';
$IdCampo        = 'id';
$reemplazarpor  = 'rut';                 // opcional
$camposCheckbox = 'campochk1,campochk2';// opcional
$ignorarPost    = 'campo1,campo2';      // opcional
$mayusculas     = true;                 // opcional (default true)
$muestraConsulta= false;                // opcional
$muestraPost    = false;                // opcional
$ejecutaConsulta= true;                 // opcional
$objetivo       = 'rut';                // opcional: para log
$IdPadre        = 'idPadre';            // opcional: nombre del campo con id del padre
*/

$guardarBD       = $guardarBD      ?? '';
$guardarTabla    = $guardarTabla   ?? '';
$IdCampo         = $IdCampo        ?? 'id';
$reemplazarpor   = $reemplazarpor  ?? '';
$camposCheckbox  = $camposCheckbox ?? '';
$ignorarPost     = $ignorarPost    ?? '';
$mayusculas      = $mayusculas     ?? true;
$muestraConsulta = $muestraConsulta?? false;
$muestraPost     = $muestraPost    ?? false;
$ejecutaConsulta = $ejecutaConsulta?? true;

// ====== Conexión mysqli (ajusta credenciales) ======
$mysqli = new mysqli('localhost', 'usuario', 'password', 'nombreBD');
if ($mysqli->connect_errno) {
    http_response_code(500);
    exit('Error de conexión DB');
}
$mysqli->set_charset('utf8mb4');

// ====== Utilidades ======
function assertValidIdentifier(string $name, string $label = 'identifier'): string {
    // Permite letras, números y guion bajo. (Evita inyección en nombres)
    if (!preg_match('/^[A-Za-z0-9_]+$/', $name)) {
        throw new InvalidArgumentException("Nombre de $label inválido: $name");
    }
    return $name;
}

function normalizeValue(string $campo, $valor, bool $mayusculas): string {
    if (is_array($valor)) return ''; // se ignoran arrays
    $valor = trim((string)$valor);
    // colapsar espacios múltiples
    $valor = preg_replace('/\s{2,}/u', ' ', $valor) ?? $valor;

    // fechas: campos que comienzan por 'fecha'
    if (str_starts_with($campo, 'fecha')) {
        if ($valor === '' || $valor === '--') {
            return '0000-00-00';
        }
    }

    // mayúsculas multibyte (se preserva 'º', acentos, etc.)
    if ($mayusculas) {
        $valor = mb_strtoupper($valor, 'UTF-8');
    }

    return $valor;
}

// Construye lista blanca de columnas/flags
$ignorar = [];
if ($ignorarPost !== '') {
    foreach (explode(',', $ignorarPost) as $c) {
        $c = trim($c);
        if ($c !== '') $ignorar[$c] = true;
    }
}
$chkFields = [];
if ($camposCheckbox !== '') {
    foreach (explode(',', $camposCheckbox) as $c) {
        $c = trim($c);
        if ($c !== '') $chkFields[$c] = true;
    }
}

// Campos especiales de control
$registroHijo = isset($_POST['registroHijo']) ? (int)$_POST['registroHijo'] : 0;
$idPadre      = isset($IdPadre, $_POST[$IdPadre]) ? $_POST[$IdPadre] : null;

// Validar identificadores (tabla, BD, PK)
$tabla   = assertValidIdentifier($guardarTabla, 'tabla');
$idCampo = assertValidIdentifier($IdCampo, 'columna');

$bdPrefijo = '';
if ($guardarBD !== '') {
    $bdPrefijo = '`' . assertValidIdentifier($guardarBD, 'base de datos') . '`.';
}

$Id = $_POST[$IdCampo] ?? 'new';

// Recolectar columnas y valores
$cols   = [];
$vals   = [];
$update = [];
$types  = '';
foreach ($_POST as $campo => $valor) {
    if ($campo === 'radio-set' || $campo === 'registroHijo' || $campo === $IdCampo) {
        continue;
    }
    // ignorar por lista
    if (isset($ignorar[$campo])) continue;

    // Sólo nombres de columnas válidos
    try { $col = assertValidIdentifier($campo, 'columna'); }
    catch (Throwable $e) { continue; }

    // checkboxes: si aparece en POST, va 1; si no, lo completamos abajo
    if (isset($chkFields[$campo])) {
        $valor = '1';
    }

    $valor = normalizeValue($campo, $valor, $mayusculas);
    if ($valor === '' && !isset($chkFields[$campo])) {
        continue; // no insertar vacíos, como hacías
    }

    $cols[]   = "`$col`";
    $vals[]   = $valor;
    $update[] = "`$col` = ?";
    $types   .= 's';
}

// Completar checkboxes faltantes con 0
foreach ($chkFields as $campo => $_x) {
    if (!array_key_exists($campo, $_POST)) {
        $col = assertValidIdentifier($campo, 'columna');
        $cols[] = "`$col`";
        $vals[] = '0';
        $update[] = "`$col` = ?";
        $types .= 's';
    }
}

// Si es new y se quiere reemplazar PK por otro campo
if ($Id === 'new' && $reemplazarpor !== '') {
    $rep = $_POST[$reemplazarpor] ?? '';
    $rep = normalizeValue($reemplazarpor, $rep, $mayusculas);
    if ($rep !== '') {
        $cols   = array_merge(["`$idCampo`"], $cols);
        $vals   = array_merge([$rep], $vals);
        $update = array_merge(["`$idCampo` = ?"], $update);
        $types  = 's' . $types;
    }
}

// Construir SQL preparado
if ($Id === 'new') {
    // REPLACE INTO
    if (empty($cols)) {
        http_response_code(400);
        exit('No hay datos para insertar.');
    }
    $placeholders = rtrim(str_repeat('?,', count($cols)), ',');
    $sql = "REPLACE INTO $bdPrefijo`$tabla` (" . implode(',', $cols) . ") VALUES ($placeholders)";
    $params = $vals;
    $typesStr = str_repeat('s', count($params));
} else {
    // UPDATE ... WHERE id = ?
    if (empty($update)) {
        http_response_code(400);
        exit('No hay datos para actualizar.');
    }
    $sql = "UPDATE $bdPrefijo`$tabla` SET " . implode(',', $update) . " WHERE `$idCampo` = ?";
    $params = array_merge($vals, [ (string)$Id ]);
    $typesStr = str_repeat('s', count($vals)) . 's';
}

if ($muestraConsulta) {
    echo "<pre>$sql\nPARAMS: " . htmlspecialchars(json_encode($params, JSON_UNESCAPED_UNICODE)) . "</pre>";
}

$IdRegistro = ($Id === 'new') ? null : $Id;
if ($ejecutaConsulta) {
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        http_response_code(500);
        exit('Error al preparar la consulta.');
    }

    // bind dinámico
    $bindParams = [$typesStr];
    foreach ($params as $k => $v) { $bindParams[] = &$params[$k]; }
    // @phpstan-ignore-next-line
    call_user_func_array([$stmt, 'bind_param'], $bindParams);

    if (!$stmt->execute()) {
        http_response_code(500);
        exit('Error al guardar.');
    }

    if ($Id === 'new') {
        $insertId = $mysqli->insert_id;
        if ($registroHijo == 0) {
            $IdRegistro = $insertId ?: ($params[0] ?? null); // si REPLACE con PK manual
        } else {
            $IdRegistro = $idPadre ?? $insertId;
        }
    }

    $stmt->close();

    // ::::: LOG (opcional, igual que tu script)
    $tablaDB      = $tabla;
    $consultaLog  = $sql; // si tu log espera la SQL literal, puedes guardarla tal cual
    if (file_exists('log.php')) {
        require_once 'log.php';
    }
} else {
    // No se ejecuta, sólo se devuelve id
    $IdRegistro = $IdRegistro ?? $Id;
}

echo json_encode([
    'ok'          => true,
    'accion'      => ($Id === 'new') ? 'ADD' : 'UPD',
    'idRegistro'  => $IdRegistro,
], JSON_UNESCAPED_UNICODE);
