<div id="modalclave" class="modal fade bd-example-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header bg-info text-center">
          <h3 class="modal-title w-100" id="mdltitulo1"></h3>
          <button type="button" class="close" data-dismiss="modal"><i class="fas fa-times" style="color: red;" data-toggle="tooltip" title="Cerrar"></i></button>
        </div>
            <form method="post" id="clave_form">
                <div class="modal-body">
                    <input type="hidden" id="user_id1" name="user_id1">
                    <div class="form-group">
                      <label for="user_nombre1">Nombre:</label>
                      <input type="text" class="form-control mt-2" name="user_nombre1" id="user_nombre1" maxlength="40" minlength="3"  disabled>
                    </div>
                    <div class="form-group">
                        <label for="user_contrasena">Nueva Contraseña:<sup>*</sup></label>
                        <div class="input-group">
                            <input type="password" class="form-control mt-2" name="user_contrasena1" maxlength="50" minlength="6" id="user_contrasena1" title="La contraseña debe tener al menos 6 caracteres, incluyendo al menos una letra mayúscula, una letra minúscula y al menos un carácter especial o número." placeholder="Digite la nueva contraseña" required/>
                                <span class="input-group-text" id="show-password1" style="cursor: pointer; height:39px; margin-top:7px;">
                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                </span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="boton" id="#" value="add" class="btn btn-rounded btn-primary"> <i class="fas fa-save"></i>&nbsp;  Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
  document.getElementById("show-password1").addEventListener("click", function () {
    var passwordInput = document.getElementById("user_contrasena1");
    if (passwordInput.type === "password") {
        passwordInput.type = "text";
    } else {
        passwordInput.type = "password";
    }
});
</script>