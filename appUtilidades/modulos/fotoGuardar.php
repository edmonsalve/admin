<?php
    session_start();
    require_once('../../defines/variables_path.php');
	require_once(PATH_DEFINES . '/variables.php');

    $usrID = $_SESSION['idUser']; 

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['imagen'])) {
        http_response_code(400);
        echo "No se recibió imagen.";
        exit;
    }

    // Extraer la parte base64 (después de "base64,")
    $imagen_parts = explode(',', $data['imagen']);
    if (count($imagen_parts) !== 2) {
        http_response_code(400);
        echo "Formato de imagen inválido.";
        exit;
    }
    $imagen_base64 = $imagen_parts[1];

    // Decodificar y guardar
    $imagen_binaria = base64_decode($imagen_base64, true);
    if ($imagen_binaria === false) {
        http_response_code(400);
        echo "Error al decodificar la imagen base64.";
        exit;
    }
    
    $nombre_archivo = "$usrID.png";
    $directorio = realpath(ALMACEN_FOTOS);

    // Verificar si el directorio existe, si no, crearlo
    if (!is_dir($directorio)) {
        if (!mkdir($directorio, 0777, true)) {
            http_response_code(500);
            echo "No se pudo crear el directorio para guardar la imagen.";
            exit;
        }
    }

    $ruta_archivo = ALMACEN_FOTOS . $nombre_archivo;
    if (file_put_contents($ruta_archivo, $imagen_binaria) !== false) {
        echo "Imagen guardada como $nombre_archivo";
    } else {
        http_response_code(500);
        $msg = ("Error al guardar la imagen en $ruta_archivo. Permisos del directorio: " . substr(sprintf('%o', fileperms($directorio)), -4));
        echo "$msg";
    }
  
