function exportarExcelFecha(moduloPHP) {
    document.getElementById('divGif').style.display='block';
    document.getElementById('divDescarga').style.display='none';

    desde = document.getElementById('desde').value;
    hasta = document.getElementById('hasta').value;
    
    urlApp = moduloPHP+'?desde='+desde+'&hasta='+hasta;  ;

    $.ajax({
        url:  urlApp,
        type: "GET",
        data: '',
        contentType: false,
        processData: false,
        success: function(response) {
            var filename = response.trim();
            document.getElementById('divGif').style.display = 'none';
            document.getElementById('divDescarga').style.display = 'block';
            
            // Actualiza el enlace de descarga
            document.getElementById('linkDescarga').href = filename;
        }
    });
}

function exportarExcelAAAA(moduloPHP) {
    document.getElementById('divGif').style.display='block';
    aaaa = document.getElementById('aaaaExport').value;    
    urlApp = moduloPHP+'?aaaa='+aaaa;

    $.ajax({
        url:  urlApp,
        type: "GET",
        data: '',
        contentType: false,
        processData: false,
        success: function(response) {
            var filename = response.trim();
            document.getElementById('divGif').style.display = 'none';
            document.getElementById('divDescarga').style.display = 'block';
            
            // Actualiza el enlace de descarga
            document.getElementById('linkDescarga').href = filename;
        }
    });
}

const exportarPlanoFecha = (modulo) => {
    document.getElementById('divGif').style.display='block';
    document.getElementById('divDescarga').style.display = 'none';
    const desde = document.getElementById('fechaInicio').value;
    const hasta = document.getElementById('fechaTermino').value;
    const noinf = document.getElementById('noinf').value;
    const urlApp = modulo+'?desde='+desde+'&hasta='+hasta+'&noinf='+noinf;

    if(noinf === '') {
        Swal.fire({
            icon: "error",
            title: "Archivo Plano RNMT",
            color:'darkblue',
            text: "Ingresar Nro. de Informe",
            showConfirmButton: false,
            timer: 2400
        });        
        return false;
    }

    $.ajax({
        url:  urlApp,
        type: "GET",
        data: '',
        contentType: false,
        processData: false,
        beforeSend: function () {
            $("#respuesta").html("Procesando, espere por favor...");
            },
            success:  function (data) {
                $("#respuesta").html(data);
                const nrorow = data.nrorow;
                const enlace = data.enlace;
                document.getElementById('divGif').style.display = 'none';
                document.getElementById('divDescarga').style.display = 'block';
                
                // Actualiza el enlace de descarga
                document.getElementById('linkDescarga').href = enlace;           
                document.getElementById('linkDescargaTxt').innerText = 'Descargar archivo: '+nrorow+' registro(s)';
            }
    });
}

function exportaConFiltrosFecha (modulo) {
    desde = document.getElementById('desde').value;
    hasta = document.getElementById('hasta').value;

    exportarPHP = modulo+'?desde='+desde+'&hasta='+hasta;  
    x   = window.open(exportarPHP);
}

const exportaConFiltrosAAAA = (modulo) => {
    const aaaa = document.getElementById('aaaaExport').value;
    const exportarPHP = modulo+'?aaaa='+aaaa;      
    window.open(exportarPHP);
}