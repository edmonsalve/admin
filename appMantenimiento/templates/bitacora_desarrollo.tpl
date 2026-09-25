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
    <link rel="stylesheet" href="/styles/dropzone.css">
    <link rel="stylesheet" href="/appMantenimiento/styles/bitacora_desarrollo.css">
    <script src="/js/dropzone-min.js"></script>
    <script>if (window.Dropzone) { Dropzone.autoDiscover = false; }</script>
</head>
<body>
    <div id="toolsbar" style="display:block;">{topbar}</div>
    <main class="principal">
        {barraLateral}
        <section class="tablero bitacora">
            <div class="filaGeneral filageneral--menu">{headerMenu}</div>
            <h3 class="bitacora__titulo">Bitácora dev.dCode</h3>

            <form class="bitacora-busqueda" method="get" action="bitacora_desarrollo.php">
                <div class="bitacora-busqueda__campos" >
                    <div class="formulario__campo">
                        <label class="campo__label" for="sistemaFiltro">Sistema</label>
                        <select class="campo__input" id="sistemaFiltro" name="sistema" onchange="this.form.submit()">{opcionesFiltroSistemas}</select>
                    </div>
                    <div class="formulario__campo">
                        <label class="campo__label" for="tipoFiltro">Tipo</label>
                        <select class="campo__input" id="tipoFiltro" name="tipo" onchange="this.form.submit()">{opcionesTipoFiltro}</select>
                    </div>
                    <div class="formulario__campo">
                        <label class="campo__label" for="estadoFiltro">Estado</label>
                        <select class="campo__input" id="estadoFiltro" name="estadoFiltro" onchange="this.form.submit()">{opcionesEstadoFiltro}</select>
                    </div>
                    <div class="formulario__campo">
                        <label class="campo__label" for="prioridadFiltro">Prioridad</label>
                        <select class="campo__input" id="prioridadFiltro" name="prioridadFiltro" onchange="this.form.submit()">{opcionesPrioridadFiltro}</select>
                    </div>
                    <div class="formulario__campo">
                        <label class="campo__label" for="ordenFecha">Orden</label>
                        <select class="campo__input" id="ordenFecha" name="ordenFecha" onchange="this.form.submit()">{opcionesOrdenFecha}</select>
                    </div>
                    <div class="formulario__campo">
                        <label class="campo__label" for="responsableFiltro">Responsable</label>
                        <select class="campo__input" id="responsableFiltro" name="responsableFiltro" onchange="this.form.submit()">{opcionesFiltroResponsables}</select>
                    </div>
                    <div class="formulario__campo">
                        <label class="campo__label">&nbsp;</label>
                        <button class="boton boton--buscar bitacora-busqueda__boton" type="submit">Filtrar</button>
                    </div>
                </div>
            </form>

            <div class="bitacora-espacio">
                <section class="bitacora-panel">
                    <div class="bitacora-panel__titulo">Detalle de entrada</div>
                    <div class="bitacora-panel__cuerpo">
                        <div class="bitacora-notice bitacora-notice--error">{mensajeError}</div>
                        <div class="bitacora-notice bitacora-notice--exito">{mensajeExito}</div>

                        <form class="bitacora-form bitacora-form--entrada" method="post" action="bitacora_desarrollo.php" id="formBitacora">
                            <input name="idBitacora" type="hidden" value="{idBitacora}">
                            <div class="bitacora-form__fila">
                                <div class="formulario__campo"><label class="campo__label" for="fechaCambio">Fecha</label><input class="campo__input" id="fechaCambio" name="fechaCambio" type="date" value="{fechaCambio}" required></div>
                                <div class="formulario__campo"><label class="campo__label" for="idSistema">Sistema</label><select class="campo__input" id="idSistema" name="idSistema">{opcionesSistemas}</select></div>
                                <div class="formulario__campo"><label class="campo__label" for="idModulo">Módulo</label><select class="campo__input" id="idModulo" name="idModulo">{opcionesModulos}</select></div>
                                <div class="formulario__campo"><label class="campo__label" for="estado">Estado</label><select class="campo__input bitacora-estado-select" id="estado" name="estado">{opcionesEstado}</select></div>
                            </div>
                            <div class="bitacora-form__titulo formulario__campo">
                                <label class="campo__label" for="titulo">Título de la entrada</label>
                                <input class="campo__input" id="titulo" name="titulo" maxlength="160" value="{titulo}" required>
                            </div>
                            <div class="bitacora-form__fila bitacora-form__fila--datos">
                                <div class="formulario__campo"><label class="campo__label" for="tipoCambio">Tipo</label><select class="campo__input" id="tipoCambio" name="tipoCambio">{opcionesTipoRegistro}</select></div>
                                <div class="formulario__campo"><label class="campo__label" for="prioridad">Prioridad</label><select class="campo__input bitacora-prioridad-select" id="prioridad" name="prioridad">{opcionesPrioridad}</select></div>
                                <div class="formulario__campo"><label class="campo__label" for="version">Versión</label><input class="campo__input" id="version" name="version" maxlength="40" value="{version}" placeholder="Ej.: 6.2.0"></div>
                                <div class="formulario__campo"><label class="campo__label" for="responsable">Responsable</label><select class="campo__input" id="responsable" name="responsable" required>{opcionesResponsables}</select></div>
                            </div>
                            <div class="bitacora-form__detalle formulario__campo">
                                <label class="campo__label" for="detalle">Detalle del cambio</label>
                                <textarea class="campo__input" id="detalle" name="detalle" maxlength="10000" required>{detalle}</textarea>
                            </div>
                            <div class="bitacora-form__acciones">
                                <button class="boton boton--guardar" name="guardarBitacora" type="submit">Guardar registro</button>
                                <button class="boton boton--salir" data-limpiar-formulario type="button">Limpiar formulario</button>
                            </div>
                        </form>

                        <section class="bitacora-adjuntos">
                            <div class="bitacora-adjuntos__cabecera"><h4>Adjuntos de la entrada</h4><span class="bitacora-adjuntos__contador" id="contadorAdjuntos">0 adjuntos</span></div>
                            <div class="bitacora-notice" style="display:{mostrarAvisoAdjuntos};">Guarde la entrada para habilitar la carga de documentos e imágenes.</div>
                            <p class="bitacora-adjuntos__ayuda" style="display:{mostrarZonaAdjuntos};">PDF, JPG, PNG, WEBP, TXT o SQL, hasta 8 MB. Los archivos guardados aparecen inmediatamente abajo.</p>
                            <div id="dropzoneEntrada" class="dropzone bitacora-dropzone" style="display:{mostrarZonaAdjuntos};"><div class="dz-message">Arrastre un documento, imagen, TXT o SQL aquí, o haga clic para seleccionarlo.</div></div>
                            <div class="bitacora-notice bitacora-notice--error" id="errorAdjunto"></div>
                            <div class="bitacora-galeria" id="galeriaAdjuntos"></div>
                        </section>

                        <section class="bitacora-hilo" style="display:{mostrarAcciones};">
                            <div class="bitacora-hilo__cabecera"><h4>Hilo de acciones</h4><button class="boton boton--nuevo" id="abrirAccion" type="button">Nueva acción</button></div>
                            <div class="bitacora-notice bitacora-notice--error">{mensajeAccion}</div>
                            <div id="hiloAcciones">{hiloAccionesHTML}</div>
                        </section>
                    </div>
                </section>
                <aside class="bitacora-panel">
                    <div class="bitacora-panel__titulo bitacora-registros__encabezado"><span>Registros</span><div class="bitacora-indicadores" aria-label="Resumen de tickets"><div class="bitacora-indicador bitacora-indicador--abierto" title="Tickets abiertos"><strong>{ticketsAbiertos}</strong><span>Abiertos</span></div><div class="bitacora-indicador bitacora-indicador--pausado" title="Tickets pausados"><strong>{ticketsPausados}</strong><span>Pausados</span></div><div class="bitacora-indicador bitacora-indicador--atrasado" title="Tickets abiertos hace más de 10 días"><strong>{ticketsAtrasados}</strong><span>+10 días</span></div><div class="bitacora-indicador bitacora-indicador--cerrado" title="Tickets cerrados"><strong>{ticketsCerrados}</strong><span>Cerrados</span></div></div></div>
                    <div class="bitacora-registros">{filasHistorial}</div>
                </aside>
            </div>
        </section>
    </main>

    <div class="bitacora-modal" id="modalAccion" aria-hidden="true">
        <section class="bitacora-modal__caja" role="dialog" aria-modal="true" aria-labelledby="tituloModalAccion">
            <header class="bitacora-modal__cabecera"><span id="tituloModalAccion">Nueva acción</span><button class="bitacora-modal__cerrar" data-cerrar-modal="modalAccion" type="button" aria-label="Cerrar">×</button></header>
            <form class="bitacora-modal__cuerpo" method="post" action="bitacora_desarrollo.php?id={idBitacora}" enctype="multipart/form-data">
                <input name="idBitacora" type="hidden" value="{idBitacora}">
                <div class="bitacora-form__fila">
                    <div class="formulario__campo"><label class="campo__label" for="fechaAccion">Fecha</label><input class="campo__input" id="fechaAccion" name="fechaAccion" type="date" value="{fechaAccion}" required></div>
                    <div class="formulario__campo"><label class="campo__label" for="tipoAccion">Acción</label><select class="campo__input" id="tipoAccion" name="tipoAccion">{opcionesTipoAccion}</select></div>
                </div>
                <div class="formulario__campo"><label class="campo__label" for="responsableAccion">Responsable</label><select class="campo__input" id="responsableAccion" name="responsableAccion" required>{opcionesResponsablesAccion}</select></div>
                <div class="formulario__campo bitacora-modal__margen"><label class="campo__label" for="detalleAccion">Detalle de la acción</label><textarea class="campo__input" id="detalleAccion" name="detalleAccion" maxlength="4000" required>{detalleAccion}</textarea></div>
                <div class="formulario__campo bitacora-modal__margen"><label class="campo__label" for="adjuntoAccion">Adjunto opcional</label><input class="campo__input" id="adjuntoAccion" name="adjuntoAccion" type="file" accept="application/pdf,image/jpeg,image/png,image/webp,text/plain,application/sql,.txt,.sql"><p class="bitacora-adjuntos__ayuda">PDF, imagen, TXT o SQL, hasta 8 MB.</p></div>
                <div class="bitacora-form__acciones"><button class="boton boton--guardar" name="guardarAccion" type="submit">Agregar acción</button><button class="boton boton--salir" data-cerrar-modal="modalAccion" type="button">Cancelar</button></div>
            </form>
        </section>
    </div>

    <div class="bitacora-modal bitacora-modal--visor" id="modalVisor" aria-hidden="true">
        <section class="bitacora-modal__caja" role="dialog" aria-modal="true" aria-labelledby="tituloVisor">
            <header class="bitacora-modal__cabecera"><span id="tituloVisor">Adjunto</span><button class="bitacora-modal__cerrar" data-cerrar-modal="modalVisor" type="button" aria-label="Cerrar">×</button></header>
            <div class="bitacora-visor__contenido" id="contenidoVisor"></div>
        </section>
    </div>

    <script>
        window.bitacoraDesarrolloConfig = {
            adjuntos: {imagenesInicialesJSON},
            idBitacora: Number('{idBitacoraImagenes}')
        };
    </script>
    <script src="/appMantenimiento/js/bitacora_desarrollo.js"></script>
</body>
</html>
