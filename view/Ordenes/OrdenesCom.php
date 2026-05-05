<?php
require_once "../../config/conexion.php";
if (isset($_SESSION["user_id"])) {
?>
<!DOCTYPE html>
<html lang="es">
<?php require_once("../MainHead/head.php"); ?>
<link rel="stylesheet" href="../../public/css/inicio.css">
<link rel="shortcut icon" href="../../public/img/Asfaltart.ico">
<title>Ordenes de Compra Siesa</title>
</head>

<body class="hold-transition sidebar-mini">

    <div class="wrapper">
        <?php require_once("../MainNav/nav.php"); ?>
        <?php require_once("../MainMenu/menu.php"); ?>
        <div class="content-wrapper">
            <!-- HEADER -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Ordenes Siesa</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../inicio/inicio.php">Inicio</a></li>
                                <li class="breadcrumb-item active">Ordenes de Compra</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>
            <!-- /.HEADER -->

            <!-- MAIN -->
            <section class="content">

                <div class="card">

                    <div class="card-header">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <select id="linea" class="form-control">
                                        <option value="">Seleccione una línea</option>
                                        <!-- Opciones se llenarán dinámicamente -->
                                    </select>
                                </div>

                            </div>
                            <div class="col-md-4">
                                <input type="text" id="rangoFechas" class="form-control"
                                    placeholder="Seleccione rango de fechas" readonly>
                            </div>
                            <div class="col-md-2">
                                <button id="btn-filtrar-ordenes" class="btn btn-primary">Filtrar</button>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="chart-container" style="position: relative;">
                            <p id="mensajeInicial"
                                style="text-align:center; margin-top:80px; color:gray; font-size: 1.1rem;">
                                Seleccione una línea y presione <b>“Filtrar”</b> para ver las compras.
                            </p>
                            <canvas id="graficoCompras" style="display:none; max-height:300px;"></canvas>
                        </div>


                    </div>
                </div>


            </section>

        </div>
        <?php require_once("../MainFooter/footer.php") ?>
        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
    </div>

    <?php require_once("../MainJS/JS.php") ?>
    <script type="text/javascript" src="OrdenesCom.js"></script>
</body>

</html>
<?php
} else {
    header("Location:" . Conectar::ruta() . "Pagina404.php");
}
?>