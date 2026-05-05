<div id="modalusuario" class="modal fade bd-example-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header bg-info text-center">
          <h3 class="modal-title w-100" id="mdltitulo"></h3>
          <button type="button" class="close" data-dismiss="modal"><i class="fas fa-times" style="color: red;" data-toggle="tooltip" title="Cerrar"></i></button>
        </div>
            <form method="post" id="usuarios_form">
                <div class="modal-body">
                  <input type="hidden" id="user_id" name="user_id">
                    <div class="form-group">
                      <label for="user_empleado">Empleado:<sup>*</sup></label>
                        <select class="form-control select2bs4" id="user_empleado" name="user_empleado" style="width: 100%;"  required>
                        </select>
                    </div>
                    <div class="form-group">
                      <label for="user_usuario">Usuario:<sup>*</sup></label>
                      <input type="text" class="form-control mt-2" name="user_usuario" id="user_usuario" maxlength="50" minlength="4" pattern="^[A-Za-z0-9]*$" title="Ingrese solo letras y números" minlength="5" placeholder="Digite el usuario" required>
                    </div>
                    <div class="form-group">
                      <label for="user_password">Contraseña:<sup>*</sup></label>
                      <div class="input-group">
                          <input type="password" class="form-control mt-2" name="user_password" maxlength="50" minlength="6" id="user_password" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$" title="La contraseña debe tener al menos 6 caracteres, incluyendo al menos una letra mayúscula, una letra minúscula y al menos un carácter especial o número." placeholder="Digite la contraseña" />
                          <span class="input-group-text contraseña-input" id="show-password" style="cursor: pointer; height:39px; margin-top:7px; display: none;">
                              <i class="fa fa-eye" aria-hidden="true"></i>
                          </span>
                      </div>
                    </div>  
                </div>
                <div class="modal-footer">
                    <button type="submit" name="action" id="#" value="add" class="btn btn-rounded btn-primary"> <i class="fas fa-save"></i>&nbsp;  Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
  document.getElementById("show-password").addEventListener("click", function () {
    var passwordInput = document.getElementById("user_password");
    if (passwordInput.type === "password") {
        passwordInput.type = "text";
    } else {
        passwordInput.type = "password";
    }
});
</script>