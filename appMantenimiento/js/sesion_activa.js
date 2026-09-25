(function () {
    'use strict';

    var verificacionEnCurso = false;
    var sesionFinalizada = false;
    var rutaSesion = '/appMantenimiento/modulos/sesion_activa.php';
    var rutaAcceso = '/index.php?err=94';

    function cerrarPantallaPorSesion() {
        if (sesionFinalizada) {
            return;
        }
        sesionFinalizada = true;

        document.querySelectorAll('[aria-modal="true"], .is-open').forEach(function (elemento) {
            elemento.classList.remove('is-open');
            elemento.setAttribute('aria-hidden', 'true');
        });

        // Solo las ventanas abiertas mediante script pueden cerrarse. En una pestaña
        // normal se vuelve al acceso, restricción impuesta por el navegador.
        if (window.opener && !window.opener.closed) {
            window.close();
        }

        window.top.location.replace(rutaAcceso);
    }

    function verificarSesion() {
        if (verificacionEnCurso || sesionFinalizada) {
            return;
        }
        verificacionEnCurso = true;

        fetch(rutaSesion, {
            cache: 'no-store',
            credentials: 'same-origin'
        }).then(function (respuesta) {
            if (!respuesta.ok) {
                throw new Error('No fue posible verificar la sesión.');
            }
            return respuesta.json();
        }).then(function (datos) {
            if (!datos || datos.activa !== true) {
                cerrarPantallaPorSesion();
            }
        }).catch(function () {
            // Un fallo transitorio de red no debe cerrar una sesión válida.
        }).finally(function () {
            verificacionEnCurso = false;
        });
    }

    verificarSesion();
    window.setInterval(verificarSesion, 30000);
}());
