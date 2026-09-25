let arrImages=[];
let usrFoto  = document.getElementById('userfoto').value;
let tipoDoc  = document.getElementById('tipoDoc').value;

let urlAPP = '@uploadGuardar.php?usrFoto='+usrFoto+'&tipoDoc='+tipoDoc;

let myDropzone = new Dropzone('.dropzone',{
    url: urlAPP,
    maxFilesize:20,
    maxFiles:1,
    // acceptedFiles: 'image/jpeg, image/png', // 'application/pdf',
    addRemoveLinks:true,
    dictRemoveFile:'Quitar'
})

myDropzone.on('addedfile', file=>{
    arrImages.push(file);
})

myDropzone.on('removedfile', file=>{
    let i = arrImages.indexOf(file);
    arrImages.splice(i, 1);
    // console.log(i);
})

// :: Cuando se ha subido todo
myDropzone.on('queuecomplete', function() {
    console.log(JSON.stringify(arrImages));
    url = "usuarios_ficha.php?IdRegistro=" + usrFoto;
    location.href = url;
});

init();