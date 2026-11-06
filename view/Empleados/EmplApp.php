<?php
require_once "../../config/conexion.php";
if (isset($_SESSION["user_id"])) {
    header('Cache-Control: no-store');
?>
<!DOCTYPE html>
<html lang="es">
    <?php require_once("../MainHead/head.php");?>
    <link rel="stylesheet" href="../../public/css/inicio.css">
    <link rel="shortcut icon"  href="../../public/img/Asfaltart.ico">
    <title>Empleados App</title>
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
            <h1 class="m-0">EMPLEADOS</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="../inicio/inicio.php">Inicio</a></li>
                <li class="breadcrumb-item"><a href="#">Empleados</a></li>
                <li class="breadcrumb-item active">App</li>
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
              <button type="button" id="btnnuevoemplapp"  class="btn btn-info">Nuevo Empleado</button>
              <br><br>
              <table id="app_data" name="app_data" class="table table-bordered table-striped table-vcenter js-dataTable-full">
                <thead class="bg-info">
                  <tr>
                    <th th class="text-center"  style="width: 2%;">CÓDIGO</th>
                    <th th class="text-center"  style="width: 15%;">NOMBRES</th>
                    <th th class="text-center"  style="width: 15%;">APELLIDOS</th>
                    <th th class="text-center"  style="width: 8%;">CEDULA</th>
                    <th th class="text-center"  style="width: 8%;">TELEFONO</th>
                    <th th class="text-center"  style="width: 10%;">EMAIL</th>
                    <th th class="text-center"  style="width: 12%;">DIRECCION</th>
                    <th th class="text-center"  style="width: 7%;">ESTADO</th>
                    <th th class="text-center"  style="width: 8%;">ROL</th>
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
  
<?php require_once ("crudEmplApp.php")?>
<?php require_once ("../MainJS/JS.php")?>
<script type="text/javascript" src="crudEmplApp.js?v=<?php echo filemtime(__DIR__ . '/crudEmplApp.js'); ?>"></script>
</body>
</html>
<?php
} else {
    header("Location:" . Conectar::ruta() . "Pagina404.php");
}
?>
