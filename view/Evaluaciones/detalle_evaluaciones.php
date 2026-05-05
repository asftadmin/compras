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
                                <h1 class="m-0">IMPORTAR EVALUACIONES SIESA</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="../inicio/inicio.php">Inicio</a></li>
                                    <li class="breadcrumb-item active"><a href="#">Importar Evaluaciones</a></li>
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

                                <div class="row g-2 align-items-center">
                                    <div class="col-md-3">
                                        <input type="text" id="fecha_inicio" class="form-control" placeholder="" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" id="fecha_fin" class="form-control" placeholder="" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <button id="btn-add-buscar" class="btn btn-dark w-100"> <i
                                                class="fas fa-search"></i>
                                            &nbsp;Buscar</button>
                                    </div>
                                    <div class="col-md-3">
                                        <button id="btnVolver" class="btn btn-primary w-100"> <i
                                                class="fas fa-arrow-circle-left"></i>
                                            &nbsp;Regresar</button>
                                    </div>
                                </div>


                            </div>
                            <div class="card-body">

                                <div class="box-typical box-typical-padding p-0">
                                    <br><br>
                                    <table id="ev_siesa_data" name="ev_siesa_data"
                                        class="table table-bordered table-striped table-vcenter js-dataTable-full"
                                        style="width:100%">
                                        <thead class="bg-info">
                                            <tr>
                                                <th><input type="checkbox" id="selectAll"></th>
                                                <th th class="text-center">NIT</th>
                                                <th th class="text-center">RAZON SOCIAL</th>
                                                <th th class="text-center">EMAIL</th>
                                                <th th class="text-center">P1</th>
                                                <th th class="text-center">P2</th>
                                                <th th class="text-center">P3</th>
                                                <th th class="text-center">P4</th>
                                                <th th class="text-center">P5</th>
                                                <th th class="text-center">P6</th>
                                                <th th class="text-center">P7</th>
                                                <th th class="text-center">P8</th>
                                                <th th class="text-center">P9</th>
                                                <th th class="text-center">% Promedio</th>
                                                <th th class="text-center">Servicio / Producto</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>

                            </div>
                            <div class="card-footer">
                                <div class="row mb-2 justify-content-center">
                                    <div class="col-md-3 text-center">
                                        <button id="btnGuardarEvaSiesa" type="button" s
                                            class="btn btn-info btn-block btn-guardar-eva-siesa">
                                            <i class="far fa-save"></i> Guardar
                                        </button>
                                    </div>
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
        <?php require_once("../MainJS/JS.php") ?>
        <script type="text/javascript" src="detalle_evaluaciones.js"></script>
    </body>

    </html>
<?php
} else {
    header("Location:" . Conectar::ruta() . "Pagina404.php");
}
?>