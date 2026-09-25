/**
 * Prueba de Optimización - Solo Ayudas Sociales Asíncrona
 * Este script carga solo el gráfico de ayudas sociales de forma asíncrona
 * para probar la funcionalidad sin afectar los demás gráficos
 */
document.addEventListener("DOMContentLoaded", function () {
    // Usar proxy local para evitar problemas de CORS
    const API_PROXY = "/api-proxy.php?tipo=ayudasSociales";

    async function loadAyudasSocialesAsync() {
        // Solo proceder si el gráfico está habilitado y existe el elemento
        const graficoElement = document.getElementById('GRayudSocial');
        const canvasElement = document.getElementById('graficoAyudSocEnt');
        
        if (!graficoElement || graficoElement.value !== 'S' || !canvasElement) {
            console.log('ℹ️ Gráfico de ayudas sociales no habilitado o elemento no encontrado');
            return;
        }

        try {
            // Mostrar indicador de carga
            const ctx = canvasElement.getContext('2d');
            ctx.fillStyle = '#f8f9fa';
            ctx.fillRect(0, 0, canvasElement.width, canvasElement.height);
            ctx.fillStyle = '#6c757d';
            ctx.font = '16px Arial';
            ctx.textAlign = 'center';
            ctx.fillText('Cargando ayudas sociales...', canvasElement.width / 2, canvasElement.height / 2);

            console.log('🔄 Cargando ayudas sociales de forma asíncrona...');

            const response = await fetch(API_PROXY, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const result = await response.json();
            console.log('📦 Respuesta del proxy:', result);

            if (!result.success) {
                throw new Error(result.error || 'Error en el proxy');
            }

            const data = result.data;
            console.log('✅ Datos de ayudas sociales recibidos:', data);

            // Verificar que existe la función del gráfico
            if (typeof graficoAyudSocEnt !== 'function') {
                throw new Error('La función graficoAyudSocEnt no está disponible');
            }

            // Limpiar el canvas antes de dibujar
            ctx.clearRect(0, 0, canvasElement.width, canvasElement.height);

            // Procesar los datos según el formato esperado
            const ayudas = data.ayudas[0]; // Los datos están en el primer elemento del array
            console.log('📊 Estructura de ayudas:', ayudas);
            
            // Convertir los datos del formato API al formato que espera la función
            const labels = ayudas.labels || [];
            const tipoAyudas = ayudas.tipoAyudas ? Object.values(ayudas.tipoAyudas) : [];
            
            // Convertir cada serie de objeto a array en el orden de las labels
            function convertSerieToArray(serieObj) {
                if (!serieObj || typeof serieObj !== 'object') return [];
                return labels.map(label => parseInt(serieObj[label] || 0));
            }
            
            const ayuda01 = convertSerieToArray(ayudas.ayuda01);
            const ayuda02 = convertSerieToArray(ayudas.ayuda02);
            const ayuda03 = convertSerieToArray(ayudas.ayuda03);
            const ayuda04 = convertSerieToArray(ayudas.ayuda04);
            const ayuda05 = convertSerieToArray(ayudas.ayuda05);
            const ayuda06 = convertSerieToArray(ayudas.ayuda06);
            const ayuda07 = convertSerieToArray(ayudas.ayuda07);
            const ayuda08 = convertSerieToArray(ayudas.ayuda08);
            const ayuda09 = convertSerieToArray(ayudas.ayuda09);
            const ayuda10 = convertSerieToArray(ayudas.ayuda10);
            const montoTotal = convertSerieToArray(ayudas.montoAyudas);
            
            console.log('📈 Datos procesados:', {
                labels,
                tipoAyudas,
                ayuda01,
                ayuda02,
                montoTotal
            });
            
            // Llamar a la función original con los datos correctamente formateados
            graficoAyudSocEnt(
                labels,
                tipoAyudas,
                ayuda01,
                ayuda02,
                ayuda03,
                ayuda04,
                ayuda05,
                ayuda06,
                ayuda07,
                ayuda08,
                ayuda09,
                ayuda10,
                montoTotal
            );

            console.log('✅ Gráfico de ayudas sociales cargado correctamente');

        } catch (error) {
            console.error('❌ Error cargando ayudas sociales:', error);
            
            // Mostrar mensaje de error en el canvas
            const ctx = canvasElement.getContext('2d');
            ctx.clearRect(0, 0, canvasElement.width, canvasElement.height);
            ctx.fillStyle = '#fff3cd';
            ctx.fillRect(0, 0, canvasElement.width, canvasElement.height);
            ctx.fillStyle = '#856404';
            ctx.font = '14px Arial';
            ctx.textAlign = 'center';
            ctx.fillText('Error cargando datos de ayudas sociales', canvasElement.width / 2, canvasElement.height / 2 - 10);
            ctx.fillText('Inténtelo más tarde', canvasElement.width / 2, canvasElement.height / 2 + 10);
        }
    }

    // Ejecutar la carga asíncrona después de un pequeño delay
    // para permitir que se cargue completamente el resto de la página
    setTimeout(loadAyudasSocialesAsync, 1000);

    console.log('🚀 Script de prueba de optimización iniciado');
});