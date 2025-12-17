<?php
/**
 * Configuración de Google OAuth
 * 
 * Este archivo contiene toda la configuración necesaria para integrar Google OAuth
 * en la aplicación. Las credenciales se obtienen desde Google Cloud Console.
 * 
 * PASOS PARA OBTENER LAS CREDENCIALES:
 * 1. Ve a https://console.cloud.google.com
 * 2. Crea un nuevo proyecto
 * 3. Ve a "APIs y servicios" > "Credenciales"
 * 4. Haz clic en "Crear credenciales" > "ID de cliente OAuth"
 * 5. Selecciona "Aplicación web"
 * 6. Agrega URIs autorizados:
 *    - http://localhost/phpweb/oauth/callback.php
 *    - http://localhost/phpweb/oauth/google-callback.php
 * 7. Copia el ID de cliente y la clave secreta
 */

// ============================================================================
// CREDENCIALES DE GOOGLE OAUTH
// ============================================================================
// REEMPLAZA ESTOS VALORES CON TUS CREDENCIALES DE GOOGLE CLOUD CONSOLE

define('GOOGLE_CLIENT_ID', '1004891282894-0kbuicovf8hcaag3ardn4ig3f4bhqiog.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', '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');
define('GOOGLE_REDIRECT_URI', 'http://localhost/phpweb/oauth/google-callback.php');

// ============================================================================
// CONFIGURACIÓN DE LA APLICACIÓN
// ============================================================================

define('APP_NAME', 'ProyectDashPHP251125');
define('APP_URL', 'http://localhost/phpweb');
define('LOGIN_PAGE', APP_URL . '/auth/login.php');
define('DASHBOARD_PAGE', APP_URL . '/dist/dashboard.php');

// ============================================================================
// SCOPES DE GOOGLE OAUTH
// ============================================================================
// Los scopes definen qué información puede acceder la aplicación

define('GOOGLE_SCOPES', implode(' ', [
    'https://www.googleapis.com/auth/userinfo.profile',
    'https://www.googleapis.com/auth/userinfo.email'
]));

// ============================================================================
// CONFIGURACIÓN DE SEGURIDAD
// ============================================================================

// Token de estado para prevenir CSRF (Cross-Site Request Forgery)
// Se genera automáticamente en el flujo de OAuth
define('SESSION_NAME', 'oauth_state');

// Tiempo de expiración de la sesión en segundos (1 hora)
define('SESSION_TIMEOUT', 3600);

// ============================================================================
// MENSAJES DE ERROR Y ÉXITO
// ============================================================================

define('ERROR_MESSAGES', [
    'invalid_state' => 'Solicitud de autenticación inválida. Por favor, intenta de nuevo.',
    'no_code' => 'No se recibió código de autorización. Por favor, intenta de nuevo.',
    'invalid_code' => 'El código de autorización es inválido. Por favor, intenta de nuevo.',
    'token_error' => 'Error al obtener el token de acceso. Por favor, intenta de nuevo.',
    'user_info_error' => 'Error al obtener la información del usuario. Por favor, intenta de nuevo.',
    'database_error' => 'Error en la base de datos. Por favor, intenta de nuevo más tarde.',
]);

define('SUCCESS_MESSAGES', [
    'login_google' => '¡Bienvenido! Has iniciado sesión con Google.',
    'account_created' => '¡Cuenta creada exitosamente con Google!',
    'account_linked' => 'Tu cuenta ha sido vinculada con Google.',
]);

?>
