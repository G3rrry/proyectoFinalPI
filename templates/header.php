<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($titulo) ? $titulo : 'Tienda Online'; ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Iconos de Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        .product-card img {
            height: 200px;
            object-fit: cover;
        }
        footer {
            margin-top: auto;
        }
    </style>
</head>
<body>

    <!-- Barra de Navegación -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php?page=catalogo">
                <i class="bi bi-shop"></i> E-Shop Pro
            </a>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=catalogo">Catálogo</a>
                    </li>
                    </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> 
                            <?php 
                            // Mostrar nombre si está logueado, si no "Cuenta"
                            echo isset($_SESSION['nombre_usuario']) ? htmlspecialchars($_SESSION['nombre_usuario']) : 'Cuenta'; 
                            ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php if (isset($_SESSION['id_usuario'])): ?>
                                <li><a class="dropdown-item" href="index.php?page=perfil">Mi Perfil</a></li>
                                <li><a class="dropdown-item" href="index.php?page=historial">Historial</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="index.php?page=logout">Cerrar Sesión</a></li>
                            <?php else: ?>
                                <li><a class="dropdown-item" href="index.php?page=login">Iniciar Sesión</a></li>
                                <li><a class="dropdown-item" href="index.php?page=registro">Registrarse</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                    
                    </ul>
            </div>
        </div>
    </nav>

    <!-- Contenedor Principal -->
    <div class="container py-4"></div>