<?php
require_once "../../config/conexion.php";
if (isset($_SESSION["user_id"])) {
?>
<!DOCTYPE html>
<html lang="es">
    <?php require_once("../MainHead/head.php");?>
    <link rel="stylesheet" href="../../public/css/inicio.css">
    <link rel="shortcut icon"  href="../../public/img/Asfaltart.ico">
    <title>Linea de Produccion</title>
</head>

<body class="hold-transition sidebar-mini">

  <div class="wrapper">
      <?php require_once("../MainNav/nav.php");?>
      <?php require_once("../MainMenu/menu.php"); ?>
    <div class="content-wrapper">
      <!-- HEADER -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
            <h1 class="m-0">LINEA DE PRODUCCION</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="../inicio/inicio.php">Inicio</a></li>
                <li class="breadcrumb-item"><a href="#">Linea de Produccion</a></li>
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
              <button type="button" id="btnnuevalinea"  class="btn btn-info">Nueva Linea de Produccion</button>
              <br><br>
              <table id="linea_data" name="linea_data" class="table table-bordered table-striped table-vcenter js-dataTable-full">
                <thead class="bg-info">
                  <tr>
                    <th th class="text-center"  style="width: 8%;">CODIGO</th>
                    <th th class="text-center"  style="width: 15%;">NOMBRE</th>
                    <th th class="text-center"  style="width: 8%;">ACCIONES</th>
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
    <?php require_once ("../MainFooter/footer.php")?>
  </div>
  
<?php require_once ("crudLinea.php")?>
<?php require_once ("../MainJS/JS.php")?>
<script type="text/javascript" src="crudLinea.js"></script>
</body>
</html>
<?php
} else {
    header("Location:" . Conectar::ruta() . "Pagina404.php");
}
?>
