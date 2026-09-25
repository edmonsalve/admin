<!DOCTYPE html>
<html lang="{lang}">
<head>
    <meta charset="utf-8">
    <title>{H2Sistema}</title>
    <link rel="icon" type="image/png" href="/images/icoDC.png">
    <link rel="stylesheet" href="/styles/normalize.css"><link rel="stylesheet" href="/styles/st_globales.css"><link rel="stylesheet" href="/styles/st_topbar.css"><link rel="stylesheet" href="/styles/st_contenedores.css"><link rel="stylesheet" href="/styles/st_formularios.css">
    <style>.licencia-resumen { margin:14px 0; padding:12px; border-left:4px solid #1b6695; background:#edf7ff; }.licencia-dato { margin:14px 0; padding:12px; border:1px solid #d6dce0; border-radius:7px; }.licencia-dato h4 { margin:0 0 5px; color:#124f87; }.licencia-dato__valor { margin:0 0 8px; font-weight:700; }.licencia-dato textarea { box-sizing:border-box; width:100%; resize:vertical; font-family:monospace; }</style>
</head>
<body>
    <div id="toolsbar" style="display:block;">{topbar}</div>
    <main class="principal">{barraLateral}<section class="tablero"><div class="filaGeneral filageneral--menu">{headerMenu}</div><div class="tablero__header"><div class="header__fila1"><h3>{H2Titulo}</h3></div></div>
        <div class="licencia-resumen"><strong>Cliente:</strong> {cliente}<br><strong>RUT:</strong> {rut}<br><strong>Número de licencia:</strong> {licencia}</div>
        <p>Los valores cifrados se generan con el algoritmo histórico para conservar compatibilidad con las instalaciones existentes.</p>
        {filasLicencia}
        <div class="filaGeneral" style="margin-top:14px;"><button class="boton boton--salir" type="button" onclick="location.href='clientes_ficha.php?IdRegistro={idCliente}'">Volver al cliente</button></div>
    </section></main>
</body>
</html>
