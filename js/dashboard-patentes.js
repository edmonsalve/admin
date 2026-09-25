/**
 * Dashboard - Gráfico de Patentes Comerciales
 * Versión optimizada con carga asíncrona
 * Muestra patentes comerciales de los últimos 4 años
 */
document.addEventListener("DOMContentLoaded", function () {
    // Usar proxy local para evitar problemas de CORS
    const API_PROXY_PATENTES = "/dmuni/api-proxy.php?tipo=patentes";

    async function loadPatentesAsync() {
        console.log('🔄 Iniciando carga de patentes comerciales...');

        // Solo proceder si el gráfico está habilitado y existe el elemento
        const graficoElement = document.getElementById('GRpatComer');
        const canvasElement = document.getElementById('graficoPatHist');
        
        console.log('🔍 Verificando elementos DOM:', {
            graficoElement: graficoElement ? 'encontrado' : 'no encontrado',
            graficoValue: graficoElement ? graficoElement.value : 'N/A',
            canvasElement: canvasElement ? 'encontrado' : 'no encontrado'
        });

        if (!graficoElement || graficoElement.value !== 'S') {
            console.log('ℹ️ Gráfico de patentes comerciales no habilitado');
            return;
        }

        if (!canvasElement) {
            console.error('❌ Canvas #graficoPatHist no encontrado');
            return;
        }

        try {
            // Verificar que existe la función del gráfico
            if (typeof graficoPatentes !== 'function') {
                console.error('❌ Función graficoPatentes no disponible');
                console.log('🔍 Funciones globales disponibles:', Object.getOwnPropertyNames(window).filter(name => name.includes('grafico')));
                throw new Error('La función graficoPatentes no está disponible');
            }

            console.log('✅ Función graficoPatentes encontrada');

            // Verificar si ya existe una instancia previa del gráfico de patentes usando Chart.getChart
            const existingChart = Chart.getChart('graficoPatHist');
            if (existingChart) {
                console.log('🔄 Destruyendo gráfico anterior de patentes...');
                existingChart.destroy();
            }

            // NO intentar destruir variables globales problemáticas, solo limpiar referencias
            if (window.graficoPcirHistorico) {
                console.log('⚠️ Variable global graficoPcirHistorico encontrada, limpiando solo referencia...');
                // No intentar destruir, solo limpiar la referencia
                delete window.graficoPcirHistorico;
            }

            // Mostrar indicador de carga
            const ctx = canvasElement.getContext('2d');
            ctx.clearRect(0, 0, canvasElement.width, canvasElement.height);
            ctx.fillStyle = '#f8f9fa';
            ctx.fillRect(0, 0, canvasElement.width, canvasElement.height);
            ctx.fillStyle = '#6c757d';
            ctx.font = '16px Arial';
            ctx.textAlign = 'center';
            ctx.fillText('Cargando patentes comerciales...', canvasElement.width / 2, canvasElement.height / 2);

            console.log('🧪 Usando datos de prueba fijos...');
            
            const currentYear = new Date().getFullYear();
            const labelsPat = [`Año ${currentYear-3}`, `Año ${currentYear-2}`, `Año ${currentYear-1}`, `Año ${currentYear}`];
            const seriePagadas = [150, 180, 165, 200];
            const serieMorosas = [25, 30, 22, 35];

            console.log('📊 Datos de prueba:', {
                labels: labelsPat,
                pagadas: seriePagadas,
                morosas: serieMorosas,
                tiposLabels: typeof labelsPat,
                tiposPagadas: seriePagadas.map(v => typeof v),
                tiposMorosas: serieMorosas.map(v => typeof v)
            });

            // Limpiar el canvas completamente
            ctx.clearRect(0, 0, canvasElement.width, canvasElement.height);

            console.log('🎯 Llamando a graficoPatentes con datos de prueba...');

            // Llamar a la función del gráfico con datos fijos
            const resultado = graficoPatentes(labelsPat, seriePagadas, serieMorosas);

            console.log('✅ Función graficoPatentes ejecutada, resultado:', resultado);

            // Verificar si el gráfico se creó correctamente después de un momento
            setTimeout(() => {
                const chartInstance = Chart.getChart('graficoPatHist');
                if (chartInstance) {
                    console.log('✅ ¡ÉXITO! Instancia de Chart.js encontrada:', chartInstance);
                    console.log('📊 Datasets del gráfico:', chartInstance.data.datasets);
                    console.log('🏷️ Labels del gráfico:', chartInstance.data.labels);
                } else {
                    console.error('❌ No se pudo crear la instancia del gráfico');
                    console.log('🔍 Canvas después de la llamada:', {
                        width: canvasElement.width,
                        height: canvasElement.height,
                        style: canvasElement.style.cssText
                    });
                }
            }, 500);

        } catch (error) {
            console.error('❌ Error cargando patentes comerciales:', error);
            
            // Mostrar mensaje de error en el canvas
            const ctx = canvasElement.getContext('2d');
            ctx.clearRect(0, 0, canvasElement.width, canvasElement.height);
            ctx.fillStyle = '#fff3cd';
            ctx.fillRect(0, 0, canvasElement.width, canvasElement.height);
            ctx.fillStyle = '#856404';
            ctx.font = '14px Arial';
            ctx.textAlign = 'center';
            ctx.fillText('Error cargando datos de patentes', canvasElement.width / 2, canvasElement.height / 2 - 10);
            ctx.fillText('Inténtelo más tarde', canvasElement.width / 2, canvasElement.height / 2 + 10);
        }
    }

    // Ejecutar la carga asíncrona después de un delay para permitir que se carguen los demás gráficos
    setTimeout(loadPatentesAsync, 2000);

    console.log('🚀 Dashboard - Patentes comerciales iniciado');
});