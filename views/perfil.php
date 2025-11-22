<?php
// views/perfil.php

// Verificar seguridad (aunque index.php ya lo hace, es buena práctica)
if (!isset($_SESSION['id_usuario'])) {
    echo "<script>window.location='index.php?page=login';</script>";
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$mensaje = "";

// 1. LÓGICA DE ACTUALIZACIÓN (Si se envió el formulario)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_nuevo = $_POST['nombre_usuario'];
    $fecha_nac = $_POST['fecha_nacimiento'];
    $direccion = $_POST['direccion_postal'];
    $tarjeta = $_POST['numero_tarjeta_bancaria'];
    
    // Validar contraseña nueva si el usuario quiere cambiarla (opcional)
    // Para simplificar, aquí actualizamos los datos básicos
    $sql_update = "UPDATE `Usuarios` SET 
                    nombre_usuario = ?, 
                    fecha_nacimiento = ?, 
                    direccion_postal = ?,
                    numero_tarjeta_bancaria = ? 
                   WHERE id_usuario = ?";
    
    $stmt = $conn->prepare($sql_update);
    $stmt->bind_param("ssssi", $nombre_nuevo, $fecha_nac, $direccion, $tarjeta, $id_usuario);
    
    if ($stmt->execute()) {
        $mensaje = "<div class='alert alert-success'>¡Datos actualizados correctamente!</div>";
        // Actualizar el nombre en la sesión para que cambie en el menú de arriba inmediatamente
        $_SESSION['nombre_usuario'] = $nombre_nuevo;
    } else {
        $mensaje = "<div class='alert alert-danger'>Error al actualizar: " . $conn->error . "</div>";
    }
}

// 2. LÓGICA DE LECTURA (Obtener datos actuales)
$sql = "SELECT * FROM `Usuarios` WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();

// Si por alguna razón no se encuentra el usuario
if (!$usuario) {
    echo "<div class='alert alert-danger'>Error: Usuario no encontrado.</div>";
    exit;
}
?>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card shadow">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="bi bi-person-circle display-1 text-primary"></i>
                </div>
                <h4><?php echo htmlspecialchars($usuario['nombre_usuario']); ?></h4>
                <p class="text-muted mb-1"><?php echo htmlspecialchars($usuario['correo_electronico']); ?></p>
                <span class="badge bg-secondary">Miembro desde <?php echo date('d/m/Y', strtotime($usuario['fecha_registro'])); ?></span>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Compras realizadas
                    <span class="badge bg-primary rounded-pill">0</span> </li>
                <li class="list-group-item">
                    <a href="index.php?page=logout" class="btn btn-outline-danger w-100 btn-sm">Cerrar Sesión</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-pencil-square"></i> Editar Información
            </div>
            <div class="card-body">
                <?php echo $mensaje; ?>
                
                <form action="" method="POST">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombre de Usuario</label>
                            <input type="text" name="nombre_usuario" class="form-control" 
                                   value="<?php echo htmlspecialchars($usuario['nombre_usuario']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" 
                                   value="<?php echo htmlspecialchars($usuario['correo_electronico']); ?>" readonly disabled>
                            <div class="form-text">El correo no se puede cambiar.</div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Fecha de Nacimiento</label>
                            <input type="date" name="fecha_nacimiento" class="form-control" 
                                   value="<?php echo $usuario['fecha_nacimiento']; ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tarjeta Bancaria</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-credit-card"></i></span>
                                <input type="text" name="numero_tarjeta_bancaria" class="form-control" 
                                       value="<?php echo htmlspecialchars($usuario['numero_tarjeta_bancaria']); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Dirección Postal</label>
                        <textarea name="direccion_postal" class="form-control" rows="3"><?php echo htmlspecialchars($usuario['direccion_postal']); ?></textarea>
                    </div>

                    <hr>
                    
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-save"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>