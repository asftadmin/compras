<?php require_once ("../../config/conexion.php"); ?>
<!DOCTYPE html>
<html lang="es">
<?php require_once("../MainHead/head.php");?>
<link rel="stylesheet" type="text/css" href="../../public/css/perfil.css">
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <?php require_once("../MainNav/nav.php");?>
        <?php require_once("../MainMenu/menu.php"); ?>

        <div class="content-wrapper">
            <!-- HEADER -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Perfil</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item">Perfil</li>
                                <li class="breadcrumb-item active"><a href="#">Cambiar Contraseña</a></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>
            <!-- HEADER -->

            <!-- MAIN -->
            <section class="content" >
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12" >
                            <div class="card-info" >
                                <div class="card-header mt-2" >
                                    <h3 class="card-title" >Cambiar Contraseña</h3>
                                </div>
                                    <div class="card-body p-3">
                                        <div class="form-group">
                                            <label for="rol_cargo">Nueva Contraseña:</label>
                                            <div class="input-group-append">
                                                <input type="password" class="form-control mt-2" name="txtpass" id="txtpass" maxlength="20" minlength="6"  title="La contraseña debe tener al menos 6 caracteres, incluyendo al menos una letra mayúscula, una letra minúscula y numeros." placeholder="**********************" required >
                                                <button class="btn btn-outline-secondary mt-2" type="button" style=" border: none;"  id="togglePassword1">
                                                
                                                    <i class="fa fa-eye" id="eye1"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="rol_cargo">Confirmar Contraseña:</label>
                                            <div class="input-group-append">
                                                <input type="password" class="form-control mt-2"  name="txtpassnew" id="txtpassnew" maxlength="20" minlength="6" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{6,}$" title="La contraseña debe tener al menos 6 caracteres, incluyendo al menos una letra mayúscula, una letra minúscula y numeros." placeholder="**********************" >
                                                <button class="btn btn-outline-secondary mt-2" style=" border: none;" type="button" id="togglePassword2">
                                                    <i class="fa fa-eye" id="eye2"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                <div class="card-footer">
                                    <button type="button" name="action" id="btnactualizar"  class="btn btn-warning"> <i class="fas fa-edit"></i> Cambiar</button>
                                </div>
                            </div>
                        </div>
                    </div>
            </section>
            <!-- /.content -->

            <aside class="control-sidebar control-sidebar-dark">
            </aside>
        </div>
            <!-- Main Footer -->
            <?php require_once ("../MainFooter/footer.php")?>
    </div>
        <?php require_once ("../MainJS/JS.php")?>
        <script type="text/javascript" src="Perfil.js"></script>

        <script>
            document.getElementById('togglePassword1').addEventListener('click', function () {
                togglePasswordVisibility('txtpass', 'eye1');
            });

            document.getElementById('togglePassword2').addEventListener('click', function () {
                togglePasswordVisibility('txtpassnew', 'eye2');
            });

            function togglePasswordVisibility(inputId, eyeId) {
                const passwordInput = document.getElementById(inputId);
                const eyeIcon = document.getElementById(eyeId);
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    eyeIcon.classList.remove('fa-eye');
                    eyeIcon.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    eyeIcon.classList.remove('fa-eye-slash');
                    eyeIcon.classList.add('fa-eye');
                }
            }
        </script>
</body>
</html>