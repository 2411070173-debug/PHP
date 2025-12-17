<?php
/**
 * google-callback.php - Procesa la respuesta de Google OAuth
 * 
 * Este archivo:
 * 1. Recibe el código de autorización de Google
 * 2. Valida el token de estado (CSRF)
 * 3. Intercambia el código por un token de acceso
 * 4. Obtiene la información del usuario
 * 5. Crea o actualiza el usuario en la base de datos
 * 6. Inicia la sesión del usuario
 * 
 * FLUJO:
 * Google → Este archivo → Base de datos → Dashboard
 */

session_start();

// Incluir configuración e includes
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../includes/conexionpdo.php';

// ============================================================================
// VALIDAR PARÁMETROS DE LA RESPUESTA
// ============================================================================

if (empty($_GET['code'])) {
    $_SESSION['error'] = ERROR_MESSAGES['no_code'];
    header('Location: ' . LOGIN_PAGE);
    exit;
}

if (empty($_GET['state']) || $_GET['state'] !== $_SESSION['oauth_state'] ?? null) {
    $_SESSION['error'] = ERROR_MESSAGES['invalid_state'];
    header('Location: ' . LOGIN_PAGE);
    exit;
}

$authCode = $_GET['code'];

// ============================================================================
// INTERCAMBIAR CÓDIGO POR TOKEN
// ============================================================================

$tokenEndpoint = 'https://oauth2.googleapis.com/token';

$tokenParams = [
    'code' => $authCode,
    'client_id' => GOOGLE_CLIENT_ID,
    'client_secret' => GOOGLE_CLIENT_SECRET,
    'redirect_uri' => GOOGLE_REDIRECT_URI,
    'grant_type' => 'authorization_code'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $tokenEndpoint);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($tokenParams));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    $_SESSION['error'] = ERROR_MESSAGES['token_error'];
    header('Location: ' . LOGIN_PAGE);
    exit;
}

$tokenData = json_decode($response, true);

if (empty($tokenData['access_token'])) {
    $_SESSION['error'] = ERROR_MESSAGES['token_error'];
    header('Location: ' . LOGIN_PAGE);
    exit;
}

$accessToken = $tokenData['access_token'];

// ============================================================================
// OBTENER INFORMACIÓN DEL USUARIO
// ============================================================================

$userInfoEndpoint = 'https://www.googleapis.com/oauth2/v2/userinfo';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $userInfoEndpoint);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $accessToken
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$userResponse = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    $_SESSION['error'] = ERROR_MESSAGES['user_info_error'];
    header('Location: ' . LOGIN_PAGE);
    exit;
}

$userInfo = json_decode($userResponse, true);

if (empty($userInfo['email']) || empty($userInfo['name'])) {
    $_SESSION['error'] = ERROR_MESSAGES['user_info_error'];
    header('Location: ' . LOGIN_PAGE);
    exit;
}

// ============================================================================
// PROCESAR USUARIO EN BASE DE DATOS
// ============================================================================

try {
    // Verificar si el usuario ya existe
    $stmt = $pdo->prepare("SELECT id, username FROM users WHERE email = ? OR oauth_google_id = ?");
    $stmt->execute([$userInfo['email'], $userInfo['id'] ?? null]);
    $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingUser) {
        // Usuario existe - actualizar información de Google
        $updateStmt = $pdo->prepare("
            UPDATE users 
            SET oauth_google_id = ?, oauth_provider = 'google', oauth_updated_at = NOW()
            WHERE id = ?
        ");
        $updateStmt->execute([$userInfo['id'], $existingUser['id']]);
        
        $userId = $existingUser['id'];
        $username = $existingUser['username'];
        $message = SUCCESS_MESSAGES['login_google'];
    } else {
        // Nuevo usuario - crear cuenta
        $username = $userInfo['email'];
        $passwordHash = password_hash(bin2hex(random_bytes(16)), PASSWORD_BCRYPT);
        
        // Obtener el rol de la sesión
        $userRole = $_SESSION['oauth_role'] ?? 'student';

        $insertStmt = $pdo->prepare("
            INSERT INTO users (username, email, password, oauth_google_id, oauth_provider, role, oauth_created_at)
            VALUES (?, ?, ?, ?, 'google', ?, NOW())
        ");
        $insertStmt->execute([
            $username,
            $userInfo['email'],
            $passwordHash,
            $userInfo['id'],
            $userRole
        ]);

        $userId = $pdo->lastInsertId();
        $message = SUCCESS_MESSAGES['account_created'];
    }

    // ========================================================================
    // OBTENER EL ROL DEL USUARIO (DE LA SESIÓN O DE LA BD)
    // ========================================================================
    
    // Primero intentar obtener el rol de la sesión (enviado desde google-login.php)
    $userRole = $_SESSION['oauth_role'] ?? 'student';
    
    // Si no está en sesión, obtener de la BD
    if (empty($userRole) || $userRole === 'student') {
        $roleStmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
        $roleStmt->execute([$userId]);
        $userRoleData = $roleStmt->fetch(PDO::FETCH_ASSOC);
        if ($userRoleData && !empty($userRoleData['role'])) {
            $userRole = $userRoleData['role'];
        } else {
            $userRole = 'student'; // Default
        }
    }

    // ========================================================================
    // INICIAR SESIÓN
    // ========================================================================

    $_SESSION['user_id'] = $userId;
    $_SESSION['username'] = $username;
    $_SESSION['email'] = $userInfo['email'];
    $_SESSION['role'] = $userRole;
    $_SESSION['oauth_provider'] = 'google';
    $_SESSION['success'] = $message;

    // Limpiar tokens
    unset($_SESSION['oauth_state']);
    unset($_SESSION['oauth_role']);

    // ========================================================================
    // REDIRIGIR SEGÚN EL ROL
    // ========================================================================
    
    if ($userRole === 'teacher') {
        header('Location: ' . APP_URL . '/dist/dashboard-profesor.php');
    } else {
        header('Location: ' . APP_URL . '/dist/dashboard-alumno.php');
    }
    exit;

} catch (PDOException $e) {
    $_SESSION['error'] = ERROR_MESSAGES['database_error'];
    error_log('Error en google-callback.php: ' . $e->getMessage());
    header('Location: ' . LOGIN_PAGE);
    exit;
}

?>
