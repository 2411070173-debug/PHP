<?php
/**
 * crud_handler.php - Manejador de operaciones CRUD
 * 
 * Este archivo maneja todas las operaciones de la base de datos:
 * - CREATE: Crear nuevos usuarios
 * - READ: Leer/mostrar usuarios
 * - UPDATE: Editar usuarios
 * - DELETE: Eliminar usuarios
 */

require_once __DIR__ . '/includes/conexionpdo.php';

// ============================================================================
// FUNCIONES DE LECTURA
// ============================================================================

/**
 * Obtiene todos los usuarios
 * 
 * @param string $search Texto de búsqueda (opcional)
 * @return array Array de usuarios
 */
function getAllUsers($search = '') {
    global $pdo;
    
    try {
        if (!empty($search)) {
            $query = "SELECT * FROM users 
                      WHERE username LIKE ? OR email LIKE ? 
                      ORDER BY id DESC";
            $stmt = $pdo->prepare($query);
            $search_term = "%$search%";
            $stmt->execute([$search_term, $search_term]);
        } else {
            $query = "SELECT * FROM users ORDER BY id DESC";
            $stmt = $pdo->prepare($query);
            $stmt->execute();
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Error en getAllUsers: ' . $e->getMessage());
        return [];
    }
}

/**
 * Obtiene un usuario por ID
 * 
 * @param int $id ID del usuario
 * @return array|null Usuario o null si no existe
 */
function getUserById($id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Error en getUserById: ' . $e->getMessage());
        return null;
    }
}

// ============================================================================
// FUNCIONES DE CREACIÓN
// ============================================================================

/**
 * Crea un nuevo usuario
 * 
 * @param string $username Nombre de usuario
 * @param string $email Email
 * @param string $password Contraseña (opcional)
 * @return array Array con resultado ['success' => bool, 'message' => string, 'id' => int]
 */
function createUser($username, $email, $phone = '', $password = '') {
    global $pdo;
    
    try {
        // Validar campos requeridos
        if (empty($username) || empty($email)) {
            return ['success' => false, 'message' => 'Usuario y Email son requeridos'];
        }
        
        // Validar email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Email inválido'];
        }
        
        // Verificar si el usuario ya existe
        $check = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
        $check->execute([$username, $email]);
        
        if ($check->fetchColumn() > 0) {
            return ['success' => false, 'message' => 'Usuario o Email ya existe'];
        }
        
        // Generar contraseña si no se proporciona
        if (empty($password)) {
            $password = bin2hex(random_bytes(8));
        }
        
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        
        // Insertar usuario
        $stmt = $pdo->prepare("
            INSERT INTO users (username, email, password) 
            VALUES (?, ?, ?)
        ");
        
        $result = $stmt->execute([$username, $email, $passwordHash]);
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'Usuario creado exitosamente',
                'id' => $pdo->lastInsertId()
            ];
        }
        
        return ['success' => false, 'message' => 'Error al crear usuario'];
    } catch (PDOException $e) {
        error_log('Error en createUser: ' . $e->getMessage());
        return ['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()];
    }
}

// ============================================================================
// FUNCIONES DE ACTUALIZACIÓN
// ============================================================================

/**
 * Actualiza un usuario
 * 
 * @param int $id ID del usuario
 * @param string $username Nombre de usuario
 * @param string $email Email
 * @return array Array con resultado ['success' => bool, 'message' => string]
 */
function updateUser($id, $username, $email, $phone = '') {
    global $pdo;
    
    try {
        // Validar campos requeridos
        if (empty($username) || empty($email)) {
            return ['success' => false, 'message' => 'Usuario y Email son requeridos'];
        }
        
        // Validar email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Email inválido'];
        }
        
        // Verificar si el usuario existe
        $check = $pdo->prepare("SELECT id FROM users WHERE id = ?");
        $check->execute([$id]);
        
        if (!$check->fetch()) {
            return ['success' => false, 'message' => 'Usuario no encontrado'];
        }
        
        // Verificar si email o username ya existe en otro usuario
        $emailCheck = $pdo->prepare("SELECT COUNT(*) FROM users WHERE (email = ? OR username = ?) AND id != ?");
        $emailCheck->execute([$email, $username, $id]);
        
        if ($emailCheck->fetchColumn() > 0) {
            return ['success' => false, 'message' => 'Email o Usuario ya existe'];
        }
        
        // Actualizar usuario
        $stmt = $pdo->prepare("
            UPDATE users 
            SET username = ?, email = ? 
            WHERE id = ?
        ");
        
        $result = $stmt->execute([$username, $email, $id]);
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'Usuario actualizado exitosamente'
            ];
        }
        
        return ['success' => false, 'message' => 'Error al actualizar usuario'];
    } catch (PDOException $e) {
        error_log('Error en updateUser: ' . $e->getMessage());
        return ['success' => false, 'message' => 'Error en la base de datos'];
    }
}

// ============================================================================
// FUNCIONES DE ELIMINACIÓN
// ============================================================================

/**
 * Elimina un usuario
 * 
 * @param int $id ID del usuario a eliminar
 * @return array Array con resultado ['success' => bool, 'message' => string]
 */
function deleteUser($id) {
    global $pdo;
    
    try {
        // Verificar si el usuario existe
        $check = $pdo->prepare("SELECT id FROM users WHERE id = ?");
        $check->execute([$id]);
        
        if (!$check->fetch()) {
            return ['success' => false, 'message' => 'Usuario no encontrado'];
        }
        
        // Eliminar usuario
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $result = $stmt->execute([$id]);
        
        if ($result) {
            return ['success' => true, 'message' => 'Usuario eliminado exitosamente'];
        }
        
        return ['success' => false, 'message' => 'Error al eliminar usuario'];
    } catch (PDOException $e) {
        error_log('Error en deleteUser: ' . $e->getMessage());
        return ['success' => false, 'message' => 'Error en la base de datos'];
    }
}

// ============================================================================
// FUNCIÓN PARA OBTENER ESTADÍSTICAS
// ============================================================================

/**
 * Obtiene estadísticas de usuarios
 * 
 * @return array Array con estadísticas
 */
function getUserStats() {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM users");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return [
            'total_users' => $result['total'] ?? 0,
            'last_updated' => date('Y-m-d H:i:s')
        ];
    } catch (PDOException $e) {
        error_log('Error en getUserStats: ' . $e->getMessage());
        return ['total_users' => 0];
    }
}

?>
