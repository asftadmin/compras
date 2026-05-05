<?php
require_once "../../config/conexion.php";
if (isset($_SESSION["user_id"])) {
?>
<!DOCTYPE html>
<html lang="es">
<?php require_once("../MainHead/head.php"); ?>
<link rel="stylesheet" href="../../public/css/inicio.css">
<link rel="shortcut icon" href="../../public/img/Asfaltart.ico">
<title>Proveedores App</title>
</head>

<body class="hold-transition sidebar-mini">

    <div class="wrapper">
        <?php require_once("../MainNav/nav.php"); ?>
        <?php require_once("../MainMenu/menu.php"); ?>
        <div class="content-wrapper">
            <!-- HEADER -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">EVALUACIONES</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../inicio/inicio.php">Inicio</a></li>
                                <li class="breadcrumb-item active"><a href="#">Evaluaciones</a></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.HEADER -->

            <!-- MAIN -->
            <div class="content">
                <div class="container-fluid">
                    <div class="card">
                        <div class="card-header">

                            <div class="row mb-3">
                                <!--                                 <div class="col-md-9">
                                    <input type="text" id="rangoFechasEva" class="form-control"
                                        placeholder="Seleccione rango de fechas" readonly>
                                </div> -->
                                <div class="col-md-3">
                                    <button id="btn-add-evaluacion" class="btn btn-dark"> <i class="fas fa-plus"></i>
                                        &nbsp;Agregar
                                        Evaluacion</button>
                                </div>
                            </div>

                            <!-- <button type="button" id="btnnuevoprov" class="btn btn-info">Nuevo Proveedores</button> -->

                        </div>
                        <div class="card-body">

                            <div class="box-typical box-typical-padding p-0"
                                style="padding-top:0 !important; margin-top:0 !important;">
                                <table id="evaluacion_data" name="evaluacion_data"
                                    class="table table-bordered table-striped table-vcenter js-dataTable-full mb-0">
                                    <thead class="bg-info">
                                        <tr>
                                            <th th class="text-center" style="width: 8%;">ID</th>
                                            <th th class="text-center" style="width: 8%;">Nombre Evaluacion</th>
                                            <th th class="text-center" style="width: 15%;">Rango de Fechas</th>
                                            <th th class="text-center" style="width: 8%;">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
            <!-- MAIN -->
            <aside class="control-sidebar control-sidebar-dark">
            </aside>
        </div>
        <?php require_once("../MainFooter/footer.php") ?>
    </div>

    <script src="../../config/config.js"></script>
    <?php require_once("modalCrudEva.php") ?>
    <?php require_once("../MainJS/JS.php") ?>
    <script type="text/javascript" src="evaluaciones.js"></script>
</body>

</html>
<?php
} else {
    header("Location:" . Conectar::ruta() . "Pagina404.php");
}
?>