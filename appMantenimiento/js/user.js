// File: dcode/appHtas/js/user.js

function guardaPermisos(sistema,username) { 
    nfield = document.getElementById('formModulos').elements.length;

    post   	  = '';
    for(i=0; i < nfield; i++) {
        fieldName  = document.getElementById('formModulos').elements[i].id;
        fieldValue = document.getElementById('formModulos').elements[i].value;
        if 	(document.getElementById('formModulos').elements[i].type != 'button') {
            if (i > 0) { post += '&'; }
            post += fieldName + '=' + fieldValue;
        }
    }
    url = 'usuarios_perModulos.php?sistema='+sistema+'&username='+username;
    llamarasincrono(url,'divModulos',post)
}

function usersModulos(usr,idSist) { 
    document.getElementById('sectFlot').style.display='block';
    urlApp = 'usuarios_modulos.php?sistemaId='+idSist+'&username='+usr;

    $.ajax({
        url:  urlApp,
        type: "GET",
        data: '',
        contentType: false,
        processData: false,
        beforeSend: function () {
            $("#divFlot").html("Procesando, espere por favor...");
            },
            success:  function (response) {
                    $("#divFlot").html(response);
            }
    });
}

function usersModulosTodos(idSist,usr,autoriza) {
    document.getElementById('sectFlot').style.display='block';
    urlApp = 'usuarios_modulos.php?sistemaId='+idSist+'&username='+usr+'&autoriza='+autoriza;

    $.ajax({
        url:  urlApp,
        type: "GET",
        data: '',
        contentType: false,
        processData: false,
        beforeSend: function () {
            $("#divFlot").html("Procesando, espere por favor...");
            },
            success:  function (response) {
                    $("#divFlot").html(response);
            }
    });
}

function permisoSistema(usr,idSist) {
    permiso = document.getElementById(idSist).value;
    url = 'usuarios_perSistema.php?sistemaId='+idSist+'&username='+usr+'&permiso='+permiso;
    Swal.fire({
        icon: "question",
        color:'darkgreen',
        confirmButtonText: "Ccnfirmar",
        confirmButtonColor: "darkgreen",
        showCancelButton: true,
        text: 'Esta seguro de modificar sutorizacion a sistema ',
    }).then((result) => {
        llamarasincrono(url,'divAjax','');
    });        
}

function verSistemas() {
    var areaSist  = document.getElementById('areaSist').value;
    
    document.getElementById('sistMuni').style.display  = 'none';
    document.getElementById('sistSal').style.display   = 'none';
    document.getElementById('sistEduc').style.display  = 'none';
    document.getElementById('sistCont').style.display  = 'none';
    document.getElementById('sistAdm').style.display   = 'none';

    switch (areaSist) {
        case 'M':
            document.getElementById('sistMuni').style.display = 'block';
            break;
        case 'S':
            document.getElementById('sistSal').style.display = 'block';
            break;
        case 'E':
            document.getElementById('sistEduc').style.display = 'block';
            break;
        case 'C':
            document.getElementById('sistCont').style.display = 'block';
            break;
        case 'A':
            document.getElementById('sistAdm').style.display = 'block';
            break;
        default : break;
    }
}

function verDeptos() {
    var areaSist  = document.getElementById('areaDeptos').value;
    
    document.getElementById('deptosSelectM').style.display  = 'none';
    document.getElementById('deptosSelectS').style.display   = 'none';
    document.getElementById('deptosSelectE').style.display  = 'none';

    switch (areaSist) {
        case 'M':
            document.getElementById('deptosSelectM').style.display = 'block';
            break;
        case 'S':
            document.getElementById('deptosSelectS').style.display = 'block';
            break;
        case 'E':
            document.getElementById('deptosSelectE').style.display = 'block';
            break;
        default : break;
    }
}

function syncDeptosUsuario() {
    var sel = document.getElementById('deptosUsr');
    var deptosPorArea = { M: [], S: [] };
    var vistos = { M: {}, S: {} };

    for (var i = 0; i < sel.options.length; i++) {
        var value = (sel.options[i].value || '').toString().trim();
        if (value === '') {
            continue;
        }

        var partes = value.split(',');
        if (partes.length < 2) {
            continue;
        }

        var idDepto = (partes[0] || '').toString().trim();
        var area = (partes[1] || '').toString().trim().toUpperCase();

        if ((area !== 'M' && area !== 'S') || idDepto === '' || vistos[area][idDepto]) {
            continue;
        }

        vistos[area][idDepto] = true;
        deptosPorArea[area].push(idDepto);
    }

    var payload = {
        M: deptosPorArea.M,
        S: deptosPorArea.S
    };

    document.getElementById('departAsoc').value = JSON.stringify(payload);
}

function addDep(area) {
    switch (area) {
        case 'M':
            var selectDepto = document.getElementById('deptosSelectM');
            break;
        case 'S':
            var selectDepto = document.getElementById('deptosSelectS');
            break;
        case 'E':
            var selectDepto = document.getElementById('deptosSelectE');
            break;
        default:
            return;
    }

    var deptosUsr   = document.getElementById('deptosUsr');
    var selectDeptoId = (selectDepto.value || '').toString().trim();

    if (selectDeptoId === '') {
        return;
    }

    var nuevoValor = selectDeptoId + ',' + area;

    for (var i = 0; i < deptosUsr.options.length; i++) {
        if (deptosUsr.options[i].value === nuevoValor) {
            syncDeptosUsuario();
            return;
        }
    }

    var option = document.createElement('option');
    option.value = nuevoValor;
    option.text = selectDepto.options[selectDepto.selectedIndex].text  + '(' + area + ')';
    deptosUsr.appendChild(option);

    syncDeptosUsuario();
}

function delDep() {
    var deptosUsr = document.getElementById('deptosUsr');
    var selectDeptoId = (deptosUsr.value || '').toString().trim();

    if (selectDeptoId === '') {
        return;
    }

    for (var i = deptosUsr.options.length - 1; i >= 0; i--) {
        if (deptosUsr.options[i].value === selectDeptoId) {
            deptosUsr.remove(i);
        }
    }

    syncDeptosUsuario();
}

function resetUsr(username) {
    var estado;
    var estadoActualUsr;
    
    var estadoActualUsr = document.getElementById('estadoUsrAux').value;

    if (estadoActualUsr == 'A') { 
        newEstado = ' INACTIVO '; 
        estado    = 'I';
    } else { 
        newEstado = ' ACTIVO '; 
        estado    = 'A';
    }

    url = 'usuarios_resetAcceso.php?username='+username+'&estado='+estado;
    Swal.fire({
        icon: "question",
        color:'darkgreen',
        confirmButtonText: "Ccnfirmar",
        confirmButtonColor: "darkgreen",
        showCancelButton: true,
        text: 'Esta seguro de modificar estado usuario a: '+newEstado,
    }).then((result) => {
        llamarasincrono(url,'divAjax',''); 

        if (estado == 'A') { 
            document.getElementById('estadoUser').value = 'Activo';
            document.getElementById('estadoActualUsr').value = 'A';
            document.getElementById('btnAct').value = 'Desactiva Usuario';
        } else { 
            document.getElementById('estadoUser').value = 'Inactivo';
            document.getElementById('estadoActualUsr').value = 'I';
            document.getElementById('btnAct').value = 'Activa Usuario';
        }
    });  
}

function resetPass(username, areaId) {
    url = 'usuarios_resetPass.php?username='+username+'&areaId='+areaId;
    Swal.fire({
        icon: "question",
        color:'darkgreen',
        confirmButtonText: "Confirmar",
        confirmButtonColor: "darkgreen",
        showCancelButton: true,
        text: 'Esta seguro de Resetear la Password?',
    }).then((result) => {
        llamarasincrono(url,'divAjax','');
    });              
}

function uploadFotoCam(username) {
    document.getElementById('sectFlotCam').style.display='block';
    urlApp = '@fotoCam.php?username='+username;
      
    $.ajax({
        url:  urlApp,
        type: "GET",
        data: '',
        contentType: false,
        processData: false,
        beforeSend: function () {
            $("#divFlotCam").html("Procesando, espere por favor...");
            },
            success:  function (response) {
                    $("#divFlotCam").html(response);
            }
    });
}

function upload(username, tipoDoc) {
    document.getElementById('sectFlot').style.display='block';
    urlApp = '@docUpload.php?username='+username+'&tipoDoc='+tipoDoc;
    
    $.ajax({
        url:  urlApp,
        type: "GET",
        data: '',
        contentType: false,
        processData: false,
        beforeSend: function () {
            $("#divFlot").html("Procesando, espere por favor...");
            },
            success:  function (response) {
                    $("#divFlot").html(response);
            }
    });
}

function editarUser(id, areaId) {

    const campo    = document.getElementById('campo')?.value ?? '';
    const criterio = document.getElementById('buscar')?.value ?? '';
    const pagina   = document.getElementById('pagina')?.value ?? 1;

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'usuarios_ficha.php';

    const add = (name, value) => {
        if (typeof value !== 'undefined' && value !== null) {
            const i = document.createElement('input');
            i.type  = 'hidden';
            i.name  = name;
            i.value = value;
            form.appendChild(i);
        }
    };

    add('IdRegistro', id);
    add('areaId', areaId);

    add('ope', 'Update');
    add('buscarpor', campo);
    add('iguala', criterio);
    add('pag', pagina);

    document.body.appendChild(form);
    form.submit();
}
