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
        .usuarios-ficha { max-width: 1160px; padding-bottom: 28px; }
        .usuarios-ficha__cabecera { align-items: center; background: linear-gradient(120deg, #174b70, #287e9a); border: 0; border-radius: 12px; box-shadow: 0 10px 24px rgba(27, 74, 99, .16); color: #fff; display: flex; margin: 18px 0; padding: 17px 24px; }
        .usuarios-ficha__cabecera h3 { color: #fff; font-size: 22px; letter-spacing: -.02em; margin: 0; }
        .usuarios-ficha__form { background: #fff; border: 1px solid #dbe7ed; border-radius: 13px; box-shadow: 0 7px 20px rgba(30, 68, 90, .08); box-sizing: border-box; margin: 0; max-width: 860px; padding: 21px; }
        .usuarios-ficha__seccion { background: linear-gradient(90deg, #edf7f8, #f9fcfd); border: 1px solid #d7e8eb; border-left: 4px solid #2a96a3; border-radius: 8px; box-sizing: border-box; color: #1c6075; font-size: 13px; font-weight: 800; margin: 0; padding: 10px 13px; }
        .usuarios-ficha__form fieldset { border: 0; display: grid; gap: 13px; margin: 0; min-width: 0; padding: 14px 0 0; }
        .usuarios-ficha .grid-flex { gap: 12px; margin: 0; padding: 3px 0; }
        .usuarios-ficha .formulario__campo { background: transparent; border-bottom: 0; margin: 0; min-width: 0; padding: 0; }
        .usuarios-ficha .campo__label { color: #365c6f; font-size: 12px; font-weight: 750; margin-bottom: 5px; }
        .usuarios-ficha .campo__input { background: #f8fbfc; border: 1px solid #c8d9e1; border-radius: 7px; box-sizing: border-box; color: #234a60; min-height: 38px; padding: 8px 10px; transition: background .16s ease, border-color .16s ease, box-shadow .16s ease; width: 100%; }
        .usuarios-ficha .campo__input:focus { background: #fff; border-color: #258fa0; box-shadow: 0 0 0 3px rgba(37, 143, 160, .15); outline: 0; }
        .usuarios-ficha .campo__input[readonly] { background: #eef3f6; color: #607783; cursor: not-allowed; }
        .usuarios-ficha__clave { background: #f7fbfc; border: 1px dashed #afcbd3; border-radius: 8px; padding: 12px !important; }
        .usuarios-ficha__clave small { color: #647984; font-size: 12px; margin-top: 5px; }
        .usuarios-ficha__mensaje { font-size: 13px; font-weight: 600; margin-top: 10px; min-height: 0; }
        .usuarios-ficha__mensaje--error { color: #a12d27; }
        .usuarios-ficha__mensaje--exito { color: #1b7a44; }
        .usuarios-ficha__acciones { align-items: center; border-top: 1px solid #e0ebef; gap: 9px; margin-top: 18px; padding-top: 16px; }
        .usuarios-ficha__acciones .boton { align-items: center; border-radius: 7px; box-shadow: 0 2px 5px rgba(25, 61, 80, .1); box-sizing: border-box; display: inline-flex; height: 38px; justify-content: center; line-height: 1; margin: 0; padding: 0 16px; transition: box-shadow .15s ease, transform .15s ease; }
        .usuarios-ficha__acciones .boton:hover { box-shadow: 0 5px 12px rgba(25, 61, 80, .16); transform: translateY(-1px); }
        @media (max-width: 650px) { .usuarios-ficha__form { padding: 14px; } .usuarios-ficha .grid-flex { grid-template-columns: 1fr !important; } .usuarios-ficha__acciones { align-items: stretch; flex-direction: column; } }
    </style>
</head>
<body>
    <div id="toolsbar" style="display:block;">{topbar}</div>
    <main class="principal">{barraLateral}<section class="tablero usuarios-ficha">
        <div class="filaGeneral filageneral--menu">{headerMenu}</div>
        <div class="tablero__header usuarios-ficha__cabecera"><div class="header__fila1"><h3>{H2Titulo}</h3></div></div>
        <form class="formularioGrande usuarios-ficha__form" method="post" action="usuarios_ficha.php">
            <input name="usuarioOriginal" type="hidden" value="{usuarioOriginal}">
            <div class="filaGeneral filaGeneral__seccion usuarios-ficha__seccion">Datos de acceso</div><fieldset>
                <div class="grid-flex" style="--columns:2fr 4fr 2fr;">
                    <div class="formulario__campo"><label class="campo__label" for="username">Usuario</label><input class="campo__input" id="username" name="username" value="{username}" {usernameReadonly} required></div>
                    <div class="formulario__campo"><label class="campo__label" for="nombre">Nombre</label><input class="campo__input" id="nombre" name="nombre" value="{nombre}" required></div>
                    <div class="formulario__campo"><label class="campo__label" for="tipoUser">Tipo</label><select class="campo__input" id="tipoUser" name="tipoUser"><option value="D" {tipoD}>D — Dios</option><option value="T" {tipoT}>T — Técnico</option><option value="A" {tipoA}>A — Administrativo</option></select></div>
                </div>
                <div class="grid-flex" style="--columns:3fr 2fr 2fr;">
                    <div class="formulario__campo"><label class="campo__label" for="email">Correo</label><input class="campo__input" id="email" name="email" type="email" value="{email}"></div>
                    <div class="formulario__campo"><label class="campo__label" for="foto">Foto</label><input class="campo__input" id="foto" name="foto" value="{foto}"></div>
                    <div class="formulario__campo"><label class="campo__label" for="estado">Estado</label><select class="campo__input" id="estado" name="estado"><option value="A" {estadoA}>Activo</option><option value="I" {estadoI}>Inactivo</option></select></div>
                </div>
                <div class="formulario__campo usuarios-ficha__clave"><label class="campo__label" for="clave">Contraseña nueva</label><input class="campo__input" id="clave" name="clave" type="password" autocomplete="new-password"><small>En edición, déjela vacía para mantener la contraseña actual.</small></div>
            </fieldset>
            <div class="usuarios-ficha__mensaje usuarios-ficha__mensaje--error">{mensajeError}</div>
            <div class="usuarios-ficha__mensaje usuarios-ficha__mensaje--exito">{mensajeExito}</div>
            <div class="filaGeneral usuarios-ficha__acciones"><button class="boton boton--guardar" name="guardarUsuario" type="submit">Guardar</button><button class="boton boton--salir" type="button" onclick="location.href='usuarios.php'">Salir</button></div>
        </form>
    </section></main>
</body>
</html>
