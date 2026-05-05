<div id="modalproveedor" class="modal fade bd-example-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header bg-info text-center">
          <h3 class="modal-title w-100" id="mdltitulo"></h3>
          <button type="button" class="close" data-dismiss="modal"><i class="fas fa-times" style="color: red;" data-toggle="tooltip" title="Cerrar"></i></button>
        </div>
            <form method="post" id="proveedor_form">
                <div class="modal-body">
                  <input type="hidden" id="prov_id" name="prov_id">
                    
                    <div class="form-group">
                      <label for="prov_nit">NIT del Proveedor:<sup>*</sup></label>
                      <input type="text" class="form-control mt-2" name="prov_nit" id="prov_nit"  placeholder="Digite el NIT del proveedor" required>
                    </div>
                    <div class="form-group">
                      <label for="prov_nit">ID Siesa:<sup>*</sup></label>
                      <input type="text" class="form-control mt-2" name="prov_cod_siesa" id="prov_cod_siesa"  placeholder="Digite el ID de Siesa" required>
                    </div>
                    <div class="form-group">
                      <label for="prov_descripcion">Descripción del Proveedor:<sup>*</sup></label>
                      <input type="text" class="form-control mt-2" name="prov_descripcion" id="prov_descripcion"  placeholder="Digite la descripcion del proveedor" required>
                    </div>
                    <div class="form-group">
                      <label for="prov_estado">Estado:<sup>*</sup></label>
                      <input type="text" class="form-control mt-2" name="prov_estado" id="prov_estado"  placeholder="Digite el Estado" required>
                    </div>
                    <div class="form-group">
                      <label for="prov_linea">Tipo de Proveedor:<sup>*</sup></label>
                        <select class="form-control select2bs4" id="prov_linea" name="prov_linea" style="width: 100%;"  required>
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

