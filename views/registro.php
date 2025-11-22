<?php
$mensaje = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recoger datos
    $nombre = $_POST['nombre_usuario'];
    $email = $_POST['correo_electronico'];
    $pass = $_POST['contrasena'];
    $fecha_nac = $_POST['fecha_nacimiento'];
    $tarjeta = $_POST['numero_tarjeta_bancaria'];
    $direccion = $_POST['direccion_postal'];

    // Validar que el email no exista previamente
    $check = $conn->prepare("SELECT id_usuario FROM `Usuarios` WHERE correo_electronico = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $mensaje = "<div class='alert alert-danger'>El correo ya está registrado.</div>";
    } else {
        // Hashear contraseña
        $passHash = password_hash($pass, PASSWORD_BCRYPT);

        // Insertar en la BD (Según tu imagen image_a29183.png)
        $sql = "INSERT INTO `Usuarios` (nombre_usuario, correo_electronico, contrasena, fecha_nacimiento, numero_tarjeta_bancaria, direccion_postal) VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        // Tipos: s=string, s=string, s=string, s=string, s=string, s=string
        $stmt->bind_param("ssssss", $nombre, $email, $passHash, $fecha_nac, $tarjeta, $direccion);
        
        if ($stmt->execute()) {
            // --- AUTO-LOGIN AQUÍ ---
            $_SESSION['id_usuario'] = $conn->insert_id; // Obtenemos el ID del nuevo usuario
            $_SESSION['nombre_usuario'] = $nombre;

            // Redirigir al catálogo inmediatamente
            echo "<script>window.location='index.php?page=catalogo';</script>";
            exit;
        } else {
            $mensaje = "<div class='alert alert-danger'>Error al registrar: " . $conn->error . "</div>";
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">Crear Nueva Cuenta</div>
            <div class="card-body">
                <?php echo $mensaje; ?>
                
                <form action="" method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre de Usuario</label>
                            <input type="text" name="nombre_usuario" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Correo Electrónico</label>
                            <input type="email" name="correo_electronico" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Contraseña</label>
                        <input type="password" name="contrasena" class="form-control" required>
                        <div class="form-text">Se guardará de forma segura (hasheada).</div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha de Nacimiento</label>
                            <input type="date" name="fecha_nacimiento" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tarjeta Bancaria</label>
                            <input type="text" name="numero_tarjeta_bancaria" class="form-control" placeholder="XXXX-XXXX-XXXX-XXXX">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Dirección Postal</label>
                        <textarea name="direccion_postal" class="form-control" rows="2"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Registrarse</button>
                </form>
            </div>
        </div>
    </div>
</div>