document.addEventListener('DOMContentLoaded', function () {
    if (typeof Dropzone === 'undefined') return;

    Dropzone.autoDiscover = false;

    function inicializarDropzone(selector, tipo, inputId, imagenId) {
        const elemento = document.querySelector(selector);
        if (!elemento) return;

        const dropzone = new Dropzone(elemento, {
            url: 'sistemas_archivo.php',
            paramName: 'file',
            maxFiles: 1,
            maxFilesize: 2,
            acceptedFiles: 'image/png,image/jpeg,image/gif,image/webp',
            addRemoveLinks: true,
            dictDefaultMessage: 'Arrastre una imagen o haga clic aquí',
            dictRemoveFile: 'Quitar',
            params: {
                id: document.getElementById('id').value,
                tipo: tipo
            }
        });

        dropzone.on('success', function (file, respuesta) {
            if (!respuesta || !respuesta.success) {
                dropzone.emit('error', file, respuesta ? respuesta.message : 'Error al cargar.');
                return;
            }

            document.getElementById(inputId).value = respuesta.filename;
            const imagen = document.getElementById(imagenId);
            imagen.src = respuesta.url + '?v=' + Date.now();
            imagen.style.display = 'block';
        });

        dropzone.on('error', function (file, mensaje) {
            if (typeof mensaje === 'object' && mensaje.message) mensaje = mensaje.message;
            if (typeof mensaje === 'string') file.previewElement.querySelector('[data-dz-errormessage]').textContent = mensaje;
        });
    }

    inicializarDropzone('#dropzoneIcono', 'icono', 'icono', 'vistaIcono');
    inicializarDropzone('#dropzoneBoton', 'btn', 'btn', 'vistaBoton');
});
