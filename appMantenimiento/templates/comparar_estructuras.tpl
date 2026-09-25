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
        .comparacion__resumen { margin: 18px 0; padding: 11px; background: #edf7ff; border-left: 4px solid #1673aa; }
        .comparacion__bloque, .comparacion__base { margin: 18px 0; padding: 12px; border: 1px solid #d6dce0; background: #fff; }
        .comparacion__bloque h4, .comparacion__base summary { margin: 0 0 10px; font-size: 16px; font-weight: 700; }
        .comparacion__bloque--sobrantes { border-left: 4px solid #b33030; }
        .comparacion__base { border-left: 4px solid #1673aa; }
        .comparacion__base > summary, .comparacion__detalle > summary { cursor: pointer; }
        .comparacion__detalle { margin: 10px 0; padding: 10px; background: #f5f8fa; border: 1px solid #d6dce0; }
        .comparacion__detalle h5 { margin: 8px 0; font-size: 14px; }
        .comparacion__tabla-wrap { overflow-x: auto; }
        .tablaComparacion { width: 100%; border-collapse: collapse; font-size: 13px; }
        .tablaComparacion th { background: #1b6695; color: #fff; text-align: left; padding: 7px; white-space: nowrap; }
        .tablaComparacion td { border-bottom: 1px solid #d6dce0; padding: 6px 7px; vertical-align: top; }
        .tablaComparacion tr:nth-child(even) { background: #f5f8fa; }
        .comparacion__ok { color: #147a2d; margin: 8px 0; }
        .comparacion__ok--grande { margin: 20px 0; padding: 12px; background: #eef9ef; border-left: 4px solid #147a2d; }
        .comparacion__aviso { color: #725000; margin: 8px 0; }
        .comparacion__ayuda { color: #4d5c65; margin: 8px 0 0; max-width: 880px; font-size: 12px; font-weight: 700; }
        .comparacion__credenciales { display: grid; grid-template-columns: repeat(2, minmax(260px, 1fr)); gap: 8px; margin-top: 12px; }
        .comparacion__credencial { padding: 8px 10px; border: 1px solid #c9d7df; background: #f5f8fa; line-height: 1.45; }
        .comparacion__credencial span { color: #4d5c65; font-size: 13px; }
        .comparacion__estado { margin: 7px 0 0; font-size: 13px; }
        .comparacion__estado--ok { color: #147a2d; }
        .comparacion__estado--error { color: #a40000; }
        .comparacion__estado--pendiente { color: #725000; }
        .comparacion__formulario { max-width: 980px; margin-left: 0; }
        .formularioGrande fieldset.comparacion__origen-destino { display: grid; grid-template-columns: repeat(2, minmax(280px, 1fr)); flex-direction: initial; gap: 12px; border: 0; padding: 0; margin: 0; }
        .comparacion__grupo { padding: 10px 12px 4px; border: 1px solid #c9d7df; background: #f5f8fa; }
        .comparacion__grupo--origen { border-top: 4px solid #1673aa; }
        .comparacion__grupo--destino { border-top: 4px solid #7b5b15; }
        .comparacion__grupo-titulo { display: block; margin: 0 0 8px; color: #29434e; font-size: 14px; font-weight: 700; }
        .comparacion__grupo .formulario__campo { margin: 0 0 8px; }
        .comparacion__seleccion { display: flex; align-items: end; gap: 10px; margin-top: 10px; }
        .comparacion__seleccion .formulario__campo { flex: 1 1 360px; margin: 0; }
        .comparacion__acciones-script { margin: 16px 0; }
        .comparacion__script { margin: 18px 0; padding: 12px; border-left: 4px solid #1673aa; background: #edf7ff; }
        .comparacion__script h4 { margin: 0 0 8px; }
        .comparacion__script p { margin: 8px 0; color: #4d5c65; }
        .comparacion__script textarea { box-sizing: border-box; width: 100%; min-height: 280px; padding: 10px; border: 1px solid #9fb4c2; background: #fff; color: #1a252b; font: 12px/1.45 Consolas, Monaco, monospace; resize: vertical; }
        @media (max-width: 700px) { .formularioGrande fieldset.comparacion__origen-destino { grid-template-columns: 1fr; } .comparacion__seleccion { display: block; } .comparacion__seleccion .boton { margin-top: 8px; } }
    </style>
</head>
<body>
    <div id="toolsbar" style="display:block;">{topbar}</div>
    <main class="principal">
        {barraLateral}
        <section class="tablero">
            <div class="filaGeneral filageneral--menu">{headerMenu}</div>
            <div class="tablero__header"><div class="header__fila1"><h3>Comparador de estructuras de bases de datos</h3></div></div>
            <form method="post" action="comparar_estructuras.php" class="formularioGrande comparacion__formulario">
                <fieldset class="comparacion__origen-destino">
                    <div class="comparacion__grupo comparacion__grupo--origen"><span class="comparacion__grupo-titulo">Servidor base</span><div class="formulario__campo"><label class="campo__label" for="idServidorBase">Servidor base</label><select class="campo__input" id="idServidorBase" name="idServidorBase" required>{opcionesBase}</select></div><div class="formulario__campo"><label class="campo__label" for="prefijoBase">Prefijo de BD base</label><input class="campo__input" id="prefijoBase" name="prefijoBase" value="{prefijoBase}" maxlength="50" required pattern="[A-Za-z0-9_]+" placeholder="t450_"></div></div>
                    <div class="comparacion__grupo comparacion__grupo--destino"><span class="comparacion__grupo-titulo">Servidor analizado</span><div class="formulario__campo"><label class="campo__label" for="idServidorHV">Servidor analizado</label><select class="campo__input" id="idServidorHV" name="idServidorHV" required>{opcionesHV}</select></div><div class="formulario__campo"><label class="campo__label" for="prefijoHV">Prefijo de BD analizado</label><input class="campo__input" id="prefijoHV" name="prefijoHV" value="{prefijoHV}" maxlength="50" required pattern="[A-Za-z0-9_]+" placeholder="hv_"></div></div>
                </fieldset>
                <div class="comparacion__seleccion"><div class="formulario__campo"><label class="campo__label" for="baseSeleccionada">Base de datos a comparar</label><select class="campo__input" id="baseSeleccionada" name="baseSeleccionada">{opcionesBases}</select></div><button class="boton boton--buscar" name="accion" value="cargar_bases" type="submit">Cargar bases</button><button class="boton boton--guardar" name="accion" value="comparar" type="submit">Comparar base</button></div>
                <p class="comparacion__ayuda">Seleccione los servidores y cargue las bases del servidor base. La base elegida se compara contra la del servidor analizado con el mismo sufijo. Sólo se realizan lecturas en INFORMATION_SCHEMA; no se modifica ninguna base.</p>
                <div class="comparacion__credenciales">{detalleServidores}</div>
                <div style="color:#a40000; margin-top:10px;">{mensajeError}</div>
                <div style="color:#725000; margin-top:10px;">{estadoConexion}</div>
            </form>
            <div>{resultadoComparacion}</div>
        </section>
    </main>
    <script>
        (function () {
            function cargarPrefijo(idServidor, idPrefijo) {
                var selector = document.getElementById(idServidor);
                var prefijo = document.getElementById(idPrefijo);
                if (!selector || !prefijo) { return; }
                selector.addEventListener('change', function () {
                    var opcion = selector.options[selector.selectedIndex];
                    prefijo.value = opcion ? (opcion.getAttribute('data-prefijo') || '') : '';
                });
            }
            cargarPrefijo('idServidorBase', 'prefijoBase');
            cargarPrefijo('idServidorHV', 'prefijoHV');
        }());
    </script>
</body>
</html>
