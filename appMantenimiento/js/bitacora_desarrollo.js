(function () {
    var configuracion = window.bitacoraDesarrolloConfig || {};
    var adjuntos = configuracion.adjuntos || [];
    var idBitacora = Number(configuracion.idBitacora || 0);
    var galeria = document.getElementById('galeriaAdjuntos');
    var contador = document.getElementById('contadorAdjuntos');
    var errorAdjunto = document.getElementById('errorAdjunto');
    var contenidoVisor = document.getElementById('contenidoVisor');

    function textoCantidad(cantidad) {
        return cantidad + (cantidad === 1 ? ' adjunto' : ' adjuntos');
    }

    function abrirModal(idModal) {
        var modal = document.getElementById(idModal);
        if (!modal) {
            return;
        }

        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
    }

    function cerrarModal(idModal) {
        var modal = document.getElementById(idModal);
        if (modal) {
            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
        }

        if (idModal === 'modalVisor') {
            contenidoVisor.innerHTML = '';
        }
    }

    function abrirVisor(url, mimeTipo, nombre) {
        contenidoVisor.innerHTML = '';
        document.getElementById('tituloVisor').textContent = nombre || 'Adjunto';

        if (mimeTipo === 'application/pdf' || mimeTipo.indexOf('image/') !== 0) {
            var visorPdf = document.createElement('iframe');
            visorPdf.src = url;
            visorPdf.title = nombre || 'Documento PDF';
            contenidoVisor.appendChild(visorPdf);
        } else {
            var visorImagen = document.createElement('img');
            visorImagen.src = url;
            visorImagen.alt = nombre || 'Imagen adjunta';
            contenidoVisor.appendChild(visorImagen);
        }

        abrirModal('modalVisor');
    }

    function enviarFormulario(url, datosFormulario) {
        return fetch(url, {
            method: 'POST',
            body: datosFormulario,
            credentials: 'same-origin'
        }).then(function (respuesta) {
            return respuesta.text();
        }).then(interpretarRespuesta);
    }

    function eliminarAdjuntoEntrada(idImagen) {
        if (!window.confirm('¿Eliminar este adjunto de la entrada?')) {
            return;
        }

        var datosFormulario = new FormData();
        datosFormulario.append('accion', 'eliminar');
        datosFormulario.append('idImagen', idImagen);
        enviarFormulario('bitacora_desarrollo_archivo.php', datosFormulario).then(function (respuesta) {
            if (!respuesta.ok) {
                errorAdjunto.textContent = respuesta.mensaje || 'No fue posible eliminar el adjunto.';
                return;
            }

            adjuntos = adjuntos.filter(function (adjunto) {
                return Number(adjunto.idImagen) !== Number(idImagen);
            });
            mostrarAdjuntos();
        }).catch(function () {
            errorAdjunto.textContent = 'No fue posible eliminar el adjunto.';
        });
    }

    function crearTarjetaAdjunto(adjunto) {
        var tarjeta = document.createElement('div');
        var vista = document.createElement('button');
        var eliminar = document.createElement('button');
        var nombre = document.createElement('span');
        var tipo = document.createElement('span');

        tarjeta.className = 'bitacora-adjunto';
        vista.type = 'button';
        vista.className = 'bitacora-adjunto__vista';
        vista.addEventListener('click', function () {
            abrirVisor(adjunto.url, adjunto.mimeTipo, adjunto.nombre);
        });

        if (adjunto.mimeTipo.indexOf('image/') === 0) {
            var miniatura = document.createElement('img');
            miniatura.src = adjunto.url;
            miniatura.alt = '';
            vista.appendChild(miniatura);
        } else if (adjunto.mimeTipo === 'application/pdf') {
            var indicadorPdf = document.createElement('span');
            indicadorPdf.className = 'bitacora-adjunto__pdf';
            indicadorPdf.textContent = 'PDF';
            vista.appendChild(indicadorPdf);
        } else {
            var indicadorTexto = document.createElement('span');
            indicadorTexto.className = 'bitacora-adjunto__pdf bitacora-adjunto__texto';
            indicadorTexto.textContent = adjunto.nombre.toLowerCase().endsWith('.sql') ? 'SQL' : 'TXT';
            vista.appendChild(indicadorTexto);
        }

        nombre.className = 'bitacora-adjunto__nombre';
        nombre.textContent = adjunto.nombre;
        tipo.className = 'bitacora-adjunto__tipo';
        tipo.textContent = adjunto.mimeTipo.indexOf('image/') === 0
            ? 'Imagen adjunta'
            : (adjunto.mimeTipo === 'application/pdf' ? 'Documento PDF' : 'Documento de texto');
        vista.appendChild(nombre);
        vista.appendChild(tipo);
        eliminar.type = 'button';
        eliminar.className = 'bitacora-adjunto__eliminar';
        eliminar.textContent = 'Eliminar';
        eliminar.addEventListener('click', function () {
            eliminarAdjuntoEntrada(adjunto.idImagen);
        });
        tarjeta.appendChild(vista);
        tarjeta.appendChild(eliminar);

        return tarjeta;
    }

    function mostrarAdjuntos() {
        if (!galeria || !contador) {
            return;
        }

        galeria.innerHTML = '';
        contador.textContent = textoCantidad(adjuntos.length);
        adjuntos.forEach(function (adjunto) {
            galeria.appendChild(crearTarjetaAdjunto(adjunto));
        });
    }

    function interpretarRespuesta(respuesta) {
        if (typeof respuesta !== 'string') {
            return respuesta || { ok: false, mensaje: 'No fue posible completar la carga.' };
        }

        try {
            return JSON.parse(respuesta);
        } catch (error) {
            return { ok: false, mensaje: respuesta };
        }
    }

    function configurarModales() {
        document.querySelectorAll('[data-cerrar-modal]').forEach(function (boton) {
            boton.addEventListener('click', function () {
                cerrarModal(boton.getAttribute('data-cerrar-modal'));
            });
        });

        document.querySelectorAll('.bitacora-modal').forEach(function (modal) {
            modal.addEventListener('click', function (evento) {
                if (evento.target === modal) {
                    cerrarModal(modal.id);
                }
            });
        });

        var botonNuevaAccion = document.getElementById('abrirAccion');
        if (botonNuevaAccion) {
            botonNuevaAccion.addEventListener('click', function () {
                abrirModal('modalAccion');
            });
        }

        document.querySelectorAll('.bitacora-adjunto-accion').forEach(function (boton) {
            boton.addEventListener('click', function () {
                abrirVisor(
                    boton.getAttribute('data-adjunto-url'),
                    boton.getAttribute('data-adjunto-mime'),
                    boton.textContent.replace('Ver adjunto: ', '')
                );
            });
        });

        document.querySelectorAll('[data-eliminar-adjunto-accion]').forEach(function (boton) {
            boton.addEventListener('click', function () {
                if (!window.confirm('¿Eliminar este adjunto de la acción?')) {
                    return;
                }

                var datosFormulario = new FormData();
                datosFormulario.append('accion', 'eliminar');
                datosFormulario.append('idArchivo', boton.getAttribute('data-eliminar-adjunto-accion'));
                enviarFormulario('bitacora_desarrollo_accion_archivo.php', datosFormulario).then(function (respuesta) {
                    if (respuesta.ok) {
                        window.location.reload();
                    } else {
                        window.alert(respuesta.mensaje || 'No fue posible eliminar el adjunto.');
                    }
                }).catch(function () {
                    window.alert('No fue posible eliminar el adjunto.');
                });
            });
        });

        document.querySelectorAll('[data-eliminar-accion]').forEach(function (boton) {
            boton.addEventListener('click', function () {
                if (!window.confirm('¿Eliminar esta acción y todos sus adjuntos?')) {
                    return;
                }

                var datosFormulario = new FormData();
                datosFormulario.append('accion', 'eliminar');
                datosFormulario.append('idAccion', boton.getAttribute('data-eliminar-accion'));
                enviarFormulario('bitacora_desarrollo_accion.php', datosFormulario).then(function (respuesta) {
                    if (respuesta.ok) {
                        window.location.reload();
                    } else {
                        window.alert(respuesta.mensaje || 'No fue posible eliminar la acción.');
                    }
                }).catch(function () {
                    window.alert('No fue posible eliminar la acción.');
                });
            });
        });
    }

    function configurarLimpiezaFormulario() {
        document.querySelectorAll('[data-limpiar-formulario]').forEach(function (boton) {
            boton.addEventListener('click', function () {
                if (!window.confirm('¿Limpiar los datos no guardados del formulario?')) {
                    return;
                }

                window.location.assign('bitacora_desarrollo.php');
            });
        });
    }

    function configurarCargaEntrada() {
        if (!idBitacora || !window.Dropzone || !document.getElementById('dropzoneEntrada')) {
            return;
        }

        var dropzone = new Dropzone('#dropzoneEntrada', {
            url: 'bitacora_desarrollo_archivo.php',
            paramName: 'file',
            acceptedFiles: 'application/pdf,image/jpeg,image/png,image/webp,text/plain,application/sql,text/x-sql,application/x-sql,.txt,.sql',
            maxFilesize: 8,
            maxFiles: 10,
            dictDefaultMessage: 'Arrastre un documento, imagen, TXT o SQL aquí, o haga clic para seleccionarlo.',
            dictInvalidFileType: 'Solo se permiten PDF, imágenes, TXT o SQL.',
            dictFileTooBig: 'El archivo supera el tamaño máximo de 8 MB.'
        });

        dropzone.on('sending', function (archivo, xhr, datosFormulario) {
            datosFormulario.append('idBitacora', idBitacora);
            errorAdjunto.textContent = '';
        });

        dropzone.on('success', function (archivo, respuesta) {
            var datos = interpretarRespuesta(respuesta);
            if (datos.ok) {
                window.location.reload();
                return;
            }

            errorAdjunto.textContent = datos.mensaje || 'No fue posible guardar el adjunto.';
            dropzone.removeFile(archivo);
        });

        dropzone.on('error', function (archivo, respuesta) {
            var datos = interpretarRespuesta(respuesta);
            errorAdjunto.textContent = datos.mensaje || 'No fue posible cargar el adjunto.';
        });
    }

    mostrarAdjuntos();
    configurarModales();
    configurarLimpiezaFormulario();
    configurarCargaEntrada();
}());
