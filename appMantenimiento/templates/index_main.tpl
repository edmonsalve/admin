<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{titulo}</title>
    <link rel="icon" type="image/png" href="/images/icoDC.png">
    <link rel="stylesheet" href="/styles/modern-admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body class="admin-home">
    <header id="toolsbar">
        <div class="admin-toolbar">
            <a class="admin-toolbar__brand" href="/appMantenimiento/index_main.php" aria-label="Ir al inicio">
                <img src="/images/dAdminLog.png" alt="dCode Administración">
            </a>
            <div class="admin-toolbar__right">
                <span class="admin-toolbar__user">{usuario}</span>
                <a class="admin-toolbar__logout" href="/logout.php">Cerrar sesión</a>
            </div>
        </div>
    </header>
    <main class="admin-home__container">
        <div class="admin-home__layout">
            <aside class="admin-home__sidebar">
                <img src="/images/dMuni.png" alt="Mantenimiento">
                <h2>Mantenimiento</h2>
                <p>Administración centralizada de plataformas y conexiones.</p>
            </aside>
            <section class="admin-home__content">
                <h1>Administración y soporte</h1>
                <p>Seleccione un mantenedor para continuar.</p>
                <div class="admin-module-grid">{modulos}</div>
            </section>
        </div>
    </main>
</body>
</html>
