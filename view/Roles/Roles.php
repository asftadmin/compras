<?php
require_once "../../config/conexion.php";
if (isset($_SESSION["user_id"])) {
?>
<!DOCTYPE html>
<html lang="es">
    <?php require_once("../MainHead/head.php");?>
    <link rel="stylesheet" href="../../public/css/tablas.css">
    <link rel="shortcut icon"  href="../../public/img/Asfaltart.ico">
    <title>Gestion Roles</title>
</head>


<body class="hold-transition sidebar-mini">

  <div class="wrapper">
    <?php require_once("../MainNav/nav.php");?>
    <?php require_once("../MainMenu/menu.php"); ?>
    <div class="content-wrapper">
      <!-- HEADER-->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">ROLES</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="../inicio/inicio.php">Inicio</a></li>
                <li class="breadcrumb-item active">Roles</li>
              </ol>
            </div>
          </div>
        </div>
      </div>
      <!-- /.HEADER -->

      <!-- MAIN-->
      <div class="content">
        <div class="container-fluid">
        <div class="box-typical box-typical-padding">
              <button type="button" id="btnnuevorol"  class="btn btn-info">Nuevo Rol</button>
              <br><br>
            <table id="rol_data" name="rol_data" class="table table-bordered table-striped table-vcenter js-dataTable-full" >
              <thead class="bg-info">
                <tr class="text-center">
                  <th th class="text-center" style="width: 4%;">CÓDIGO</th>
                  <th th class="text-center" style="width: 10%;">NOMBRE DEL ROL</th>
                  <th th class="text-center"  style="width: 2%;">ACCIONES</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
      </div>
    </div>
    <!-- /.MAIN -->

    <aside class="control-sidebar control-sidebar-dark">
    </aside>
    </div>
    <?php require_once ("../MainFooter/footer.php")?>
  </div>
  <?php require_once ("crudRoles.php")?>
  <?php require_once ("crudPermisos.php")?>
<?php require_once ("../MainJS/JS.php")?>
<script type="text/javascript" src="crudRoles.js"></script>

</body>
</html>
<?php
} else {
    header("Location:" . Conectar::ruta() . "Pagina404.php");
}
?>

