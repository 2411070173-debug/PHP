# Ejemplo de Uso: config-vercel.php

Este archivo muestra cómo usar `config-vercel.php` para que tu código funcione tanto en local como en Vercel sin cambios.

## Configuración Inicial

Incluye el archivo al inicio de cada página PHP:

```php
<?php
require_once __DIR__ . '/config-vercel.php';
// o si estás en una subcarpeta:
require_once __DIR__ . '/../config-vercel.php';
?>
```

## Ejemplos de Uso

### 1. Enlaces en HTML

**Antes:**
```php
<a href="/phpweb/auth/login.php">Login</a>
<a href="/phpweb/menu.php">Menu</a>
```

**Después:**
```php
<a href="<?php echo url('/auth/login.php'); ?>">Login</a>
<a href="<?php echo url('/menu.php'); ?>">Menu</a>
```

### 2. Redirecciones

**Antes:**
```php
header("Location: /phpweb/menu.php");
exit;
```

**Después:**
```php
redirect('/menu.php');
// o
header("Location: " . url('/menu.php'));
exit;
```

### 3. Formularios

**Antes:**
```php
<form action="/phpweb/crud_handler.php" method="POST">
```

**Después:**
```php
<form action="<?php echo url('/crud_handler.php'); ?>" method="POST">
```

### 4. Scripts y CSS

**Antes:**
```php
<link rel="stylesheet" href="/phpweb/css/style.css">
<script src="/phpweb/js/script.js"></script>
```

**Después:**
```php
<link rel="stylesheet" href="<?php echo url('/css/style.css'); ?>">
<script src="<?php echo url('/js/script.js'); ?>"></script>
```

## Ventajas de Este Método

✅ **Un solo código** para local y producción
✅ **No necesitas** ejecutar el script de reemplazo
✅ **Fácil de mantener** - solo incluye el archivo
✅ **Funciona en cualquier entorno** - detecta automáticamente

## Testing

Para verificar que funciona correctamente:

```
http://localhost/phpweb/config-vercel.php?debug_config
```

Esto mostrará:
- Si está en Vercel o local
- El BASE_PATH configurado
- El BASE_URL configurado
- Ejemplos de URLs generadas

## Migración Gradual

Puedes migrar tu código gradualmente:

1. Incluye `config-vercel.php` en tus archivos principales
2. Reemplaza las rutas una por una usando `url()` y `redirect()`
3. Prueba localmente
4. Despliega en Vercel

No necesitas cambiar todo de una vez.
