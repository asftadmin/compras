<?php
require_once "../../config/conexion.php";
if (isset($_SESSION["user_id"])) {
?>
<!DOCTYPE html>
<html lang="es">
    <?php require_once("../MainHead/head.php");?>
    <link rel="stylesheet" href="../../public/css/inicio.css">
    <link rel="shortcut icon"  href="../../public/img/Asfaltart.ico">
    <title>Proveedores App</title>
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
            <h1 class="m-0">PROVEEDORES</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="../inicio/inicio.php">Inicio</a></li>
                <li class="breadcrumb-item"><a href="#">Proveedores</a></li>
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
              <button type="button" id="btnnuevoprov"  class="btn btn-info">Nuevo Proveedores</button>
              <br><br>
              <table id="proveedor_data" name="proveedor_data" class="table table-bordered table-striped table-vcenter js-dataTable-full">
                <thead class="bg-info">
                  <tr>
                    <th th class="text-center"  style="width: 8%;">CODIGO</th>
                    <th th class="text-center"  style="width: 8%;">ID SIESA</th>
                    <th th class="text-center"  style="width: 8%;">NIT</th>
                    <th th class="text-center"  style="width: 15%;">DESCRIPCION</th>
                    <th th class="text-center"  style="width: 10%;">ESTADO</th>
                    <th th class="text-center"  style="width: 10%;">LINEA</th>
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
  
<?php require_once ("crudProveedores.php")?>
<?php require_once ("../MainJS/JS.php")?>
<script type="text/javascript" src="crudProveedores.js"></script>
</body>
</html>
<?php
} else {
    header("Location:" . Conectar::ruta() . "Pagina404.php");
}
?>
