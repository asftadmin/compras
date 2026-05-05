<div id="modalrol" class="modal fade bd-example-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header bg-info text-center">
          <h3 class="modal-title w-100" id="mdltitulo"></h3>
          <button type="button" class="close" data-dismiss="modal"><i class="fas fa-times" style="color: red;" data-toggle="tooltip" title="Cerrar"></i></button>
        </div>
            <form method="post" id="roles_form">
                <div class="modal-body">
                  <input type="hidden" id="rol_id" name="rol_id">
                    <div class="form-group">
                      <label for="rol_nom">Nombre del Rol:<sup>*</sup></label>
                      <input type="text" class="form-control mt-2" name="rol_nom" id="rol_nom" placeholder="Digite el nombre del Rol" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="action" id="#" value="add" class="btn btn-rounded btn-primary"> <i class="fas fa-save"></i>&nbsp;  Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

