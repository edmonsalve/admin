<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="dCode">
    <title>{titulo}</title>
    <link rel="icon" type="image/png" href="/images/icoDC.png">
    <link rel="stylesheet" href="/styles/modern-admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body class="login-page">
    <main class="login-shell">
        <section class="login-brand" aria-label="dCode Administración">
            <img src="/images/dCode_Bl.png" width="200" alt="dCode Administración">
            <p>Plataforma de administración y soporte para la gestión de sistemas, servidores y sincronización.</p>
        </section>
        <section class="login-card">
            <p class="login-kicker">Acceso seguro</p>
            <h1>Iniciar sesión</h1>
            <form name="flogin" action="login.php" method="post">
                <div class="login-field">
                    <i class="fa-solid fa-user" aria-hidden="true"></i>
                    <input id="user-id" name="user-id" type="text" placeholder="Usuario" value="{usr}" autocomplete="username" required autofocus>
                </div>
                <div class="login-field">
                    <i class="fa-solid fa-lock" aria-hidden="true"></i>
                    <input id="user-pw" name="user-pw" type="password" placeholder="Contraseña" autocomplete="current-password" required>
                </div>
                <button class="login-submit" type="submit">{enviar}</button>
                <p class="login-status" role="alert">{msgEstado}</p>
            </form>
        </section>
    </main>
</body>
</html>
