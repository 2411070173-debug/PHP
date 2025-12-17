<?php
/**
 * oauth-helper.php - Funciones auxiliares para OAuth
 * 
 * Este archivo contiene funciones útiles para:
 * - Verificar si un usuario está autenticado con OAuth
 * - Obtener información del proveedor OAuth
 * - Desvinculación de cuentas OAuth
 * - Gestión de sesiones OAuth
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../includes/conexionpdo.php';

// ============================================================================
// FUNCIONES DE VERIFICACIÓN
// ============================================================================

/**
 * Verifica si el usuario está autenticado
 * 
 * @return bool true si está autenticado, false en caso contrario
 */
function isUserAuthenticated() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Verifica si el usuario está autenticado con un proveedor específico
 * 
 * @param string $provider Nombre del proveedor (google, facebook, etc.)
 * @return bool true si está autenticado con ese proveedor
 */
function isAuthenticatedWith($provider) {
    return isset($_SESSION['oauth_provider']) && $_SESSION['oauth_provider'] === $provider;
}

/**
 * Obtiene el proveedor OAuth del usuario actual
 * 
 * @return string|null El nombre del proveedor o null
 */
function getCurrentOAuthProvider() {
    return $_SESSION['oauth_provider'] ?? null;
}

// ============================================================================
// FUNCIONES DE USUARIO
// ============================================================================

/**
 * Obtiene la información OAuth de un usuario
 * 
 * @param int $userId ID del usuario
 * @return array Array con información de OAuth
 */
function getUserOAuthInfo($userId) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT oauth_google_id, oauth_provider, oauth_created_at, oauth_updated_at
            FROM users
            WHERE id = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    } catch (PDOException $e) {
        error_log('Error en getUserOAuthInfo: ' . $e->getMessage());
        return [];
    }
}

/**
 * Vincula una cuenta OAuth a una cuenta de usuario existente
 * 
 * @param int $userId ID del usuario
 * @param string $provider Nombre del proveedor
 * @param string $providerId ID del usuario en el proveedor
 * @return bool true si fue exitoso, false en caso contrario
 */
function linkOAuthAccount($userId, $provider, $providerId) {
    global $pdo;
    
    try {
        if ($provider === 'google') {
            $stmt = $pdo->prepare("
                UPDATE users 
                SET oauth_google_id = ?, oauth_provider = ?, oauth_created_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$providerId, $provider, $userId]);
            return true;
        }
        return false;
    } catch (PDOException $e) {
        error_log('Error en linkOAuthAccount: ' . $e->getMessage());
        return false;
    }
}

/**
 * Desvincula una cuenta OAuth de una cuenta de usuario
 * 
 * @param int $userId ID del usuario
 * @param string $provider Nombre del proveedor
 * @return bool true si fue exitoso, false en caso contrario
 */
function unlinkOAuthAccount($userId, $provider) {
    global $pdo;
    
    try {
        if ($provider === 'google') {
            $stmt = $pdo->prepare("
                UPDATE users 
                SET oauth_google_id = NULL, oauth_provider = NULL, oauth_updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$userId]);
            return true;
        }
        return false;
    } catch (PDOException $e) {
        error_log('Error en unlinkOAuthAccount: ' . $e->getMessage());
        return false;
    }
}

// ============================================================================
// FUNCIONES DE SESIÓN
// ============================================================================

/**
 * Destruye la sesión del usuario
 */
function destroyOAuthSession() {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }
    session_destroy();
}

/**
 * Redirige a la página de login si no está autenticado
 * 
 * @param string $redirectUrl URL a redirigir después de autenticarse
 */
function requireLogin($redirectUrl = null) {
    if (!isUserAuthenticated()) {
        if ($redirectUrl) {
            $_SESSION['redirect_after_login'] = $redirectUrl;
        }
        header('Location: ' . LOGIN_PAGE);
        exit;
    }
}

// ============================================================================
// FUNCIONES DE INFORMACIÓN
// ============================================================================

/**
 * Obtiene el nombre del proveedor en formato legible
 * 
 * @param string $provider Código del proveedor
 * @return string Nombre legible del proveedor
 */
function getProviderName($provider) {
    $providers = [
        'google' => 'Google',
        'facebook' => 'Facebook',
        'github' => 'GitHub',
        'traditional' => 'Contraseña'
    ];
    return $providers[$provider] ?? 'Desconocido';
}

/**
 * Obtiene el ícono del proveedor
 * 
 * @param string $provider Código del proveedor
 * @return string Clase de ícono Bootstrap
 */
function getProviderIcon($provider) {
    $icons = [
        'google' => 'bi-google',
        'facebook' => 'bi-facebook',
        'github' => 'bi-github',
        'traditional' => 'bi-lock'
    ];
    return $icons[$provider] ?? 'bi-question-circle';
}

?>
