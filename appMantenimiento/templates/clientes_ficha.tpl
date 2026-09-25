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
        .clientes-ficha { max-width: 1320px; padding-bottom: 30px; }
        .clientes-ficha__cabecera { align-items: center; background: linear-gradient(120deg, #164b70, #25839a); border: 0; border-radius: 12px; box-shadow: 0 10px 24px rgba(27, 75, 100, .16); color: #fff; display: flex; margin: 18px 0; padding: 17px 24px; }
        .clientes-ficha__cabecera h3 { color: #fff; font-size: 22px; letter-spacing: -.02em; margin: 0; }
        .clientes-ficha__form { background: #fff; border: 1px solid #dbe7ed; border-radius: 14px; box-shadow: 0 7px 20px rgba(30, 68, 90, .08); box-sizing: border-box; margin: 0; max-width: 1160px; padding: 21px; }
        .clientes-ficha__form > fieldset { border: 0; display: grid; gap: 12px; margin: 0 0 17px; min-width: 0; padding: 13px 0 0; }
        .clientes-ficha__seccion { background: linear-gradient(90deg, #edf7f8, #f9fcfd); border: 1px solid #d7e8eb; border-left: 4px solid #2995a2; border-radius: 8px; box-sizing: border-box; color: #1b6075; font-size: 13px; font-weight: 800; letter-spacing: .01em; margin: 0; padding: 10px 13px; }
        .clientes-ficha .grid-flex { gap: 12px; margin: 0; padding: 3px 0; }
        .clientes-ficha .formulario__campo { background: transparent; border-bottom: 0; margin: 0; min-width: 0; padding: 0; }
        .clientes-ficha .campo__label { color: #375d70; font-size: 12px; font-weight: 750; margin-bottom: 5px; }
        .clientes-ficha .campo__input { background: #f8fbfc; border: 1px solid #c8d9e1; border-radius: 7px; box-sizing: border-box; color: #20495f; min-height: 38px; padding: 8px 10px; transition: background .16s ease, border-color .16s ease, box-shadow .16s ease; width: 100%; }
        .clientes-ficha .campo__input:focus { background: #fff; border-color: #238fa0; box-shadow: 0 0 0 3px rgba(35, 143, 160, .15); outline: 0; }
        .clientes-ficha .campo__input[readonly] { background: #eef3f6; color: #627782; }
        .clientes-ficha textarea.campo__input { line-height: 1.45; min-height: 75px; resize: vertical; }
        .clientes-ficha__tecnica { border-top: 1px solid #e1ebef; margin-top: 4px; padding-top: 17px; }
        .clientes-ficha__tecnica > fieldset { border: 0; display: grid; gap: 12px; margin: 0 0 17px; min-width: 0; padding: 13px 0 0; }
        .clientes-sistemas { background: #fbfdfe; border: 1px solid #dce9ed; border-radius: 10px; margin-top: 12px; padding: 12px; }
        .clientes-sistemas__grupo { border: 1px solid #d6e4e9; border-radius: 8px; margin: 0 0 10px; padding: 10px 12px; }
        .clientes-sistemas__grupo:last-child { margin-bottom: 0; }
        .clientes-sistemas__grupo legend { color: #24667d; font-size: 12px; font-weight: 800; padding: 0 5px; }
        .clientes-sistemas__lista { display: grid; gap: 5px 15px; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); }
        .clientes-sistemas__item { color: #375767; font-size: 13px; padding: 4px 1px; }
        .clientes-sistemas__item input { accent-color: #278d9c; }
        .clientes-sistemas__vacio { color: #697783; font-size: 13px; }
        .clientes-ficha__mensaje { font-size: 13px; font-weight: 600; margin-top: 10px; min-height: 0; }
        .clientes-ficha__mensaje--error { color: #a12c27; }
        .clientes-ficha__mensaje--exito { color: #1b7b45; }
        .clientes-ficha__acciones { align-items: center; border-top: 1px solid #e1ebef; gap: 9px; margin-top: 18px; padding-top: 16px; }
        .clientes-ficha__acciones .boton { align-items: center; border-radius: 7px; box-shadow: 0 2px 5px rgba(26, 61, 79, .1); box-sizing: border-box; display: inline-flex; height: 38px; justify-content: center; line-height: 1; margin: 0; padding: 0 15px; transition: box-shadow .15s ease, transform .15s ease; }
        .clientes-ficha__acciones .boton:hover { box-shadow: 0 5px 12px rgba(26, 61, 79, .16); transform: translateY(-1px); }
        @media (max-width: 680px) { .clientes-ficha__form { padding: 14px; } .clientes-ficha__acciones { align-items: stretch; flex-direction: column; } }
    </style>
</head>
<body>
    <div id="toolsbar" style="display:block;">{topbar}</div>
    <main class="principal">
        {barraLateral}
        <section class="tablero clientes-ficha">
            <div class="filaGeneral filageneral--menu">{headerMenu}</div>
            <div class="tablero__header clientes-ficha__cabecera"><div class="header__fila1"><h3>{H2Titulo}</h3></div></div>
            <form class="formularioGrande clientes-ficha__form" method="post" action="clientes_ficha.php">
                <input name="idCliente" type="hidden" value="{idCliente}">
                <input name="modulosNoVigentes" type="hidden" value="{modulosNoVigentes}">

                <div class="filaGeneral filaGeneral__seccion clientes-ficha__seccion">Datos del cliente</div>
                <fieldset>
                    <div class="grid-flex" style="--columns:2fr 5fr 2fr 2fr;">
                        <div class="formulario__campo"><label class="campo__label" for="rut">RUT</label><input class="campo__input" id="rut" name="rut" value="{rut}" required></div>
                        <div class="formulario__campo"><label class="campo__label" for="cliente">Cliente</label><input class="campo__input" id="cliente" name="cliente" value="{cliente}" required></div>
                        <div class="formulario__campo"><label class="campo__label" for="prefijoBD">Prefijo BD</label><input class="campo__input" id="prefijoBD" name="prefijoBD" value="{prefijoBD}"></div>
                        <div class="formulario__campo"><label class="campo__label" for="estadoCliente">Estado</label><select class="campo__input" id="estadoCliente" name="estadoCliente"><option value="ACTIVO" {estadoClienteActivo}>Activo</option><option value="INACTIVO" {estadoClienteInactivo}>Inactivo</option></select></div>
                    </div>
                </fieldset>

                <div class="filaGeneral filaGeneral__seccion clientes-ficha__seccion">Contrato</div>
                <fieldset>
                    <div class="grid-flex" style="--columns:2fr 2fr 2fr 3fr;">
                        <div class="formulario__campo"><label class="campo__label" for="inicioContrato">Inicio</label><input class="campo__input" id="inicioContrato" name="inicioContrato" type="date" value="{inicioContrato}"></div>
                        <div class="formulario__campo"><label class="campo__label" for="venctoContrato">Vencimiento</label><input class="campo__input" id="venctoContrato" name="venctoContrato" type="date" value="{venctoContrato}"></div>
                        <div class="formulario__campo"><label class="campo__label" for="montoMensual">Monto mensual</label><input class="campo__input" id="montoMensual" name="montoMensual" value="{montoMensual}"></div>
                        <div class="formulario__campo"><label class="campo__label" for="contactoAdmin">Contacto administrativo</label><input class="campo__input" id="contactoAdmin" name="contactoAdmin" value="{contactoAdmin}"></div>
                    </div>
                    <div class="formulario__campo"><label class="campo__label" for="observContrato">Observaciones</label><textarea class="campo__input" id="observContrato" name="observContrato" rows="2">{observContrato}</textarea></div>
                </fieldset>

                <div class="filaGeneral filaGeneral__seccion clientes-ficha__seccion">Contacto de cobranza</div>
                <fieldset>
                    <div class="grid-flex" style="--columns:2fr 3fr 2fr 1fr;">
                        <div class="formulario__campo"><label class="campo__label" for="contactoCobranza">Contacto 1</label><input class="campo__input" id="contactoCobranza" name="contactoCobranza" maxlength="100" value="{contactoCobranza}"></div>
                        <div class="formulario__campo"><label class="campo__label" for="emailCobranza">Correo</label><input class="campo__input" id="emailCobranza" name="emailCobranza" type="email" maxlength="100" value="{emailCobranza}"></div>
                        <div class="formulario__campo"><label class="campo__label" for="telefonoCobranza">Teléfono</label><input class="campo__input" id="telefonoCobranza" name="telefonoCobranza" maxlength="50" value="{telefonoCobranza}"></div>
                        <div class="formulario__campo"><label class="campo__label" for="estadoContactoCobranza">Estado</label><select class="campo__input" id="estadoContactoCobranza" name="estadoContactoCobranza"><option value="ACTIVO" {estadoContactoCobranzaActivo}>Activo</option><option value="INACTIVO" {estadoContactoCobranzaInactivo}>Inactivo</option></select></div>
                    </div>
                    <div class="grid-flex" style="--columns:2fr 3fr 2fr 1fr;">
                        <div class="formulario__campo"><label class="campo__label" for="contactoCobranza2">Contacto 2</label><input class="campo__input" id="contactoCobranza2" name="contactoCobranza2" maxlength="100" value="{contactoCobranza2}"></div>
                        <div class="formulario__campo"><label class="campo__label" for="emailCobranza2">Correo</label><input class="campo__input" id="emailCobranza2" name="emailCobranza2" type="email" maxlength="100" value="{emailCobranza2}"></div>
                        <div class="formulario__campo"><label class="campo__label" for="telefonoCobranza2">Teléfono</label><input class="campo__input" id="telefonoCobranza2" name="telefonoCobranza2" maxlength="50" value="{telefonoCobranza2}"></div>
                        <div class="formulario__campo"><label class="campo__label" for="estadoContactoCobranza2">Estado</label><select class="campo__input" id="estadoContactoCobranza2" name="estadoContactoCobranza2"><option value="ACTIVO" {estadoContactoCobranza2Activo}>Activo</option><option value="INACTIVO" {estadoContactoCobranza2Inactivo}>Inactivo</option></select></div>
                    </div>
                    <div class="grid-flex" style="--columns:2fr 3fr 2fr 1fr;">
                        <div class="formulario__campo"><label class="campo__label" for="contactoCobranza3">Contacto 3</label><input class="campo__input" id="contactoCobranza3" name="contactoCobranza3" maxlength="100" value="{contactoCobranza3}"></div>
                        <div class="formulario__campo"><label class="campo__label" for="emailCobranza3">Correo</label><input class="campo__input" id="emailCobranza3" name="emailCobranza3" type="email" maxlength="100" value="{emailCobranza3}"></div>
                        <div class="formulario__campo"><label class="campo__label" for="telefonoCobranza3">Teléfono</label><input class="campo__input" id="telefonoCobranza3" name="telefonoCobranza3" maxlength="50" value="{telefonoCobranza3}"></div>
                        <div class="formulario__campo"><label class="campo__label" for="estadoContactoCobranza3">Estado</label><select class="campo__input" id="estadoContactoCobranza3" name="estadoContactoCobranza3"><option value="ACTIVO" {estadoContactoCobranza3Activo}>Activo</option><option value="INACTIVO" {estadoContactoCobranza3Inactivo}>Inactivo</option></select></div>
                    </div>
                </fieldset>

                <div class="clientes-ficha__tecnica" style="display:{mostrarGestionTecnica};">
                    <div class="filaGeneral filaGeneral__seccion clientes-ficha__seccion">Administradores de sistemas</div>
                    <fieldset>
                        <div class="grid-flex" style="--columns:2fr 3fr 2fr 3fr;">
                            <div class="formulario__campo"><label class="campo__label" for="administrador1">Municipal</label><input class="campo__input" id="administrador1" name="administrador1" value="{administrador1}"></div>
                            <div class="formulario__campo"><label class="campo__label" for="emailAdm1">Correo</label><input class="campo__input" id="emailAdm1" name="emailAdm1" type="email" value="{emailAdm1}"></div>
                            <div class="formulario__campo"><label class="campo__label" for="administrador2">Salud</label><input class="campo__input" id="administrador2" name="administrador2" value="{administrador2}"></div>
                            <div class="formulario__campo"><label class="campo__label" for="emailAdm2">Correo</label><input class="campo__input" id="emailAdm2" name="emailAdm2" type="email" value="{emailAdm2}"></div>
                        </div>
                        <div class="grid-flex" style="--columns:2fr 3fr;">
                            <div class="formulario__campo"><label class="campo__label" for="administrador3">Educación</label><input class="campo__input" id="administrador3" name="administrador3" value="{administrador3}"></div>
                            <div class="formulario__campo"><label class="campo__label" for="emailAdm3">Correo</label><input class="campo__input" id="emailAdm3" name="emailAdm3" type="email" value="{emailAdm3}"></div>
                        </div>
                    </fieldset>

                    <div class="filaGeneral filaGeneral__seccion clientes-ficha__seccion">Licencia</div>
                    <fieldset>
                        <div class="grid-flex" style="--columns:4fr 2fr 1fr 1fr 1fr 1fr;">
                            <div class="formulario__campo"><label class="campo__label" for="licencia">Referencia de licencia</label><input class="campo__input" id="licencia" name="licencia" value="{licencia}"></div>
                            <div class="formulario__campo"><label class="campo__label" for="vencimientoLic">Vencimiento</label><input class="campo__input" id="vencimientoLic" name="vencimientoLic" type="date" value="{vencimientoLic}"></div>
                            <div class="formulario__campo"><label class="campo__label" for="estadoLic">Estado</label><select class="campo__input" id="estadoLic" name="estadoLic"><option value="A" {estadoLicA}>Activa</option><option value="D" {estadoLicD}>Desactivada</option></select></div>
                            <div class="formulario__campo"><label class="campo__label" for="tipoLic">Tipo</label><select class="campo__input" id="tipoLic" name="tipoLic"><option value="C" {tipoLicC}>Compra</option><option value="A" {tipoLicA}>Arriendo</option></select></div>
                            <div class="formulario__campo"><label class="campo__label" for="m">Muni</label><select class="campo__input" id="m" name="m"><option value="S" {mSi}>Sí</option><option value="N" {mNo}>No</option></select></div>
                            <div class="formulario__campo"><label class="campo__label" for="s">Salud</label><select class="campo__input" id="s" name="s"><option value="S" {sSi}>Sí</option><option value="N" {sNo}>No</option></select></div>
                        </div>
                        <div class="grid-flex" style="--columns:1fr 1fr 4fr;">
                            <div class="formulario__campo"><label class="campo__label" for="e">Educación</label><select class="campo__input" id="e" name="e"><option value="S" {eSi}>Sí</option><option value="N" {eNo}>No</option></select></div>
                            <div class="formulario__campo"><label class="campo__label" for="c">Administración</label><select class="campo__input" id="c" name="c"><option value="S" {cSi}>Sí</option><option value="N" {cNo}>No</option></select></div>
                            <div class="formulario__campo"><label class="campo__label" for="modulos">Sistemas codificados</label><input class="campo__input" id="modulos" value="{modulos}" readonly></div>
                        </div>
                    </fieldset>
                    <div class="filaGeneral filaGeneral__seccion clientes-ficha__seccion">Sistemas contratados vigentes</div>
                    <div class="clientes-sistemas">{sistemasHTML}</div>
                </div>

                <div class="clientes-ficha__mensaje clientes-ficha__mensaje--error">{mensajeError}</div>
                <div class="clientes-ficha__mensaje clientes-ficha__mensaje--exito">{mensajeExito}</div>
                <div class="filaGeneral clientes-ficha__acciones">
                    <button class="boton boton--guardar" name="guardarCliente" type="submit">Guardar</button>
                    <span class="clientes-ficha__licencia-accion" style="display:{mostrarGestionTecnica};"><a class="boton boton--buscar" href="{urlLicencia}">Generar licencia</a></span>
                    <button class="boton boton--salir" type="button" onclick="location.href='clientes.php'">Salir</button>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
