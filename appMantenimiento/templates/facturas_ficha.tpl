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
    <link rel="stylesheet" href="/styles/st_columnas_flexibles.css">
    <style>
        .facturas-ficha { max-width: 1240px; padding-bottom: 28px; }
        .facturas-ficha__cabecera { align-items: center; background: linear-gradient(120deg, #174b70, #257e9c); border: 0; border-radius: 12px; box-shadow: 0 10px 24px rgba(28, 75, 103, .16); color: #fff; display: flex; margin: 18px 0; padding: 17px 24px; }
        .facturas-ficha__cabecera h3 { color: #fff; font-size: 22px; letter-spacing: -.02em; margin: 0; }
        .facturas-ficha__form { background: #fff; border: 1px solid #dbe7ed; border-radius: 13px; box-shadow: 0 7px 20px rgba(30, 68, 90, .08); box-sizing: border-box; margin: 0; max-width: 960px; padding: 21px; }
        .facturas-ficha__datos { border: 0; display: grid; gap: 13px; margin: 0; min-width: 0; padding: 0; }
        .facturas-ficha__seccion { background: linear-gradient(90deg, #edf7f8, #f9fcfd); border: 1px solid #d8e8eb; border-left: 4px solid #2a98a4; border-radius: 8px; box-sizing: border-box; color: #1c5f74; font-size: 13px; font-weight: 800; margin: 0; padding: 10px 13px; }
        .facturas-ficha .grid-flex { gap: 13px; margin: 0; padding: 3px 0; }
        .facturas-ficha .formulario__campo { background: transparent; border-bottom: 0; margin: 0; min-width: 0; padding: 0; }
        .facturas-ficha .campo__label { color: #365d70; font-size: 12px; font-weight: 750; margin-bottom: 5px; }
        .facturas-ficha .campo__input { background: #f8fbfc; border: 1px solid #c8d9e1; border-radius: 7px; box-sizing: border-box; color: #20495f; min-height: 38px; padding: 8px 10px; transition: background .16s ease, border-color .16s ease, box-shadow .16s ease; width: 100%; }
        .facturas-ficha .campo__input:focus { background: #fff; border-color: #238fa0; box-shadow: 0 0 0 3px rgba(35, 143, 160, .15); outline: 0; }
        .factura-referencia { background: #f4fafb !important; border: 1px dashed #a6cad1 !important; border-radius: 8px; margin-top: 3px !important; padding: 12px !important; }
        .factura-pdf { background: #fbfdfe; border: 1px solid #dbe8ed; border-radius: 10px; box-sizing: border-box; margin-top: 15px; max-width: 100%; overflow: hidden; }
        .factura-pdf__contenido { align-items: center; border: 0; display: flex; gap: 16px; justify-content: space-between; padding: 14px; }
        .factura-pdf__contenido > div:first-child { min-width: 0; }
        .factura-pdf__enlace { color: #17648a; font-size: 13px; font-weight: 750; overflow-wrap: anywhere; }
        .factura-pdf__vacio { color: #71838d; font-size: 13px; font-style: italic; }
        .factura-pdf input[type=file] { max-width: 355px; }
        .factura-referencia[hidden] { display: none; }
        .facturas-ficha__mensaje { font-size: 13px; font-weight: 600; margin-top: 10px; min-height: 0; }
        .facturas-ficha__mensaje--error { color: #a12d28; }
        .facturas-ficha__mensaje--exito { color: #1a7a43; }
        .facturas-ficha__acciones { border-top: 1px solid #e1ebef; gap: 9px; margin-top: 18px; padding-top: 16px; }
        .facturas-ficha__acciones .boton { border-radius: 7px; box-shadow: 0 2px 5px rgba(26, 61, 79, .1); min-height: 36px; padding: 8px 15px; transition: box-shadow .15s ease, transform .15s ease; }
        .facturas-ficha__acciones .boton:hover { box-shadow: 0 5px 12px rgba(26, 61, 79, .16); transform: translateY(-1px); }
        @media (max-width: 650px) { .facturas-ficha__form { padding: 14px; } .factura-pdf__contenido { align-items: stretch; flex-direction: column; } .factura-pdf input[type=file] { max-width: none; } .facturas-ficha__acciones { align-items: stretch; flex-direction: column; } }
    </style>
</head>
<body>
    <div id="toolsbar" style="display:block;">{topbar}</div>
    <main class="principal">
        {barraLateral}
        <section class="tablero facturas-ficha">
            <div class="filaGeneral filageneral--menu">{headerMenu}</div>
            <div class="tablero__header facturas-ficha__cabecera"><div class="header__fila1"><h3>{H2Titulo}</h3></div></div>
            <form class="formularioGrande facturas-ficha__form" method="post" action="facturas_ficha.php" enctype="multipart/form-data">
                <input name="id" type="hidden" value="{id}">
                <input name="fechaPago" type="hidden" value="{fechaPago}">
                <fieldset class="facturas-ficha__datos"><div class="filaGeneral filaGeneral__seccion facturas-ficha__seccion">Datos del documento</div>
                    <div class="grid-flex" style="--columns:1fr 2fr 3fr;">
                        <div class="formulario__campo"><label class="campo__label" for="nroFactura">N° documento</label><input class="campo__input" id="nroFactura" name="nroFactura" type="number" min="1" value="{nroFactura}" required></div>
                        <div class="formulario__campo"><label class="campo__label" for="fechaFactura">Fecha emisión</label><input class="campo__input" id="fechaFactura" name="fechaFactura" type="date" value="{fechaFactura}" required></div>
                        <div class="formulario__campo"><label class="campo__label" for="rutCliente">Cliente</label><select class="campo__input" id="rutCliente" name="rutCliente" required>{opcionesClientes}</select></div>
                    </div>
                    <div class="grid-flex" style="--columns:2fr 2fr 2fr;">
                        <div class="formulario__campo"><label class="campo__label" for="montoFactura">Monto</label><input class="campo__input" id="montoFactura" name="montoFactura" type="number" min="0" value="{montoFactura}" required></div>
                        <div class="formulario__campo"><label class="campo__label" for="tipoDocumento">Tipo de documento</label><select class="campo__input" id="tipoDocumento" name="tipoDocumento"><option value="FACTURA" {tipoFactura}>Factura</option><option value="NOTA_CREDITO" {tipoNotaCredito}>Nota de crédito</option></select></div>
                        <div class="formulario__campo"><label class="campo__label" for="estadoFactura">Estado</label><select class="campo__input" id="estadoFactura" name="estadoFactura"><option value="P" {estadoP}>Pendiente</option><option value="C" {estadoC}>Cancelada / pagada</option><option value="A" {estadoA}>Anulada</option></select></div>
                    </div>
                    <div class="formulario__campo factura-referencia" id="bloqueFacturaReferencia">
                        <label class="campo__label" for="idFacturaReferencia">Factura afectada</label>
                        <select class="campo__input" id="idFacturaReferencia" name="idFacturaReferencia">{opcionesFacturasReferencia}</select>
                    </div>
                </fieldset>
                <section class="factura-pdf facturas-ficha__adjunto">
                    <div class="filaGeneral filaGeneral__seccion facturas-ficha__seccion">PDF del documento</div>
                    <div class="factura-pdf__contenido">
                        <div>{pdfFacturaHTML}</div>
                        <div class="formulario__campo"><label class="campo__label" for="archivoFactura">Cargar o reemplazar PDF</label><input class="campo__input" id="archivoFactura" name="archivoFactura" type="file" accept="application/pdf"></div>
                    </div>
                </section>
                <section class="factura-pdf facturas-ficha__adjunto">
                    <div class="filaGeneral filaGeneral__seccion facturas-ficha__seccion">Informe técnico (opcional)</div>
                    <div class="factura-pdf__contenido">
                        <div>{informeTecnicoHTML}</div>
                        <div class="formulario__campo"><label class="campo__label" for="archivoInformeTecnico">Cargar o reemplazar informe</label><input class="campo__input" id="archivoInformeTecnico" name="archivoInformeTecnico" type="file" accept="application/pdf"></div>
                    </div>
                </section>
                <div class="facturas-ficha__mensaje facturas-ficha__mensaje--error">{mensajeError}</div>
                <div class="facturas-ficha__mensaje facturas-ficha__mensaje--exito">{mensajeExito}</div>
                <div class="filaGeneral facturas-ficha__acciones"><button class="boton boton--guardar" name="guardarFactura" type="submit">Guardar</button><button class="boton boton--salir" type="button" onclick="location.href='facturas.php'">Salir</button></div>
            </form>
        </section>
    </main>
    <script>
        (function () {
            var tipo = document.getElementById('tipoDocumento');
            var cliente = document.getElementById('rutCliente');
            var bloque = document.getElementById('bloqueFacturaReferencia');
            var referencia = document.getElementById('idFacturaReferencia');
            function actualizarFacturaReferencia() {
                var esNotaCredito = tipo.value === 'NOTA_CREDITO';
                bloque.hidden = !esNotaCredito;
                referencia.required = esNotaCredito;
                Array.prototype.forEach.call(referencia.options, function (opcion) {
                    var otroCliente = opcion.dataset.cliente && opcion.dataset.cliente !== cliente.value;
                    opcion.hidden = !!otroCliente;
                    opcion.disabled = !!otroCliente;
                });
                if (referencia.selectedOptions.length && referencia.selectedOptions[0].disabled) {
                    referencia.value = '';
                }
            }
            tipo.addEventListener('change', actualizarFacturaReferencia);
            cliente.addEventListener('change', actualizarFacturaReferencia);
            actualizarFacturaReferencia();
        }());
    </script>
</body>
</html>
