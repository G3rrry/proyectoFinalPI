<h2 class="mb-4 border-bottom pb-2">Catálogo de Productos</h2>
<div class="row">
    <?php
    // Simulamos 30 productos con un ciclo PHP
    // En el futuro, aquí harás: foreach($productos as $producto) { ... }
    for ($i = 1; $i <= 30; $i++) {
        // Datos simulados
        $precio = rand(100, 5000);
        $stock = rand(0, 50);
        $estado = $stock > 0 ? 'En Stock' : 'Agotado';
        $colorBadge = $stock > 0 ? 'success' : 'danger';
    ?>
        <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
            <div class="card h-100 product-card shadow-sm">
                <!-- Placeholder de imagen -->
                <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 200px;">
                    Foto Producto <?php echo $i; ?>
                </div>
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">Producto <?php echo $i; ?></h5>
                    <p class="card-text text-muted small">Descripción generada dinámicamente desde PHP...</p>
                    <div class="mt-auto">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fs-5 fw-bold">$<?php echo number_format($precio, 2); ?></span>
                            <span class="badge bg-<?php echo $colorBadge; ?>"><?php echo $estado; ?></span>
                        </div>
                        <button class="btn btn-primary w-100">
                            <i class="bi bi-cart-plus"></i> Agregar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php } // Fin del for ?>
</div>