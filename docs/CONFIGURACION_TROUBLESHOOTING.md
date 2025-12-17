# 🔧 Guía de Configuración y Troubleshooting

## 📋 Tabla de Contenidos

1. [Verificar Instalación](#verificar-instalación)
2. [Configurar Base de Datos](#configurar-base-de-datos)
3. [Problemas Comunes](#problemas-comunes)
4. [Testing](#testing)
5. [Optimización](#optimización)

---

## ✅ Verificar Instalación

### 1. Servidor Web

```bash
# Verificar que Apache está corriendo
# Windows XAMPP:
- Abre XAMPP Control Panel
- Verifica que Apache está "running" (verde)

# Acceder a:
http://localhost/phpweb/
```

### 2. PHP

```bash
# Ver versión instalada
php -v

# Requisito: 7.4 o superior
# Ejemplo salida esperada:
# PHP 8.0.0 (cli) (built: Nov 24 2020 18:55:51)
```

### 3. MySQL

```bash
# Verificar que MySQL está corriendo
# Windows XAMPP:
- En XAMPP Control Panel, MySQL debe estar "running" (verde)

# Conectar desde terminal:
mysql -u root -p

# Si pide contraseña y dejaste en blanco, simplemente presiona Enter

# Ver bases de datos:
SHOW DATABASES;

# Ver versión:
SELECT VERSION();
```

### 4. PDO Extension

```php
<?php
// Crear archivo: test_pdo.php

if (extension_loaded('pdo')) {
    echo "✅ PDO está disponible";
} else {
    echo "❌ PDO NO está disponible";
}

if (extension_loaded('pdo_mysql')) {
    echo "✅ PDO MySQL está disponible";
} else {
    echo "❌ PDO MySQL NO está disponible";
}

// Para habilitar:
// 1. Abre: C:\xampp\php\php.ini
// 2. Busca: ;extension=pdo_mysql
// 3. Quita el ; al inicio
// 4. Reinicia Apache
?>
```

---

## 🗄️ Configurar Base de Datos

### Paso 1: Crear Base de Datos

#### Opción A: phpMyAdmin (Recomendado)

```
1. Abre: http://localhost/phpmyadmin
2. Haz clic en "Nueva"
3. Nombre: phpweb
4. Cotejamiento: utf8mb4_unicode_ci
5. Clic en "Crear"
```

#### Opción B: Terminal MySQL

```bash
mysql -u root -p

# (Presionar Enter si no hay contraseña)

CREATE DATABASE phpweb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Paso 2: Crear Tabla Users

```sql
USE phpweb;

CREATE TABLE `users` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `username` VARCHAR(100) NOT NULL UNIQUE,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `phone` VARCHAR(20),
    `password` VARCHAR(255) NOT NULL,
    `oauth_google_id` VARCHAR(255),
    `oauth_provider` VARCHAR(50),
    `oauth_created_at` TIMESTAMP NULL,
    `oauth_updated_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_username` (`username`),
    INDEX `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Verificar que se creó
SHOW TABLES;
DESCRIBE users;
```

### Paso 3: Verificar Conexión

```php
<?php
// test_conexion.php

try {
    $pdo = new PDO(
        'mysql:host=localhost;dbname=phpweb;charset=utf8mb4',
        'root',  // Usuario MySQL
        '',      // Contraseña (vacío si no tiene)
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
    
    echo "✅ Conexión exitosa";
    
    // Probar query
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
    $result = $stmt->fetch();
    echo "Usuarios en BD: " . $result['total'];
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
```

---

## ⚠️ Problemas Comunes

### Problema 1: "Connection refused" o "Can't connect to MySQL"

```
Causa: MySQL no está corriendo

Solución:
1. Abre XAMPP Control Panel
2. Haz clic en "Start" en MySQL
3. Espera a que esté en verde (running)
4. Intenta nuevamente
```

### Problema 2: "Access denied for user 'root'"

```
Causa: Contraseña incorrecta en conexionpdo.php

Solución:
1. Abre: includes/conexionpdo.php
2. Verifica:
   - $user = 'root'          (usuario MySQL)
   - $pass = ''              (contraseña - déjalo vacío si no tiene)
3. Si tienes contraseña, escríbela aquí
4. Prueba conexión nuevamente

Nota: Si la contraseña tiene caracteres especiales, usa:
$pass = 'mi@contraseña';
```

### Problema 3: "Unknown database 'phpweb'"

```
Causa: La base de datos no existe

Solución:
1. Abre phpMyAdmin: http://localhost/phpmyadmin
2. Verifica que existe la BD "phpweb"
3. Si no existe, créala:
   - Haz clic en "Nueva"
   - Nombre: phpweb
   - Haz clic en "Crear"
4. Luego ejecuta el SQL para crear la tabla
```

### Problema 4: "Table 'phpweb.users' doesn't exist"

```
Causa: La tabla users no se creó

Solución:
1. Abre phpMyAdmin
2. Selecciona BD phpweb
3. Haz clic en "SQL"
4. Copia y pega el SQL de creación de tabla
5. Haz clic en "Continuar"

O desde terminal:
mysql -u root -p phpweb < create_table.sql
```

### Problema 5: La página muestra "Error de conexión"

```
Pasos para diagnosticar:

1. Verifica XAMPP esté corriendo:
   - Apache: verde
   - MySQL: verde

2. Verifica que includes/conexionpdo.php existe

3. Revisa credenciales:
   - Host: localhost
   - Base de datos: phpweb
   - Usuario: root
   - Contraseña: (vacío o la tuya)

4. Ejecuta: http://localhost/phpweb/test_crud.php

5. Si aún falla, revisa error_log:
   - C:\xampp\apache\logs\error.log
   - C:\xampp\mysql\data\mysql.err
```

### Problema 6: "No se puede crear usuario"

```
Causas posibles:

1. El nombre de usuario o email ya existe
   - Solución: Usa datos únicos

2. Email tiene formato inválido
   - Solución: Usa: usuario@dominio.com

3. La tabla no tiene permisos de escritura
   - Solución: Verifica permisos de archivo:
   chmod 775 /xampp/htdocs/phpweb

4. El campo password es NULL
   - Solución: Usa createUser con contraseña o déjalo vacío
```

### Problema 7: PDF no se descarga

```
Causas:

1. No hay usuarios para mostrar
   - Solución: Crea al menos un usuario primero

2. Permisos de carpeta insuficientes
   - Solución: Verifica que phpweb tiene permisos de escritura

3. Encabezados HTTP ya enviados
   - Solución: Asegúrate que no hay output antes de pdf_generator.php

Prueba:
- Abre directamente: http://localhost/phpweb/pdf_generator.php?action=download
- Verifica si genera contenido
```

### Problema 8: "Sesión no funciona"

```
Causa: Sessions no están habilitadas

Verificación:
1. Crea: test_session.php

<?php
session_start();
$_SESSION['test'] = 'funciona';
echo $_SESSION['test'];
?>

2. Si funciona debería mostrar "funciona"

Solución si no funciona:
1. Verifica php.ini:
   - C:\xampp\php\php.ini
2. Busca: session.save_path
3. Asegúrate que la carpeta existe
4. Reinicia Apache
```

---

## 🧪 Testing

### Test 1: Verificar Toda la Instalación

```
1. Ve a: http://localhost/phpweb/test_crud.php
2. Deberías ver un resumen de todo el sistema
3. Todos los tests deberían estar en verde
```

### Test 2: Crear Usuario de Prueba

```
1. Ve a: http://localhost/phpweb/index.php
2. Haz clic en "Nuevo Usuario"
3. Completa:
   - Usuario: test_user
   - Email: test@example.com
   - Teléfono: +34 612345678
   - Contraseña: (dejar en blanco)
4. Haz clic en "Crear"
5. Deberías ver el usuario en la tabla
```

### Test 3: Probar CRUD

```
1. Crear: Haz clic en "Nuevo Usuario"
2. Leer: Ver en tabla
3. Actualizar: Haz clic en "Editar" y cambiar datos
4. Eliminar: Haz clic en "Eliminar" y confirmar
```

### Test 4: Generar PDF

```
1. Ve a: http://localhost/phpweb/index.php
2. Crea al menos 2 usuarios
3. Haz clic en "Descargar PDF"
4. Se debería descargar archivo PDF con los usuarios
```

---

## ⚡ Optimización

### Performance

```php
// Agregar índices
CREATE INDEX idx_created_at ON users(created_at);
CREATE INDEX idx_updated_at ON users(updated_at);

// Ver índices
SHOW INDEX FROM users;

// Analizar tabla
ANALYZE TABLE users;
```

### Caché

```php
// Agregar caché simple
$cache_file = 'cache/users.json';

if (file_exists($cache_file) && time() - filemtime($cache_file) < 3600) {
    // Usar caché de 1 hora
    $users = json_decode(file_get_contents($cache_file), true);
} else {
    // Obtener de BD y guardar en caché
    $users = getAllUsers();
    file_put_contents($cache_file, json_encode($users));
}
```

### Limitar Resultados

```php
// Agregar LIMIT a getAllUsers
$query = "SELECT * FROM users LIMIT 100";

// Para paginación
$page = $_GET['page'] ?? 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;
$query = "SELECT * FROM users LIMIT $per_page OFFSET $offset";
```

---

## 📊 Monitoreo

### Ver Logs de Error

```bash
# Windows CMD
type C:\xampp\apache\logs\error.log

# PowerShell
Get-Content C:\xampp\apache\logs\error.log

# Ver últimas líneas
Get-Content C:\xampp\apache\logs\error.log -Tail 20
```

### Estadísticas de BD

```sql
-- Tamaño de tabla
SELECT 
    table_name,
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb
FROM information_schema.tables
WHERE table_schema = 'phpweb';

-- Usuarios por fecha
SELECT DATE(created_at) as fecha, COUNT(*) as total
FROM users
GROUP BY DATE(created_at);

-- Búsqueda más usada
SELECT phone, COUNT(*) as total
FROM users
WHERE phone IS NOT NULL
GROUP BY phone;
```

---

## 🔒 Seguridad - Checklist

```
✅ Usuario MySQL con contraseña (no root vacío)
✅ BD en servidor privado
✅ HTTPS habilitado (en producción)
✅ Backups regulares de BD
✅ Monitoreo de logs
✅ Validación de entrada
✅ Contraseñas hasheadas
✅ Sesiones seguras
✅ CSRF tokens (si es necesario)
✅ Rate limiting (si es necesario)
```

---

## 🎯 Resumen de URLs Importantes

| URL | Propósito |
|-----|-----------|
| http://localhost/phpweb/ | Página principal |
| http://localhost/phpweb/index.php | CRUD |
| http://localhost/phpweb/test_crud.php | Tests |
| http://localhost/phpmyadmin | Gestor BD |
| http://localhost/phpweb/auth/login.php | Login |
| http://localhost/phpweb/auth/registrar.php | Registro |

---

## 📞 Checklist de Instalación

```
ANTES DE EMPEZAR:
☐ XAMPP instalado y corriendo
☐ Apache está "running"
☐ MySQL está "running"
☐ phpMyAdmin accesible

CONFIGURACIÓN:
☐ Base de datos "phpweb" creada
☐ Tabla "users" creada
☐ includes/conexionpdo.php actualizado con credenciales correctas
☐ Carpeta phpweb en htdocs

VERIFICACIÓN:
☐ http://localhost/phpweb/index.php carga correctamente
☐ http://localhost/phpweb/test_crud.php muestra todos tests en verde
☐ Puedes crear usuarios
☐ Puedes ver usuarios en tabla
☐ PDF se descarga correctamente

LISTO PARA USAR:
☐ Sistema CRUD completamente funcional
☐ Documentación leída
☐ Ejemplos entendidos
```

---

**Versión:** 1.0 | **Última actualización:** 2025 | **Estado:** ✅ Completo
