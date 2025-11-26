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
// LÓGICA DE LOGOUT (MOVIDA AQUÍ, ANTES DEL HTML)
// ---------------------------------------------------------
if ($pagina == 'logout') {
    // 1. Destruir todas las variables de sesión
    $_SESSION = array(); 
    
    // 2. Destruir la cookie de sesión si existe
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // 3. Destruir la sesión
    session_destroy();
    
    // 4. Redirigir al login
    header("Location: index.php?page=login");
    exit; // Importante: detiene la ejecución aquí
}
// ---------------------------------------------------------


// AHORA SÍ: Incluir la cabecera común (esto genera HTML)
include 'templates/header.php';

// Lista blanca de páginas permitidas
// Nota: Ya no es estrictamente necesario tener 'logout' aquí porque se captura arriba,
// pero no hace daño dejarlo.
$paginasPermitidas = [
    'catalogo', 
    'login', 
    'registro', 
    'carrito', 
    'perfil', 
    'historial', 
    'admin', 
    'contacto'
];

// Lógica de enrutamiento
if (in_array($pagina, $paginasPermitidas)) {
    $archivoVista = "views/$pagina.php";
    
    if (file_exists($archivoVista)) {
        include $archivoVista;
    } else {
        // Opción: crear una vista 404 personalizada o solo mensaje
        echo "<div class='container py-5'><h2>Error 404: Vista no encontrada</h2></div>";
    }
} else {
    // Si la página no existe en la lista, mostrar catálogo
    include "views/catalogo.php";
}

// Incluir el pie de página común
include 'templates/footer.php';
?>