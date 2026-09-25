<!DOCTYPE html>
<html lang="{lang}">
<head>
    <meta charset="utf-8">
    <title>{H2Sistema}</title>
    <link rel="icon" type="image/png" href="/images/icoDC.png">
    <link rel="stylesheet" href="/styles/normalize.css"><link rel="stylesheet" href="/styles/st_globales.css"><link rel="stylesheet" href="/styles/st_topbar.css"><link rel="stylesheet" href="/styles/st_contenedores.css"><link rel="stylesheet" href="/styles/st_formularios.css">
    <script src="/js/dcode.js"></script><script src="/js/loadAjax.js"></script><script>function editarFactura(id){location.href='facturas_ficha.php?IdRegistro='+encodeURIComponent(id)}</script>
</head>
<body onload="cargaInicial('{moduloPHP}',{_async},{pagina})">
    <div id="toolsbar" style="display:block;">{topbar}</div>
    <main class="principal">{barraLateral}<section class="tablero">
        <div class="filaGeneral filageneral--menu">{headerMenu}</div>
        <div class="tablero__header"><div class="tHeader__derecha"><div class="header__fila1"><h3>{H2Titulo}</h3></div></div></div>
        <div class="filaGeneral">
            <input id="pagina" type="hidden" value="{pagina}">
            <input id="campo" type="hidden" value="facturas_busqueda">
            <div class="formulario__campo"><label class="campo__label"><br></label><input class="campo__input" id="buscar" type="text" value="{iguala}" ondblclick="this.value=''"></div>
            <div class="formulario__campo formulario__campo--sin"><label class="campo__label"><br></label><input onclick="filtrarJSON('{moduloPHP}',1)" type="button" class="boton boton--buscar" value="Filtrar"></div>
            <div class="formulario__campo formulario__campo--sin"><label class="campo__label"><br></label><input onclick="location.href='facturas_ficha.php?IdRegistro=new'" type="button" class="boton boton--nuevo" value="Nuevo"></div>
            <div onchange="filtrarJSON('{moduloPHP}',1)" class="formulario__campo formulario__campo--filtro"><label class="campo__label" for="aux1">Estado</label><select class="campo__input" id="aux1" name="f.estadoFactura"><option value="" {estadoTodos}>Todos</option><option value="P" {estadoP}>Pendiente</option><option value="C" {estadoC}>Cancelada / pagada</option><option value="A" {estadoA}>Anulada</option></select></div>
            <div onchange="filtrarJSON('{moduloPHP}',1)" class="formulario__campo formulario__campo--filtro"><label class="campo__label" for="aux2">Cliente</label><select class="campo__input" id="aux2" name="f.rutCliente">{opcionesClientes}</select></div>
            <div onchange="filtrarJSON('{moduloPHP}',1)" class="formulario__campo formulario__campo--filtro"><label class="campo__label" for="aux3">Tipo</label><select class="campo__input" id="aux3" name="f.tipoDocumento"><option value="" {tipoTodos}>Todos</option><option value="FACTURA" {tipoFactura}>Factura</option><option value="NOTA_CREDITO" {tipoNotaCredito}>Nota de crédito</option></select></div>
            <div class="formulario__campo formulario__campo--orden"><label class="campo__label">Ordenar por<br></label><select onchange="filtrarJSON('{moduloPHP}',1)" class="campo__input" id="ordenarPor"><option value="f.nroFactura" {_ord0}>N° factura</option><option value="f.fechaFactura" {_ord1}>Fecha emisión</option><option value="c.cliente" {_ord2}>Cliente</option><option value="f.montoFactura" {_ord3}>Monto</option><option value="f.tipoDocumento" {_ord4}>Tipo</option><option value="f.estadoFactura" {_ord5}>Estado</option></select></div>
            <div class="formulario__campo formulario__campo--orden"><label class="campo__label">Sentido<br></label><select onchange="filtrarJSON('{moduloPHP}',1)" class="campo__input" id="sentido"><option value="ASC" {sentASC}>▲ Ascendente</option><option value="DESC" {sentDESC}>▼ Descendente</option></select></div>
            <div class="formulario__campo formulario__campo--orden"><label class="campo__label">Filas<br></label><select onchange="filtrarJSON('{moduloPHP}',1)" class="campo__input" id="filas"><option value="8" {fl8}>8</option><option value="10" {fl10}>10</option><option value="12" {fl12}>12</option><option value="15" {fl15}>15</option><option value="20" {fl20}>20</option></select></div>
        </div>
        <div id="contenedorResultados">{grillaHTMLTit}{grillaHTML}<div class="tablero__footer"><ul class="paginacion">{paginacionHTML}</ul></div></div>
    </section></main>
</body>
</html>
