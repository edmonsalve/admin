/**
 * PLANTILLA PARA NUEVOS GRÁFICOS ASÍNCRONOS
 * 
 * Instrucciones de uso:
 * 1. Copia este archivo y renómbralo: dashboard-test-[tuGrafico].js
 * 2. Cambia las variables marcadas con [CAMBIAR]
 * 3. Ajusta el procesamiento de datos según tu API
 * 4. Incluye el script en tu template PHP
 */

// [CAMBIAR] Configuración de la API
const API_PROXY_PLANTILLA = '/dmuni/api-proxy.php?tipo=nombreGrafico'; // [CAMBIAR] nombre del tipo

// [CAMBIAR] Función principal de carga asíncrona
async function cargarGraficoPlantilla() { // [CAMBIAR] nombre de la función
    const canvasElement = document.getElementById('miCanvas'); // [CAMBIAR] ID del canvas
    
    if (!canvasElement) {
        console.error('❌ Canvas #miCanvas no encontrado'); // [CAMBIAR] nombre del canvas
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
        ctx.fillText('Cargando datos...', canvasElement.width / 2, canvasElement.height / 2); // [CAMBIAR] mensaje

        console.log('🔄 Cargando gráfico de [NOMBRE] de forma asíncrona...'); // [CAMBIAR] descripción

        // [CAMBIAR] Verificar que existe la función del gráfico
        if (typeof miFuncionGrafico !== 'function') { // [CAMBIAR] nombre de la función
            throw new Error('La función miFuncionGrafico no está disponible'); // [CAMBIAR] nombre
        }

        // Construir URL con parámetros si es necesario
        let url = API_PROXY_PLANTILLA;
        
        // [OPCIONAL] Agregar parámetros dinámicos
        const anioActual = new Date().getFullYear();
        // url += `&anio=${anioActual}&mes=11`;

        console.log('🌐 Solicitando datos desde:', url);

        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const result = await response.json();
        console.log('📦 Respuesta del proxy ([NOMBRE]):', result); // [CAMBIAR] descripción

        if (!result.success) {
            console.warn('⚠️ API no exitosa, usando datos de prueba');
            // Usar datos de prueba
            const labelsTest = ['Item 1', 'Item 2', 'Item 3', 'Item 4']; // [CAMBIAR] datos de prueba
            const valoresTest = [100, 200, 150, 300]; // [CAMBIAR] datos de prueba
            
            ctx.clearRect(0, 0, canvasElement.width, canvasElement.height);
            miFuncionGrafico(labelsTest, valoresTest); // [CAMBIAR] llamada a función
            console.log('🧪 Usando datos de prueba por API no exitosa');
            return;
        }

        const data = result.data;
        console.log('✅ Datos recibidos:', data);

        // [CAMBIAR] Procesar los datos según el formato de tu API
        let labels = [];
        let valores = [];

        // Opción 1: API retorna { labels: [...], valores: [...] }
        if (data.labels && data.valores) {
            labels = Array.isArray(data.labels) ? data.labels : Object.keys(data.labels);
            valores = Array.isArray(data.valores) ? data.valores : Object.values(data.valores);
            valores = valores.map(v => parseInt(v) || 0); // Asegurar números
        }
        // Opción 2: API retorna { serie1: [...], serie2: [...] }
        else if (data.serie1 && data.serie2) {
            labels = Array.isArray(data.serie1) ? data.serie1 : Object.keys(data.serie1);
            valores = Array.isArray(data.serie2) ? data.serie2 : Object.values(data.serie2);
            valores = valores.map(v => parseInt(v) || 0);
        }
        // Opción 3: API retorna objeto plano { "2021": 100, "2022": 200, ... }
        else if (typeof data === 'object') {
            labels = Object.keys(data);
            valores = Object.values(data).map(v => parseInt(v) || 0);
        }
        // Opción 4: Procesamiento personalizado
        else {
            // [CAMBIAR] Agregar tu lógica de procesamiento aquí
            console.warn('⚠️ Formato de datos no reconocido, usando valores por defecto');
            labels = ['Dato 1', 'Dato 2', 'Dato 3'];
            valores = [50, 100, 75];
        }

        console.log('🎯 Datos procesados:', {
            labels,
            valores,
            tipos: valores.map(v => typeof v),
            suma: valores.reduce((a, b) => a + b, 0)
        });

        // Verificar que tenemos datos válidos
        if (labels.length === 0 || valores.length === 0) {
            throw new Error('No se pudieron procesar los datos correctamente');
        }

        // Verificar que las longitudes coinciden
        if (labels.length !== valores.length) {
            console.warn('⚠️ Longitudes de arrays no coinciden, ajustando...');
            const minLength = Math.min(labels.length, valores.length);
            labels = labels.slice(0, minLength);
            valores = valores.slice(0, minLength);
        }

        // Limpiar el canvas antes de dibujar
        ctx.clearRect(0, 0, canvasElement.width, canvasElement.height);

        // [CAMBIAR] Llamar a la función original del gráfico
        miFuncionGrafico(labels, valores); // [CAMBIAR] nombre y parámetros según tu función

        console.log('✅ Gráfico de [NOMBRE] cargado correctamente'); // [CAMBIAR] descripción

    } catch (error) {
        console.error('❌ Error cargando gráfico de [NOMBRE]:', error); // [CAMBIAR] descripción
        
        // Mostrar datos de prueba en caso de error
        const ctx = canvasElement.getContext('2d');
        ctx.clearRect(0, 0, canvasElement.width, canvasElement.height);
        
        if (typeof miFuncionGrafico === 'function') { // [CAMBIAR] nombre de función
            // [CAMBIAR] Datos de fallback apropiados para tu gráfico
            const fallbackLabels = ['Fallback 1', 'Fallback 2', 'Fallback 3', 'Fallback 4'];
            const fallbackValores = [25, 50, 35, 60];
            
            miFuncionGrafico(fallbackLabels, fallbackValores); // [CAMBIAR] llamada
            console.log('🧪 Usando datos de prueba por error de API');
        } else {
            // Mostrar mensaje de error en el canvas
            const ctx = canvasElement.getContext('2d');
            ctx.fillStyle = '#f8d7da';
            ctx.fillRect(0, 0, canvasElement.width, canvasElement.height);
            ctx.fillStyle = '#721c24';
            ctx.font = '14px Arial';
            ctx.textAlign = 'center';
            ctx.fillText('Error: Función de gráfico no disponible', canvasElement.width / 2, canvasElement.height / 2);
        }
    }
}

// Auto-ejecutar cuando el DOM esté listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', cargarGraficoPlantilla); // [CAMBIAR] nombre de función
} else {
    cargarGraficoPlantilla(); // [CAMBIAR] nombre de función
}

/**
 * CHECKLIST PARA PERSONALIZAR:
 * 
 * □ Cambiar API_PROXY_PLANTILLA por el nombre de tu API
 * □ Cambiar cargarGraficoPlantilla por un nombre descriptivo
 * □ Cambiar 'miCanvas' por el ID real de tu canvas
 * □ Cambiar miFuncionGrafico por tu función real
 * □ Ajustar el procesamiento de datos según tu API
 * □ Personalizar los datos de prueba y fallback
 * □ Actualizar todos los mensajes de log con el nombre correcto
 * □ Agregar parámetros URL si tu API los necesita
 * □ Probar con datos reales y verificar que funciona
 */