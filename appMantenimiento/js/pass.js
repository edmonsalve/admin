function init(){ 
    $("#passForm").on("submit",function(e){
        guardarPass(e);
    });
}

function validarPass() {
    var newPassword = document.getElementById('pass1').value;
    var minNumberofChars = 8;
    var maxNumberofChars = 16; 
    
    var regularExpression      = "^(?=.*[0-9])"
                               + "(?=.*[a-z])(?=.*[A-Z])"
                               + "(?=.*[*@#$%^&+=.])";
                               
    var reqClave = new RegExp(regularExpression);
    
    if(newPassword.length < minNumberofChars || newPassword.length > maxNumberofChars){
        swal.fire("Clave Requiere:", "El largo mínimo es de 8 caracteres, debe incluir mayúsculas y minúsculas, números y al menos un carácter especial", "info");	
        // document.getElementById('btnEnviar').disabled=true;			
        return false;
    }
    if(!reqClave.test(newPassword)) {
        swal.fire("Clave Requiere:", "Letras mayúsculas y minúsculas, números y al menos un carácter especial (*@#$%^&+=)", "info");  
        // document.getElementById('btnEnviar').disabled=true;              				
        return false;
    }
}


function guardarPass(e){
    e.preventDefault();

    validarPass();
 
    var formData = new FormData($("#passForm")[0]);

    var erroresDet = "";
    var errores = 0;

    var pass1 = document.getElementById('pass1').value;
    var passR = document.getElementById('passR').value;

    if (pass1 != passR) { 
        errores = 1; 
        erroresDet  += "Las claves ingresadas no coinciden! \n\r"; 
    } 
    
    if (errores == 1) {
        Swal.fire({
            icon: "error",
            title: "Oops...",
            color:'darkred',
            confirmButtonText: "Salir",
            confirmButtonColor: "darkred",
            text: erroresDet,
        });
        return false;
    } 


    $.ajax({
        url: "cambioClave_guardar.php",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        beforeSend: function () {
            $("#divAjax").html("Procesando, espere por favor...");
            },
            success:  function (response) {
                    $("#divAjax").html(response);
                    
                    Swal.fire({
                        icon: "success",
                        color:'darkgreen',
                        confirmButtonText: "Salir",
                        confirmButtonColor: "darkgreen",
                        text: 'Su clave de acceso ha sido actualizada correctamente.',
                    }).then((result) => {
                        var url = "/index.php";
                        location.href=url;
                    });   
            }
    });
}

init();