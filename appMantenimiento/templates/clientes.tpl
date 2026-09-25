<!DOCTYPE html>
<html lang="{lang}">
<head>
    <meta charset="utf-8">
    <title>{H2Sistema}</title>
    <link rel="icon" type="image/png" href="/images/icoDC.png">
    <link rel="stylesheet" href="/styles/normalize.css">
    <link rel="stylesheet" href="/styles/st_globales.css">
    <link rel="stylesheet" href="/styles/st_topbar.css">
    <link rel="stylesheet" href="/styles/st_contenedores.css">
    <link rel="stylesheet" href="/styles/st_formularios.css">
    <script src="/js/dcode.js"></script>
    <script src="/js/loadAjax.js"></script>
    <script>
        function editarCliente(idCliente) { location.href = 'clientes_ficha.php?IdRegistro=' + encodeURIComponent(idCliente); }
        function generarLicencia(idCliente) { location.href = 'licencia_calculo.php?idCliente=' + encodeURIComponent(idCliente); }
    </script>
</head>
<body onload="cargaInicial('{moduloPHP}',{_async},{pagina})">
    <div id="toolsbar" style="display:block;">{topbar}</div>
    <main class="principal">
        {barraLateral}
        <section class="tablero">
            <div class="filaGeneral filageneral--menu">{headerMenu}</div>
            <div class="tablero__header"><div class="tHeader__derecha"><div class="header__fila1"><h3>{H2Titulo}</h3></div></div></div>
            <div class="filaGeneral">
                <input id="pagina" type="hidden" value="{pagina}">
                <div class="formulario__campo"><label class="campo__label">{filtrarpor}</label><select class="campo__input" id="campo">{htmlCriterios}</select></div>
                <div class="formulario__campo"><label class="campo__label"><br></label><input class="campo__input" id="buscar" type="text" value="{iguala}" ondblclick="this.value=''"></div>
                <div class="formulario__campo formulario__campo--sin"><label class="campo__label"><br></label><input onclick="filtrarJSON('{moduloPHP}',1)" type="button" class="boton boton--buscar" value="Filtrar"></div>
                <div class="formulario__campo formulario__campo--sin"><label class="campo__label"><br></label><input onclick="location.href='clientes_ficha.php?IdRegistro=new'" type="button" class="boton boton--nuevo" value="Nuevo"></div>
<div class="formulario__campo formulario__campo--orden"><label class="campo__label">Ordenar por<br></label><select onchange="filtrarJSON('{moduloPHP}',1)" class="campo__input" id="ordenarPor"><option value="c.cliente" {_ord0}>Cliente</option><option value="c.rut" {_ord1}>RUT</option><option value="c.vencimientoLic" {_ord2}>Vencimiento</option><option value="c.prefijoBD" {_ord3}>Prefijo BD</option><option value="c.estadoCliente" {_ord4}>Estado</option></select></div>
                <div class="formulario__campo formulario__campo--orden"><label class="campo__label">Sentido<br></label><select onchange="filtrarJSON('{moduloPHP}',1)" class="campo__input" id="sentido"><option value="ASC" {sentASC}>▲ Ascendente</option><option value="DESC" {sentDESC}>▼ Descendente</option></select></div>
                <div class="formulario__campo formulario__campo--orden"><label class="campo__label">Filas<br></label><select onchange="filtrarJSON('{moduloPHP}',1)" class="campo__input" id="filas"><option value="8" {fl8}>8</option><option value="10" {fl10}>10</option><option value="12" {fl12}>12</option><option value="15" {fl15}>15</option><option value="20" {fl20}>20</option></select></div>
            </div>
            <div id="contenedorResultados">{grillaHTMLTit}{grillaHTML}<div class="tablero__footer"><ul class="paginacion">{paginacionHTML}</ul></div></div>
        </section>
    </main>
</body>
</html>
