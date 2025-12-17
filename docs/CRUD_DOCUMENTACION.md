# 📋 Documentación Sistema CRUD - Gestión de Usuarios

**Versión:** 1.0  
**Fecha de Creación:** 2025  
**Lenguaje:** PHP 7.4+  
**Base de Datos:** MySQL 5.7+

---

## 📚 Tabla de Contenidos

1. [Descripción General](#descripción-general)
2. [Arquitectura](#arquitectura)
3. [Archivos Principales](#archivos-principales)
4. [Funciones CRUD](#funciones-crud)
5. [Uso del Sistema](#uso-del-sistema)
6. [Estructura de la Base de Datos](#estructura-de-la-base-de-datos)
7. [Guía de Instalación](#guía-de-instalación)
8. [Ejemplos de Uso](#ejemplos-de-uso)
9. [Manejo de Errores](#manejo-de-errores)
10. [Seguridad](#seguridad)

---

## 🎯 Descripción General

Este es un **Sistema CRUD (Create, Read, Update, Delete)** completo desarrollado en PHP con base de datos MySQL. Proporciona una interfaz web moderna e intuitiva para la gestión de usuarios con las siguientes características:

- ✅ **Crear usuarios** con validación de datos
- ✅ **Leer/Listar** usuarios con búsqueda avanzada
- ✅ **Editar usuarios** sin recargar la página
- ✅ **Eliminar usuarios** con confirmación
- ✅ **Generar reportes en PDF** de los usuarios
- ✅ **Autenticación segura** con sesiones PHP
- ✅ **Interfaz responsiva** con Bootstrap 5
- ✅ **Búsqueda en tiempo real** por usuario, email o teléfono

---

## 🏗️ Arquitectura

```
┌─────────────────────────────────────────────┐
│         CAPA DE PRESENTACIÓN                │
│  ┌──────────┬──────────────┬──────────────┐ │
│  │ index.php│ pdf_gen...php│  HTML/CSS/JS │ │
│  └────┬─────┴────┬─────────┴────┬─────────┘ │
└──────┼──────────┼──────────────┼────────────┘
       │          │              │
┌──────┼──────────┼──────────────┼────────────┐
│         CAPA DE LÓGICA DE NEGOCIO           │
│  ┌────────────────────────────────────────┐ │
│  │      crud_handler.php                  │ │
│  │  - getAllUsers()                       │ │
│  │  - getUserById()                       │ │
│  │  - createUser()                        │ │
│  │  - updateUser()                        │ │
│  │  - deleteUser()                        │ │
│  │  - getUserStats()                      │ │
│  └────────────────────────────────────────┘ │
└──────┼────────────────────────────────────────┘
       │
┌──────┼────────────────────────────────────────┐
│         CAPA DE ACCESO A DATOS               │
│  ┌────────────────────────────────────────┐ │
│  │    includes/conexionpdo.php            │ │
│  │  - Conexión segura a MySQL             │ │
│  │  - Uso de prepared statements          │ │
│  └────────────────────────────────────────┘ │
└──────┼────────────────────────────────────────┘
       │
┌──────┼────────────────────────────────────────┐
│         BASE DE DATOS                        │
│  ┌────────────────────────────────────────┐ │
│  │      Tabla: users                      │ │
│  │  - id (INT, PRIMARY KEY)               │ │
│  │  - username (VARCHAR 100, UNIQUE)      │ │
│  │  - email (VARCHAR 255, UNIQUE)         │ │
│  │  - phone (VARCHAR 20, NULLABLE)        │ │
│  │  - password (VARCHAR 255)              │ │
│  │  - created_at (TIMESTAMP)              │ │
│  │  - updated_at (TIMESTAMP)              │ │
│  └────────────────────────────────────────┘ │
└──────────────────────────────────────────────┘
```

---

## 📁 Archivos Principales

### 1. **index.php** - Página Principal
- Archivo público de inicio con CRUD
- Muestra tabla de usuarios
- Gestión de formularios (crear, editar, eliminar)
- Barra de búsqueda
- Botones de Login/Registro

### 2. **crud_handler.php** - Lógica CRUD
- Contiene todas las funciones de base de datos
- Validación de datos
- Manejo de errores
- Logging de operaciones

### 3. **pdf_generator.php** - Generador de PDF
- Exporta usuarios a PDF
- Incluye estadísticas
- Formato profesional
- Descarga directa

### 4. **includes/conexionpdo.php** - Conexión BD
- Conexión segura a MySQL
- Uso de PDO (PHP Data Objects)
- Manejo de errores de conexión

### 5. **auth/login.php** - Autenticación
- Formulario de inicio de sesión
- Validación de credenciales
- Gestión de sesiones

### 6. **auth/registrar.php** - Registro
- Formulario de registro de usuarios
- Validación de datos
- Hash de contraseñas

---

## 🔧 Funciones CRUD

### READ - Lectura de Datos

#### **getAllUsers($search = '')**
```php
/**
 * Obtiene todos los usuarios con búsqueda opcional
 * @param string $search Término de búsqueda
 * @return array Array de usuarios
 */
$users = getAllUsers();           // Obtener todos
$users = getAllUsers('juan');     // Buscar por username
```

#### **getUserById($id)**
```php
/**
 * Obtiene un usuario por ID
 * @param int $id ID del usuario
 * @return array|null Usuario o null
 */
$user = getUserById(5);  // Obtener usuario #5
```

#### **getUserStats()**
```php
/**
 * Obtiene estadísticas de usuarios
 * @return array ['total_users' => int, 'timestamp' => string]
 */
$stats = getUserStats();
echo $stats['total_users'];  // 25
```

---

### CREATE - Crear Datos

#### **createUser($username, $email, $phone, $password)**
```php
/**
 * Crea un nuevo usuario
 * @return array ['success' => bool, 'message' => string, 'id' => int]
 */

// Ejemplo básico
$result = createUser('juan', 'juan@email.com', '+34 612345678');
if ($result['success']) {
    echo "Usuario creado con ID: " . $result['id'];
}

// Con contraseña personalizada
$result = createUser('maria', 'maria@email.com', '+34 698765432', 'password123');

// Validaciones automáticas:
// - Username y Email requeridos
// - Email válido
// - Username y Email únicos
// - Contraseña hasheada con bcrypt
```

**Retorno:**
```php
// Éxito
['success' => true, 'message' => 'Usuario creado exitosamente', 'id' => 5]

// Error
['success' => false, 'message' => 'Email inválido']
```

---

### UPDATE - Actualizar Datos

#### **updateUser($id, $username, $email, $phone)**
```php
/**
 * Actualiza datos de un usuario
 * @return array ['success' => bool, 'message' => string]
 */

$result = updateUser(
    5,                          // ID
    'juanUpdated',             // Nuevo username
    'juan.new@email.com',      // Nuevo email
    '+34 912345678'            // Nuevo teléfono
);

if ($result['success']) {
    echo "Usuario actualizado";
}

// Validaciones:
// - Usuario debe existir
// - Email válido
// - Evita duplicados
```

**Retorno:**
```php
// Éxito
['success' => true, 'message' => 'Usuario actualizado exitosamente']

// Error
['success' => false, 'message' => 'Usuario no existe']
```

---

### DELETE - Eliminar Datos

#### **deleteUser($id)**
```php
/**
 * Elimina un usuario por ID
 * @return array ['success' => bool, 'message' => string]
 */

$result = deleteUser(5);  // Eliminar usuario #5

if ($result['success']) {
    echo "Usuario eliminado";
}

// Validación:
// - Usuario debe existir
```

**Retorno:**
```php
// Éxito
['success' => true, 'message' => 'Usuario eliminado exitosamente']

// Error
['success' => false, 'message' => 'Usuario no existe']
```

---

## 💻 Uso del Sistema

### Acceso a la Página Principal
```
http://localhost/phpweb/index.php
```

### Operaciones Disponibles

#### **1. Crear Usuario**
1. Haz clic en "Nuevo Usuario"
2. Completa el formulario (nombre, email, teléfono, contraseña opcional)
3. Haz clic en "Crear"

#### **2. Ver Usuarios**
- Se muestran automáticamente en la tabla
- Columnas: ID, Usuario, Email, Teléfono, Acciones

#### **3. Buscar Usuarios**
1. Ingresa término en la barra de búsqueda
2. Busca por: nombre usuario, email o teléfono
3. Haz clic en "Buscar" o presiona Enter

#### **4. Editar Usuario**
1. Haz clic en "Editar" en la fila del usuario
2. Se abre modal con los datos
3. Modifica los campos necesarios
4. Haz clic en "Guardar Cambios"

#### **5. Eliminar Usuario**
1. Haz clic en "Eliminar" en la fila del usuario
2. Se pide confirmación
3. Haz clic en "Eliminar Definitivamente"

#### **6. Generar PDF**
1. Haz clic en "Descargar PDF"
2. Se genera PDF con tabla de usuarios
3. Incluye estadísticas y fecha

---

## 🗄️ Estructura de la Base de Datos

### Tabla: `users`

```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email)
);
```

**Campos:**
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | INT | Identificador único |
| username | VARCHAR(100) | Nombre de usuario único |
| email | VARCHAR(255) | Correo único |
| phone | VARCHAR(20) | Teléfono (opcional) |
| password | VARCHAR(255) | Contraseña hasheada |
| created_at | TIMESTAMP | Fecha de creación |
| updated_at | TIMESTAMP | Fecha última actualización |

---

## 🚀 Guía de Instalación

### Requisitos
- PHP 7.4 o superior
- MySQL 5.7 o superior
- Servidor web (Apache, Nginx, etc.)
- XAMPP o similar

### Pasos

#### 1. **Copiar archivos**
```bash
# Copiar todo el proyecto a htdocs
cp -r phpweb /xampp/htdocs/
```

#### 2. **Crear base de datos**
```bash
# Inicia MySQL
mysql -u root -p

# Crea la base de datos
CREATE DATABASE phpweb;
USE phpweb;

# Ejecuta el script SQL
# (consulta la ruta en tu proyecto)
```

#### 3. **Configurar conexión**
Edita `includes/conexionpdo.php`:
```php
$host = 'localhost';
$db = 'phpweb';
$user = 'root';
$pass = '';  // Tu contraseña MySQL
```

#### 4. **Acceder**
```
http://localhost/phpweb/index.php
```

---

## 📖 Ejemplos de Uso

### Ejemplo 1: Crear y Listar Usuarios
```php
<?php
require_once 'crud_handler.php';

// Crear usuario
$result = createUser('carlos', 'carlos@email.com', '+34 123456789', 'pass123');

if ($result['success']) {
    echo "Usuario creado ID: " . $result['id'];
    
    // Listar todos
    $usuarios = getAllUsers();
    foreach ($usuarios as $user) {
        echo $user['username'] . ' - ' . $user['email'] . "\n";
    }
}
?>
```

### Ejemplo 2: Buscar y Editar
```php
<?php
require_once 'crud_handler.php';

// Buscar usuario por nombre
$usuarios = getAllUsers('carlos');

if (!empty($usuarios)) {
    $user = $usuarios[0];
    
    // Editar
    $result = updateUser(
        $user['id'],
        'carlos_updated',
        'carlos.new@email.com',
        '+34 987654321'
    );
    
    echo $result['message'];
}
?>
```

### Ejemplo 3: Eliminar Usuario
```php
<?php
require_once 'crud_handler.php';

$user = getUserById(5);

if ($user) {
    $result = deleteUser(5);
    echo $result['message'];  // "Usuario eliminado exitosamente"
} else {
    echo "Usuario no existe";
}
?>
```

### Ejemplo 4: Generar PDF
```php
<?php
require_once 'crud_handler.php';
require_once 'pdf_generator.php';

// Obtener usuarios
$users = getAllUsers();

// Generar PDF
generatePDF($users);

// O descargar directamente
if (isset($_GET['action']) && $_GET['action'] === 'download') {
    downloadPDF('usuarios_' . date('Y-m-d') . '.pdf');
}
?>
```

---

## ⚠️ Manejo de Errores

### Errores Comunes y Soluciones

#### Error: "Usuario o Email ya existe"
```php
// Causa: Intento de crear usuario con datos duplicados
// Solución: Usar otros username/email únicos
```

#### Error: "Email inválido"
```php
// Causa: Formato de email incorrecto
// Solución: Usar formato válido (ejemplo@dominio.com)
```

#### Error de Conexión BD
```php
// Causa: Credenciales incorrectas en conexionpdo.php
// Solución: Verificar usuario, contraseña y nombre BD
```

#### Error: "Usuario no existe"
```php
// Causa: Intento de actualizar/eliminar usuario inexistente
// Solución: Verificar ID antes de operación
```

### Logging de Errores
Todos los errores se registran en los logs de PHP:
```
error_log('Error en getAllUsers: ' . $e->getMessage());
```

Ubicación: `php_error.log` en tu servidor

---

## 🔐 Seguridad

### Medidas Implementadas

#### 1. **Prepared Statements (SQL Injection)**
```php
// ✅ Seguro
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);

// ❌ Inseguro
$query = "SELECT * FROM users WHERE email = '$email'";
```

#### 2. **Hashing de Contraseñas**
```php
// Se usa bcrypt automáticamente
$hashed = password_hash($password, PASSWORD_BCRYPT);
```

#### 3. **Validación de Datos**
```php
// Email válido
filter_var($email, FILTER_VALIDATE_EMAIL)

// Campos requeridos
if (empty($username) || empty($email)) { ... }
```

#### 4. **Escapado de Salida**
```php
// En HTML
echo htmlspecialchars($user['username']);

// En JavaScript
echo json_encode($data);
```

#### 5. **Sesiones Seguras**
```php
// Verificación de sesión
if (isset($_SESSION['user_id'])) {
    // Usuario autenticado
}
```

### Recomendaciones Adicionales

```php
// 1. Usar HTTPS en producción
// 2. Limitar acceso por IP si es necesario
// 3. Implementar CSRF tokens
// 4. Usar rate limiting para login
// 5. Validar lado servidor siempre
// 6. Mantener software actualizado
```

---

## 🧪 Pruebas

### Prueba de Funcionalidad

```php
// Prueba CRUD completo
function testCRUD() {
    // CREATE
    $result = createUser('test_user', 'test@example.com', '+34 612345678');
    assert($result['success'] === true);
    $userId = $result['id'];
    
    // READ
    $user = getUserById($userId);
    assert($user['username'] === 'test_user');
    
    // UPDATE
    $result = updateUser($userId, 'test_updated', 'test2@example.com', '+34 698765432');
    assert($result['success'] === true);
    
    // DELETE
    $result = deleteUser($userId);
    assert($result['success'] === true);
    
    echo "✅ Todas las pruebas pasaron";
}
```

---

## 📞 Soporte y Contacto

Para reportar bugs o solicitar mejoras, por favor contacta al equipo de desarrollo.

---

## 📜 Licencia

Este proyecto está bajo licencia MIT. Úsalo libremente en tus proyectos.

---

**Última actualización:** 2025  
**Versión:** 1.0  
**Estado:** ✅ Producción
