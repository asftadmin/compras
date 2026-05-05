<?php
require_once "../../config/conexion.php";
if (isset($_SESSION["user_id"])) {
?>
    <!DOCTYPE html>
    <html lang="es">
    <?php require_once("../MainHead/head.php"); ?>
    <link rel="stylesheet" href="../../public/css/inicio.css">
    <link rel="shortcut icon" href="../../public/img/Asfaltart.ico">
    <title>Empleados Siesa</title>
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
                                <h1 class="m-0">Empleados Siesa</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="../inicio/inicio.php">Inicio</a></li>
                                    <li class="breadcrumb-item"><a href="#">Empleados</a></li>
                                    <li class="breadcrumb-item active">Siesa</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.HEADER -->

                <!-- MAIN -->
                <div class="content">
                    <div class="container-fluid">
                        <div class="box-typical box-typical-padding">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-md-6">
                                        <input type="text" id="filtro-fechas" class="form-control" placeholder="Rango de fechas">
                                    </div>
                                    <div class="col-md-6">
                                        <button id="btn-filtrar-fechas" class="btn btn-primary">Buscar</button>
                                    </div>
                                </div>

                            </div>
                            <table id="empleados_data_siesa" name="empleados_data_siesa" class="table table-bordered table-striped table-vcenter js-dataTable-full">
                                <thead class="bg-info">
                                    <tr>
                                        <th th class="text-center" style="width: 6%;">CÉDULA</th>
                                        <th th class="text-center" style="width: 10%">NOMBRES</th>
                                        <th th class="text-center" style="width: 10%;">PRIMER APELLIDO</th>
                                        <th th class="text-center" style="width: 10%;">SEGUNDO APELLIDO</th>
                                        <th th class="text-center" style="width: 8%;">FECHA INGRESO</th>
                                        <th th class="text-center" style="width: 8%;">FECHA NACIMIENTO</th>
                                        <th th class="text-center" style="width: 10%;">DIRECCION</th>
                                        <th th class="text-center" style="width: 7%;">CORREO</th>
                                        <th th class="text-center" style="width: 7%;">TELEFONO</th>
                                        <th th class="text-center" style="width: 7%;">ESTADO</th>
                                        <th th class="text-center" style="width: 8%;">ACCIONES</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- MAIN -->
                <aside class="control-sidebar control-sidebar-dark">
                </aside>
            </div>
            <?php require_once("../MainFooter/footer.php") ?>
        </div>

        <?php require_once("../MainJS/JS.php") ?>
        <script type="text/javascript" src="crudEmplSiesa.js"></script>
    </body>

    </html>
<?php
} else {
    header("Location:" . Conectar::ruta() . "Pagina404.php");
}
?>