<div id="modalemplapp" class="modal fade bd-example-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header bg-info text-center">
          <h3 class="modal-title w-100" id="mdltitulo"></h3>
          <button type="button" class="close" data-dismiss="modal"><i class="fas fa-times" style="color: red;" data-toggle="tooltip" title="Cerrar"></i></button>
        </div>
            <form method="post" id="Emplapp_form">
                <div class="modal-body">
                  <input type="hidden" id="empl_id" name="empl_id">
                    
                    <div class="form-group">
                      <label for="empl_nom">Nombre del Empleado:<sup>*</sup></label>
                      <input type="text" class="form-control mt-2" name="empl_nom" id="empl_nom" maxlength="40" minlength="3"  placeholder="Digite el nombre del empleado" required>
                    </div>
                    <div class="form-group">
                      <label for="empl_apellido">Apellidos del Empleado:<sup>*</sup></label>
                      <input type="text" class="form-control mt-2" name="empl_apellido" id="empl_apellido" maxlength="60" minlength="5"  placeholder="Digite los apellidos del empleado" required>
                    </div>
                    <div class="form-group">
                      <label for="empl_documento">N° Documento:<sup>*</sup></label>
                      <input type="text" class="form-control mt-2" name="empl_documento" id="empl_documento" maxlength="15" minlength="5" pattern="[0-9]+" title="Solo se permiten números" placeholder="Digite el número de documento" required>
                    </div>
                    <div class="form-group">
                      <label for="empl_telefono">N° Telefono:<sup>*</sup></label>
                      <input type="text" class="form-control mt-2" name="empl_telefono" id="empl_telefono"  title="Solo se permiten números" placeholder="Digite el número de documento" required>
                    </div>
                    <div class="form-group">
                      <label for="empl_email">Correo electronico:<sup>*</sup></label>
                      <input type="email" class="form-control mt-2" name="empl_email" id="empl_email" maxlength="50" minlength="5" pattern="[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}" placeholder="Digite el correo electronico"  required>
                    </div>
                    <div class="form-group">
                      <label for="empl_direccion">Direccion:<sup>*</sup></label>
                      <input type="text" class="form-control mt-2" name="empl_direccion" id="empl_direccion" placeholder="Digite la direccion"  required>
                    </div>
                    <div class="form-group">
                        <label for="empl_estado" id="lbestado"></label>
                        <div id="empl_estado">    
                        </div>
                    </div>
                    <div class="form-group">
                      <label for="empl_rol">Cargo del Empleado:<sup>*</sup></label>
                        <select class="form-control select2bs4" id="empl_rol" name="empl_rol" style="width: 100%;"  required>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="action" id="#" value="add" class="btn btn-rounded btn-primary"> <i class="fas fa-save"></i>&nbsp;  Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

