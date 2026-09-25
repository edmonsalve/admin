<!DOCTYPE html>
<html lang="{lang}">
<head>
    <title>{H2Sistema}</title>
    <link rel="icon" type="image/png" href="/images/icoDC.png">
    <link rel="stylesheet" href="/styles/normalize.css" type="text/css" media="screen, projection" />
    <link rel="stylesheet" href="/styles/st_globales.css" type="text/css" media="screen, projection" />
    <link rel="stylesheet" href="/styles/st_topbar.css" type="text/css" media="screen, projection" />
    <link rel="stylesheet" href="/styles/st_contenedores.css" type="text/css" media="screen, projection" />
    <link rel="stylesheet" href="/styles/st_formularios.css" type="text/css" media="screen, projection" />
    <style type="text/css">
        .comparacion__resumen { margin: 18px 0; padding: 10px; background: #edf7ff; border-left: 4px solid #1673aa; }
        .comparacion__titulo { margin: 24px 0 8px; padding: 8px; font-size: 16px; }
        .comparacion__titulo--faltantes { background: #fff2cf; border-left: 4px solid #d49a00; }
        .comparacion__titulo--exclusivos { background: #fce4e4; border-left: 4px solid #b33030; }
        .tablaComparacion { width: 100%; border-collapse: collapse; font-size: 13px; }
        .tablaComparacion th { background: #1b6695; color: #fff; text-align: left; padding: 7px; white-space: nowrap; }
        .tablaComparacion td { border-bottom: 1px solid #d6dce0; padding: 6px 7px; vertical-align: top; }
        .tablaComparacion tr:nth-child(even) { background: #f5f8fa; }
    </style>
</head>
<body>
    <script type="text/javascript">function seleccionarModulos(nombre, marcado){document.querySelectorAll('input[name="'+nombre+'[]"]').forEach(function(campo){campo.checked=marcado;});}</script>
    <div id="toolsbar" style="display:block;">{topbar}</div>
    <main class="principal">
        {barraLateral}
        <section class="tablero">
            <div class="filaGeneral filageneral--menu">{headerMenu}</div>
            <div class="tablero__header"><div class="header__fila1"><h3>{H2Titulo}</h3></div></div>
            <form method="post" action="sincronizar.php" class="formularioGrande" style="max-width:850px; margin-left:0;">
                <div class="filaGeneral filaGeneral__seccion">Servidor a comparar</div>
                <fieldset>
                    <div class="formulario__campo" style="max-width:640px;">
                        <label class="campo__label" for="idServidor">Servidor</label>
                        <select class="campo__input" id="idServidor" name="idServidor" required>{opcionesServidores}</select>
                    </div>
                </fieldset>
                <div style="color:#a40000; margin-top:10px;">{mensajeError}</div>
                <div style="color:#147a2d; margin-top:10px;">{mensajeExito}</div>
                <div style="color:#725000; margin-top:10px;">{estadoConexion}</div>
                <div class="filaGeneral" style="gap:8px; margin-top:12px;"><button class="boton boton--buscar" name="comparar" type="submit">Comparar módulos</button></div>
            </form>
            <div>{resultadoComparacion}</div>
        </section>
    </main>
</body>
</html>
