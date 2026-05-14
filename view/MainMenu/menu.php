<?php
require_once("../../config/conexion.php");
require_once("../../models/Menu.php");

$menuModel = new Menu();
$roles     = $_SESSION["user_roles"] ?? [];
$jerarquia = $menuModel->get_menu_por_roles($roles);
?>

<head>
    <link rel="stylesheet" href="../../public/css/inicio.css">
</head>

<aside class="main-sidebar elevation-4">
    <div class="sidebar">

        <!-- logo -->
        <div class="user-panel mt-5 pb-5 mb-3 d-flex">
            <div class="image">
                <img src="../../public/img/logo-horizontal.svg" alt="ASFALTAR S.A.S">
            </div>
        </div>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column"
                data-widget="treeview" role="menu" data-accordion="false">

                <!-- inicio siempre visible -->
                <li class="nav-item">
                    <a href="<?php echo Conectar::ruta(); ?>view/inicio/inicio.php" class="nav-link">
                        <i class="nav-icon fas fa-home"></i>
                        <p>Inicio</p>
                    </a>
                </li>

                <?php foreach ($jerarquia as $grupo => $padres): ?>

                    <li class="nav-header">
                        <?php echo strtoupper(htmlspecialchars($grupo)); ?>
                    </li>

                    <?php foreach ($padres as $padre): ?>

                        <?php if (empty($padre['hijos'])): ?>

                            <li class="nav-item">
                                <a href="<?php echo Conectar::ruta() . $padre['menu_ruta']; ?>" class="nav-link">
                                    <i class="nav-icon <?php echo $padre['menu_icono']; ?>"></i>
                                    <p><?php echo htmlspecialchars($padre['menu_nom']); ?></p>
                                </a>
                            </li>

                        <?php else: ?>

                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon <?php echo $padre['menu_icono']; ?>"></i>
                                    <p>
                                        <?php echo htmlspecialchars($padre['menu_nom']); ?>
                                        <i class="fas fa-angle-left right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <?php foreach ($padre['hijos'] as $hijo): ?>
                                        <li class="nav-item">
                                            <a href="<?php echo Conectar::ruta() . $hijo['menu_ruta']; ?>" class="nav-link">
                                                <i class="nav-icon <?php echo $hijo['menu_icono']; ?>"></i>
                                                <p><?php echo htmlspecialchars($hijo['menu_nom']); ?></p>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>

                        <?php endif; ?>

                    <?php endforeach; ?>

                <?php endforeach; ?>

            </ul>
        </nav>
    </div>
</aside>