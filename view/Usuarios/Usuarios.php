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
    <title>Usuarios</title>
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
            <h1 class="m-0">USUARIOS</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="../inicio/inicio.php">Inicio</a></li>
                <li class="breadcrumb-item active">Usuarios</li>
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
              <button type="button" id="btnnuevouser"  class="btn btn-info">Nuevo Usuario</button>
              <br><br>
              <table id="user_data" name="user_data" class="table table-bordered table-striped table-vcenter js-dataTable-full">
                <thead class="bg-info">
                  <tr>
                    <th th class="text-center"  style="width: 2%;">CÓDIGO</th>
                    <th th class="text-center"  style="width: 15%;">EMPLEADO</th>
                    <th th class="text-center"  style="width: 10%;">USUARIO</th>
                    <th th class="text-center"  style="width: 10%;">CARGO</th>
                    <th th class="text-center"  style="width: 6%;">ACCIONES</th>
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
  
<?php require_once ("crudUsuarios.php")?>
<?php require_once ("Clave.php")?>
<?php require_once ("../MainJS/JS.php")?>
<script type="text/javascript" src="crudUsuarios.js?v=<?php echo filemtime(__DIR__ . '/crudUsuarios.js'); ?>"></script>
</body>
</html>
<?php
} else {
    header("Location:" . Conectar::ruta() . "Pagina404.php");
}
?>
