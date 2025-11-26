<?php
// views/carrito.php

// 1. Verificar sesión
if (!isset($_SESSION['id_usuario'])) {
    echo "<script>window.location='index.php?page=login';</script>";
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$mensaje = "";

// Función auxiliar para obtener (o crear) el ID del carrito activo del usuario
function obtenerIdCarrito($conn, $id_usuario) {
    $sql = "SELECT id_carrito FROM Carritos WHERE id_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $res = $stmt->get_result();
    
    if ($fila = $res->fetch_assoc()) {
        return $fila['id_carrito'];
    } else {
        // Crear carrito si no existe
        $stmt_ins = $conn->prepare("INSERT INTO Carritos (id_usuario) VALUES (?)");
        $stmt_ins->bind_param("i", $id_usuario);
        $stmt_ins->execute();
        return $conn->insert_id;
    }
}

$id_carrito = obtenerIdCarrito($conn, $id_usuario);

// ---------------------------------------------------------
// 2. PROCESAR ACCIONES (POST)
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    
    $accion = $_POST['action'];

    // --- AGREGAR PRODUCTO ---
    if ($accion == 'agregar') {
        $id_prod = $_POST['id_producto'];
        $cant    = intval($_POST['cantidad']);

        // Verificar si ya existe en el carrito
        $check = $conn->prepare("SELECT id_detalle_carrito, cantidad FROM Detalle_Carrito WHERE id_carrito = ? AND id_producto = ?");
        $check->bind_param("ii", $id_carrito, $id_prod);
        $check->execute();
        $res_check = $check->get_result();

        if ($row = $res_check->fetch_assoc()) {
            // Actualizar cantidad existente
            $nueva_cant = $row['cantidad'] + $cant;
            $update = $conn->prepare("UPDATE Detalle_Carrito SET cantidad = ? WHERE id_detalle_carrito = ?");
            $update->bind_param("ii", $nueva_cant, $row['id_detalle_carrito']);
            $update->execute();
        } else {
            // Insertar nuevo
            $insert = $conn->prepare("INSERT INTO Detalle_Carrito (id_carrito, id_producto, cantidad) VALUES (?, ?, ?)");
            $insert->bind_param("iii", $id_carrito, $id_prod, $cant);
            $insert->execute();
        }
        $mensaje = "<div class='alert alert-success'>Producto agregado al carrito.</div>";
    }

    // --- ACTUALIZAR CANTIDAD ---
    if ($accion == 'actualizar') {
        $id_detalle = $_POST['id_detalle_carrito'];
        $cant       = intval($_POST['cantidad']);
        
        if ($cant > 0) {
            $upd = $conn->prepare("UPDATE Detalle_Carrito SET cantidad = ? WHERE id_detalle_carrito = ?");
            $upd->bind_param("ii", $cant, $id_detalle);
            $upd->execute();
            $mensaje = "<div class='alert alert-info'>Carrito actualizado.</div>";
        }
    }

    // --- ELIMINAR PRODUCTO ---
    if ($accion == 'eliminar') {
        $id_detalle = $_POST['id_detalle_carrito'];
        $del = $conn->prepare("DELETE FROM Detalle_Carrito WHERE id_detalle_carrito = ?");
        $del->bind_param("i", $id_detalle);
        $del->execute();
        $mensaje = "<div class='alert alert-warning'>Producto eliminado.</div>";
    }

    // --- FINALIZAR COMPRA (CHECKOUT) ---
    if ($accion == 'finalizar') {
        // 1. Calcular total y obtener items
        $sql_items = "SELECT dc.id_producto, dc.cantidad, p.precio, p.cantidad_en_almacen 
                      FROM Detalle_Carrito dc
                      JOIN Productos p ON dc.id_producto = p.id_producto
                      WHERE dc.id_carrito = ?";
        $stmt_items = $conn->prepare($sql_items);
        $stmt_items->bind_param("i", $id_carrito);
        $stmt_items->execute();
        $res_items = $stmt_items->get_result();

        $items = [];
        $total_compra = 0;
        $error_stock = false;

        while ($item = $res_items->fetch_assoc()) {
            if ($item['cantidad'] > $item['cantidad_en_almacen']) {
                $error_stock = true;
                $mensaje = "<div class='alert alert-danger'>Error: No hay suficiente stock para el producto ID " . $item['id_producto'] . "</div>";
                break;
            }
            $subtotal = $item['cantidad'] * $item['precio'];
            $total_compra += $subtotal;
            $items[] = $item;
        }

        if (!$error_stock && count($items) > 0) {
            // INICIO TRANSACCIÓN
            $conn->begin_transaction();

            try {
                // A. Crear registro en Compras
                $sql_compra = "INSERT INTO Compras (id_usuario, total) VALUES (?, ?)";
                $stmt_c = $conn->prepare($sql_compra);
                $stmt_c->bind_param("id", $id_usuario, $total_compra);
                $stmt_c->execute();
                $id_compra = $conn->insert_id;

                // B. Procesar cada item
                foreach ($items as $prod) {
                    // Insertar en Detalle_Compras
                    $sql_det = "INSERT INTO Detalle_Compras (id_compra, id_producto, cantidad, precio_unitario) VALUES (?, ?, ?, ?)";
                    $stmt_det = $conn->prepare($sql_det);
                    $stmt_det->bind_param("iiid", $id_compra, $prod['id_producto'], $prod['cantidad'], $prod['precio']);
                    $stmt_det->execute();

                    // Actualizar Inventario (Restar stock)
                    $nuevo_stock = $prod['cantidad_en_almacen'] - $prod['cantidad'];
                    $sql_upd = "UPDATE Productos SET cantidad_en_almacen = ? WHERE id_producto = ?";
                    $stmt_upd = $conn->prepare($sql_upd);
                    $stmt_upd->bind_param("ii", $nuevo_stock, $prod['id_producto']);
                    $stmt_upd->execute();
                }

                // C. Vaciar Carrito (Borrar detalles)
                $sql_vaciar = "DELETE FROM Detalle_Carrito WHERE id_carrito = ?";
                $stmt_vac = $conn->prepare($sql_vaciar);
                $stmt_vac->bind_param("i", $id_carrito);
                $stmt_vac->execute();

                // COMMIT
                $conn->commit();
                $mensaje = "<div class='alert alert-success'>¡Compra realizada con éxito! Gracias por tu preferencia.</div>";
            } catch (Exception $e) {
                $conn->rollback();
                $mensaje = "<div class='alert alert-danger'>Ocurrió un error al procesar la compra: " . $e->getMessage() . "</div>";
            }
        } elseif (count($items) == 0) {
            $mensaje = "<div class='alert alert-warning'>El carrito está vacío.</div>";
        }
    }
}

// ---------------------------------------------------------
// 3. OBTENER VISTA DEL CARRITO
// ---------------------------------------------------------
$sql_ver = "SELECT dc.id_detalle_carrito, p.nombre, p.precio, dc.cantidad, (p.precio * dc.cantidad) as subtotal
            FROM Detalle_Carrito dc
            JOIN Productos p ON dc.id_producto = p.id_producto
            WHERE dc.id_carrito = ?";
$stmt_ver = $conn->prepare($sql_ver);
$stmt_ver->bind_param("i", $id_carrito);
$stmt_ver->execute();
$resultado_carrito = $stmt_ver->get_result();

$gran_total = 0;
?>

<h2 class="mb-4">Tu Carrito de Compras</h2>
<?php echo $mensaje; ?>

<?php if ($resultado_carrito->num_rows > 0): ?>
    <div class="row">
        <div class="col-lg-8">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th style="width: 150px;">Cantidad</th>
                            <th>Subtotal</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $resultado_carrito->fetch_assoc()): 
                            $gran_total += $row['subtotal'];
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                            <td>$<?php echo number_format($row['precio'], 2); ?></td>
                            <td>
                                <form action="" method="POST" class="d-flex">
                                    <input type="hidden" name="action" value="actualizar">
                                    <input type="hidden" name="id_detalle_carrito" value="<?php echo $row['id_detalle_carrito']; ?>">
                                    <input type="number" name="cantidad" value="<?php echo $row['cantidad']; ?>" min="1" class="form-control form-control-sm me-2">
                                    <button type="submit" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-clockwise"></i></button>
                                </form>
                            </td>
                            <td class="fw-bold">$<?php echo number_format($row['subtotal'], 2); ?></td>
                            <td>
                                <form action="" method="POST">
                                    <input type="hidden" name="action" value="eliminar">
                                    <input type="hidden" name="id_detalle_carrito" value="<?php echo $row['id_detalle_carrito']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">Resumen del Pedido</div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <span>Total:</span>
                        <span class="fs-4 fw-bold">$<?php echo number_format($gran_total, 2); ?></span>
                    </div>
                    <hr>
                    <form action="" method="POST">
                        <input type="hidden" name="action" value="finalizar">
                        <button type="submit" class="btn btn-success w-100 py-2">
                            <i class="bi bi-credit-card"></i> Pagar y Finalizar
                        </button>
                    </form>
                    <a href="index.php?page=catalogo" class="btn btn-outline-primary w-100 mt-2">Seguir Comprando</a>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="text-center py-5">
        <i class="bi bi-cart-x display-1 text-muted"></i>
        <h3 class="mt-3 text-muted">Tu carrito está vacío</h3>
        <a href="index.php?page=catalogo" class="btn btn-primary mt-3">Ir al Catálogo</a>
    </div>
<?php endif; ?>