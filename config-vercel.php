<?php
/**
 * config-vercel.php
 * Archivo de configuración para detectar automáticamente el entorno
 * y ajustar las rutas en consecuencia
 */

// Detectar si estamos en Vercel
define('IS_VERCEL', isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']));

// Detectar si estamos en desarrollo local
define('IS_LOCAL', !IS_VERCEL && (
    $_SERVER['SERVER_NAME'] === 'localhost' || 
    $_SERVER['SERVER_ADDR'] === '127.0.0.1' ||
    strpos($_SERVER['SERVER_NAME'], 'localhost') !== false
));

// Definir la ruta base según el entorno
if (IS_VERCEL) {
    define('BASE_PATH', '');
    define('BASE_URL', 'https://' . $_SERVER['HTTP_HOST']);
} elseif (IS_LOCAL) {
    // En local, detectar si estamos en /phpweb/ o en la raíz
    $scriptName = $_SERVER['SCRIPT_NAME'];
    if (strpos($scriptName, '/phpweb/') !== false) {
        define('BASE_PATH', '/phpweb');
    } else {
        define('BASE_PATH', '');
    }
    define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST'] . BASE_PATH);
} else {
    // Otro entorno de producción
    define('BASE_PATH', '');
    define('BASE_URL', 'https://' . $_SERVER['HTTP_HOST']);
}

// Función helper para generar URLs
function url($path) {
    // Asegurar que el path empiece con /
    if ($path[0] !== '/') {
        $path = '/' . $path;
    }
    return BASE_PATH . $path;
}

// Función helper para redirecciones
function redirect($path) {
    header('Location: ' . url($path));
    exit;
}

// Información de debug (solo en desarrollo)
if (IS_LOCAL && isset($_GET['debug_config'])) {
    echo "<pre>";
    echo "IS_VERCEL: " . (IS_VERCEL ? 'true' : 'false') . "\n";
    echo "IS_LOCAL: " . (IS_LOCAL ? 'true' : 'false') . "\n";
    echo "BASE_PATH: " . BASE_PATH . "\n";
    echo "BASE_URL: " . BASE_URL . "\n";
    echo "SERVER_NAME: " . $_SERVER['SERVER_NAME'] . "\n";
    echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "\n";
    echo "\nEjemplos de uso:\n";
    echo "url('/auth/login.php') = " . url('/auth/login.php') . "\n";
    echo "url('/menu.php') = " . url('/menu.php') . "\n";
    echo "</pre>";
    exit;
}
?>
