<?php
// views/login.php

$error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // MODIFICADO: Ahora seleccionamos también el campo 'rol'
    $stmt = $conn->prepare("SELECT id_usuario, nombre_usuario, contrasena, rol FROM `Usuarios` WHERE correo_electronico = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($user = $resultado->fetch_assoc()) {
        // Verificar contraseña hasheada
        if (password_verify($password, $user['contrasena'])) {
            // ¡LOGIN CORRECTO! Guardamos datos en sesión
            $_SESSION['id_usuario'] = $user['id_usuario'];
            $_SESSION['nombre_usuario'] = $user['nombre_usuario'];
            
            // NUEVO: Guardamos el rol en la sesión para usarlo después
            $_SESSION['rol'] = $user['rol']; 
            
            // Redirigir al catálogo
            echo "<script>window.location='index.php?page=catalogo';</script>";
            exit;
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "No existe una cuenta con ese correo.";
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">Iniciar Sesión</div>
            <div class="card-body">
                <?php if($error): ?>
                    <div class="alert alert-danger py-2"><?php echo $error; ?></div>
                <?php endif; ?>

                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Correo Electrónico</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contraseña</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Entrar</button>
                </form>
            </div>
            <div class="card-footer text-center">
                <small>¿No tienes cuenta? <a href="index.php?page=registro">Regístrate aquí</a></small>
            </div>
        </div>
    </div>
</div>