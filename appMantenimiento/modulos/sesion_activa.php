<?php

// Consulta de solo lectura: detecta una sesión vencida sin prolongar su vigencia.
session_start(['read_and_close' => true]);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

echo json_encode(['activa' => isset($_SESSION['idUser'])]);
