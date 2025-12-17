-- ============================================================================
-- Script SQL para agregar campo TELÉFONO a la tabla users
-- ============================================================================
-- Este script agrega el campo de teléfono a la tabla de usuarios
-- EJECUTA ESTE SCRIPT EN TU BASE DE DATOS ANTES DE USAR EL CRUD
--
-- PASOS:
-- 1. Abre phpMyAdmin
-- 2. Selecciona tu base de datos
-- 3. Ve a la pestaña "SQL"
-- 4. Copia y pega este contenido
-- 5. Haz clic en "Ejecutar"

-- Agregar columna de teléfono a la tabla users
ALTER TABLE users ADD COLUMN IF NOT EXISTS phone VARCHAR(20) NULL COMMENT 'Número de teléfono del usuario';

-- Crear índice para búsquedas rápidas
CREATE INDEX IF NOT EXISTS idx_phone ON users(phone);

-- Verificar que la columna se agregó correctamente
-- Ejecuta: DESC users;
-- Deberías ver la columna 'phone' en la lista

