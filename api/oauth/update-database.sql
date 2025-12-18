-- ============================================================================
-- Script de actualización de la tabla 'users' para soportar OAuth
-- ============================================================================
-- Este script agrega los campos necesarios para OAuth a la tabla de usuarios.
-- 
-- EJECUTA ESTE SCRIPT EN TU BASE DE DATOS ANTES DE USAR GOOGLE OAUTH:
-- 1. Abre MySQL/phpMyAdmin
-- 2. Selecciona tu base de datos
-- 3. Ve a la pestaña "SQL"
-- 4. Copia y pega todo el contenido de este archivo
-- 5. Haz clic en "Ejecutar"

-- ============================================================================
-- OPCIÓN 1: Si la tabla 'users' YA EXISTE (más probable)
-- ============================================================================
-- Descomenta las siguientes líneas si ya tienes la tabla 'users'

ALTER TABLE users ADD COLUMN IF NOT EXISTS oauth_google_id VARCHAR(255) UNIQUE NULL COMMENT 'ID de usuario de Google';
ALTER TABLE users ADD COLUMN IF NOT EXISTS oauth_provider VARCHAR(50) NULL COMMENT 'Proveedor OAuth (google, facebook, github, etc.)';
ALTER TABLE users ADD COLUMN IF NOT EXISTS oauth_created_at TIMESTAMP NULL COMMENT 'Fecha de creación de la cuenta OAuth';
ALTER TABLE users ADD COLUMN IF NOT EXISTS oauth_updated_at TIMESTAMP NULL COMMENT 'Fecha de última actualización de OAuth';

-- Crear índice para búsquedas rápidas
CREATE INDEX IF NOT EXISTS idx_oauth_google_id ON users(oauth_google_id);
CREATE INDEX IF NOT EXISTS idx_oauth_provider ON users(oauth_provider);

-- ============================================================================
-- OPCIÓN 2: Si la tabla 'users' NO EXISTE (crear desde cero)
-- ============================================================================
-- Descomenta las siguientes líneas si aún no tienes la tabla 'users'

/*
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    oauth_google_id VARCHAR(255) UNIQUE NULL,
    oauth_provider VARCHAR(50) NULL,
    oauth_created_at TIMESTAMP NULL,
    oauth_updated_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_email (email),
    INDEX idx_oauth_google_id (oauth_google_id),
    INDEX idx_oauth_provider (oauth_provider)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
*/

-- ============================================================================
-- VERIFICAR LOS CAMBIOS
-- ============================================================================
-- Ejecuta este comando para verificar que los cambios se aplicaron correctamente:
-- DESC users;
-- Deberías ver las columnas: oauth_google_id, oauth_provider, oauth_created_at, oauth_updated_at

