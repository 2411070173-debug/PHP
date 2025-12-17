# 🔑 Referencia Rápida CRUD - API Functions

## Inicio Rápido

```php
<?php
require_once 'crud_handler.php';

// Obtener todos los usuarios
$users = getAllUsers();

// Crear usuario
$result = createUser('username', 'email@example.com', '+34 123456789', 'password');

// Actualizar usuario
$result = updateUser($id, 'nuevo_username', 'nuevo@email.com', '+34 987654321');

// Eliminar usuario
$result = deleteUser($id);

// Obtener usuario por ID
$user = getUserById($id);

// Obtener estadísticas
$stats = getUserStats();
?>
```

---

## 📚 Referencia Completa de Funciones

### 🔍 **getAllUsers($search = '')**

**Descripción:** Obtiene todos los usuarios con búsqueda opcional

**Parámetros:**
- `$search` (string, opcional): Término de búsqueda por username, email o teléfono

**Retorna:** 
- Array de usuarios o array vacío

**Ejemplo:**
```php
// Todos los usuarios
$users = getAllUsers();

// Búsqueda
$results = getAllUsers('juan');

// Iterar resultados
foreach ($users as $user) {
    echo $user['id'];       // int
    echo $user['username']; // string
    echo $user['email'];    // string
    echo $user['phone'];    // string|null
}
```

---

### ➕ **createUser($username, $email, $phone, $password)**

**Descripción:** Crea un nuevo usuario en la base de datos

**Parámetros:**
- `$username` (string, requerido): Nombre de usuario único
- `$email` (string, requerido): Email válido y único
- `$phone` (string, opcional): Número de teléfono
- `$password` (string, opcional): Contraseña (se autogenera si está vacía)

**Retorna:**
```php
[
    'success' => bool,     // true/false
    'message' => string,   // Mensaje de resultado
    'id' => int            // ID del usuario creado (si éxito)
]
```

**Validaciones Automáticas:**
- ✅ Username y Email requeridos
- ✅ Email válido (formato)
- ✅ Username único
- ✅ Email único
- ✅ Contraseña hasheada con bcrypt

**Ejemplo:**
```php
// Crear usuario completo
$result = createUser(
    'carlos',
    'carlos@example.com',
    '+34 612345678',
    'miContraseña123'
);

if ($result['success']) {
    echo "Usuario #" . $result['id'] . " creado";
} else {
    echo "Error: " . $result['message'];
}

// Crear con contraseña autogenerada
$result = createUser('maria', 'maria@example.com', '+34 698765432');
```

---

### 🔄 **updateUser($id, $username, $email, $phone)**

**Descripción:** Actualiza datos de un usuario existente

**Parámetros:**
- `$id` (int, requerido): ID del usuario
- `$username` (string, requerido): Nuevo nombre de usuario
- `$email` (string, requerido): Nuevo email
- `$phone` (string, requerido): Nuevo teléfono

**Retorna:**
```php
[
    'success' => bool,
    'message' => string
]
```

**Validaciones:**
- ✅ Usuario debe existir
- ✅ Email válido
- ✅ Username no duplicado
- ✅ Email no duplicado

**Ejemplo:**
```php
$result = updateUser(
    5,
    'carlos_updated',
    'carlos.new@example.com',
    '+34 912345678'
);

if ($result['success']) {
    echo "Usuario actualizado correctamente";
} else {
    echo "Error: " . $result['message'];
}
```

---

### 🗑️ **deleteUser($id)**

**Descripción:** Elimina un usuario de la base de datos

**Parámetros:**
- `$id` (int, requerido): ID del usuario a eliminar

**Retorna:**
```php
[
    'success' => bool,
    'message' => string
]
```

**Validación:**
- ✅ Usuario debe existir

**Ejemplo:**
```php
$result = deleteUser(5);

if ($result['success']) {
    echo "Usuario eliminado";
} else {
    echo "Error: " . $result['message'];
}
```

---

### 👤 **getUserById($id)**

**Descripción:** Obtiene datos de un usuario específico

**Parámetros:**
- `$id` (int, requerido): ID del usuario

**Retorna:**
```php
[
    'id' => int,
    'username' => string,
    'email' => string,
    'phone' => string|null,
    'password' => string,      // Hasheada
    'oauth_google_id' => string|null,
    'oauth_provider' => string|null,
    'created_at' => string,
    'updated_at' => string
]
// o null si no existe
```

**Ejemplo:**
```php
$user = getUserById(5);

if ($user) {
    echo "Usuario: " . $user['username'];
    echo "Email: " . $user['email'];
    echo "Creado: " . $user['created_at'];
} else {
    echo "Usuario no encontrado";
}
```

---

### 📊 **getUserStats()**

**Descripción:** Obtiene estadísticas generales de usuarios

**Parámetros:** Ninguno

**Retorna:**
```php
[
    'total_users' => int,
    'timestamp' => string  // Fecha actual
]
```

**Ejemplo:**
```php
$stats = getUserStats();

echo "Total de usuarios: " . $stats['total_users'];
echo "Generado: " . $stats['timestamp'];
```

---

## 🧪 Ejemplos de Casos de Uso

### Caso 1: Listar todos con búsqueda

```php
<?php
require_once 'crud_handler.php';

// Obtener parámetro de búsqueda desde formulario
$search = $_GET['q'] ?? '';

// Buscar usuarios
$users = getAllUsers($search);

// Mostrar resultados
if (empty($users)) {
    echo "No se encontraron usuarios";
} else {
    foreach ($users as $user) {
        echo "<p>" . $user['username'] . " (" . $user['email'] . ")</p>";
    }
}
?>
```

### Caso 2: Crear usuario desde formulario

```php
<?php
require_once 'crud_handler.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = createUser(
        $_POST['username'],
        $_POST['email'],
        $_POST['phone'] ?? '',
        $_POST['password'] ?? ''
    );
    
    if ($result['success']) {
        header("Location: usuarios.php?msg=Creado");
    } else {
        $error = $result['message'];
    }
}
?>

<form method="POST">
    <input type="text" name="username" required>
    <input type="email" name="email" required>
    <input type="text" name="phone">
    <input type="password" name="password">
    <button type="submit">Crear</button>
</form>

<?php if (isset($error)): ?>
    <div class="error"><?php echo $error; ?></div>
<?php endif; ?>
```

### Caso 3: Validar datos antes de crear

```php
<?php
require_once 'crud_handler.php';

function validarUsuario($username, $email, $phone) {
    $errores = [];
    
    // Validar username
    if (strlen($username) < 3) {
        $errores[] = "Username debe tener al menos 3 caracteres";
    }
    
    // Validar email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "Email inválido";
    }
    
    // Validar teléfono (si se proporciona)
    if (!empty($phone) && !preg_match('/^\+?[0-9]{7,15}$/', $phone)) {
        $errores[] = "Formato de teléfono inválido";
    }
    
    return $errores;
}

// Usar validación
$errors = validarUsuario($_POST['username'], $_POST['email'], $_POST['phone']);

if (empty($errors)) {
    $result = createUser($_POST['username'], $_POST['email'], $_POST['phone']);
    // Procesar resultado
} else {
    // Mostrar errores
    foreach ($errors as $error) {
        echo "<p class='error'>$error</p>";
    }
}
?>
```

### Caso 4: Editar usuario

```php
<?php
require_once 'crud_handler.php';

$user_id = $_GET['id'];

// Mostrar datos actuales
$user = getUserById($user_id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Actualizar
    $result = updateUser(
        $user_id,
        $_POST['username'],
        $_POST['email'],
        $_POST['phone']
    );
    
    if ($result['success']) {
        echo "Usuario actualizado";
    } else {
        echo "Error: " . $result['message'];
    }
}
?>

<form method="POST">
    <input type="text" name="username" value="<?php echo $user['username']; ?>" required>
    <input type="email" name="email" value="<?php echo $user['email']; ?>" required>
    <input type="text" name="phone" value="<?php echo $user['phone']; ?>">
    <button type="submit">Guardar</button>
</form>
```

### Caso 5: Eliminar con confirmación

```php
<?php
require_once 'crud_handler.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['confirm'] === 'yes') {
    $result = deleteUser($_POST['user_id']);
    
    if ($result['success']) {
        echo "Usuario eliminado";
    } else {
        echo "Error: " . $result['message'];
    }
} else {
    $user = getUserById($_GET['id']);
    ?>
    <form method="POST">
        <p>¿Estás seguro que deseas eliminar a <?php echo $user['username']; ?>?</p>
        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
        <input type="hidden" name="confirm" value="yes">
        <button type="submit" class="btn-danger">Eliminar</button>
    </form>
    <?php
}
?>
```

---

## 🔴 Códigos de Error Comunes

| Mensaje | Causa | Solución |
|---------|-------|----------|
| "Usuario y Email son requeridos" | Falta username o email | Completa ambos campos |
| "Email inválido" | Formato email incorrecto | Usa: usuario@dominio.com |
| "Usuario o Email ya existe" | Datos duplicados | Usa otros datos únicos |
| "Usuario no existe" | ID inválido | Verifica que el ID existe |
| "Error de conexión" | BD no disponible | Inicia MySQL |

---

## 💡 Consejos Prácticos

### 🎯 Validación en Cliente y Servidor

```php
// Siempre validar en SERVIDOR también
function validarServerSide($data) {
    $errors = [];
    
    if (empty($data['username'])) {
        $errors[] = "Username requerido";
    }
    
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email inválido";
    }
    
    return $errors;
}
```

### 🔒 Contraseñas Seguras

```php
// NO almacenar en texto plano
$password = $_POST['password'];

// Usar createUser - ya hashea automáticamente
$result = createUser($username, $email, $phone, $password);

// O si necesitas hashear manualmente:
$hashed = password_hash($password, PASSWORD_BCRYPT);
```

### 📱 Búsqueda Responsive

```php
// En tu HTML
<input type="text" id="search" placeholder="Buscar usuario...">

// JavaScript para búsqueda en tiempo real
document.getElementById('search').addEventListener('keyup', function() {
    let search = this.value;
    
    // Llamar AJAX a endpoint
    fetch('api/search.php?q=' + search)
        .then(r => r.json())
        .then(data => {
            // Actualizar tabla con resultados
            console.log(data);
        });
});
```

### ✨ Feedback Visual

```php
// Mensaje de éxito
if ($result['success']) {
    echo '<div class="alert alert-success">' . $result['message'] . '</div>';
    // Auto-desaparecer después de 3 segundos
    echo '<script>setTimeout(() => { 
        document.querySelector(".alert").style.display = "none"; 
    }, 3000);</script>';
}
```

---

## 📝 Notas Importantes

1. **Todas las funciones son seguras** - Usan prepared statements
2. **Errores registrados** - Se guardan en logs para debugging
3. **Transacciones** - Operaciones son atómicas
4. **Timestamps** - Se actualizan automáticamente
5. **Validación automática** - No necesitas validar manualmente

---

## 🆘 Debugging

### Ver últimos errores

```php
<?php
// En el inicio de tu script
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ver logs
tail -f error_log

// Verificar conexión
try {
    $user = getUserById(1);
    var_dump($user);
} catch (Exception $e) {
    echo $e->getMessage();
}
?>
```

---

## 📞 Función de Ayuda Rápida

```php
<?php
/**
 * Función de utilidad para ver estructura de datos
 */
function debugUser($id) {
    $user = getUserById($id);
    
    echo "<pre>";
    echo "Usuario ID: $id\n";
    echo "Datos: " . json_encode($user, JSON_PRETTY_PRINT);
    echo "</pre>";
    
    return $user;
}

// Uso: debugUser(5);
?>
```

---

**Versión:** 1.0 | **Última actualización:** 2025 | **Estado:** ✅ Completo
