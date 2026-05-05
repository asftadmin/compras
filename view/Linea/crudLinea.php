<div id="modallinea" class="modal fade bd-example-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header bg-info text-center">
          <h3 class="modal-title w-100" id="mdltitulo"></h3>
          <button type="button" class="close" data-dismiss="modal"><i class="fas fa-times" style="color: red;" data-toggle="tooltip" title="Cerrar"></i></button>
        </div>
            <form method="post" id="linea_form">
                <div class="modal-body">
                  <input type="hidden" id="linea_id" name="linea_id">
                    <div class="form-group">
                      <label for="linea_nom">Nombre de la linea de Produccion:<sup>*</sup></label>
                      <input type="text" class="form-control mt-2" name="linea_nom" id="linea_nom" placeholder="Digite el nombre de la linea de produccion" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="action" id="#" value="add" class="btn btn-rounded btn-primary"> <i class="fas fa-save"></i>&nbsp;  Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

