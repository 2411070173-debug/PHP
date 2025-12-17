# GUÍA DE USO - Sistema de Gestión de Usuarios con Autenticación

## Descripción General

Se ha implementado un sistema completo de gestión de usuarios con:
- ✅ Autenticación (Login/Registro)
- ✅ Dashboard personalizado para cada usuario
- ✅ CRUD de usuarios
- ✅ Gestión de fotos de perfil (local)
- ✅ Soporte para OAuth Google
- ✅ Acceso público a la tabla de usuarios sin necesidad de login

---

## 📋 Estructura del Sistema

### Acceso Público (SIN necesidad de login)
- **`index.php`** - Página principal con tabla de todos los usuarios
  - Puedes ver todos los usuarios registrados
  - Si NO estás autenticado, solo puedes ver la lista
  - Botones "Iniciar Sesión" y "Registrarse" en la parte superior

### Acceso Restringido (REQUIERE login)
- **`dashboard.php`** - Tu panel de control personalizado
  - Muestra tu información de perfil
  - Permite subir/cambiar foto de perfil
  - Opción para cerrar sesión
  - Botón para acceder al CRUD

### Autenticación
- **`login.php`** - Iniciar sesión con cuenta existente
- **`registrar.php`** - Crear nueva cuenta

### CRUD de Usuarios
- **`index.php`** (con autenticación)
  - Crear nuevo usuario
  - Ver todos los usuarios
  - Editar usuarios existentes
  - Eliminar usuarios
  - Descargar PDF de usuarios

---

## 🚀 Cómo Empezar

### Opción 1: Usar Usuarios Existentes de la Base de Datos

**Usuarios ya creados (consulta usuarios2.0.sql):**
```
1. Username: die90
   Email: ramoscortez@gmail.com
   Contraseña: [contactar administrador]
   
2. Username: fabri1907
   Email: fabricio@gmail.com
   
3. Username: ramos20
   Email: ramos20@gmail.com
   
4. Username: fabricio2025
   Email: fabricioramos@gmail.com
   
5. Username: 2411070173@undc.edu.pe
   Email: 2411070173@undc.edu.pe
   Nota: Este usuario tiene autenticación Google OAuth
```

### Opción 2: Crear un Nuevo Usuario

1. Ve a **http://localhost/phpweb/registrar.php**
2. Completa el formulario:
   - **Nombre de usuario:** Ej. `miusuario`
   - **Email:** Ej. `micorreo@gmail.com`
   - **Contraseña:** Mínimo 8 caracteres
3. Haz clic en "Registrarse"
4. Automáticamente serás redirigido a tu **Dashboard**

---

## 💻 Flujo de Navegación

### Sin Autenticación (Usuario Anónimo)

```
http://localhost/phpweb/index.php
    ↓ (Ver tabla pública)
    ↓ (Clic en "Iniciar Sesión" o "Registrarse")
    ↓
http://localhost/phpweb/login.php (O registrar.php)
```

### Con Autenticación (Usuario Logueado)

```
http://localhost/phpweb/index.php
    ↓ (Ver dropdown de usuario en navbar)
    ↓ (Opciones: Mi Perfil, Cerrar Sesión)
    ↓
http://localhost/phpweb/dashboard.php
    ↓ (Tu panel personalizado)
    ↓ (Opciones: Cambiar foto, Ver CRUD, Cerrar Sesión)
    ↓
http://localhost/phpweb/index.php (CRUD con todas las funciones)
```

---

## 🎯 Funcionalidades Detalladas

### 1. Dashboard Personal (`dashboard.php`)

**Solo accesible si estás autenticado**

Muestra:
- Foto de perfil (o foto de Google si tienes OAuth)
- Nombre de usuario
- Email registrado
- Tipo de autenticación (Local o Google OAuth)

Acciones disponibles:
- ✏️ Cambiar foto de perfil (subir JPG/PNG/GIF, máximo 5MB)
- 📋 Ver y administrar usuarios (botón "Ver CRUD")
- 🚪 Cerrar sesión

**URLs de fotos:**
- Las fotos se guardan en: `uploads/profiles/`
- Si tienes Google OAuth, se muestra automáticamente

### 2. Gestión de Usuarios (CRUD)

**Solo disponible para usuarios autenticados en `index.php`**

**Crear:**
- Botón azul "Nuevo Usuario"
- Formulario modal con campos: Username, Email, Contraseña (opcional)

**Leer:**
- Tabla con todos los usuarios
- Campos: ID, Usuario, Email, Provider, Acciones

**Actualizar:**
- Clic en botón "Editar" en la tabla
- Modifica username, email
- Guarda cambios

**Eliminar:**
- Clic en botón "Eliminar"
- Confirma en modal
- Usuario se borra de la BD

**Buscar:**
- Barra de búsqueda en la parte superior
- Filtra por username, email o teléfono

**Descargar PDF:**
- Botón "Descargar PDF"
- Genera reporte de todos los usuarios

---

## 🔐 Seguridad Implementada

1. **Contraseñas Hasheadas** - Se usan con `password_hash()` (bcrypt)
2. **Validación de Sesiones** - Verifica autenticación en cada página protegida
3. **Prepared Statements** - Protección contra SQL Injection
4. **Validación de Archivos** - Solo se aceptan imágenes válidas (máx 5MB)

---

## 📂 Estructura de Carpetas

```
phpweb/
├── uploads/
│   └── profiles/           # Fotos de perfil de usuarios
├── includes/
│   └── conexionpdo.php    # Conexión a BD
├── index.php              # Página principal + CRUD
├── login.php              # Iniciar sesión
├── registrar.php          # Crear nueva cuenta
├── dashboard.php          # Panel personal del usuario
├── crud_handler.php       # Funciones de CRUD
├── pdf_generator.php      # Generador de PDF
└── ...otros archivos
```

---

## 🗄️ Estructura de Base de Datos

```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(50) UNIQUE NOT NULL,
    oauth_google_id VARCHAR(255),
    oauth_provider VARCHAR(50),
    oauth_created_at TIMESTAMP,
    oauth_updated_at TIMESTAMP,
    profile_photo VARCHAR(255),  -- Nueva columna para fotos
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## ❓ Preguntas Frecuentes

### ¿Puedo ver todos los usuarios sin iniciar sesión?
✅ **SÍ** - La tabla en `index.php` es pública. Solo los botones de crear/editar/eliminar requieren autenticación.

### ¿Dónde se guardan mis fotos de perfil?
📁 En la carpeta `/phpweb/uploads/profiles/`. Se nombran automáticamente como `profile_[user_id]_[timestamp].[ext]`

### ¿Qué pasa si olvido mi contraseña?
🔑 Actualmente no hay sistema de recuperación. Contacta al administrador para resetearla.

### ¿Puedo usar mi cuenta de Google?
🔑 Sí, si tu usuario tiene configurado OAuth Google en la BD (campo `oauth_google_id`).

### ¿Qué tipos de imágenes puedo subir?
🖼️ JPG, PNG, GIF (máximo 5MB)

---

## 🔧 Troubleshooting

### Error: "No se puede conectar a la base de datos"
- Verifica que XAMPP/MySQL esté iniciado
- Revisa que la BD `bd-ventas` exista
- Confirma credenciales en `includes/conexionpdo.php`

### No se ve el botón de "Nuevo Usuario"
- Debes estar autenticado
- Ve a login.php o registrar.php primero

### La foto de perfil no se sube
- Revisa que `/uploads/profiles/` tenga permisos de escritura
- Verifica que el archivo sea menor a 5MB
- Comprueba que sea JPG, PNG o GIF

### Error al editar/eliminar usuarios
- Verifica que estés autenticado
- Comprueba que el usuario exista en la BD

---

## 📞 Soporte

Para reportar problemas o sugerencias, contacta al equipo de desarrollo.

---

**Última actualización:** 26/11/2025
**Versión:** 2.0 (Con autenticación y dashboard)
