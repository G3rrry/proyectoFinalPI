<?php
// views/contacto.php

$mensaje_envio = "";

// Lógica simple para simular el envío
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Logica de contacto real iria aca

    $nombre = htmlspecialchars($_POST['nombre']);
    $mensaje_envio = "
    <div class='alert alert-success alert-dismissible fade show' role='alert'>
        <i class='bi bi-check-circle-fill me-2'></i> 
        ¡Gracias <strong>$nombre</strong>! Hemos recibido tu mensaje. Nos pondremos en contacto contigo pronto.
        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
    </div>";
}
?>

<div class="container mb-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold">Contáctanos</h2>
        <p class="text-muted">¿Tienes alguna duda o sugerencia? Estamos aquí para ayudarte.</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-5 mb-4">
            <div class="card border-0 shadow-sm h-100 bg-primary text-white">
                <div class="card-body p-4 d-flex flex-column justify-content-center">
                    <h4 class="mb-4">Información de Contacto</h4>
                    
                    <div class="d-flex mb-3">
                        <i class="bi bi-geo-alt-fill fs-4 me-3"></i>
                        <div>
                            <h6 class="fw-bold mb-0">Dirección</h6>
                            <p class="mb-0 small">Av. Librerías 161, Ciudad de México, Tlalpan</p>
                        </div>
                    </div>

                    <div class="d-flex mb-3">
                        <i class="bi bi-telephone-fill fs-4 me-3"></i>
                        <div>
                            <h6 class="fw-bold mb-0">Teléfono</h6>
                            <p class="mb-0 small">+52 55 1234 5678</p>
                        </div>
                    </div>

                    <div class="d-flex mb-3">
                        <i class="bi bi-envelope-fill fs-4 me-3"></i>
                        <div>
                            <h6 class="fw-bold mb-0">Correo Electrónico</h6>
                            <p class="mb-0 small">gmelo@libreriasmelo.com</p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h6 class="fw-bold">Horario de Atención</h6>
                        <p class="small mb-0">Lunes a Viernes: 9:00 AM - 6:00 PM</p>
                        <p class="small">Sábados: 10:00 AM - 2:00 PM</p>
                    </div>

                    <div class="mt-auto pt-4">
                        <!-- Solo muestra, no apuntan a ningun lado -->
                        <a href="#" class="text-white me-3 text-decoration-none"><i class="bi bi-facebook fs-4"></i></a>
                        <a href="#" class="text-white me-3 text-decoration-none"><i class="bi bi-instagram fs-4"></i></a>
                        <a href="#" class="text-white text-decoration-none"><i class="bi bi-twitter-x fs-4"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body p-4">
                    <h4 class="mb-3 text-primary">Envíanos un mensaje</h4>
                    
                    <?php echo $mensaje_envio; ?>

                    <form action="" method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nombre" class="form-label small fw-bold">Nombre Completo</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required placeholder="Tu nombre">
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label small fw-bold">Correo Electrónico</label>
                                <input type="email" class="form-control" id="email" name="email" required placeholder="nombre@ejemplo.com">
                            </div>
                            <div class="col-12">
                                <label for="asunto" class="form-label small fw-bold">Asunto</label>
                                <input type="text" class="form-control" id="asunto" name="asunto" required placeholder="¿En qué podemos ayudarte?">
                            </div>
                            <div class="col-12">
                                <label for="mensaje" class="form-label small fw-bold">Mensaje</label>
                                <textarea class="form-control" id="mensaje" name="mensaje" rows="5" required placeholder="Escribe tu mensaje aquí..."></textarea>
                            </div>
                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-primary px-4 py-2">
                                    <i class="bi bi-send-fill me-2"></i> Enviar Mensaje
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>