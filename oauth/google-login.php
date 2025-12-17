<?php
/**
 * google-login.php - Inicia el proceso de autenticación con Google OAuth
 * 
 * Este archivo:
 * 1. Genera un token de estado para seguridad CSRF
 * 2. Construye la URL de autorización de Google
 * 3. Redirige al usuario al servidor de Google para autenticación
 * 
 * FLUJO:
 * Usuario hace clic en "Login con Google" → Este archivo → Google Authorization Server
 */

session_start();

// Incluir configuración
require_once __DIR__ . '/config.php';

// Verificar que las credenciales estén configuradas
if (GOOGLE_CLIENT_ID === 'TU_CLIENT_ID_AQUI.apps.googleusercontent.com' || 
    GOOGLE_CLIENT_SECRET === 'TU_CLIENT_SECRET_AQUI') {
    die('❌ Error: Credenciales de Google no configuradas. Por favor, actualiza oauth/config.php con tus credenciales.');
}

// ============================================================================
// GENERAR TOKEN DE ESTADO (CSRF PROTECTION)
// ============================================================================

$state = bin2hex(random_bytes(32));
$_SESSION['oauth_state'] = $state;

// ============================================================================
// CAPTURAR ROLE SELECCIONADO
// ============================================================================

$selectedRole = $_GET['role'] ?? 'student'; // Por defecto es estudiante
if (!in_array($selectedRole, ['student', 'teacher'])) {
    $selectedRole = 'student'; // Seguridad: validar que sea un rol válido
}
$_SESSION['oauth_role'] = $selectedRole; // Guardar el rol en la sesión

// ============================================================================
// CONSTRUIR URL DE AUTORIZACIÓN DE GOOGLE
// ============================================================================

$googleAuthUrl = 'https://accounts.google.com/o/oauth2/v2/auth';

$authParams = [
    'client_id' => GOOGLE_CLIENT_ID,
    'redirect_uri' => GOOGLE_REDIRECT_URI,
    'response_type' => 'code',
    'scope' => GOOGLE_SCOPES,
    'state' => $state,
    'access_type' => 'offline',
    'prompt' => 'consent'
];

$authUrl = $googleAuthUrl . '?' . http_build_query($authParams);

// ============================================================================
// REDIRIGIR A GOOGLE
// ============================================================================

header('Location: ' . $authUrl);
exit;
?>
