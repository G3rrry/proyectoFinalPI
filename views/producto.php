<?php
// views/producto.php

// 1. Validar que llegue un ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>window.location='index.php?page=catalogo';</script>";
    exit;
}

$id_producto = intval($_GET['id']);

// 2. Obtener datos del producto y sus fotos
// Usamos LEFT JOIN para traer las fotos si existen
$sql = "SELECT p.*, f.foto1, f.foto2, f.foto3, f.foto4, f.foto5 
        FROM Productos p 
        LEFT JOIN Fotos_Producto f ON p.id_fotos = f.id_fotos 
        WHERE p.id_producto = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_producto);
$stmt->execute();
$result = $stmt->get_result();
$producto = $result->fetch_assoc();

// Si no existe el producto
if (!$producto) {
    echo "<div class='container py-5'><div class='alert alert-danger'>Producto no encontrado.</div></div>";
    exit;
}

// 3. Preparar las imágenes para el Carrusel
$imagenes = [];
for ($i = 1; $i <= 5; $i++) {
    if (!empty($producto["foto$i"])) {
        $imagenes[] = base64_encode($producto["foto$i"]);
    }
}

// Variables de Stock
$stock = $producto['cantidad_en_almacen'];
$enStock = $stock > 0;
$btnDisabled = $enStock ? '' : 'disabled';
?>

<div class="container mb-5">
    <a href="index.php?page=catalogo" class="btn btn-outline-secondary mb-4">
        <i class="bi bi-arrow-left"></i> Volver al Catálogo
    </a>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body p-2">
                    <?php if (count($imagenes) > 0): ?>
                        <div id="carouselProducto" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-indicators">
                                <?php foreach ($imagenes as $index => $img): ?>
                                    <button type="button" data-bs-target="#carouselProducto" data-bs-slide-to="<?php echo $index; ?>" class="<?php echo $index === 0 ? 'active' : ''; ?>"></button>
                                <?php endforeach; ?>
                            </div>

                            <div class="carousel-inner rounded">
                                <?php foreach ($imagenes as $index => $img): ?>
                                    <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                        <img src="data:image/jpeg;base64,<?php echo $img; ?>" class="d-block w-100" style="height: 500px; object-fit: contain; background-color: #f8f9fa;" alt="Imagen Producto">
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <?php if (count($imagenes) > 1): ?>
                                <button class="carousel-control-prev" type="button" data-bs-target="#carouselProducto" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon bg-dark rounded-circle p-2" aria-hidden="true"></span>
                                    <span class="visually-hidden">Anterior</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#carouselProducto" data-bs-slide="next">
                                    <span class="carousel-control-next-icon bg-dark rounded-circle p-2" aria-hidden="true"></span>
                                    <span class="visually-hidden">Siguiente</span>
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="d-flex align-items-center justify-content-center bg-secondary text-white rounded" style="height: 500px;">
                            <div class="text-center">
                                <i class="bi bi-book display-1"></i>
                                <p class="mt-2">Sin imagen disponible</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if (count($imagenes) > 1): ?>
            <div class="d-flex mt-2 justify-content-center gap-2">
                <?php foreach ($imagenes as $index => $img): ?>
                    <img src="data:image/jpeg;base64,<?php echo $img; ?>" 
                         onclick="document.querySelector('[data-bs-slide-to=\'<?php echo $index; ?>\']').click()" 
                         class="border rounded" 
                         style="width: 60px; height: 60px; object-fit: cover; cursor: pointer;">
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <div class="col-md-6">
            <h1 class="display-5 fw-bold mb-3"><?php echo htmlspecialchars($producto['nombre']); ?></h1>
            
            <div class="d-flex align-items-center mb-3">
                <h2 class="text-primary fw-bold me-3 mb-0">$<?php echo number_format($producto['precio'], 2); ?></h2>
                <?php if ($enStock): ?>
                    <span class="badge bg-success fs-6">En Stock (<?php echo $stock; ?> disponibles)</span>
                <?php else: ?>
                    <span class="badge bg-danger fs-6">Agotado</span>
                <?php endif; ?>
            </div>

            <p class="lead text-muted"><?php echo nl2br(htmlspecialchars($producto['descripcion'])); ?></p>

            <hr>

            <div class="row mb-4">
                <div class="col-6">
                    <small class="text-muted fw-bold text-uppercase">Fabricante / Editorial</small>
                    <p><?php echo htmlspecialchars($producto['fabricante'] ?: 'No especificado'); ?></p>
                </div>
                <div class="col-6">
                    <small class="text-muted fw-bold text-uppercase">Origen</small>
                    <p><?php echo htmlspecialchars($producto['origen'] ?: 'No especificado'); ?></p>
                </div>
            </div>

            <div class="card bg-light border-0 p-3">
                <form action="index.php?page=carrito" method="POST">
                    <input type="hidden" name="action" value="agregar">
                    <input type="hidden" name="id_producto" value="<?php echo $id_producto; ?>">
                    
                    <div class="row g-2 align-items-end">
                        <div class="col-4">
                            <label class="form-label fw-bold">Cantidad</label>
                            <input type="number" name="cantidad" value="1" min="1" max="<?php echo $stock; ?>" class="form-control" <?php echo $btnDisabled; ?>>
                        </div>
                        <div class="col-8">
                            <button type="submit" class="btn btn-primary w-100 py-2" <?php echo $btnDisabled; ?>>
                                <i class="bi bi-cart-plus me-2"></i> Agregar al Carrito
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="mt-4 small text-muted">
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-shield-check fs-4 me-2 text-success"></i>
                    <span>Compra protegida y segura.</span>
                </div>
                <div class="d-flex align-items-center">
                    <i class="bi bi-truck fs-4 me-2 text-primary"></i>
                    <span>Envío disponible a todo el país.</span>
                </div>
            </div>
        </div>
    </div>
</div>