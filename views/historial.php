<?php
// views/historial.php

// 1. Verificar sesión
if (!isset($_SESSION['id_usuario'])) {
    echo "<script>window.location='index.php?page=login';</script>";
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

// 2. Obtener las compras del usuario (Encabezados)
$sql_compras = "SELECT id_compra, fecha_compra, total 
                FROM Compras 
                WHERE id_usuario = ? 
                ORDER BY fecha_compra DESC";
$stmt = $conn->prepare($sql_compras);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result_compras = $stmt->get_result();
?>

<h2 class="mb-4"><i class="bi bi-clock-history"></i> Historial de Compras</h2>

<?php if ($result_compras->num_rows > 0): ?>
    <div class="accordion" id="accordionHistorial">
        <?php 
        // Iteramos sobre cada compra
        while ($compra = $result_compras->fetch_assoc()): 
            $id_compra = $compra['id_compra'];
            $fecha = date("d/m/Y H:i", strtotime($compra['fecha_compra']));
            $total = $compra['total'];
            $accordionId = "collapse" . $id_compra;
            $headerId = "heading" . $id_compra;
        ?>
            <div class="accordion-item shadow-sm mb-3 border">
                <h2 class="accordion-header" id="<?php echo $headerId; ?>">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $accordionId; ?>">
                        <div class="d-flex justify-content-between w-100 me-3 align-items-center">
                            <span>
                                <strong>Pedido #<?php echo str_pad($id_compra, 6, "0", STR_PAD_LEFT); ?></strong>
                                <span class="text-muted small ms-2"><i class="bi bi-calendar-event"></i> <?php echo $fecha; ?></span>
                            </span>
                            <span class="badge bg-success rounded-pill p-2">
                                Total: $<?php echo number_format($total, 2); ?>
                            </span>
                        </div>
                    </button>
                </h2>
                <div id="<?php echo $accordionId; ?>" class="accordion-collapse collapse" data-bs-parent="#accordionHistorial">
                    <div class="accordion-body bg-light">
                        <h6 class="border-bottom pb-2 mb-3">Detalles de la compra:</h6>
                        
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered bg-white">
                                <thead class="table-secondary">
                                    <tr>
                                        <th>Producto</th>
                                        <th class="text-center">Cantidad</th>
                                        <th class="text-end">Precio Unitario</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // 3. Obtener detalles de ESTA compra específica
                                    // Hacemos una sub-consulta o consulta anidada para los items
                                    $sql_detalles = "SELECT p.nombre, dc.cantidad, dc.precio_unitario 
                                                     FROM Detalle_Compras dc
                                                     JOIN Productos p ON dc.id_producto = p.id_producto
                                                     WHERE dc.id_compra = ?";
                                    $stmt_det = $conn->prepare($sql_detalles);
                                    $stmt_det->bind_param("i", $id_compra);
                                    $stmt_det->execute();
                                    $res_detalles = $stmt_det->get_result();
                                    
                                    while ($item = $res_detalles->fetch_assoc()):
                                        $subtotal_item = $item['cantidad'] * $item['precio_unitario'];
                                    ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($item['nombre']); ?></td>
                                            <td class="text-center"><?php echo $item['cantidad']; ?></td>
                                            <td class="text-end">$<?php echo number_format($item['precio_unitario'], 2); ?></td>
                                            <td class="text-end">$<?php echo number_format($subtotal_item, 2); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="text-end mt-2">
                            <button class="btn btn-sm btn-outline-primary" onclick="window.print()">
                                <i class="bi bi-printer"></i> Imprimir Comprobante
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
<?php else: ?>
    <div class="alert alert-info text-center py-5">
        <i class="bi bi-bag-x display-1 mb-3"></i>
        <h4>Aún no has realizado ninguna compra.</h4>
        <p>¡Explora nuestro catálogo y encuentra lo que buscas!</p>
        <a href="index.php?page=catalogo" class="btn btn-primary mt-2">Ir a Comprar</a>
    </div>
<?php endif; ?>