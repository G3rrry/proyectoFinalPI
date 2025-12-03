<?php
// views/admin.php

// ---------------------------------------------------------
// 1. SEGURIDAD
// ---------------------------------------------------------
if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin') {
    ?>
    <div class="container py-5 text-center">
        <h2 class="text-danger fw-bold">Acceso Denegado</h2>
        <p class="lead text-muted">Se requieren permisos de administrador.</p>
        <a href="index.php?page=catalogo" class="btn btn-primary mt-3">Volver al Catálogo</a>
    </div>
    <?php
    exit;
}

// Inicializar variables
$editMode = false;
$prodData = [
    'id_producto' => '', 'nombre' => '', 'descripcion' => '', 'precio' => '', 
    'cantidad_en_almacen' => '', 'fabricante' => '', 'origen' => ''
];
$mensaje = "";

// ---------------------------------------------------------
// FUNCION AUXILIAR PARA FOTOS
// ---------------------------------------------------------
function prepararFotos($files) {
    $blobs = array_fill(0, 5, null);
    $count = 0;
    if (isset($files['tmp_name']) && is_array($files['tmp_name'])) {
        foreach ($files['tmp_name'] as $index => $tmpName) {
            if ($count >= 5) break;
            if ($files['error'][$index] === UPLOAD_ERR_OK) {
                $blobs[$count] = file_get_contents($tmpName);
                $count++;
            }
        }
    }
    return ['blobs' => $blobs, 'count' => $count];
}

// ---------------------------------------------------------
// 2. LÓGICA DE GESTIÓN DE PRODUCTOS (POST)
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // --- ELIMINAR ---
    if (isset($_POST['action']) && $_POST['action'] == 'eliminar') {
        $id = $_POST['id_producto'];
        
        $q = $conn->query("SELECT id_fotos FROM Productos WHERE id_producto = $id");
        $row = $q->fetch_assoc();
        
        $conn->begin_transaction();
        try {
            $stmt = $conn->prepare("DELETE FROM Productos WHERE id_producto = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            
            if ($row && $row['id_fotos']) {
                $conn->query("DELETE FROM Fotos_Producto WHERE id_fotos = " . $row['id_fotos']);
            }
            $conn->commit();
            $mensaje = "<div class='alert alert-warning alert-dismissible fade show'>Producto eliminado. <button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
        } catch (Exception $e) {
            $conn->rollback();
            $mensaje = "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
        }
    }

    // --- GUARDAR (NUEVO O EDITAR) ---
    if (isset($_POST['action']) && $_POST['action'] == 'guardar') {
        $nombre = $_POST['nombre'];
        $desc   = $_POST['descripcion'];
        $precio = $_POST['precio'];
        $stock  = $_POST['cantidad_en_almacen'];
        $fab    = $_POST['fabricante'];
        $origen = $_POST['origen'];
        $id     = $_POST['id_producto'];
        
        $fotosInfo = prepararFotos($_FILES['fotos']);
        $hayFotosNuevas = $fotosInfo['count'] > 0;
        
        if (empty($id) && !$hayFotosNuevas) {
            $mensaje = "<div class='alert alert-danger'>Error: Se requiere al menos una imagen para productos nuevos.</div>";
        } else {
            try {
                $conn->begin_transaction();
                
                $id_fotos_final = null;
                
                if ($hayFotosNuevas) {
                    $target_id_fotos = null;
                    if (!empty($id)) {
                        $resP = $conn->query("SELECT id_fotos FROM Productos WHERE id_producto = $id");
                        $filaP = $resP->fetch_assoc();
                        $target_id_fotos = $filaP['id_fotos'];
                    }

                    if ($target_id_fotos) {
                        $sqlF = "UPDATE Fotos_Producto SET foto1=?, foto2=?, foto3=?, foto4=?, foto5=? WHERE id_fotos=?";
                        $stmtF = $conn->prepare($sqlF);
                        $stmtF->bind_param("sssssi", 
                            $fotosInfo['blobs'][0], $fotosInfo['blobs'][1], $fotosInfo['blobs'][2], 
                            $fotosInfo['blobs'][3], $fotosInfo['blobs'][4], $target_id_fotos
                        );
                        $stmtF->execute();
                        $id_fotos_final = $target_id_fotos;
                    } else {
                        $sqlF = "INSERT INTO Fotos_Producto (foto1, foto2, foto3, foto4, foto5) VALUES (?, ?, ?, ?, ?)";
                        $stmtF = $conn->prepare($sqlF);
                        $stmtF->bind_param("sssss", 
                            $fotosInfo['blobs'][0], $fotosInfo['blobs'][1], $fotosInfo['blobs'][2], 
                            $fotosInfo['blobs'][3], $fotosInfo['blobs'][4]
                        );
                        $stmtF->execute();
                        $id_fotos_final = $conn->insert_id;
                    }
                }

                if (!empty($id)) {
                    // UPDATE
                    if ($hayFotosNuevas) {
                        $sql = "UPDATE Productos SET nombre=?, descripcion=?, precio=?, cantidad_en_almacen=?, fabricante=?, origen=?, id_fotos=? WHERE id_producto=?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("ssdissii", $nombre, $desc, $precio, $stock, $fab, $origen, $id_fotos_final, $id);
                    } else {
                        $sql = "UPDATE Productos SET nombre=?, descripcion=?, precio=?, cantidad_en_almacen=?, fabricante=?, origen=? WHERE id_producto=?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("ssdissi", $nombre, $desc, $precio, $stock, $fab, $origen, $id);
                    }
                    $msgSuccess = "Producto actualizado.";
                } else {
                    // INSERT
                    $sql = "INSERT INTO Productos (nombre, descripcion, precio, cantidad_en_almacen, fabricante, origen, id_fotos) VALUES (?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssdissi", $nombre, $desc, $precio, $stock, $fab, $origen, $id_fotos_final);
                    $msgSuccess = "Producto creado.";
                }

                $stmt->execute();
                $conn->commit();
                $mensaje = "<div class='alert alert-success alert-dismissible fade show'>$msgSuccess <button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
                $editMode = false;

            } catch (Exception $e) {
                $conn->rollback();
                $mensaje = "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
            }
        }
    }
}

// ---------------------------------------------------------
// 3. CARGAR DATOS PARA EDICIÓN
// ---------------------------------------------------------
if (isset($_GET['edit_id'])) {
    $editMode = true;
    $id = $_GET['edit_id'];
    $stmt = $conn->prepare("SELECT * FROM Productos WHERE id_producto = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($fila = $res->fetch_assoc()) {
        $prodData = $fila;
    }
}
?>

<div class="row mb-3">
    <div class="col-md-12">
        <h2 class="mb-3"><i class="bi bi-shield-lock"></i> Panel de Administración</h2>
        <?php echo $mensaje; ?>
        
        <ul class="nav nav-tabs" id="adminTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link <?php echo isset($_GET['sales']) ? '' : 'active'; ?>" id="inventario-tab" data-bs-toggle="tab" data-bs-target="#inventario" type="button">
                    <i class="bi bi-box-seam"></i> Gestión de Inventario
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link <?php echo isset($_GET['sales']) ? 'active' : ''; ?>" id="ventas-tab" data-bs-toggle="tab" data-bs-target="#ventas" type="button">
                    <i class="bi bi-currency-dollar"></i> Historial de Ventas Global
                </button>
            </li>
        </ul>
    </div>
</div>

<div class="tab-content" id="adminTabContent">
    
    <div class="tab-pane fade <?php echo isset($_GET['sales']) ? '' : 'show active'; ?>" id="inventario">
        <div class="row pt-3">
            <div class="col-lg-4 mb-4">
                <div class="card shadow border-<?php echo $editMode ? 'warning' : 'success'; ?>">
                    <div class="card-header text-white bg-<?php echo $editMode ? 'warning' : 'success'; ?>">
                        <h5 class="mb-0">
                            <i class="bi bi-<?php echo $editMode ? 'pencil-square' : 'plus-circle'; ?>"></i> 
                            <?php echo $editMode ? 'Editar Producto' : 'Nuevo Producto'; ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="index.php?page=admin" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="guardar">
                            <input type="hidden" name="id_producto" value="<?php echo $prodData['id_producto']; ?>">

                            <div class="mb-2">
                                <label class="form-label small fw-bold">Nombre</label>
                                <input type="text" name="nombre" class="form-control" required value="<?php echo htmlspecialchars($prodData['nombre']); ?>">
                            </div>
                            <div class="mb-2">
                                <label class="form-label small fw-bold">Descripción</label>
                                <textarea name="descripcion" class="form-control" rows="2" required><?php echo htmlspecialchars($prodData['descripcion']); ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Imágenes (1-5)</label>
                                <input type="file" name="fotos[]" class="form-control" multiple accept="image/*">
                                <div class="form-text" style="font-size: 0.8rem;">
                                    <?php echo $editMode ? '(Deja vacío para mantener actuales)' : '* Requerido para nuevos'; ?>
                                </div>
                            </div>
                            <div class="row g-2 mb-2">
                                <div class="col-6">
                                    <label class="form-label small fw-bold">Precio ($)</label>
                                    <input type="number" step="0.01" name="precio" class="form-control" required value="<?php echo $prodData['precio']; ?>">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-bold">Stock</label>
                                    <input type="number" name="cantidad_en_almacen" class="form-control" required value="<?php echo $prodData['cantidad_en_almacen']; ?>">
                                </div>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="form-label small fw-bold">Fabricante</label>
                                    <input type="text" name="fabricante" class="form-control" value="<?php echo htmlspecialchars($prodData['fabricante']); ?>">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-bold">Origen</label>
                                    <input type="text" name="origen" class="form-control" value="<?php echo htmlspecialchars($prodData['origen']); ?>">
                                </div>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-<?php echo $editMode ? 'warning' : 'success'; ?>">
                                    <?php echo $editMode ? 'Actualizar' : 'Guardar'; ?>
                                </button>
                                <?php if($editMode): ?>
                                    <a href="index.php?page=admin" class="btn btn-outline-secondary">Cancelar</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">Inventario Actual</div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                            <table class="table table-striped table-hover mb-0 align-middle">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th>IMG</th>
                                        <th>Producto</th>
                                        <th>Stock</th>
                                        <th>Precio</th>
                                        <th class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT p.*, f.foto1 FROM Productos p 
                                            LEFT JOIN Fotos_Producto f ON p.id_fotos = f.id_fotos 
                                            ORDER BY p.id_producto DESC";
                                    $result = $conn->query($sql);
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            $bgStock = ($row['cantidad_en_almacen'] < 5) ? 'bg-danger' : 'bg-success';
                                            $thumb = "<i class='bi bi-image text-muted'></i>";
                                            if(!empty($row['foto1'])) {
                                                $b64 = base64_encode($row['foto1']);
                                                $thumb = "<img src='data:image/jpeg;base64,$b64' style='width: 40px; height: 40px; object-fit: cover;' class='rounded'>";
                                            }
                                    ?>
                                    <tr>
                                        <td><?php echo $thumb; ?></td>
                                        <td>
                                            <div class="fw-bold"><?php echo htmlspecialchars($row['nombre']); ?></div>
                                            <small class="text-muted">ID: <?php echo $row['id_producto']; ?></small>
                                        </td>
                                        <td><span class="badge <?php echo $bgStock; ?>"><?php echo $row['cantidad_en_almacen']; ?></span></td>
                                        <td>$<?php echo number_format($row['precio'], 2); ?></td>
                                        <td class="text-end">
                                            <a href="index.php?page=admin&edit_id=<?php echo $row['id_producto']; ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                            <form action="index.php?page=admin" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar?');">
                                                <input type="hidden" name="action" value="eliminar">
                                                <input type="hidden" name="id_producto" value="<?php echo $row['id_producto']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php } } else { echo "<tr><td colspan='5' class='text-center p-3'>Sin productos.</td></tr>"; } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane fade <?php echo isset($_GET['sales']) ? 'show active' : ''; ?>" id="ventas">
        <div class="pt-3">
            <h4 class="mb-3">Registro de Todas las Compras</h4>
            
            <?php
            // CONSULTA: Todas las compras + Nombre del Usuario
            $sql_global = "SELECT c.id_compra, c.fecha_compra, c.total, u.nombre_usuario, u.correo_electronico 
                           FROM Compras c 
                           JOIN Usuarios u ON c.id_usuario = u.id_usuario 
                           ORDER BY c.fecha_compra DESC";
            $res_global = $conn->query($sql_global);
            
            if ($res_global->num_rows > 0): 
            ?>
                <div class="accordion" id="accordionVentasAdmin">
                    <?php 
                    while ($compra = $res_global->fetch_assoc()): 
                        $idC = $compra['id_compra'];
                        $fecha = date("d/m/Y H:i", strtotime($compra['fecha_compra']));
                        $usuario = htmlspecialchars($compra['nombre_usuario']);
                        $correo = htmlspecialchars($compra['correo_electronico']);
                        $total = $compra['total'];
                    ?>
                    <div class="accordion-item shadow-sm mb-2 border">
                        <h2 class="accordion-header" id="headingAdmin<?php echo $idC; ?>">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAdmin<?php echo $idC; ?>">
                                <div class="row w-100 align-items-center me-0">
                                    <div class="col-md-2">
                                        <strong>#<?php echo str_pad($idC, 6, "0", STR_PAD_LEFT); ?></strong>
                                    </div>
                                    <div class="col-md-4">
                                        <i class="bi bi-person-fill"></i> <?php echo $usuario; ?> 
                                        <span class="text-muted small">(<?php echo $correo; ?>)</span>
                                    </div>
                                    <div class="col-md-3 text-muted small">
                                        <i class="bi bi-calendar-event"></i> <?php echo $fecha; ?>
                                    </div>
                                    <div class="col-md-3 text-end fw-bold text-success">
                                        Total: $<?php echo number_format($total, 2); ?>
                                    </div>
                                </div>
                            </button>
                        </h2>
                        <div id="collapseAdmin<?php echo $idC; ?>" class="accordion-collapse collapse" data-bs-parent="#accordionVentasAdmin">
                            <div class="accordion-body bg-light">
                                <h6 class="border-bottom pb-2 mb-2 fw-bold">Detalle del Pedido:</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered bg-white mb-0">
                                        <thead class="table-secondary">
                                            <tr>
                                                <th>Producto</th>
                                                <th class="text-center">Cant</th>
                                                <th class="text-end">P. Unit</th>
                                                <th class="text-end">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // Subconsulta para items de ESTA compra
                                            $sql_items = "SELECT dc.cantidad, dc.precio_unitario, p.nombre 
                                                          FROM Detalle_Compras dc 
                                                          JOIN Productos p ON dc.id_producto = p.id_producto 
                                                          WHERE dc.id_compra = ?";
                                            $stmt_i = $conn->prepare($sql_items);
                                            $stmt_i->bind_param("i", $idC);
                                            $stmt_i->execute();
                                            $res_items = $stmt_i->get_result();
                                            
                                            while ($item = $res_items->fetch_assoc()):
                                                $sub = $item['cantidad'] * $item['precio_unitario'];
                                            ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($item['nombre']); ?></td>
                                                <td class="text-center"><?php echo $item['cantidad']; ?></td>
                                                <td class="text-end">$<?php echo number_format($item['precio_unitario'], 2); ?></td>
                                                <td class="text-end">$<?php echo number_format($sub, 2); ?></td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info text-center py-5">
                    <i class="bi bi-inbox display-4 mb-3"></i>
                    <h4>No hay ventas registradas en el sistema.</h4>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>