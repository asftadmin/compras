<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>
    <ul class="navbar-nav">
        <input type="hidden" id="empl_idx" value="<?php echo $_SESSION["empl_id"] ?>">
        <li class="nav-item">
            <div class="dropdown dropdown-typical"><span class="fas fa-user-alt" style="color:#17a2b8"></span> <span class="lblcontactonomx"><?php echo $_SESSION["empl_nom"] ?> <?php echo $_SESSION["empl_apellido"] ?></span></div>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto">
        <li class="nav-item">
            <div class="site-header-shown">
                <div class="dropdown user-menu">
                    <button class="dropdown-toggle border-0 pl-3 pr-2 pt-1 pb-1" id="dd-user-menu" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img src="../../public/img/perfil.png" alt="usuario">
                    </button>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dd-user-menu">
                        <a class="dropdown-item" href="../MntPerfil/Perfil.php"><span class="fas fa-user "></span>&nbsp;Perfil</a>
                        <a class="dropdown-item" href="#"><span class="fas fa-question-circle"></span>&nbsp;Ayuda</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item bg-danger" href="../Logout/logout.php"><span class="fas fa-sign-out-alt"></span>&nbsp;Cerrar Sesion</a>
                    </div>
                </div>
            </div>
        </li>
    </ul>
</nav>