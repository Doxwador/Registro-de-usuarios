-- Importar desde phpMyAdmin (pestaña "Importar") o ejecutar en consola MySQL.
-- Nota: la aplicación también crea esto automáticamente si no existe.
CREATE DATABASE IF NOT EXISTS registro_usuarios
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE registro_usuarios;

CREATE TABLE IF NOT EXISTS usuario (
  id        INT AUTO_INCREMENT PRIMARY KEY,
  nombre    VARCHAR(100) NOT NULL,
  direccion VARCHAR(150) NOT NULL,
  telefono  VARCHAR(20)  NOT NULL,
  correo    VARCHAR(100) NOT NULL UNIQUE,
  cargo     VARCHAR(80)  NOT NULL,
  usuario   VARCHAR(50)  NOT NULL UNIQUE,
  clave     VARCHAR(255) NOT NULL   -- hash (password_hash), nunca texto plano
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
