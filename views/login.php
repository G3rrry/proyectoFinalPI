<?php
// views/login.php

$error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Recogemos 'nombre_usuario' en lugar de email
    $nombre = $_POST['nombre_usuario'];
    $password = $_POST['password'];

    // 2. Modificamos el WHERE para buscar por nombre de usuario
    $stmt = $conn->prepare("SELECT id_usuario, nombre_usuario, contrasena, rol FROM `Usuarios` WHERE nombre_usuario = ?");
    $stmt->bind_param("s", $nombre);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($user = $resultado->fetch_assoc()) {
        // Verificar contraseña hasheada
        if (password_verify($password, $user['contrasena'])) {
            // ¡LOGIN CORRECTO! Guardamos datos en sesión
            $_SESSION['id_usuario'] = $user['id_usuario'];
            $_SESSION['nombre_usuario'] = $user['nombre_usuario'];
            
            // Guardamos el rol
            $_SESSION['rol'] = $user['rol']; 
            
            // Redirigir al catálogo
            echo "<script>window.location='index.php?page=catalogo';</script>";
            exit;
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        // Mensaje actualizado
        $error = "No existe una cuenta con ese nombre de usuario.";
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
                        <label class="form-label">Nombre de Usuario</label>
                        <input type="text" name="nombre_usuario" class="form-control" required>
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