<?php
// 1. INICIAR SESIÓN (Obligatorio al inicio de todo)
session_start();

// Definir la página por defecto
$pagina = isset($_GET['page']) ? $_GET['page'] : 'catalogo';

// Título dinámico según la página
$titulo = ucfirst($pagina) . " - Librerias Melo";

// Conexión a la base de datos
include 'db.php';

// ---------------------------------------------------------
// LÓGICA DE LOGOUT
// ---------------------------------------------------------
if ($pagina == 'logout') {
    $_SESSION = array(); 
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    header("Location: index.php?page=login");
    exit;
}

// ---------------------------------------------------------
// SOLUCIÓN AL CARRITO: OUTPUT BUFFERING
// ---------------------------------------------------------
// Iniciamos el buffer. Esto captura todo el HTML que generen las vistas
// pero NO lo envía al navegador todavía.
ob_start();

$paginasPermitidas = [
    'catalogo', 
    'login', 
    'registro', 
    'carrito', 
    'perfil', 
    'historial', 
    'admin', 
    'contacto',
    'producto',
];

// Incluimos la vista PRIMERO. Si es 'carrito.php', aquí se procesará el agregar/borrar
// y se actualizará la base de datos.
if (in_array($pagina, $paginasPermitidas)) {
    $archivoVista = "views/$pagina.php";
    
    if (file_exists($archivoVista)) {
        include $archivoVista;
    } else {
        echo "<div class='container py-5'><h2>Error 404: Vista no encontrada</h2></div>";
    }
} else {
    include "views/catalogo.php";
}

// Guardamos todo el HTML que generó la vista en una variable y limpiamos el buffer
$contenido_vista = ob_get_clean();

// ---------------------------------------------------------
// AHORA SÍ: RENDERIZADO FINAL
// ---------------------------------------------------------

// 1. Incluir Header: Como la vista ya se ejecutó arriba, la BD ya está actualizada
// y el contador del carrito saldrá correcto.
include 'templates/header.php';

// 2. Imprimir el contenido de la vista que guardamos
echo $contenido_vista;

// 3. Incluir Footer
include 'templates/footer.php';
?>