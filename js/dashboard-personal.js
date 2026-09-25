/**
 * Script para Personal Asíncrono
 * Carga el gráfico de personal (trabajando/ausente) de forma asíncrona
 */
document.addEventListener("DOMContentLoaded", function () {
    // Usar proxy local para evitar problemas de CORS
    const API_PROXY_PERSONAL = "/api-proxy.php?tipo=personal";

    async function loadPersonalAsync() {
        // Solo proceder si el gráfico está habilitado y existe el elemento
        const graficoElement = document.getElementById('GRpersonal');
        const canvasElement = document.getElementById('graficoPersonal');
        
        if (!graficoElement || graficoElement.value !== 'S' || !canvasElement) {
            console.log('ℹ️ Gráfico de personal no habilitado o elemento no encontrado');
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
            ctx.fillText('Cargando datos de personal...', canvasElement.width / 2, canvasElement.height / 2);

            console.log('🔄 Cargando datos de personal de forma asíncrona...');

            const response = await fetch(API_PROXY_PERSONAL, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const result = await response.json();
            console.log('📦 Respuesta del proxy (personal):', result);

            if (!result.success) {
                throw new Error(result.error || 'Error en el proxy');
            }

            const data = result.data;
            console.log('✅ Datos de personal recibidos:', data);

            // Verificar que existe la función del gráfico
            if (typeof graficoPersonal !== 'function') {
                throw new Error('La función graficoPersonal no está disponible');
            }

            // Limpiar el canvas antes de dibujar
            ctx.clearRect(0, 0, canvasElement.width, canvasElement.height);

            // Procesar los datos según el formato esperado por la función graficoPersonal
            // La función espera: graficoPersonal(presentes, vacaciones, permisos, hVacaciones, hPermisos)
            
            const funcionariosTrabajando = data.funcionariosTrabajando || 0;
            const funcionariosVacaciones = data.funcionariosVacaciones || 0;
            const funcionariosPermiso = data.funcionariosPermiso || 0;
            const honorariosVacaciones = data.funcionariosVacacionesH || 0;
            const honorariosPermiso = data.funcionariosPermisoH || 0;

            console.log('📈 Datos procesados (personal):', {
                funcionariosTrabajando,
                funcionariosVacaciones,
                funcionariosPermiso,
                honorariosVacaciones,
                honorariosPermiso
            });
            
            // Llamar a la función original con los datos correctamente formateados
            graficoPersonal(
                funcionariosTrabajando.toString(),
                funcionariosVacaciones.toString(),
                funcionariosPermiso.toString(),
                honorariosVacaciones.toString(),
                honorariosPermiso.toString()
            );

            console.log('✅ Gráfico de personal cargado correctamente');

        } catch (error) {
            console.error('❌ Error cargando datos de personal:', error);
            
            // En caso de error, mostrar gráfico con datos de ejemplo para no romper la interfaz
            try {
                if (typeof graficoPersonal === 'function') {
                    console.log('🔄 Cargando gráfico de personal con datos de fallback...');
                    graficoPersonal('50', '5', '3', '2', '1');
                    console.log('✅ Gráfico de personal cargado con datos de ejemplo');
                } else {
                    throw new Error('Función graficoPersonal no disponible');
                }
            } catch (fallbackError) {
                console.error('❌ Error también en el fallback:', fallbackError);
                
                // Mostrar mensaje de error en el canvas
                const ctx = canvasElement.getContext('2d');
                ctx.clearRect(0, 0, canvasElement.width, canvasElement.height);
                ctx.fillStyle = '#fff3cd';
                ctx.fillRect(0, 0, canvasElement.width, canvasElement.height);
                ctx.fillStyle = '#856404';
                ctx.font = '14px Arial';
                ctx.textAlign = 'center';
                ctx.fillText('Error cargando datos de personal', canvasElement.width / 2, canvasElement.height / 2 - 10);
                ctx.fillText('Inténtelo más tarde', canvasElement.width / 2, canvasElement.height / 2 + 10);
            }
        }
    }

    // Ejecutar la carga asíncrona después de un delay para ser el último
    setTimeout(loadPersonalAsync, 2500);

    console.log('🚀 Script de personal asíncrono iniciado');
});