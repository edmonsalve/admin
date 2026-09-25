<!DOCTYPE html>
<html lang="{lang}">
<head>
    <title>{H2Sistema}</title>
    <link rel="icon" type="image/png" href="/images/icoDC.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/styles/normalize.css" type="text/css" media="screen, projection" />
    <link rel="stylesheet" href="/styles/st_globales.css" type="text/css" media="screen, projection" />
    <link rel="stylesheet" href="/styles/st_topbar.css" type="text/css" media="screen, projection" />
    <link rel="stylesheet" href="/styles/st_contenedores.css" type="text/css" media="screen, projection" />
    <link rel="stylesheet" href="/styles/st_formularios.css" type="text/css" media="screen, projection" />
    <link rel="stylesheet" href="/styles/st_columnas_flexibles.css" type="text/css" media="screen, projection" />
    <link rel="stylesheet" href="/styles/dropzone.css" type="text/css" />
    <script src="/js/dcode.js" type="text/javascript"></script>
    <script src="/js/dropzone-min.js" type="text/javascript"></script>
    <script>Dropzone.autoDiscover = false;</script>
<style>
.sistemas-ficha { max-width: 1280px; padding-bottom: 28px; }
.sistemas-ficha__cabecera { align-items: center; background: linear-gradient(120deg, #123d5d 0%, #1d6d91 58%, #45a2aa 100%); border: 0; border-radius: 14px; box-shadow: 0 12px 26px rgba(25, 72, 100, .18); color: #fff; display: flex; margin: 18px 0; padding: 17px 24px; }
.sistemas-ficha__cabecera h3 { color: #fff; font-size: 23px; letter-spacing: -.02em; margin: 3px 0; position: relative; z-index: 1; }
.sistemas-ficha__form { background: #fff; border: 1px solid #dbe7ed; border-radius: 14px; box-shadow: 0 8px 22px rgba(30, 68, 90, .09); box-sizing: border-box; margin: 0; max-width: 1080px; padding: 20px; }
.sistemas-ficha__form fieldset { border: 0; display: grid; gap: 12px; margin: 0; min-width: 0; padding: 15px 0 0; }
.sistemas-ficha__seccion { background: linear-gradient(90deg, #e8f5f7, #f6fbfc); border: 1px solid #d4e8eb; border-left: 4px solid #2997a4; border-radius: 8px; box-sizing: border-box; color: #18566d; font-size: 14px; font-weight: 800; letter-spacing: .01em; margin: 0; padding: 10px 13px; }
.sistemas-ficha .grid-flex { box-sizing: border-box; gap: 12px; margin: 0; padding: 5px 0; }
.sistemas-ficha .formulario__campo { background: transparent; border-bottom: 0; margin: 0; min-width: 0; padding: 0; }
.sistemas-ficha .campo__label { color: #375b6e; font-size: 12px; font-weight: 750; margin-bottom: 5px; }
.sistemas-ficha .campo__input { background: #f8fbfc; border: 1px solid #c9d9e1; border-radius: 7px; box-shadow: inset 0 1px 2px rgba(36, 73, 92, .03); box-sizing: border-box; color: #23485c; min-height: 37px; padding: 8px 10px; transition: border-color .16s ease, box-shadow .16s ease, background .16s ease; width: 100%; }
.sistemas-ficha .campo__input:focus { background: #fff; border-color: #1c91a0; box-shadow: 0 0 0 3px rgba(28, 145, 160, .16); outline: 0; }
.sistemas-ficha .campo__input[readonly] { background: #eef3f6; color: #617784; cursor: not-allowed; }
.sistemas-ficha textarea.campo__input { line-height: 1.45; min-height: 96px; resize: vertical; }
.sistemas-ficha__media { padding-top: 10px !important; }
.sistemas-ficha__vista-previa { align-items: center; display: flex; height: 144px; justify-content: center; }
.sistemas-ficha__media img { background: #fff; border: 1px solid #d8e5ea; border-radius: 8px; box-shadow: 0 4px 10px rgba(27, 76, 99, .1); display: block; padding: 8px; }
.sistemas-ficha .dropzone { border: 1px dashed #8bbbc3; border-radius: 8px; color: #3e7782; margin-top: 8px; transition: background .16s ease, border-color .16s ease; }
.sistemas-ficha .dropzone:hover { background: #edf8f8 !important; border-color: #2997a4; }
.sistemas-ficha__media-ayuda { color: #826000; font-size: 12px; font-weight: 600; min-height: 0; padding: 0 2px; }
.sistemas-ficha__mensaje-error { color: #a12c26; font-size: 13px; font-weight: 600; min-height: 0; padding: 2px 2px 0; }
.sistemas-ficha__acciones { border-top: 1px solid #e2ebef; gap: 9px; margin-top: 18px; padding-top: 16px; }
.sistemas-ficha__acciones .boton { border-radius: 7px; box-shadow: 0 2px 5px rgba(28, 63, 81, .12); min-height: 36px; padding: 8px 15px; transition: transform .14s ease, box-shadow .14s ease; }
.sistemas-ficha__acciones .boton:hover { box-shadow: 0 5px 12px rgba(28, 63, 81, .16); transform: translateY(-1px); }
@media (max-width: 740px) { .sistemas-ficha__cabecera { align-items: flex-start; padding: 18px; } .sistemas-ficha__cabecera h3 { font-size: 20px; } .sistemas-ficha__form { padding: 14px; } .sistemas-ficha .grid-flex { padding: 11px; } .sistemas-ficha__acciones { align-items: stretch; flex-direction: column; } }
</style>
</head>
<body>
    <div id="toolsbar" style="display: block;">{topbar}</div>
    <main class="principal">
        {barraLateral}
        <section class="tablero sistemas-ficha">
            <div class="filaGeneral filageneral--menu">{headerMenu}</div>
            <div class="tablero__header sistemas-ficha__cabecera"><div class="header__fila1"><h3>Mantenedor de sistemas</h3></div></div>

            <form class="formularioGrande sistemas-ficha__form" id="formularioFicha" method="post" action="sistemas_ficha.php">
                <input name="idAnterior" type="hidden" value="{_idAnterior}" />
                <div class="filaGeneral filaGeneral__seccion sistemas-ficha__seccion">Identificación del sistema</div>
                <fieldset>
                    <div class="grid-flex" style="--columns: 1fr 3fr 2fr 1fr;">
                        <div class="formulario__campo">
                            <label class="campo__label" for="id">ID</label>
                            <input class="campo__input" id="id" name="id" type="number" min="1" max="999" value="{_id}" {idReadonly} required />
                        </div>
                        <div class="formulario__campo">
                            <label class="campo__label" for="nombre">Nombre</label>
                            <input class="campo__input" id="nombre" name="nombre" type="text" value="{_nombre}" required />
                        </div>
                        <div class="formulario__campo">
                            <label class="campo__label" for="area">Área</label>
                            <select class="campo__input" id="area" name="area">{optionAreas}</select>
                        </div>
                        <div class="formulario__campo">
                            <label class="campo__label" for="estado">Estado</label>
                            <select class="campo__input" id="estado" name="estado">
                                <option value="S" {estadoActivo}>Activo</option>
                                <option value="N" {estadoInactivo}>Inactivo</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid-flex" style="--columns: 3fr 2fr 2fr;">
                        <div class="formulario__campo">
                            <label class="campo__label" for="ruta">Ruta</label>
                            <input class="campo__input" id="ruta" name="ruta" type="text" value="{_ruta}" />
                        </div>
                        <div class="formulario__campo">
                            <label class="campo__label" for="dbase">Base de datos</label>
                            <input class="campo__input" id="dbase" name="dbase" type="text" value="{_dbase}" />
                        </div>
                        <div class="formulario__campo">
                            <label class="campo__label" for="prefijoTablas">Prefijo de tablas</label>
                            <input class="campo__input" id="prefijoTablas" name="prefijoTablas" type="text" value="{_prefijoTablas}" />
                        </div>
                    </div>
                    <div class="grid-flex" style="--columns: 1fr 1fr;">
                        <div class="formulario__campo">
                            <label class="campo__label" for="icono">Ícono</label>
                            <input class="campo__input" id="icono" name="icono" type="text" value="{_icono}" />
                        </div>
                        <div class="formulario__campo">
                            <label class="campo__label" for="btn">Botón</label>
                            <input class="campo__input" id="btn" name="btn" type="text" value="{_btn}" />
                        </div>
                    </div>
                    <div class="grid-flex sistemas-ficha__media" style="--columns: 1fr 1fr; display: {mediaSistema};">
                        <div class="formulario__campo">
                            <label class="campo__label">Vista previa ícono</label>
                            <div class="sistemas-ficha__vista-previa"><img id="vistaIcono" src="{urlIcono}" alt="Ícono del sistema" style="display: {mostrarIcono}; max-width: 110px; max-height: 110px; object-fit: contain; margin: 8px auto;" /></div>
                            <div id="dropzoneIcono" class="dropzone" style="min-height: 105px; background: #f7f7f7;"></div>
                        </div>
                        <div class="formulario__campo">
                            <label class="campo__label">Vista previa botón</label>
                            <div class="sistemas-ficha__vista-previa"><img id="vistaBoton" src="{urlBoton}" alt="Botón del sistema" style="display: {mostrarBoton}; max-width: 180px; max-height: 110px; object-fit: contain; margin: 8px auto;" /></div>
                            <div id="dropzoneBoton" class="dropzone" style="min-height: 105px; background: #f7f7f7;"></div>
                        </div>
                    </div>
                    <div class="sistemas-ficha__media-ayuda">{mensajeMedia}</div>
                    <div class="grid-flex" style="--columns: 1fr 1fr;">
                        <div class="formulario__campo">
                            <label class="campo__label" for="version">Versión</label>
                            <input class="campo__input" id="version" name="version" type="text" value="{_version}" />
                        </div>
                        <div class="formulario__campo">
                            <label class="campo__label" for="fechaDesarrollo">Fecha de desarrollo</label>
                            <input class="campo__input" id="fechaDesarrollo" name="fechaDesarrollo" type="text" value="{_fechaDesarrollo}" />
                        </div>
                    </div>
                    <div class="formulario__campo">
                        <label class="campo__label" for="descripcion">Descripción</label>
                        <textarea class="campo__input" id="descripcion" name="descripcion" rows="4">{_descripcion}</textarea>
                    </div>
                </fieldset>
                <div class="sistemas-ficha__mensaje-error">{mensajeError}</div>
                <div class="filaGeneral sistemas-ficha__acciones">
                    <button class="boton boton--guardar" name="guardarSistema" type="submit">Guardar</button>
                    <button class="boton boton--nuevo" type="button" onclick="editarPost('sistemas', 'new')">Nuevo</button>
                    <button class="boton boton--salir" type="button" onclick="location.href='sistemas.php'">Salir</button>
                </div>
            </form>
        </section>
    </main>
</body>
<script src="../js/sistemas_ficha.js" type="text/javascript"></script>
</html>
