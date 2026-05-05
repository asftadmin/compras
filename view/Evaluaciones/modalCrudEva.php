<div id="modalEvaluacion" class="modal fade bd-example-modal" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-center">
                <h3 class="modal-title w-100" id="mdltitulo"></h3>
                <button type="button" class="close" data-dismiss="modal"><i class="fas fa-times" style="color: red;"
                        data-toggle="tooltip" title="Cerrar"></i></button>
            </div>
            <form method="post" id="evaluacion_form">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="eval_nombre">Nombre de Evaluación:<sup>*</sup></label>
                        <input type="text" class="form-control mt-2" name="eval_nombre" id="eval_nombre"
                            placeholder="Digite el nombre de la Evaluación" required>
                    </div>
                    <div class="form-group">
                        <label for="rangoFechasEva">Rango de Fechas:<sup>*</sup></label>
                        <input type="text" id="rangoFechasEva" name="rangoFechasEva" class="form-control"
                            placeholder="Seleccione rango de fechas" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="action" id="#" value="add" class="btn btn-rounded btn-info"> <i
                            class="fas fa-save"></i>&nbsp; Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>