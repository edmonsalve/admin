/**
 * Script para Permisos de Circulación Asíncronos
 * Carga los gráficos histórico y activo de permisos de circulación de forma asíncrona
 */
document.addEventListener("DOMContentLoaded", function () {
    // Usar proxy local para evitar problemas de CORS
    const API_SOC = "/api-proxy.php?tipo=soc";

    async function loadSocAsync() {
        // Solo proceder si el gráfico está habilitado y existe el elemento
        const graficoElement = document.getElementById('GRpSOC');
        const canvasElement  = document.getElementById('socDoughnut');
        
        if (!graficoElement || graficoElement.value !== 'S' || !canvasElement) {
            console.log('ℹ️ Gráfico de solicitudes pendientes no habilitado o elemento no encontrado');
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
            ctx.fillText('Cargando datos activos...', canvasElement.width / 2, canvasElement.height / 2);

            console.log('🔄 Cargando SOC pendientes de forma asíncrona...');

            // Para el gráfico activo, usamos los mismos datos del histórico pero solo del año actual
            const response = await fetch(API_SOC, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const result = await response.json();

            if (!result.success) {
                throw new Error(result.error || 'Error en el proxy');
            }

            const data = result.data;
            console.log('✅ Datos de SOC pendientes recibidos:', data);

            // Verificar que existe la función del gráfico
            if (typeof graficoSOC !== 'function') {
                throw new Error('La función graficoSOC no está disponible');
            }

            // Limpiar el canvas antes de dibujar
            ctx.clearRect(0, 0, canvasElement.width, canvasElement.height);

            // Usar el año actual y los montos correspondientes
            const aprobN0 = parseInt(data.socPendientes['aprobN0'] || 0);
            const aprobN1 = parseInt(data.socPendientes['aprobN1'] || 0);
            const aprobN2 = parseInt(data.socPendientes['aprobN2'] || 0);
            const aprobN3 = parseInt(data.socPendientes['aprobN3'] || 0);
            const aprobN4 = parseInt(data.socPendientes['aprobN4'] || 0);
            const aprobN5 = parseInt(data.socPendientes['aprobN5'] || 0);
            const aprobN6 = parseInt(data.socPendientes['aprobN6'] || 0);
            const aprobN7 = parseInt(data.socPendientes['aprobN7'] || 0);
            const aprobN8 = parseInt(data.socPendientes['aprobN8'] || 0);
            
            console.log('📈 Datos procesados (activo):', {
                aprobN0,
                aprobN1,
                aprobN2,
                aprobN3,
                aprobN4,
                aprobN5,
                aprobN6,
                aprobN7,
                aprobN8
            });
            
            // Llamar a la función original con los datos correctamente formateados
            graficoSOC(aprobN1.toString(), aprobN2.toString(), aprobN3.toString(), aprobN4.toString(), aprobN5.toString(), aprobN6.toString(), aprobN7.toString(), aprobN8.toString());

            console.log('✅ Gráfico de SOC cargado correctamente');

        } catch (error) {
            console.error('❌ Error cargando SOC:', error);
            
            // Mostrar mensaje de error en el canvas
            const ctx = canvasElement.getContext('2d');
            ctx.clearRect(0, 0, canvasElement.width, canvasElement.height);
            ctx.fillStyle = '#fff3cd';
            ctx.fillRect(0, 0, canvasElement.width, canvasElement.height);
            ctx.fillStyle = '#856404';
            ctx.font = '14px Arial';
            ctx.textAlign = 'center';
            ctx.fillText('Error cargando datos de SOC', canvasElement.width / 2, canvasElement.height / 2 - 10);
            ctx.fillText('Inténtelo más tarde', canvasElement.width / 2, canvasElement.height / 2 + 10);
        }
    }

    // Ejecutar la carga asíncrona después de un delay para permitir que se carguen los demás gráficos
    setTimeout(() => {
        loadSocAsync();
    }, 1500);

    console.log('🚀 Script de permisos de circulación asíncrono iniciado');
});