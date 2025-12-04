<?php
// templates/header.php

// Lógica del carrito
$cart_count = 0;
if (isset($_SESSION['id_usuario']) && isset($conn)) {
    $uid = $_SESSION['id_usuario'];
    $sql_count = "SELECT SUM(dc.cantidad) as total FROM Carritos c JOIN Detalle_Carrito dc ON c.id_carrito = dc.id_carrito WHERE c.id_usuario = ?";
    $stmt_c = $conn->prepare($sql_count);
    $stmt_c->bind_param("i", $uid);
    $stmt_c->execute();
    $res_c = $stmt_c->get_result();
    if ($row_c = $res_c->fetch_assoc()) {
        $cart_count = $row_c['total'] ? $row_c['total'] : 0;
    }
}
?>
<!DOCTYPE html>
<html lang="es" class="h-100"> <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($titulo) ? $titulo : 'Tienda Online'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        /* Eliminamos los estilos manuales del body para usar clases de Bootstrap */
        .product-card img { height: 200px; object-fit: cover; }
    </style>
</head>

<body class="d-flex flex-column min-vh-100 bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php?page=catalogo">
                <i class="bi bi-book"></i> Librerías Melo
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($pagina == 'catalogo') ? 'active' : ''; ?>" href="index.php?page=catalogo">Catálogo</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($pagina == 'contacto') ? 'active' : ''; ?>" href="index.php?page=contacto">Contacto</a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> 
                            <?php echo isset($_SESSION['nombre_usuario']) ? htmlspecialchars($_SESSION['nombre_usuario']) : 'Cuenta'; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php if (isset($_SESSION['id_usuario'])): ?>
                                <li><a class="dropdown-item" href="index.php?page=perfil">Mi Perfil</a></li>
                                <li><a class="dropdown-item" href="index.php?page=historial">Historial de Compras</a></li>
                                <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] == 'admin'): ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item fw-bold" href="index.php?page=admin"><i class="bi bi-shield-lock"></i> Panel Admin</a></li> 
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="index.php?page=logout">Cerrar Sesión</a></li>
                            <?php else: ?>
                                <li><a class="dropdown-item" href="index.php?page=login">Iniciar Sesión</a></li>
                                <li><a class="dropdown-item" href="index.php?page=registro">Registrarse</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?php echo ($pagina == 'carrito') ? 'active' : ''; ?>" href="index.php?page=carrito">
                            <i class="bi bi-cart4"></i> 
                            <span class="badge bg-danger rounded-pill"><?php echo $cart_count; ?></span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-4 flex-grow-1">