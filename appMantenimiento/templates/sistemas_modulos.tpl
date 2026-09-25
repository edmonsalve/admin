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
    <link rel="stylesheet" href="/styles/st_columnas_flexibles.css" type="text/css" media="screen, projection" />
    <script src="/js/dcode.js" type="text/javascript"></script>
    <script src="/js/loadAjax.js" type="text/javascript"></script>
    <script>
        function editarModuloSistema(idModulo, idSistema) {
            location.href = 'sistemas_modulos.php?idSistema=' + encodeURIComponent(idSistema) + '&idModulo=' + encodeURIComponent(idModulo);
        }
        function cambiarEstadoModulo(idModulo, idSistema, estadoActual) {
            const nuevoEstado = String(estadoActual) === '1' ? '0' : '1';
            const accion = nuevoEstado === '0' ? 'desactivar' : 'activar';
            if (!confirm('¿Desea ' + accion + ' este módulo?')) return;
            const formulario = document.createElement('form');
            formulario.method = 'POST';
            formulario.action = 'sistemas_modulos.php?idSistema=' + encodeURIComponent(idSistema);
            [['accion', 'estado'], ['idModulo', idModulo], ['estado', nuevoEstado]].forEach(function (campo) {
                const input = document.createElement('input'); input.type = 'hidden'; input.name = campo[0]; input.value = campo[1]; formulario.appendChild(input);
            });
            document.body.appendChild(formulario); formulario.submit();
        }
        function nuevoModuloSistema() {
            location.href = 'sistemas_modulos.php?idSistema={idSistema}&idModulo=new';
        }
    </script>
</head>
<body onload="cargaInicial('{moduloPHP}', {_async}, {pagina})">
    <div id="toolsbar" style="display:block;">{topbar}</div>
    <main class="principal">
        {barraLateral}
        <section class="tablero">
            <div class="filaGeneral filageneral--menu">{headerMenu}</div>
            <div class="tablero__header"><div class="header__fila1"><h3>{H2Titulo}: {nombreSistema}</h3></div></div>
            <div class="filaGeneral">
                <input id="pagina" type="hidden" value="{pagina}" />
                <input id="aux5" name="m.idsistema" type="hidden" value="{idSistema}" />
                <div class="formulario__campo"><label class="campo__label">Filtrar por</label><select class="campo__input" id="campo">{htmlCriterios}</select></div>
                <div class="formulario__campo"><label class="campo__label"><br></label><input class="campo__input" id="buscar" type="text" value="{iguala}" ondblclick="this.value=''" /></div>
                <div class="formulario__campo formulario__campo--sin"><label class="campo__label"><br></label><input onclick="filtrarJSON('{moduloPHP}', 1)" type="button" class="boton boton--buscar" value="Filtrar" /></div>
                <div class="formulario__campo formulario__campo--sin"><label class="campo__label"><br></label><input onclick="nuevoModuloSistema()" type="button" class="boton boton--nuevo" value="Nuevo módulo" /></div>
                <div class="formulario__campo formulario__campo--filtro" style="margin-left:30px;"><label class="campo__label">Tipo de módulo</label><select onchange="filtrarJSON('{moduloPHP}',1)" class="campo__input" id="aux1" name="m.tipo">{opcionesTipo}</select></div>
                <div class="formulario__campo formulario__campo--orden"><label class="campo__label">Ordenar por</label><select onchange="filtrarJSON('{moduloPHP}',1)" class="campo__input" id="ordenarPor"><option value="m.tipo, m.modulo" {_ord0}>Tipo y módulo</option><option value="m.modulo" {_ord1}>Módulo</option><option value="m.php" {_ord2}>Archivo PHP</option><option value="m.id" {_ord3}>ID</option></select></div>
                <div class="formulario__campo formulario__campo--orden"><label class="campo__label">Sentido</label><select onchange="filtrarJSON('{moduloPHP}',1)" class="campo__input" id="sentido"><option value="ASC" {sentASC}>▲ Ascendente</option><option value="DESC" {sentDESC}>▼ Descendente</option></select></div>
                <div class="formulario__campo formulario__campo--orden"><label class="campo__label">Filas</label><select onchange="filtrarJSON('{moduloPHP}',1)" class="campo__input" id="filas"><option value="8" {fl8}>8</option><option value="10" {fl10}>10</option><option value="12" {fl12}>12</option><option value="15" {fl15}>15</option><option value="20" {fl20}>20</option></select></div>
            </div>
            <div id="contenedorResultados">{grillaHTMLTit}{grillaHTML}<div class="tablero__footer"><ul class="paginacion">{paginacionHTML}</ul></div></div>
            <form id="formularioModulo" method="post" action="sistemas_modulos.php?idSistema={idSistema}" style="display:{mostrarFicha}; max-width:1100px; margin-top:20px;" class="formularioGrande">
                <input name="accion" type="hidden" value="guardar" /><input name="idSistema" type="hidden" value="{idSistema}" /><input name="idModulo" type="hidden" value="{idModulo}" />
                <div class="filaGeneral filaGeneral__seccion">Datos del módulo</div>
                <fieldset>
                    <div class="grid-flex" style="--columns:3fr 4fr 1fr 2fr;"><div class="formulario__campo"><label class="campo__label">Módulo</label><input class="campo__input" name="modulo" value="{modulo}" required maxlength="60" /></div><div class="formulario__campo"><label class="campo__label">Detalle</label><input class="campo__input" name="detalle" value="{detalle}" maxlength="100" /></div><div class="formulario__campo"><label class="campo__label">Tipo</label><select class="campo__input" name="tipo" required>{opcionesTipoFicha}</select></div><div class="formulario__campo"><label class="campo__label">Archivo PHP</label><input class="campo__input" name="php" value="{php}" required maxlength="50" /></div></div>
                    <div class="grid-flex" style="--columns:4fr 1fr 1fr 1fr 1fr;"><div class="formulario__campo"><label class="campo__label">Parámetros</label><input class="campo__input" name="parametros" value="{parametros}" maxlength="255" /></div><div class="formulario__campo"><label class="campo__label">Destino</label><select class="campo__input" name="target"><option value="T" {targetT}>Actual</option><option value="B" {targetB}>Nueva ventana</option></select></div><div class="formulario__campo"><label class="campo__label">Estado</label><select class="campo__input" name="estado"><option value="1" {estadoActivo}>Activo</option><option value="0" {estadoInactivo}>Inactivo</option></select></div><div class="formulario__campo"><label class="campo__label">Versión</label><select class="campo__input" name="sistemaVer"><option value="2" {versionActual}>Actual</option><option value="1" {versionAntigua}>Antigua</option></select></div><div class="formulario__campo"><label class="campo__label">Móvil</label><select class="campo__input" name="movil"><option value="2" {movilSi}>Sí</option><option value="0" {movilNo}>No</option></select></div></div>
                    <div class="grid-flex" style="--columns:2fr 3fr 1fr;"><div class="formulario__campo"><label class="campo__label">Texto ícono</label><input class="campo__input" name="txtIco" value="{txtIco}" maxlength="15" /></div><div class="formulario__campo"><label class="campo__label">Archivo ícono</label><input class="campo__input" name="icon" value="{icon}" maxlength="50" /></div><div class="formulario__campo"><label class="campo__label">Orden ícono</label><input class="campo__input" name="ordenICO" type="number" min="0" value="{ordenICO}" required /></div></div>
                </fieldset>
                <div style="color:#a40000; margin-top:8px;">{mensajeError}</div><div class="filaGeneral" style="margin-top:12px; gap:8px;"><button class="boton boton--guardar" type="submit">Guardar módulo</button><button class="boton boton--salir" type="button" onclick="location.href='sistemas_modulos.php?idSistema={idSistema}'">Cancelar</button></div>
            </form>
            <div class="filaGeneral" style="margin-top:20px;"><button class="boton boton--salir" type="button" onclick="location.href='sistemas.php'">Volver a sistemas</button></div>
        </section>
    </main>
</body>
</html>
