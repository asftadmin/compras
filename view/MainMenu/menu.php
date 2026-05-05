<head>
    <link rel="stylesheet" href="../../public/css/inicio.css">
</head>

<aside class="main-sidebar elevation-4">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="user-panel mt-5 pb-5 mb-3 d-flex">
            <div class="image">
                <img src="../../public/img/logo-horizontal.svg" alt="ASFALTAR S.A.S">
            </div>
        </div>
        <!-- INICIO -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                <li class="nav-item">
                    <a href="../inicio/inicio.php" class="nav-link">
                        <i class="nav-icon fas fa-home"></i>
                        <p>Inicio</p>
                    </a>
                </li>

                <!-- Listado del Mantenedor -->
                <li class="nav-item">
                    <a href="../Roles/Roles.php" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Roles</p>
                    </a>
                </li>
                <!-- Listado del Mantenedor -->
                <li class="nav-item">
                    <a href="../Usuarios/Usuarios.php" class="nav-link">
                        <i class="nav-icon fas fa-user-alt"></i>
                        <p>Usuarios</p>
                        </p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon 	fas fa-briefcase"></i>
                        <p>
                            Empleados
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="../Empleados/EmplSiesa.php" class="nav-link">
                                <i class="nav-icon fas fa-angle-right "></i>
                                <p>SIESA</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="../Empleados/EmplApp.php" class="nav-link">
                                <i class="nav-icon fas fa-angle-right "></i>
                                <p>APP</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon 	fas fa-shipping-fast"></i>
                        <p>
                            Proveedores
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="../Proveedores/ProvSiesa.php" class="nav-link">
                                <i class="nav-icon fas fa-angle-right "></i>
                                <p>SIESA</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="../Proveedores/Proveedores.php" class="nav-link">
                                <i class="nav-icon fas fa-angle-right "></i>
                                <p>APP</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="../Linea/Linea.php" class="nav-link">
                        <i class="nav-icon fas fa-chart-area"></i>
                        <p>Linea</p>
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../Ordenes/OrdenesCom.php" class="nav-link">
                        <i class="nav-icon fas fa-money-bill"></i>
                        <p>Ordenes Compra</p>
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../Evaluaciones/evaluaciones.php" class="nav-link">
                        <i class="nav-icon far fa-question-circle"></i>
                        <p>Evaluaciones Prov</p>
                        </p>
                    </a>
                </li>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>