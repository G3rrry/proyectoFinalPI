<h2 class="mb-4 border-bottom pb-2">Catálogo de Productos</h2>
<div class="row">
    <?php
    // CONSULTA REAL A LA BASE DE DATOS
    $sql = "SELECT * FROM Productos";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($prod = $result->fetch_assoc()) {
            $id = $prod['id_producto'];
            $nombre = htmlspecialchars($prod['nombre']);
            $desc = htmlspecialchars($prod['descripcion']);
            $precio = $prod['precio'];
            $stock = $prod['cantidad_en_almacen'];
            
            // Lógica de stock
            $enStock = $stock > 0;
            $estado = $enStock ? 'En Stock: ' . $stock : 'Agotado';
            $colorBadge = $enStock ? 'success' : 'danger';
            $btnDisabled = $enStock ? '' : 'disabled';
    ?>
        <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
            <div class="card h-100 product-card shadow-sm">
                <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 200px;">
                    <i class="bi bi-box-seam display-4"></i>
                </div>
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><?php echo $nombre; ?></h5>
                    <p class="card-text text-muted small text-truncate"><?php echo $desc; ?></p>
                    
                    <div class="mt-auto">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fs-5 fw-bold">$<?php echo number_format($precio, 2); ?></span>
                            <span class="badge bg-<?php echo $colorBadge; ?>"><?php echo $estado; ?></span>
                        </div>

                        <form action="index.php?page=carrito" method="POST">
                            <input type="hidden" name="action" value="agregar">
                            <input type="hidden" name="id_producto" value="<?php echo $id; ?>">
                            
                            <div class="input-group mb-2">
                                <span class="input-group-text small">Cant.</span>
                                <input type="number" name="cantidad" value="1" min="1" max="<?php echo $stock; ?>" class="form-control" <?php echo $btnDisabled; ?>>
                            </div>

                            <button type="submit" class="btn btn-primary w-100" <?php echo $btnDisabled; ?>>
                                <i class="bi bi-cart-plus"></i> Agregar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php 
        } 
    } else {
        echo "<div class='alert alert-info'>No hay productos disponibles en este momento.</div>";
    }
    ?>
</div>