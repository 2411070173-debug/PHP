# Google OAuth Integration - Documentación Completa

## 📋 Tabla de Contenidos
1. [Requisitos](#requisitos)
2. [Configuración de Google Cloud](#configuración-de-google-cloud)
3. [Instalación](#instalación)
4. [Archivos de la Carpeta OAuth](#archivos-de-la-carpeta-oauth)
5. [Integración en Login y Register](#integración-en-login-y-register)
6. [Pruebas](#pruebas)
7. [Seguridad](#seguridad)
8. [Solución de Problemas](#solución-de-problemas)

---

## ✅ Requisitos

- PHP 7.0+
- cURL habilitado en PHP
- Base de datos MySQL
- Cuenta de Google
- Acceso a Google Cloud Console

---

## 🔧 Configuración de Google Cloud

### Paso 1: Crear un Proyecto en Google Cloud

1. Ve a https://console.cloud.google.com
2. Haz clic en "Seleccionar un proyecto" > "Nuevo proyecto"
3. Escribe un nombre (ej: "Mi Aplicación PHP")
4. Haz clic en "Crear"

### Paso 2: Habilitar APIs de Google

1. En el panel izquierdo, haz clic en "APIs y servicios"
2. Haz clic en "Biblioteca"
3. Busca "Google+ API"
4. Haz clic en ella y luego en "Habilitar"
5. Haz clic en "Crear credenciales"

### Paso 3: Crear Credenciales OAuth

1. Ve a "APIs y servicios" > "Credenciales"
2. Haz clic en "Crear credenciales" > "ID de cliente OAuth"
3. Si aparece una ventana de "Pantalla de consentimiento de OAuth":
   - Selecciona "Externo"
   - Haz clic en "Crear"
   - Completa la información básica
   - Haz clic en "Guardar y continuar"
4. Vuelve a "Crear credenciales" > "ID de cliente OAuth"
5. Selecciona "Aplicación web"

### Paso 4: Configurar URIs

En "Orígenes de JavaScript autorizados", agrega:
```
http://localhost
```

En "URIs de redirección autorizados", agrega:
```
http://localhost/phpweb/oauth/google-callback.php
```

### Paso 5: Copiar Credenciales

1. Haz clic en el cliente OAuth creado
2. Copia el "ID de cliente"
3. Copia la "Clave de cliente"

---

## 📦 Instalación

### 1. Actualizar la Base de Datos

1. Abre phpMyAdmin
2. Selecciona tu base de datos
3. Ve a la pestaña "SQL"
4. Copia el contenido de `oauth/update-database.sql`
5. Pega y ejecuta

**O ejecuta desde terminal:**
```bash
mysql -u root -p tu_base_de_datos < oauth/update-database.sql
```

### 2. Configurar Credenciales

Edita `oauth/config.php`:

```php
define('GOOGLE_CLIENT_ID', 'TU_CLIENT_ID_AQUI.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', 'TU_CLIENT_SECRET_AQUI');
```

Reemplaza:
- `TU_CLIENT_ID_AQUI` con tu ID de cliente de Google
- `TU_CLIENT_SECRET_AQUI` con tu clave secreta de Google

---

## 📁 Archivos de la Carpeta OAuth

```
oauth/
├── config.php              # Configuración de credenciales y constantes
├── google-login.php        # Inicia el flujo de autenticación
├── google-callback.php     # Procesa la respuesta de Google
├── oauth-helper.php        # Funciones auxiliares
├── update-database.sql     # Script SQL para actualizar tabla
└── README.md               # Esta documentación
```

### config.php
- Define las credenciales de Google
- Configura constantes de la aplicación
- Define mensajes de error y éxito
- **Edita este archivo con tus credenciales**

### google-login.php
- Genera un token de estado (protección CSRF)
- Construye la URL de autorización de Google
- Redirige al usuario a Google

### google-callback.php
- Recibe el código de autorización
- Intercambia el código por un token
- Obtiene la información del usuario
- Crea o actualiza la cuenta en la base de datos
- Inicia la sesión del usuario

### oauth-helper.php
- Funciones de verificación de autenticación
- Gestión de información de usuario
- Control de sesiones
- Utilidades generales

---

## 🔗 Integración en Login y Register

### En `auth/login.php`

Agrega este botón después del formulario de login tradicional:

```html
<!-- Divider -->
<hr class="my-4">

<!-- Google OAuth Button -->
<div class="text-center">
    <a href="/phpweb/oauth/google-login.php" class="btn btn-danger w-100">
        <i class="bi bi-google me-2"></i>
        Inicia sesión con Google
    </a>
</div>
```

**Ejemplo completo:**
```php
<?php
session_start();
require '../includes/conexionpdo.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Código de login tradicional...
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <form method="POST">
        <input type="text" name="username" placeholder="Usuario" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit">Iniciar Sesión</button>
    </form>

    <hr>

    <a href="/phpweb/oauth/google-login.php" class="btn btn-danger">
        <i class="bi bi-google"></i> Inicia sesión con Google
    </a>
</body>
</html>
```

### En `auth/registrar.php`

Agrega similar a login:

```html
<!-- Google OAuth Button -->
<div class="text-center">
    <p>O regístrate con:</p>
    <a href="/phpweb/oauth/google-login.php" class="btn btn-danger w-100">
        <i class="bi bi-google me-2"></i>
        Regístrate con Google
    </a>
</div>
```

### Mostrar Mensajes de Sesión

En ambos archivos, agrega al inicio del body:

```php
<?php
if (!empty($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['error']); endif; ?>

<?php if (!empty($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success']); endif; ?>
```

---

## 🧪 Pruebas

### Prueba Local

1. Accede a http://localhost/phpweb/auth/login.php
2. Haz clic en "Inicia sesión con Google"
3. Deberías ser redirigido a Google
4. Inicia sesión con tu cuenta de Google
5. Deberías ser redirigido a tu dashboard

### Prueba de Registro

1. Accede a http://localhost/phpweb/auth/registrar.php
2. Haz clic en "Regístrate con Google"
3. Si es la primera vez, se creará una cuenta automáticamente

### Verificar Base de Datos

```sql
SELECT id, username, email, oauth_google_id, oauth_provider 
FROM users 
WHERE oauth_provider = 'google';
```

---

## 🔐 Seguridad

### Protección CSRF
- Se genera un token de estado en `google-login.php`
- Se valida en `google-callback.php`
- Se almacena en la sesión

### Validación de Datos
- Se verifica el código de autorización
- Se valida el token de acceso
- Se verifica la información del usuario

### HTTPS en Producción
En producción, cambia en `config.php`:
```php
define('GOOGLE_REDIRECT_URI', 'https://tudominio.com/phpweb/oauth/google-callback.php');
```

Y registra la URI en Google Cloud Console con HTTPS.

---

## 🐛 Solución de Problemas

### "Credenciales no configuradas"
- Verifica que `GOOGLE_CLIENT_ID` y `GOOGLE_CLIENT_SECRET` estén en `config.php`
- No debes dejar los valores por defecto

### "Solicitud de autenticación inválida"
- Verifica que la URI de redirección coincida exactamente en Google Cloud Console
- Asegúrate de que la sesión está habilitada

### "El código de autorización es inválido"
- El código puede haber expirado
- Intenta de nuevo el proceso de login

### "Error al obtener la información del usuario"
- Verifica que cURL esté habilitado en PHP
- Comprueba que `php_curl` está en php.ini

### "Error en la base de datos"
- Verifica que has ejecutado `update-database.sql`
- Comprueba que las columnas oauth_* existen en la tabla users
- Verifica la conexión a la base de datos

---

## 📚 Referencias

- [Google OAuth 2.0 Documentation](https://developers.google.com/identity/protocols/oauth2)
- [Google Cloud Console](https://console.cloud.google.com)
- [PHP cURL Documentation](https://www.php.net/manual/en/book.curl.php)

---

## 📝 Notas Importantes

1. **Nunca compartas tus credenciales** - GOOGLE_CLIENT_SECRET es privado
2. **En producción**, almacena las credenciales en variables de entorno
3. **Habilita HTTPS** en producción para mayor seguridad
4. **Valida siempre** los datos que recibes de Google
5. **Registra los errores** para debugging

