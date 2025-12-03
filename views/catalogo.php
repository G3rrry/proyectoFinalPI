<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h2 class="border-bottom pb-2">Catálogo de Productos</h2>
    </div>
    <div class="col-md-6">
        <div class="input-group">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
            <input type="text" id="buscador" class="form-control border-start-0 ps-0" placeholder="Buscar por título...">
        </div>
    </div>
</div>

<div class="row" id="contenedor-productos">
    <?php
    // Usamos LEFT JOIN para traer la foto principal
    $sql = "SELECT p.*, f.foto1 
            FROM Productos p 
            LEFT JOIN Fotos_Producto f ON p.id_fotos = f.id_fotos";
    
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($prod = $result->fetch_assoc()) {
            $id = $prod['id_producto'];
            $nombre = htmlspecialchars($prod['nombre']);
            $precio = $prod['precio'];
            $stock = $prod['cantidad_en_almacen'];
            
            // Lógica de stock visual
            $enStock = $stock > 0;
            $textoStock = $enStock ? 'Disponible: ' . $stock : 'Agotado';
            $claseStock = $enStock ? 'text-success' : 'text-danger';

            // Lógica de Imagen (BLOB a Base64)
            $imagenHtml = "";
            if (!empty($prod['foto1'])) {
                $imagenData = base64_encode($prod['foto1']);
                $src = 'data:image/jpeg;base64,' . $imagenData;
                $imagenHtml = "<img src='{$src}' class='card-img-top' style='height: 250px; object-fit: cover;' alt='{$nombre}'>";
            } else {
                $imagenHtml = "<div class='bg-light text-secondary d-flex align-items-center justify-content-center' style='height: 250px;'>
                                <i class='bi bi-image display-4'></i>
                               </div>";
            }
    ?>
        <div class="col-sm-6 col-md-4 col-lg-3 mb-4 producto-item" data-titulo="<?php echo strtolower($nombre); ?>">
            <div class="card h-100 product-card shadow-sm border-0 hover-effect">
                
                <a href="index.php?page=producto&id=<?php echo $id; ?>" class="text-decoration-none text-dark">
                    <?php echo $imagenHtml; ?>
                </a>

                <div class="card-body d-flex flex-column text-center">
                    <h5 class="card-title fs-6">
                        <a href="index.php?page=producto&id=<?php echo $id; ?>" class="text-decoration-none text-dark stretched-link">
                            <?php echo $nombre; ?>
                        </a>
                    </h5>
                    
                    <div class="mt-auto pt-2">
                        <h4 class="text-primary fw-bold mb-1">$<?php echo number_format($precio, 2); ?></h4>
                        
                        <small class="<?php echo $claseStock; ?> fw-bold" style="font-size: 0.8rem;">
                            <i class="bi bi-box-seam"></i> <?php echo $textoStock; ?>
                        </small>
                    </div>
                </div>
                
                <div class="card-footer bg-white border-top-0 pb-3">
                    <a href="index.php?page=producto&id=<?php echo $id; ?>" class="btn btn-outline-primary btn-sm w-100 rounded-pill">Ver Detalles</a>
                </div>

            </div>
        </div>
    <?php 
        } 
    } else {
        echo "<div class='col-12'><div class='alert alert-info'>No hay productos disponibles.</div></div>";
    }
    ?>
    
    <div id="no-results" class="col-12 text-center py-5 d-none">
        <i class="bi bi-search display-1 text-muted"></i>
        <h4 class="text-muted mt-3">No encontramos coincidencias</h4>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const buscador = document.getElementById('buscador');
    const items = document.querySelectorAll('.producto-item');
    const noResults = document.getElementById('no-results');

    buscador.addEventListener('input', function() {
        const texto = this.value.toLowerCase().trim();
        let visibles = 0;

        items.forEach(item => {
            // Obtenemos el título guardado en el atributo data-titulo
            const titulo = item.getAttribute('data-titulo');
            
            if (titulo.includes(texto)) {
                item.classList.remove('d-none'); // Mostrar
                visibles++;
            } else {
                item.classList.add('d-none'); // Ocultar
            }
        });

        // Mostrar mensaje si no hay nada visible
        if (visibles === 0) {
            noResults.classList.remove('d-none');
        } else {
            noResults.classList.add('d-none');
        }
    });
});
</script>

<style>
    /* Efecto hover suave para las tarjetas */
    .hover-effect {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .hover-effect:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
</style>