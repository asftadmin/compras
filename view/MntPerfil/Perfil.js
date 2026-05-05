
$(document).on("click", "#btnactualizar", function(){
    var pass=  $('#txtpass').val();
    var newpass=  $('#txtpassnew').val();

    if(pass.trim().length == 0 || newpass.trim().length == 0){
        swal({
            title: "Error", 
            text: "Campos Vacios!!!",
            type: "error",
            confirmButtonClass: "btn-danger",
        });
    
    }else if(pass.trim().length < 6 || newpass.trim().length < 6){
        swal({
            title: "Error", 
            text: "Ingresa Minimo 6 Digitos",
            type: "error",
            confirmButtonClass: "btn-danger",
        });   
    }else if(!/[A-Z]/.test(pass) || !/[A-Z]/.test(newpass) || !/[a-z]/.test(pass) || !/[a-z]/.test(newpass) || !/\d/.test(pass) || !/\d/.test(newpass)){
        swal({
            title: "Error", 
            text: "Debes incluir al menos una letra en mayúscula, una letra en minúscula, un número y un carácter especial en tu contraseña.",
            type: "error",
            confirmButtonClass: "btn-danger",
        });
    
    }else{
    
    if(pass.trim() == newpass.trim()){
       
         var user_id = $('#user_idx').val();
         console.log(user_id);
         $.post ("../../controller/Usuario.php?op=Password", {user_id:user_id,user_contrasena:newpass}, function(data){
            swal({
                title: "Correcto", 
                text: "Contraseña Actualizada Correctamente!!",
                type: "success",
                confirmButtonClass: "btn-success",
            });
        });


    }else{
        swal({
            title: "Error", 
            text: "Las Contraseñas no coinciden!!",
            type: "error",
            confirmButtonClass: "btn-danger",
        });
    }
}
});