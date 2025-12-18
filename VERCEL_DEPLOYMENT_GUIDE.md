# Guía de Deployment en Vercel para PHP

## ✅ Archivos Ya Configurados

1. **`vercel.json`** - Configuración del runtime PHP
2. **`index.php`** - Ruta de redirección actualizada

## 🔧 Cambios Necesarios Antes del Deployment

### Problema Principal: Rutas Absolutas

Tu aplicación usa rutas como `/phpweb/auth/login.php` que funcionan en XAMPP local, pero **NO funcionarán en Vercel** porque:
- En local: `http://localhost/phpweb/` → la carpeta `phpweb` está dentro de `htdocs`
- En Vercel: `https://tu-app.vercel.app/` → la raíz es tu repositorio

### Solución: Dos Opciones

#### **Opción 1: Usar Rutas Relativas (Recomendado para Vercel)**

Cambiar todas las rutas de:
```php
// ❌ Ruta absoluta con /phpweb/
header("Location: /phpweb/auth/login.php");
```

A:
```php
// ✅ Ruta relativa o sin prefijo
header("Location: /auth/login.php");
```

#### **Opción 2: Usar Variable de Entorno**

Crear una constante en un archivo de configuración:

```php
// config.php
define('BASE_PATH', getenv('VERCEL') ? '' : '/phpweb');

// Usar en el código:
header("Location: " . BASE_PATH . "/auth/login.php");
```

## 📝 Archivos que Necesitan Actualización

He encontrado **120+ referencias** a `/phpweb/` en tu código. Los archivos principales son:

### Archivos Críticos:
- ✅ `index.php` - **YA ACTUALIZADO**
- ⚠️ `menu.php` - Contiene 3 referencias
- ⚠️ `oauth/config.php` - Configuración de Google OAuth
- ⚠️ Todos los archivos en `dist/` (dashboard-alumno.php, dashboard-profesor.php, etc.)

### Script de Actualización Automática

He creado un script PowerShell para ayudarte: `update-paths-for-vercel.ps1`

**Uso:**
```powershell
# Ejecutar desde la raíz del proyecto
.\update-paths-for-vercel.ps1
```

Este script:
1. Hace backup de todos los archivos PHP
2. Reemplaza `/phpweb/` por `/` en todos los archivos PHP
3. Muestra un resumen de cambios

## 🔐 Configuración de OAuth para Producción

**IMPORTANTE:** El archivo `oauth/config.php` tiene la URL de callback hardcodeada:

```php
define('GOOGLE_REDIRECT_URI', 'http://localhost/phpweb/oauth/google-callback.php');
```

Debes cambiarlo a:
```php
define('GOOGLE_REDIRECT_URI', 'https://TU-APP.vercel.app/oauth/google-callback.php');
```

Y registrar esta nueva URL en Google Cloud Console:
1. Ve a [Google Cloud Console](https://console.cloud.google.com/)
2. Selecciona tu proyecto
3. APIs & Services → Credentials
4. Edita tu OAuth 2.0 Client ID
5. Agrega la URI de redirección de Vercel

## 📦 Pasos para Deployment

### 1. Preparar el Código
```powershell
# Ejecutar script de actualización
.\update-paths-for-vercel.ps1

# Verificar cambios
git status
```

### 2. Configurar Variables de Entorno en Vercel

En el dashboard de Vercel, agrega estas variables:
- `DB_HOST` - Host de tu base de datos
- `DB_NAME` - Nombre de la base de datos
- `DB_USER` - Usuario de la base de datos
- `DB_PASS` - Contraseña de la base de datos
- `GOOGLE_CLIENT_ID` - Tu Client ID de Google
- `GOOGLE_CLIENT_SECRET` - Tu Client Secret de Google

### 3. Subir a GitHub
```powershell
git add .
git commit -m "Configure for Vercel deployment"
git push origin main
```

### 4. Conectar con Vercel
1. Ve a [vercel.com](https://vercel.com)
2. Importa tu repositorio de GitHub
3. Vercel detectará automáticamente `vercel.json`
4. Haz clic en "Deploy"

## ⚠️ Consideraciones Importantes

### Base de Datos
- **No puedes usar MySQL local** en Vercel
- Opciones recomendadas:
  - **PlanetScale** (MySQL compatible, gratis)
  - **Railway** (PostgreSQL/MySQL)
  - **Supabase** (PostgreSQL)

### Archivos Subidos
- La carpeta `uploads/` no persistirá entre deployments
- Usa un servicio de almacenamiento:
  - **Cloudinary** (imágenes)
  - **AWS S3**
  - **Vercel Blob Storage**

### Sesiones PHP
- Las sesiones en archivos no funcionarán
- Usa sesiones en base de datos o Redis

## 🧪 Testing Local con Rutas de Producción

Para probar localmente con las rutas de Vercel:

1. Mueve tu proyecto a la raíz de htdocs:
```powershell
# Desde c:\xampp\htdocs\
# Acceder como http://localhost/ en lugar de http://localhost/phpweb/
```

O usa el servidor PHP integrado:
```powershell
cd c:\xampp\htdocs\phpweb
php -S localhost:8000
```

## 📚 Recursos Adicionales

- [Vercel PHP Runtime](https://github.com/vercel-community/php)
- [Vercel Environment Variables](https://vercel.com/docs/concepts/projects/environment-variables)
- [PlanetScale Setup](https://planetscale.com/docs)

---

## ¿Necesitas Ayuda?

Si encuentras errores después del deployment, revisa:
1. Los logs en Vercel Dashboard
2. Que todas las rutas estén actualizadas
3. Que las variables de entorno estén configuradas
4. Que la base de datos sea accesible desde internet
