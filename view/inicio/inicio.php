<?php
require_once "../../config/conexion.php";
if (isset($_SESSION["user_id"])) {
    $anioActual = (int) date('Y');
?>
<!DOCTYPE html>
<html lang="es">
<?php require_once "../MainHead/head.php"; ?>
<link rel="stylesheet" href="../../public/css/inicio.css">
<link rel="shortcut icon" href="../../public/img/Asfaltart.ico">
<style>
    .dashboard-filter-card .form-group label {
        font-size: .82rem;
        font-weight: 600;
        color: #495057;
        margin-bottom: .35rem;
    }

    .dashboard-total-card {
        border-left: 4px solid #17a2b8;
        min-height: 92px;
    }

    .dashboard-total-card .card-body {
        padding: .85rem 1rem;
    }

    .dashboard-total-card .total-label {
        color: #6c757d;
        font-size: .82rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .dashboard-total-card .total-value {
        color: #212529;
        font-size: 1.45rem;
        font-weight: 700;
        line-height: 1.25;
        margin-top: .2rem;
    }

    .dashboard-chart-scroll {
        overflow-x: auto;
        overflow-y: hidden;
        width: 100%;
    }

    .dashboard-chart-box {
        height: 470px;
        min-width: 920px;
        position: relative;
    }

    .dashboard-empty {
        align-items: center;
        color: #6c757d;
        display: flex;
        height: 260px;
        justify-content: center;
        text-align: center;
    }
</style>
<title>Dashboard Compras</title>
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <?php require_once "../MainNav/nav.php"; ?>
        <?php require_once "../MainMenu/menu.php"; ?>

        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Dashboard Compras</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item active">Inicio</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-4 col-md-5">
                            <div class="card dashboard-filter-card">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-filter mr-1"></i>
                                        Filtros
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="linea_dashboard">Linea</label>
                                        <select id="linea_dashboard" class="form-control">
                                            <option value="">Seleccione una linea</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="anios_comparativo">Anios a comparar</label>
                                        <select id="anios_comparativo" class="form-control" multiple>
                                            <?php for ($anio = $anioActual; $anio >= $anioActual - 6; $anio--) { ?>
                                                <option value="<?php echo $anio; ?>" <?php echo ($anio >= $anioActual - 2) ? 'selected' : ''; ?>>
                                                    <?php echo $anio; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <button id="btn_consultar_compras" type="button" class="btn btn-info btn-block">
                                        <i class="fas fa-search mr-1"></i>
                                        Consultar
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-8 col-md-7">
                            <div id="indicadores_compras" class="row"></div>

                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-chart-bar mr-1"></i>
                                        Compras por proveedor
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div id="mensaje_compras_inicial" class="dashboard-empty">
                                        Seleccione una linea y consulte las compras.
                                    </div>

                                    <div id="contenedor_grafico_compras" class="dashboard-chart-scroll d-none">
                                        <div id="grafico_compras_box" class="dashboard-chart-box">
                                            <canvas id="grafico_compras_anios"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <?php require_once "../MainFooter/footer.php" ?>
    </div>

    <?php require_once "../MainJS/JS.php" ?>
    <script type="text/javascript" src="inicio.js"></script>
</body>
</html>
<?php
} else {
    header("location:" . Conectar::ruta() . "index.php");
}
?>
