<?php
require_once("config/conexion.php");
if (isset($_POST["ingreso"]) and $_POST["ingreso"] == "si") {
  require_once("models/Usuario.php");
  $usuario = new Usuario();
  $usuario->login();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <title>Login</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="public/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css" />
  <link href="public/css/login.css" rel="stylesheet">
  <link rel="shortcut icon"  href="public/img/Asfaltart.ico">
</head>

<body>

  <div class="container" id="container">
    <div class="form">
      <div class="form login">
        <img src="public/img/logo.png" class="logo" alt="ASFALTART S.A.S" />
          <h3 class="title" id="lbltitulo">Control de Proveedores</h3> 
        <!--1 VALOR DEL USUARIO -->
        <form action="" method="post" id="login_form">
          <input type="hidden" name="user_rol_usuario" id="user_rol_usuario" value="1" />
          <?php
          if (isset($_GET["m"])) {
            switch ($_GET["m"]) {
              case "1";
          ?>
                <div class="alert alert-danger alert-icon alert-close alert-dismissible fade in" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                  </button>
                  <i class="fas fa-exclamation-triangle"></i>
                  <strong>Error de autenticación:</strong> Usuario y/o contraseña incorrectos. Por favor, inténtalo nuevamente
                </div>
              <?php
                break;
              case "2";
              ?>
                <div class="alert alert-info alert-icon alert-close alert-dismissible fade in" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                  </button>
                  <i class="fas fa-bullhorn"></i>
                  <strong>Campos vacíos:</strong> Por favor, completa todos los campos obligatorios para acceder.
                </div>

              <?php
                break;

              case "3";
              ?>

          <?php
                break;
            }
          }
          ?>
          <div class="form-group">
            <div class="row">
              <i class="fas fa-user"></i>
              <input type="text" name="usuario" id="usuario" placeholder="Usuario" class="form-control" minlength="2" maxlength="50">

            </div>
          </div>
          <div class="form-group">
            <div class="row">
              <i class="fas fa-lock"></i>
              <input type="password" name="password" id="password" placeholder="Contraseña" class="form-control" minlength="6" maxlength="50" title="La contraseña debe contener al menos 6 caracteres, incluyendo al menos una letra y un número.">
              <div class="toggle-password">
                <i id="togglePassword" class="fas fa-eye-slash"></i>
              </div>
            </div>
          </div>
          <div>
            <div class="form-group text-center" id="register-link">
              <a href="view/ResetPassword/ResetPass.php" class="text-primary">¿Haz olvidado tu clave?</a>
            </div>
          </div>
            <div class="form-group text-center">
              <input type="hidden" name="ingreso" class="btn btn-info btn-block" value="si">
              <input type="submit" class="btn btn-info btn-block" value="Entrar">
            </div>
          </div>
        </form>

      </div>
    </div>
  </div>

  <script src="public/plugins/jquery/jquery.min.js"></script>
  <script src="public/plugins/bootstrap/js/bootstrap.min.js"></script>

  <script>
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const togglePasswordContainer = document.querySelector('.toggle-password');

    togglePassword.addEventListener('click', function() {
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);
      togglePassword.classList.toggle('fa-eye-slash');
      togglePassword.classList.toggle('fa-eye');
    });

    passwordInput.addEventListener('input', function() {
      if (passwordInput.value.trim() !== '') {
        togglePasswordContainer.style.visibility = 'visible';
      } else {
        togglePasswordContainer.style.visibility = 'hidden';
      }
    });
  </script>
  <script src="index.js"></script>

</body>

</html>
