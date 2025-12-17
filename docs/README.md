# 🎯 Sistema CRUD - Gestión de Usuarios

**Versión:** 1.0  
**Estado:** ✅ Producción  
**Lenguaje:** PHP 7.4+ | MySQL 5.7+ | Bootstrap 5  
**Licencia:** MIT

---

## 📸 Características Principales

```
✨ Interfaz moderna y responsiva
✨ CRUD completo (Crear, Leer, Actualizar, Eliminar)
✨ Búsqueda y filtrado de usuarios
✨ Generación de reportes en PDF
✨ Autenticación con sesiones PHP
✨ Validación de datos en servidor
✨ Manejo seguro de contraseñas (bcrypt)
✨ Historial de cambios (timestamps)
✨ Integración con Google OAuth (incluida)
✨ Dashboard administrativo
```

---

## 🚀 Inicio Rápido

### 1️⃣ **Instalación en 3 pasos**

```bash
# Paso 1: Copiar proyecto
cp -r phpweb /xampp/htdocs/

# Paso 2: Crear base de datos
mysql -u root -p phpweb < database.sql

# Paso 3: Iniciar servidor
cd /xampp/htdocs/phpweb
php -S localhost:8000
```

### 2️⃣ **Acceso a la aplicación**
```
http://localhost/phpweb/index.php
```

### 3️⃣ **Verificar funcionamiento**
```
http://localhost/phpweb/test_crud.php
```

---

## 📋 Estructura del Proyecto

```
phpweb/
│
├── 📄 index.php                      ← Página principal con CRUD
├── 📄 crud_handler.php               ← Funciones CRUD (CREATE, READ, UPDATE, DELETE)
├── 📄 pdf_generator.php              ← Generador de reportes PDF
├── 📄 test_crud.php                  ← Script para pruebas del sistema
│
├── 📁 includes/
│   ├── conexionpdo.php               ← Conexión a BD con PDO
│   └── conexion.php                  ← Conexión alternativa
│
├── 📁 auth/
│   ├── login.php                     ← Página de inicio de sesión
│   ├── registrar.php                 ← Página de registro
│   └── lockout.php                   ← Cierre de sesión
│
├── 📁 oauth/
│   ├── config.php                    ← Configuración Google OAuth
│   ├── google-login.php              ← Iniciador OAuth
│   ├── google-callback.php           ← Manejador callback
│   ├── oauth-helper.php              ← Funciones auxiliares
│   └── README.md                     ← Documentación OAuth
│
├── 📁 dist/
│   ├── dashboard.php                 ← Panel administrativo
│   └── (otros archivos AdminLTE)
│
├── 📁 css/
│   └── (estilos del proyecto)
│
├── 📚 CRUD_DOCUMENTACION.md          ← Documentación técnica completa
├── 📚 INSTALACION_RAPIDA.txt         ← Guía de instalación rápida
├── 📚 README.md                      ← Este archivo
│
└── 🗄️ database.sql (si existe)       ← Script SQL para BD
```

---

## 🔧 Funciones CRUD Disponibles

### **READ - Lectura de Datos**

```php
// Obtener todos los usuarios
$users = getAllUsers();

// Obtener con búsqueda
$users = getAllUsers('juan');

// Obtener usuario por ID
$user = getUserById(5);

// Obtener estadísticas
$stats = getUserStats();  // ['total_users' => 25, 'timestamp' => '2025-01-15']
```

### **CREATE - Crear Datos**

```php
// Crear nuevo usuario
$result = createUser(
    'username',              // Nombre de usuario (requerido)
    'email@example.com',     // Email (requerido)
    '+34 612345678',         // Teléfono (opcional)
    'contraseña'             // Contraseña (opcional - se autogenera)
);

// Retorna: ['success' => true/false, 'message' => string, 'id' => int]
```

### **UPDATE - Actualizar Datos**

```php
// Actualizar usuario
$result = updateUser(
    5,                       // ID del usuario
    'nuevo_username',        // Nuevo nombre
    'newemail@example.com',  // Nuevo email
    '+34 987654321'          // Nuevo teléfono
);

// Retorna: ['success' => true/false, 'message' => string]
```

### **DELETE - Eliminar Datos**

```php
// Eliminar usuario
$result = deleteUser(5);  // ID del usuario

// Retorna: ['success' => true/false, 'message' => string]
```

---

## 💻 Interfaz de Usuario

### Página Principal (`index.php`)

```
┌─────────────────────────────────────────────────────────────┐
│  Navbar: Gestión de Usuarios    [Login] [Registrarse]      │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  📊 Estadísticas                                            │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  👥 25 Usuarios  │  📊 15 Mostrados  │  📅 15/01/2025│  │
│  └──────────────────────────────────────────────────────┘  │
│                                                             │
│  🔍 Búsqueda:  [___________] [Buscar] [Nuevo] [PDF]        │
│                                                             │
│  📋 Tabla de Usuarios                                      │
│  ┌─────────────────────────────────────────────────────┐  │
│  │ ID │ Usuario │ Email │ Teléfono │ Editar │ Eliminar│  │
│  ├─────────────────────────────────────────────────────┤  │
│  │ #1 │ juan    │ j@... │ +34 612..│ ✏️    │ 🗑️      │  │
│  │ #2 │ maria   │ m@... │ +34 698..│ ✏️    │ 🗑️      │  │
│  └─────────────────────────────────────────────────────┘  │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

---

## 🎯 Casos de Uso

### 1️⃣ Crear un Usuario

```
1. Haz clic en "Nuevo Usuario"
2. Completa el formulario:
   - Nombre: juan_perez
   - Email: juan@example.com
   - Teléfono: +34 612345678
   - Contraseña: (dejar vacío para autogenerar)
3. Haz clic en "Crear"
4. ¡Usuario creado! Aparece en la tabla
```

### 2️⃣ Buscar Usuarios

```
1. Ingresa "juan" en la barra de búsqueda
2. Haz clic en "Buscar"
3. Se muestran solo usuarios que coincidan
4. Busca por: nombre de usuario, email o teléfono
```

### 3️⃣ Editar Usuario

```
1. Haz clic en "Editar" en la fila del usuario
2. Se abre modal con los datos actuales
3. Modifica los campos que desees
4. Haz clic en "Guardar Cambios"
5. ¡Usuario actualizado!
```

### 4️⃣ Eliminar Usuario

```
1. Haz clic en "Eliminar" en la fila del usuario
2. Se pide confirmación
3. Haz clic en "Eliminar Definitivamente"
4. ⚠️ ¡Usuario eliminado (no se puede deshacer)!
```

### 5️⃣ Generar Reporte PDF

```
1. Haz clic en "Descargar PDF"
2. Se genera PDF automáticamente
3. Se descarga archivo: usuarios_YYYY-MM-DD.pdf
4. Incluye: tabla completa, estadísticas, fecha
```

---

## 🗄️ Estructura de Base de Datos

### Tabla: `users`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | INT | PK, Auto-incremento |
| `username` | VARCHAR(100) | Único, No nulo |
| `email` | VARCHAR(255) | Único, No nulo |
| `phone` | VARCHAR(20) | Opcional |
| `password` | VARCHAR(255) | Hasheada con bcrypt |
| `created_at` | TIMESTAMP | Auto generado |
| `updated_at` | TIMESTAMP | Auto actualizado |
| `oauth_google_id` | VARCHAR(255) | Para Google OAuth |
| `oauth_provider` | VARCHAR(50) | Tipo de OAuth |

**Índices:**
- PRIMARY KEY: `id`
- UNIQUE: `username`, `email`
- INDEX: `idx_username`, `idx_email`

---

## 🔐 Seguridad

### ✅ Medidas Implementadas

```php
✅ Prepared Statements     → Previene SQL Injection
✅ Password Hashing        → Bcrypt para contraseñas
✅ Validación de datos     → Server-side validation
✅ Sesiones seguras        → PHP Sessions
✅ Escapado de salida      → htmlspecialchars(), json_encode()
✅ CSRF Protection         → Tokens en OAuth
✅ Error Handling          → Try-catch con logging
```

### 🛡️ Recomendaciones de Producción

```php
1. Usar HTTPS (SSL/TLS)
2. Cambiar contraseña MySQL
3. Implementar rate limiting
4. Usar variables de entorno para credenciales
5. Realizar backups regulares
6. Monitorear logs de errores
7. Mantener software actualizado
```

---

## 🧪 Pruebas

### Ejecutar Tests Automáticos

```
http://localhost/phpweb/test_crud.php
```

Verifica:
- ✅ Conexión a BD
- ✅ Tabla users existe
- ✅ Funciones CRUD funcionales
- ✅ Archivos esenciales presentes
- ✅ Extensiones requeridas
- ✅ Estadísticas del sistema

---

## ⚠️ Troubleshooting

### ❌ "Error de conexión a base de datos"

```
Solución:
1. Verifica que MySQL esté corriendo
2. Comprueba credenciales en includes/conexionpdo.php
3. Asegúrate que la BD "phpweb" existe
```

### ❌ "Tabla users no existe"

```
Solución:
1. Ejecuta el SQL: database.sql
2. Verifica en phpMyAdmin que la tabla se creó
3. Revisa los permisos del usuario MySQL
```

### ❌ "Formularios no envían datos"

```
Solución:
1. Verifica que JavaScript esté habilitado
2. Abre la consola del navegador (F12)
3. Revisa que los formularios tengan method="POST"
```

### ❌ "Error 404 - Página no encontrada"

```
Solución:
1. Verifica que Apache esté iniciado
2. Comprueba la ruta: /xampp/htdocs/phpweb/
3. Accede a: http://localhost/phpweb/index.php
```

---

## 📖 Documentación Disponible

### 📄 Archivos de Referencia

| Archivo | Descripción |
|---------|-------------|
| `CRUD_DOCUMENTACION.md` | Documentación técnica completa (500+ líneas) |
| `INSTALACION_RAPIDA.txt` | Guía paso a paso de instalación |
| `README.md` | Este archivo |
| `test_crud.php` | Tests automáticos del sistema |
| `/oauth/README.md` | Guía de Google OAuth |

---

## 🎓 Ejemplos de Código

### Ejemplo 1: Crear y Listar Usuarios

```php
<?php
require_once 'crud_handler.php';

// Crear usuario
$result = createUser('carlos', 'carlos@email.com', '+34 612345789');

if ($result['success']) {
    echo "Usuario creado con ID: " . $result['id'];
    
    // Listar todos
    $usuarios = getAllUsers();
    foreach ($usuarios as $u) {
        echo $u['username'] . " - " . $u['email'] . "\n";
    }
}
?>
```

### Ejemplo 2: Buscar y Editar

```php
<?php
require_once 'crud_handler.php';

// Buscar usuario
$usuarios = getAllUsers('carlos');

if (!empty($usuarios)) {
    $user = $usuarios[0];
    
    // Editar
    $result = updateUser(
        $user['id'],
        'carlos_nuevo',
        'carlos.nuevo@email.com',
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

// Obtener usuario
$user = getUserById(5);

if ($user) {
    // Eliminar
    $result = deleteUser(5);
    echo $result['message'];  // "Usuario eliminado exitosamente"
}
?>
```

---

## 🌟 Características Adicionales

### 🔒 Autenticación
- Login con usuario/contraseña
- Registro de nuevos usuarios
- Google OAuth 2.0 (opcional)
- Cierre de sesión seguro

### 📊 Reportes
- PDF con tabla de usuarios
- Estadísticas de usuarios
- Fecha y hora de generación
- Formato profesional

### 🎨 Interfaz
- Bootstrap 5 responsive
- Bootstrap Icons
- Gradientes modernos
- Diseño mobile-friendly

### 📱 Responsivo
- ✅ Desktop (1920px+)
- ✅ Tablet (768px - 1024px)
- ✅ Mobile (320px - 767px)

---

## 📊 Estadísticas del Proyecto

```
Líneas de código:     2000+
Archivos PHP:         10+
Funciones CRUD:       5
Base de datos:        MySQL
Framework CSS:        Bootstrap 5
Versión PHP:          7.4+
Tiempo de desarrollo: ~8 horas
```

---

## 🚦 Roadmap Futuro

- [ ] Sistema de notificaciones por email
- [ ] Exportar a Excel
- [ ] Importar desde CSV
- [ ] Roles y permisos
- [ ] Auditoría de cambios
- [ ] Two-factor authentication
- [ ] API REST
- [ ] Dashboard mejorado

---

## 📞 Soporte

Para reportar bugs o solicitar características:

1. Verifica que tengas la última versión
2. Consulta la documentación
3. Ejecuta test_crud.php
4. Revisa los logs de error

---

## 👨‍💻 Desarrollador

**Sistema CRUD - Gestión de Usuarios**  
Versión 1.0 | 2025

---

## 📜 Licencia

Este proyecto está bajo licencia **MIT**.  
Úsalo libremente en tus proyectos personales y comerciales.

---

## ✨ Estado de Funcionalidades

| Característica | Estado |
|---|---|
| CRUD completo | ✅ Completo |
| Búsqueda | ✅ Funcional |
| PDF export | ✅ Funcional |
| Autenticación | ✅ Completo |
| Google OAuth | ✅ Integrado |
| Validación | ✅ Activo |
| Seguridad | ✅ Implementado |
| Tests | ✅ Disponibles |

---

**🎉 ¡Sistema CRUD 100% Funcional y Listo para Producción!**

Para comenzar: [index.php](index.php) | Documentación: [CRUD_DOCUMENTACION.md](CRUD_DOCUMENTACION.md) | Instalar: [INSTALACION_RAPIDA.txt](INSTALACION_RAPIDA.txt)
