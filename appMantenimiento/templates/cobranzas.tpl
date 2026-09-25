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
    <link rel="stylesheet" href="/appMantenimiento/styles/cobranzas.css">
</head>
<body>
    <div id="toolsbar" style="display:block;">{topbar}</div>
    <main class="principal">
        {barraLateral}
        <section class="tablero cobranzas">
            <div class="filaGeneral filageneral--menu">{headerMenu}</div>
            <h3 class="cobranzas__titulo">Cobranzas</h3>

            <form class="cobranzas-selector" method="get" action="cobranzas.php">
                <div class="formulario__campo cobranzas-selector__cliente">
                    <label class="campo__label" for="cliente">Cliente</label>
                    <select class="campo__input" id="cliente" name="cliente">{opcionesClientes}</select>
                </div>
                <div class="formulario__campo">
                    <label class="campo__label" for="estado">Facturas</label>
                    <select class="campo__input" id="estado" name="estado">
                        <option value="" {estadoTodos}>Pagadas y pendientes</option>
                        <option value="P" {estadoPendientes}>Pendientes de pago</option>
                        <option value="C" {estadoPagadas}>Pagadas</option>
                    </select>
                </div>
                <button class="boton boton--buscar cobranzas-selector__boton" type="submit">Consultar</button>
            </form>

            <section class="cobranzas-dashboard" style="display:{mostrarDashboard};">
                <div class="cobranzas-dashboard__cabecera">
                    <div><h4>Resumen de cuentas por cobrar</h4><p>Facturas pendientes, descontadas las notas de crédito activas. La antigüedad se calcula desde la emisión.</p></div>
                    <a class="boton cobranzas-dashboard__informe" href="cobranzas_pendientes_pdf.php" target="_blank" rel="noopener">Informe de pendientes por cobrar</a>
                </div>
                <div class="cobranzas-resumen cobranzas-dashboard__resumen">
                    <div class="cobranzas-resumen__item cobranzas-resumen__item--pendiente"><span>Total pendiente</span><strong>${dashboardMontoPendiente}</strong><small>{dashboardFacturasPendientes} facturas</small></div>
                    <div class="cobranzas-resumen__item cobranzas-dashboard__item--clientes"><span>Clientes con deuda</span><strong>{dashboardClientesPendientes}</strong><small>con saldo pendiente</small></div>
                    <div class="cobranzas-resumen__item cobranzas-dashboard__item--antiguedad"><span>Deuda más antigua</span><strong>{dashboardFechaMasAntigua}</strong><small>hasta {dashboardMesesAtrasoMaximo} meses desde emisión</small></div>
                    <div class="cobranzas-resumen__item cobranzas-dashboard__item--deudor"><span>Mayor saldo por cliente</span><strong>{dashboardMayorDeudor}</strong><small>${dashboardMontoMayorDeudor}</small></div>
                </div>
                <div class="cobranzas-dashboard__grid">
                    <section class="cobranzas-panel">
                        <div class="cobranzas-panel__titulo">Pendiente por cliente</div>
                        <div class="cobranzas-tabla"><table><thead><tr><th>Cliente</th><th>Facturas</th><th>Más antigua</th><th>Saldo pendiente</th></tr></thead><tbody>{filasDashboardClientes}</tbody></table></div>
                    </section>
                    <div class="cobranzas-dashboard__derecha">
                        <section class="cobranzas-panel cobranzas-gestiones-resumen">
                            <div class="cobranzas-panel__titulo">Gestiones de cobro realizadas</div>
                            <div class="cobranzas-gestiones-resumen__tarjetas">{tarjetasDashboardGestiones}</div>
                        </section>
                        <section class="cobranzas-panel">
                            <div class="cobranzas-panel__titulo">Antigüedad del saldo</div>
                            <div class="cobranzas-tabla"><table><thead><tr><th>Desde emisión</th><th>Facturas</th><th>Saldo pendiente</th></tr></thead><tbody>{filasDashboardAntiguedad}</tbody></table></div>
                        </section>
                    </div>
                </div>
            </section>
            <div class="cobranzas-contenido" style="display:{mostrarContenido};">
                <div class="cobranzas-cliente"><span>Cliente seleccionado</span><strong>{clienteSeleccionado}</strong></div>
                <section class="cobranzas-contacto"><div class="cobranzas-contacto__titulo">Contacto de cobranza</div><div class="cobranzas-contacto__lista">{contactosCobranzaHTML}</div></section>
                <div class="cobranzas-resumen">
                    <div class="cobranzas-resumen__item cobranzas-resumen__item--pendiente"><span>Pendientes</span><strong>{pendientes}</strong><small>${montoPendiente}</small></div>
                    <div class="cobranzas-resumen__item cobranzas-resumen__item--pagada"><span>Pagadas</span><strong>{pagadas}</strong><small>${montoPagado}</small></div>
                    <div class="cobranzas-resumen__item cobranzas-resumen__item--creditada"><span>Notas de crédito aplicadas</span><strong>{creditadas}</strong><small>${montoCreditado}</small></div>
                </div>

                <div class="cobranzas-grid">
                    <section class="cobranzas-panel">
                        <div class="cobranzas-panel__titulo">Facturas del cliente</div>
                        <div class="cobranzas-tabla"><table><thead><tr><th>N° factura</th><th>Emisión</th><th>Monto</th><th>NC aplicadas</th><th>Saldo pendiente</th><th>Estado</th><th>Fecha pago</th><th>Acción</th></tr></thead><tbody>{filasFacturas}</tbody></table></div>

                        <section class="cobranzas-gestion">
                            <div class="cobranzas-panel__titulo">Registrar acción de cobro</div>
                            <form method="post" action="cobranzas.php">
                                <input name="rutCliente" type="hidden" value="{rutCliente}">
                                <input name="estadoFiltro" type="hidden" value="{estadoFiltro}">
                                <div class="cobranzas-form__fila"><div class="formulario__campo"><label class="campo__label" for="idFactura">Factura asociada</label><select class="campo__input" id="idFactura" name="idFactura">{opcionesFacturas}</select></div><div class="formulario__campo"><label class="campo__label" for="fechaAccion">Fecha de acción</label><input class="campo__input" id="fechaAccion" name="fechaAccion" type="date" value="{fechaAccion}" required></div><div class="formulario__campo"><label class="campo__label" for="tipoAccion">Acción</label><select class="campo__input" id="tipoAccion" name="tipoAccion">{opcionesTipoAccion}</select></div></div>
                                <div class="formulario__campo"><label class="campo__label" for="detalle">Gestión realizada</label><textarea class="campo__input" id="detalle" name="detalle" maxlength="4000" required>{detalleAccion}</textarea></div>
                                <div class="cobranzas-form__fila cobranzas-form__fila--compromiso"><div class="formulario__campo"><label class="campo__label" for="fechaCompromiso">Fecha comprometida</label><input class="campo__input" id="fechaCompromiso" name="fechaCompromiso" type="date" value="{fechaCompromiso}"></div><div class="formulario__campo"><label class="campo__label" for="responsable">Responsable</label><input class="campo__input" id="responsable" name="responsable" maxlength="100" value="{responsable}" required></div></div>
                                <div class="formulario__campo"><label class="campo__label" for="compromisoCliente">Compromiso del cliente</label><textarea class="campo__input" id="compromisoCliente" name="compromisoCliente" maxlength="4000">{compromisoCliente}</textarea></div>
                                <div class="cobranzas-mensaje cobranzas-mensaje--error">{mensajeError}</div>
                                <div class="cobranzas-mensaje cobranzas-mensaje--exito">{mensajeExito}</div>
                                <div class="cobranzas-form__acciones"><button class="boton boton--guardar" name="guardarAccionCobranza" type="submit">Guardar acción</button></div>
                            </form>
                        </section>
                    </section>
                    <aside class="cobranzas-panel cobranzas-hilo">
                        <div class="cobranzas-panel__titulo">Seguimiento de cobranza</div>
                        <div class="cobranzas-hilo__cuerpo">{hiloAcciones}</div>
                    </aside>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
