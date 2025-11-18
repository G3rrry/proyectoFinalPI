<?php
// Definir la página por defecto
$pagina = isset($_GET['page']) ? $_GET['page'] : 'catalogo';

// Título dinámico según la página
$titulo = ucfirst($pagina) . " - E-Shop Pro";

// Incluir la cabecera común
include 'templates/header.php';

// Lista blanca de páginas permitidas (seguridad básica)
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
        echo "<div class='container py-5'><h2>Error 404: Archivo de vista no encontrado ($pagina)</h2></div>";
    }
} else {
    // Si la página no existe en la lista, mostrar error o redirigir al catálogo
    include "views/catalogo.php";
}

// Incluir el pie de página común
include 'templates/footer.php';
?>