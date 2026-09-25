<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
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
    <link rel="stylesheet" href="/styles/st_formulariosFlotantes.css" type="text/css" media="screen, projection" />
    <link rel="stylesheet" href="/styles/st_columnas_flexibles.css" type="text/css" media="screen, projection" />
    <script src="/js/dcode.js" type="text/javascript"></script>
    <script src="/js/loadAjax.js" type="text/javascript"></script>
    <script type="text/javascript">
        function abrirServidor(idServidor) {
            location.href = 'servidores.php?IdRegistro=' + encodeURIComponent(idServidor) + '&ope=Update';
        }

        function alternarContrasena(idCampo, boton) {
            const campo = document.getElementById(idCampo);
            const visible = campo.type === 'text';
            campo.type = visible ? 'password' : 'text';
            boton.textContent = visible ? 'Ver' : 'Ocultar';
            boton.setAttribute('aria-label', visible ? 'Ver contraseña' : 'Ocultar contraseña');
        }
    </script>
    <style type="text/css">
        .servidores-modal { background: #fff !important; border: 1px solid #c9dce5 !important; border-left: 1px solid #c9dce5 !important; border-radius: 14px !important; box-shadow: 0 18px 46px rgba(24, 57, 76, .28); max-height: calc(100vh - 32px) !important; padding: 0 !important; }
        .servidores-modal__form { box-sizing: border-box; padding: 18px 22px 22px; }
        .servidores-modal__cabecera { align-items: center; background: linear-gradient(120deg, #174b70, #287f9c); border-radius: 11px 11px 0 0; box-sizing: border-box; display: flex; height: 52px; justify-content: flex-end; margin: -18px -22px 16px; padding: 0 14px; }
        .servidores-modal__cerrar { align-items: center; background: rgba(255,255,255,.14); border: 1px solid rgba(255,255,255,.34); border-radius: 6px; cursor: pointer; display: inline-flex; height: 30px; justify-content: center; padding: 5px; width: 30px; }
        .servidores-modal__cerrar:hover { background: rgba(255,255,255,.26); }
        .servidores-modal__cerrar img { filter: brightness(0) invert(1); height: 17px; width: 17px; }
        .servidores-modal__cabecera h3 { color: #fff; font-size: 19px; letter-spacing: -.02em; margin: 0 auto 0 0; text-align: left; }
        .servidores-modal .filaGeneral__seccion { background: linear-gradient(90deg, #edf7f8, #f9fcfd); border: 1px solid #d8e8ec; border-left: 4px solid #2b96a3; border-radius: 7px; box-sizing: border-box; color: #1d6075; font-size: 13px; font-weight: 800; margin: 15px 0 11px; padding: 9px 11px; }
        .servidores-modal .grid-flex { gap: 11px; margin: 0; }
        .servidores-modal .ff-contenedor { background: transparent; border-bottom: 0; gap: 5px; margin: 0 0 11px; padding: 0; }
        .servidores-modal .ff-contenedor label { color: #365c6e; font-size: 12px; font-weight: 750; padding: 0; }
        .servidores-modal .ff-contenedor input, .servidores-modal .ff-contenedor select { background: #f8fbfc; border: 1px solid #c8d9e1; border-radius: 7px; box-sizing: border-box; color: #234b60; font-size: 14px; height: 38px; margin: 0; padding: 8px 10px; transition: background .16s ease, border-color .16s ease, box-shadow .16s ease; }
        .servidores-modal .ff-contenedor input:focus, .servidores-modal .ff-contenedor select:focus { background: #fff; border-color: #258fa0; box-shadow: 0 0 0 3px rgba(37, 143, 160, .15); outline: 0; padding: 8px 10px; }
        .servidores-modal .campo-contrasena { align-items: stretch; display: flex; flex-wrap: nowrap; gap: 6px; }
        .servidores-modal .campo-contrasena input { flex: 1 1 0; min-width: 0; width: auto; }
        .servidores-modal .campo-contrasena button { background: #e7f2f5; border: 1px solid #aac8d2; border-radius: 6px; color: #205f75; cursor: pointer; flex: 0 0 auto; font-size: 12px; height: 38px; padding: 0 10px; width: auto; }
        .servidores-modal .campo-contrasena button:hover { background: #d6ebef; }
        .servidores-modal__mensaje { font-size: 13px; font-weight: 600; margin: 8px 0; }
        .servidores-modal__mensaje--error { color: #a12d27; }
        .servidores-modal__mensaje--exito { color: #1c7a45; }
        .servidores-modal__acciones { border-top: 1px solid #e0ebef; display: flex; gap: 9px; margin-top: 16px; padding-top: 14px; }
        .servidores-modal__acciones .boton { align-items: center; border-radius: 7px; box-shadow: 0 2px 5px rgba(25, 61, 80, .12); display: inline-flex; height: 38px; justify-content: center; line-height: 1; margin: 0; padding: 0 16px; }
        .servidores-modal__acciones .boton:hover { box-shadow: 0 5px 12px rgba(25, 61, 80, .16); transform: translateY(-1px); }
        .servidores-filtro__busqueda { min-width: 360px; width: min(440px, 38vw); }
        .servidores-filtro__busqueda .campo__input { box-sizing: border-box; width: 100%; }
        @media (max-width: 768px) { .servidores-filtro__busqueda { min-width: 0; width: 100%; } .servidores-grid { grid-template-columns: 1fr !important; } .servidores-modal { max-height: calc(100vh - 16px) !important; width: calc(100vw - 16px) !important; } .servidores-modal__form { padding: 14px; } .servidores-modal__cabecera { margin: -14px -14px 14px; } }
    </style>
</head>
<body onload="cargaInicial('{moduloPHP}',{_async},{pagina})">
    <div id="toolsbar" style="display: block;">{topbar}</div>
    <main class="principal">
        {barraLateral}
        <section class="tablero">
            <div class="filaGeneral filageneral--menu">{headerMenu}</div>
            <div class="tablero__header"><div class="tHeader__derecha"><div class="header__fila1"><h3>{H2Titulo}</h3></div></div></div>
            <div class="filaGeneral">
                <input id="pagina" type="hidden" value="{pagina}" />
                <input id="campo" type="hidden" value="servidores_busqueda" />
                <div class="formulario__campo servidores-filtro__busqueda">
                    <label class="campo__label" for="buscar">Buscar por servidor, cliente o URL</label>
                    <input class="campo__input" id="buscar" type="text" value="{iguala}" ondblclick="this.value=''" />
                </div>
                <div class="formulario__campo formulario__campo--sin">
                    <label class="campo__label"><br></label>
                    <input onclick="filtrarJSON('{moduloPHP}',1)" type="button" class="boton boton--buscar" value="Filtrar" />
                </div>
                <div class="formulario__campo formulario__campo--sin">
                    <label class="campo__label"><br></label>
                    <input onclick="location.href='servidores.php?IdRegistro=new&ope=Add'" type="button" class="boton boton--nuevo" value="Nuevo" />
                </div>
                <div onchange="filtrarJSON('{moduloPHP}',1)" class="formulario__campo formulario__campo--filtro">
                    <label class="campo__label" for="aux1">Cliente</label>
                    <select class="campo__input" id="aux1" name="s.idCliente">{opcionesClientesFiltro}</select>
                </div>
                <div class="formulario__campo formulario__campo--orden">
                    <label class="campo__label">Ordenar por<br></label>
                    <select onchange="filtrarJSON('{moduloPHP}',1)" class="campo__input" id="ordenarPor">
                        <option value="s.nombreServidor" {_ord0}>Servidor</option>
                        <option value="c.cliente" {_ord1}>Cliente</option>
                        <option value="s.ipPublica" {_ord2}>IP pública</option>
                        <option value="s.prefijoBD" {_ord3}>Prefijo BD</option>
                    </select>
                </div>
                <div onchange="filtrarJSON('{moduloPHP}',1)" class="formulario__campo formulario__campo--orden">
                    <label class="campo__label">Sentido<br></label>
                    <select class="campo__input" id="sentido"><option value="ASC" {sentASC}>▲ Ascendente</option><option value="DESC" {sentDESC}>▼ Descendente</option></select>
                </div>
                <div onchange="filtrarJSON('{moduloPHP}',1)" class="formulario__campo formulario__campo--orden">
                    <label class="campo__label">Filas<br></label>
                    <select class="campo__input" id="filas"><option value="8" {fl8}>8</option><option value="10" {fl10}>10</option><option value="12" {fl12}>12</option><option value="15" {fl15}>15</option><option value="20" {fl20}>20</option></select>
                </div>
            </div>
            <div id="contenedorResultados">
                {grillaHTMLTit}{grillaHTML}
                <div class="tablero__footer"><ul class="paginacion">{paginacionHTML}</ul></div>
            </div>
        </section>
    </main>

    <section class="formulario-flotante ff-interior servidores-modal" style="display:{mostrarFormulario}; width: min(1100px, 96vw);">
        <form class="servidores-modal__form" method="post" action="servidores.php" style="width:100%;">
            <input name="idServidor" type="hidden" value="{idServidor}" />
            <div class="ff-fila servidores-modal__cabecera"><h3>{tituloFormulario}</h3><button class="servidores-modal__cerrar" type="button" onclick="location.href='servidores.php'" title="Cerrar" aria-label="Cerrar"><img src="/iconos/ico_salir.png" alt=""></button></div>
            <div class="filaGeneral filaGeneral__seccion">Identificación y conectividad</div>
            <div class="grid-flex servidores-grid" style="--columns: 4fr 6fr;">
                <div class="ff-contenedor"><label for="idCliente">Cliente</label><select id="idCliente" name="idCliente" required>{opcionesClientes}</select></div>
                <div class="ff-contenedor"><label for="nombreServidor">Nombre del servidor</label><input id="nombreServidor" name="nombreServidor" type="text" value="{_nombreServidor}" required maxlength="100" /></div>
            </div>
            <div class="grid-flex servidores-grid" style="--columns: 12fr 12fr 28fr 7fr 8fr;">
                <div class="ff-contenedor"><label for="ipLocal">IP local</label><input id="ipLocal" name="ipLocal" type="text" value="{_ipLocal}" maxlength="100" /></div>
                <div class="ff-contenedor"><label for="ipPublica">IP pública</label><input id="ipPublica" name="ipPublica" type="text" value="{_ipPublica}" maxlength="100" /></div>
                <div class="ff-contenedor"><label for="url">URL</label><input id="url" name="url" type="text" value="{_url}" maxlength="150" /></div>
                <div class="ff-contenedor"><label for="phpVer">Versión PHP</label><input id="phpVer" name="phpVer" type="text" value="{_phpVer}" maxlength="20" /></div>
                <div class="ff-contenedor"><label for="puertoHTTP">Puerto HTTP</label><input id="puertoHTTP" name="puertoHTTP" type="number" min="1" max="65535" value="{_puertoHTTP}" /></div>
            </div>
            <div class="grid-flex servidores-grid" style="--columns: 1fr;"><div class="ff-contenedor"><label for="urlApiCliente">URL API cliente</label><input id="urlApiCliente" name="urlApiCliente" type="text" value="{_urlApiCliente}" maxlength="150" /></div></div>
            <div class="filaGeneral filaGeneral__seccion">Base de datos</div>
            <div class="grid-flex servidores-grid" style="--columns: 8fr 8fr 14fr 22fr;">
                <div class="ff-contenedor"><label for="prefijoBD">Prefijo BD</label><input id="prefijoBD" name="prefijoBD" type="text" value="{_prefijoBD}" maxlength="10" /></div>
                <div class="ff-contenedor"><label for="puertoSQL">Puerto SQL</label><input id="puertoSQL" name="puertoSQL" type="number" min="1" max="65535" value="{_puertoSQL}" /></div>
                <div class="ff-contenedor"><label for="usrSQL">Usuario SQL</label><input id="usrSQL" name="usrSQL" type="text" value="{_usrSQL}" maxlength="50" autocomplete="off" /></div>
                <div class="ff-contenedor"><label for="passSQL">Contraseña SQL</label><div class="campo-contrasena"><input id="passSQL" name="passSQL" type="password" value="{_passSQL}" maxlength="50" autocomplete="new-password" /><button type="button" onclick="alternarContrasena('passSQL', this)" aria-label="Ver contraseña">Ver</button></div></div>
            </div>
            <div class="filaGeneral filaGeneral__seccion">Acceso SSH</div>
            <div class="grid-flex servidores-grid" style="--columns: 14fr 8fr 22fr 18fr 11fr;">
                <div class="ff-contenedor"><label for="usrSSH">Usuario SSH</label><input id="usrSSH" name="usrSSH" type="text" value="{_usrSSH}" maxlength="50" autocomplete="off" /></div>
                <div class="ff-contenedor"><label for="puertoSSH">Puerto SSH</label><input id="puertoSSH" name="puertoSSH" type="number" min="1" max="65535" value="{_puertoSSH}" /></div>
                <div class="ff-contenedor"><label for="passSSH">Contraseña SSH</label><div class="campo-contrasena"><input id="passSSH" name="passSSH" type="password" value="{_passSSH}" maxlength="50" autocomplete="new-password" /><button type="button" onclick="alternarContrasena('passSSH', this)" aria-label="Ver contraseña">Ver</button></div></div>
                <div class="ff-contenedor"><label for="llavePrivada">Llave privada</label><select id="llavePrivada" name="llavePrivada">{opcionesLlaves}</select></div>
                <div class="ff-contenedor"><label for="tunelSSH">Túnel SSH</label><select id="tunelSSH" name="tunelSSH"><option value="N" {tunelSSHNo}>No</option><option value="S" {tunelSSHSi}>Sí</option></select></div>
            </div>
            <div class="servidores-modal__mensaje servidores-modal__mensaje--error">{mensajeError}</div>
            <div class="servidores-modal__mensaje servidores-modal__mensaje--exito">{mensajeExito}</div>
            <div class="filaGeneral servidores-modal__acciones"><button class="boton boton--guardar" name="guardarServidor" type="submit">Guardar</button><button class="boton boton--salir" type="button" onclick="location.href='servidores.php'">Salir</button></div>
        </form>
    </section>
</body>
</html>
