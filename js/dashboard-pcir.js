/**
 * Script para Permisos de Circulación Asíncronos
 * Carga los gráficos histórico y activo de permisos de circulación de forma asíncrona
 */
document.addEventListener("DOMContentLoaded", function () {
    // Usar proxy local para evitar problemas de CORS
    const API_PROXY_HISTORICO = "/api-proxy.php?tipo=perCirculacion";

    async function loadPcirHistoricoAsync() {
        // Solo proceder si el gráfico está habilitado y existe el elemento
        const graficoElement = document.getElementById('GRpCircu');
        const canvasElement = document.getElementById('graficoPcirHist');
        
        if (!graficoElement || graficoElement.value !== 'S' || !canvasElement) {
            console.log('ℹ️ Gráfico de permisos circulación histórico no habilitado o elemento no encontrado');
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
            ctx.fillText('Cargando permisos históricos...', canvasElement.width / 2, canvasElement.height / 2);

            console.log('🔄 Cargando permisos de circulación históricos de forma asíncrona...');

            const response = await fetch(API_PROXY_HISTORICO, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const result = await response.json();
            console.log('📦 Respuesta del proxy (histórico):', result);

            if (!result.success) {
                throw new Error(result.error || 'Error en el proxy');
            }

            const data = result.data;
            console.log('✅ Datos de permisos históricos recibidos:', data);

            // Verificar que existe la función del gráfico
            if (typeof graficoPcirHistorico !== 'function') {
                throw new Error('La función graficoPcirHistorico no está disponible');
            }

            // Limpiar el canvas antes de dibujar
            ctx.clearRect(0, 0, canvasElement.width, canvasElement.height);

            // Procesar los datos según el formato esperado
            const aaaaSeries = data.aaaaSeries || {};
            const ventasContado = data.ventasContado || {};
            const ventas1cuota = data.ventas1cuota || {};
            const ventas2cuota = data.ventas2cuota || {};
            
            // Convertir los datos del formato API al formato que espera la función
            const labels = Object.keys(aaaaSeries).map(year => `Año ${year}`);
            const contadoData = Object.values(ventasContado).map(val => Math.round(parseInt(val) / 1000000));
            const cuota1Data = Object.values(ventas1cuota).map(val => Math.round(parseInt(val) / 1000000));
            const cuota2Data = Object.values(ventas2cuota).map(val => Math.round(parseInt(val) / 1000000));
            
            console.log('📈 Datos procesados (histórico):', {
                labels,
                contadoData,
                cuota1Data,
                cuota2Data
            });
            
            // Llamar a la función original con los datos correctamente formateados
            graficoPcirHistorico(labels, contadoData, cuota1Data, cuota2Data);

            console.log('✅ Gráfico de permisos histórico cargado correctamente');

        } catch (error) {
            console.error('❌ Error cargando permisos históricos:', error);
            
            // Mostrar mensaje de error en el canvas
            const ctx = canvasElement.getContext('2d');
            ctx.clearRect(0, 0, canvasElement.width, canvasElement.height);
            ctx.fillStyle = '#fff3cd';
            ctx.fillRect(0, 0, canvasElement.width, canvasElement.height);
            ctx.fillStyle = '#856404';
            ctx.font = '14px Arial';
            ctx.textAlign = 'center';
            ctx.fillText('Error cargando datos de permisos históricos', canvasElement.width / 2, canvasElement.height / 2 - 10);
            ctx.fillText('Inténtelo más tarde', canvasElement.width / 2, canvasElement.height / 2 + 10);
        }
    }

    
    async function loadPcirActivoAsync() {
        // Solo proceder si el gráfico está habilitado y existe el elemento
        const graficoElement = document.getElementById('GRpCircu');
        const canvasElement = document.getElementById('miDoughnut');
        
        if (!graficoElement || graficoElement.value !== 'S' || !canvasElement) {
            console.log('ℹ️ Gráfico de permisos circulación activo no habilitado o elemento no encontrado');
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

            console.log('🔄 Cargando permisos de circulación activos de forma asíncrona...');

            // Para el gráfico activo, usamos los mismos datos del histórico pero solo del año actual
            const response = await fetch(API_PROXY_HISTORICO, {
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
            console.log('✅ Datos de permisos activos recibidos:', data);

            // Verificar que existe la función del gráfico
            if (typeof graficoPcirActivo !== 'function') {
                throw new Error('La función graficoPcirActivo no está disponible');
            }

            // Limpiar el canvas antes de dibujar
            ctx.clearRect(0, 0, canvasElement.width, canvasElement.height);

            // Usar el año actual y los montos correspondientes
            const anoActual = data.aaaaAct || new Date().getFullYear();
            const montoContado = Math.round(parseInt(data.ventasContado[anoActual] || 0) / 1000000);
            const monto1Cuota = Math.round(parseInt(data.ventas1cuota[anoActual] || 0) / 1000000);
            const monto2Cuota = Math.round(parseInt(data.ventas2cuota[anoActual] || 0) / 1000000);
            
            console.log('📈 Datos procesados (activo):', {
                anoActual,
                montoContado,
                monto1Cuota,
                monto2Cuota
            });
            
            // Llamar a la función original con los datos correctamente formateados
            graficoPcirActivo(anoActual.toString(), montoContado.toString(), monto1Cuota.toString(), monto2Cuota.toString());

            console.log('✅ Gráfico de permisos activo cargado correctamente');

        } catch (error) {
            console.error('❌ Error cargando permisos activos:', error);
            
            // Mostrar mensaje de error en el canvas
            const ctx = canvasElement.getContext('2d');
            ctx.clearRect(0, 0, canvasElement.width, canvasElement.height);
            ctx.fillStyle = '#fff3cd';
            ctx.fillRect(0, 0, canvasElement.width, canvasElement.height);
            ctx.fillStyle = '#856404';
            ctx.font = '14px Arial';
            ctx.textAlign = 'center';
            ctx.fillText('Error cargando datos de permisos activos', canvasElement.width / 2, canvasElement.height / 2 - 10);
            ctx.fillText('Inténtelo más tarde', canvasElement.width / 2, canvasElement.height / 2 + 10);
        }
    }

    // Ejecutar la carga asíncrona después de un delay para permitir que se carguen los demás gráficos
    setTimeout(() => {
        loadPcirHistoricoAsync();
        // loadPcirActivoAsync();
    }, 1500);

    console.log('🚀 Script de permisos de circulación asíncrono iniciado');
});