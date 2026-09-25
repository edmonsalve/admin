//import Swal from 'sweetalert2'

/**
 * @file utilidades.js
 * @author Felipe Monsalve
 * 
 * Este archivo contiene las siguientes funciones:
 * 
 * 1. generaTabla: Genera una tabla HTML a partir de un array de objetos, con paginador y opciones de editar y borrar.
 * 2. muestraAlerta: Muestra una alerta utilizando SweetAlert2.
 * 3. construirTarjetas: Construye tarjetas HTML a partir de arrays de enlaces, títulos y contenidos.
 * 4. toggleSwitch: Crea un interruptor de palanca (toggle switch) con estilos personalizados.
 */

/**
 * Función que genera una tabla de HTML a partir de un array de objetos. Devuevle un elemento HTML 'DIV' identificado
 * con la clase 'contenedor-tabla' que contiene la tabla y un paginador. La clase 'contenedor-tabla' no llevas estilos
 * CSS asociados.
 * El contenedor con los números de las páginas viene identificados con la clase 'paginador' y los botones con la clase
 * 'paginador__boton'. El botón de la página actual lleva la clase 'actual'.
 * @param {Array} datos - [Obligatorio] Array de objetos con los datos a mostrar en la tabla.
 * @param {Array} cabeceras - [Opcional] Array con los nombres de las cabeceras de la tabla.
 * Si no se proporciona, se usarán las llaves de los objetos. Si se proporcionan menos cabeceras
 * que llaves, se completará con las llaves restantes. Si se proporcionan más cabeceras que llaves,
 * se ignorarán las cabeceras sobrantes.
 * @param {Array} anchos - [Opcional] Array con los anchos de las columnas de la tabla.
 * Si se proporciona, la misma cantidad de datos que el array de objetos, sino se ignorará.
 * @param {Array} excluir - [Opcional] Array con las llaves de los objetos que se desean excluir de la tabla.
 * @param {Array} clases - [Opcional] Array de dos posiciones con las clases que se aplicarán a las filas de la tabla.
 * La primera clase se aplicará a las filas impares y la segunda a las pares. Por defecto ['impar', 'par'].
 * @param {Number} registros - [Opcional] Número de registros a mostrar por página. Por defecto 10.
 * @param {String} urlEditar - [Opcional] URL a la que se redirigirá al hacer clic en el enlace de editar.
 * @param {String} urlBorrar - [Opcional] URL a la que se redirigirá al hacer clic en el enlace de borrar.
 * @param {Function} funcionRecarga - [Opcional] Función que se ejecutará cuando necesite recagar la tabla.
 * @param {Boolean} acciones - [Opcional] Indica si se deben mostrar las acciones de editar y borrar. Por defecto true.
 * 
 * @returns {HTMLElement} Tabla HTML con los datos proporcionados.
 */
export function generaTabla(datos, cabeceras = [], anchos = [], excluir = [], clases = ['impar', 'par'], registros = 10, urlEditar = '', urlBorrar = '', funcionRecarga = '', acciones = true) {
    // Verificamos que se pase una array y que no esté vacío
    if (!Array.isArray(datos)) {
        return ''
    }
    if (datos.length === 0) {
        datos = [{}]
    }

    const tabla = document.createElement('TABLE')
    const thead = document.createElement('THEAD')
    const tbody = document.createElement('TBODY')
    const paginador = document.createElement('DIV')
    paginador.classList.add('paginador')

    // Filtramos las llaves que se desean excluir
    let keys = Object.keys(datos[0]).filter(key => !excluir.includes(key))

    // Si no se proporcionan suficientes cabeceras, completamos con las llaves de los objetos
    if (cabeceras.length < keys.length) {
        cabeceras = [...cabeceras, ...keys.slice(cabeceras.length)]
    }

    if (acciones) {
        cabeceras = [...cabeceras, 'Acciones']
        if(anchos.length > 0) {
            anchos = [...anchos, 150]
        }
    }

    // Creamos el encabezado de la tabla
    const headerRow = document.createElement('TR')
    cabeceras.forEach((header, index) => {
        const th = document.createElement('TH')
        th.textContent = header
        // Aplicamos el ancho de la columna si se proporciona
        if (anchos.length === cabeceras.length) {
            th.style.width = `${anchos[index]}px`
        }
        headerRow.appendChild(th)
    })
    thead.appendChild(headerRow)

    // Función para renderizar una página de la tabla
    function renderPage(page) {
        tbody.innerHTML = ''
        const start = (page - 1) * registros
        const end = start + registros
        const pageData = datos.slice(start, end)

        pageData.forEach((item, index) => {
            const row = document.createElement('TR')
            // Añadimos la clase impar o par según corresponda
            row.classList.add(index % 2 === 0 ? clases[0] : clases[1])
            keys.forEach(header => {
                const td = document.createElement('TD')
                td.textContent = item[header] !== undefined ? item[header] : ''
                row.appendChild(td)
            })

            if (acciones) {
                //Creamos la columna de acciones
                const acciones = document.createElement('TD')
                const editar = document.createElement('A')
                const borrar = document.createElement('A')
    
                //Pasamos las URL
                editar.href = `${urlEditar}?id=${item[Object.keys(item)[0]]}`
                //borrar.href = `${urlBorrar}?id=${item[Object.keys(item)[0]]}`
                //Se pasa la URL mas abajo en el evento click
    
                //Insertamos los tooltips
                editar.title = 'Editar registro'
                borrar.title = 'Borrar registro'
    
                //Insertamos los íconos de fontawesome
                editar.innerHTML = '<i class="fa-solid fa-pen-to-square"></i>'
                borrar.innerHTML = '<i class="fa-solid fa-trash"></i>'
    
    
                borrar.addEventListener('click', async (e) => {
                    e.preventDefault()
                    
                    Swal.fire({
                        title: `¿Estás seguro de querer borrar el registro?`,
                        text: "¡Esta acción no se puede revertir!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#115a79",
                        cancelButtonColor: "#a90000",
                        confirmButtonText: "Si, ¡eliminar!",
                        cancelButtonText: "Cancelar"
                    }).then(async (result) => {
                        if (result.isConfirmed) {
                            // Borramos el registro
                            const datos = new FormData();
                            datos.append('id', item[Object.keys(item)[0]]); // Pasamos el ID del registro a borrar
    
                            const url = urlBorrar;
                            const respuesta = await fetch(url, {
                                method: 'POST',
                                body: datos
                            });
                            const resultado = await respuesta.json();
    
                            if (resultado.resultado) {
                                if (typeof funcionRecarga === 'function') {
                                    funcionRecarga();
                                }
                                Swal.fire({
                                    title: "¡Registro eliminado!",
                                    text: resultado.mensaje,
                                    icon: "success",
                                    confirmButtonColor: "#115a79"
                                });
                            } else {
                                Swal.fire({
                                    title: "¡Error!",
                                    text: resultado.mensaje,
                                    icon: "error"
                                });
                            }
                        }
                    });
    
                })
    
                //Pasamos los íconos a la columna
                acciones.appendChild(editar)
                acciones.appendChild(document.createTextNode(' '))
                acciones.appendChild(borrar)
    
                //Pasamos la columna a la fila
                row.appendChild(acciones)
            }

            tbody.appendChild(row)
        })

        // Actualizamos la clase 'actual' en los botones del paginador
        const botones = paginador.querySelectorAll('button')
        botones.forEach(boton => boton.classList.remove('actual'))
        botones[page - 1].classList.add('actual')
    }

    // Función para crear los botones del paginador
    function createPaginator(totalPages) {
        paginador.innerHTML = ''
        for (let i = 1; i <= totalPages; i++) {
            const boton = document.createElement('BUTTON')
            boton.classList.add('paginador__boton')
            boton.textContent = i
            boton.addEventListener('click', () => renderPage(i))
            paginador.appendChild(boton)
        }
    }

    //Calculamos el total de páginas y creamos el paginador
    const totalPages = Math.ceil(datos.length / registros)
    createPaginator(totalPages)
    renderPage(1)

    //Añadimos los elementos a la tabla
    tabla.appendChild(thead)
    tabla.appendChild(tbody)

    //Creamos el contenedor de la tabla y el paginador
    const contenedor = document.createElement('DIV')
    contenedor.classList.add('contenedor-tabla')
    contenedor.appendChild(tabla)
    contenedor.appendChild(paginador)

    //Devolvemos el contenedor
    return contenedor
}

/**
 * Función que muestra una alerta de SweetAlert2. Recibe dos parámetros:
 * @param {String} icon Ícono de SweetAlert2 a mostrar en la alerta ('success', 'error', 'warning', 'info', 'question').
 * @param {String} mensaje Mensaje a mostrar en la alerta.
 */
/*
export function muestraAlerta(icon, mensaje, html = '') {
    Swal.fire({
        title: mensaje,
        icon: icon,
        confirmButtonColor: "#115a79",
        html: html,
        target: document.getElementById("contenedor-swa"),
        customClass: {
            popup: 'sa-lista'
        }
    })
}
*/

/**
 * Función que construye las tarjetas de un contenedor. Está diseñada para trabajar con la hoja de SASS 'tarjetas.scss'.
 * Recibe los siguientes parámetros:
 * @param {string} tarjeta - [Obligatorio] Elemento HTML que contendrá las tarjetas generadas.
 * @param {Array} enlaces - [Obligatorio] Array de enlaces para cada una de las tarjetas.
 * @param {Array} titulos - [Obligatorio] Array de títulos para cada una de las tarjetas.
 * @param {Array} contenidosP1 - [Obligatorio] Array con el contenido del primer párrafo para cada una de las tarjetas.
 * @param {*} contenidosP2 - [Obligatorio] Array con el contenido del segundo párrafo para cada una de las tarjetas.
 */
export function construirTarjetas(tarjeta, enlaces, titulos, contenidosP1, contenidosP2) {
    enlaces.forEach((enlace, index) => {
        // Construimos la tarjeta
        const tarjetaElemento = document.createElement('a')
        tarjetaElemento.classList.add('tarjetas__tarjeta')
        tarjetaElemento.href = enlace

        // Construimos los elementos de la tarjeta
        const cabecera = document.createElement('DIV')
        cabecera.classList.add('tarjetas__tarjeta-cabecera')
        const titulo = document.createElement('H4')
        const icono = document.createElement('I')
        const p1 = document.createElement('P')
        const p2 = document.createElement('P')

        // Agregamos contenido
        titulo.textContent = titulos[index]
        icono.innerHTML = "<i class='fa-solid fa-circle-right'></i>"
        p1.textContent = contenidosP1[index]
        p2.textContent = contenidosP2[index]

        // Inyectamos los elementos
        cabecera.appendChild(titulo)
        cabecera.appendChild(icono)
        tarjetaElemento.appendChild(cabecera)
        tarjetaElemento.appendChild(p1)
        tarjetaElemento.appendChild(p2)

        // Inyectamos la tarjeta en el contenedor
        tarjeta.appendChild(tarjetaElemento)
    })
}

/**
 * Función que crea un interruptor de palanca (toggle switch). Funciona con los estilos de
 * la hoja de SASS 'toggleswitch.scss'.
 * @param {String} id - [Obligatorio] ID que se asignará al contenedor del interruptor.
 * @param {String} clase - [Opcional] Clase CSS que se aplicará al contenedor del interruptor.
 * @param {String} texto - [Opcional] Texto que se mostrará junto al interruptor.
 * @returns {HTMLElement} Elemento HTML del interruptor de palanca.
 */
export function toggleSwitch(id, clase = '', texto = '') {
    const toggleSwitch = document.createElement('DIV');
    toggleSwitch.classList.add(clase, 'toggle-container');
    toggleSwitch.innerHTML = `
        <p class="tag">${texto}</p>
        <input type="checkbox" id="${id}" class="toggle-input" />
        <label for="${id}" class="toggle-label">Toggle</label>
    `;
    
    return toggleSwitch;
}