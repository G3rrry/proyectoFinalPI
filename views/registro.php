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

    // 1. Validar formato de correo (que tenga @ y .)
    if (strpos($email, '@') === false || strpos($email, '.') === false) {
        $mensaje = "<div class='alert alert-danger'>El correo debe contener un '@' y un punto '.'.</div>";
    } 
    // 2. Validar tarjeta (solo números, cualquier longitud)
    elseif (!preg_match('/^[0-9]+$/', $tarjeta)) {
        $mensaje = "<div class='alert alert-danger'>La tarjeta debe contener **únicamente números** (sin espacios ni guiones).</div>";
    } 
    else {
        // 4. Validar usuario O correo repetido
        $check = $conn->prepare("SELECT id_usuario FROM `Usuarios` WHERE correo_electronico = ? OR nombre_usuario = ?");
        $check->bind_param("ss", $email, $nombre);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $mensaje = "<div class='alert alert-danger'>El nombre de usuario o el correo ya están registrados.</div>";
        } else {
            // --- HASHING ACTIVO (Seguridad mantenida) ---
            $passHash = password_hash($pass, PASSWORD_BCRYPT);

            // Insertar en la BD
            $sql = "INSERT INTO `Usuarios` (nombre_usuario, correo_electronico, contrasena, fecha_nacimiento, numero_tarjeta_bancaria, direccion_postal) VALUES (?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($sql);
            // Tipos: s=string, s=string, s=string, s=string, s=string, s=string
            // Usamos $passHash para guardar la contraseña encriptada
            $stmt->bind_param("ssssss", $nombre, $email, $passHash, $fecha_nac, $tarjeta, $direccion);
            
            if ($stmt->execute()) {
                // --- AUTO-LOGIN ---
                $_SESSION['id_usuario'] = $conn->insert_id; 
                $_SESSION['nombre_usuario'] = $nombre;

                echo "<script>window.location='index.php?page=catalogo';</script>";
                exit;
            } else {
                $mensaje = "<div class='alert alert-danger'>Error al registrar: " . $conn->error . "</div>";
            }
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
                        </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha de Nacimiento</label>
                            <input type="date" name="fecha_nacimiento" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tarjeta Bancaria</label>
                            <input type="text" name="numero_tarjeta_bancaria" class="form-control" placeholder="Ingrese los números de su tarjeta" required>
                            <div class="form-text text-muted">Solo números, sin espacios ni guiones.</div>
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