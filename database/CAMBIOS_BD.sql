-- ============================================================================
-- CAMBIOS DE BASE DE DATOS PARA SISTEMA DE ESCUELA
-- ============================================================================
-- Ejecuta estos comandos en MySQL para completar la configuración

-- 1. Agregar columna 'role' a la tabla users (si no existe)
ALTER TABLE users ADD COLUMN role VARCHAR(20) DEFAULT 'student' AFTER email;

-- 2. Actualizar usuarios existentes con rol de estudiante (opcional)
UPDATE users SET role = 'student' WHERE role IS NULL OR role = '';

-- 3. Opcionalmente: Designar algunos usuarios como profesores
-- Descomentar y ejecutar si deseas convertir algunos usuarios en profesores
-- UPDATE users SET role = 'teacher' WHERE username = 'nombre_usuario_profesor';

-- ============================================================================
-- Información de la BD después de estos cambios:
-- - Tabla users tendrá columna 'role' con valores: 'student' o 'teacher'
-- - Todos los usuarios existentes serán 'student' por defecto
-- - Los nuevos usuarios seleccionarán su rol al registrarse
-- ============================================================================
