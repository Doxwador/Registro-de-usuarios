# Registro de usuarios (PHP + MySQL + XAMPP)

## Instalación
1. Instala y abre **XAMPP**; inicia los módulos **Apache** y **MySQL**.
2. Copia la carpeta `registro_usuarios_php` dentro de `C:\xampp\htdocs\`.
3. Abre en el navegador: http://localhost/registro_usuarios_php/

La base de datos `registro_usuarios` y la tabla `usuario` se crean automáticamente
la primera vez. (Opcional: también puedes importar `database.sql` desde phpMyAdmin.)

## Configuración
Si tu MySQL tiene otra contraseña, edita las constantes al inicio de `config.php`.

## Archivos
- `config.php`    conexión PDO, utilidades y plantilla de página
- `index.php`     listado y búsqueda
- `formulario.php` registrar y actualizar
- `eliminar.php`  eliminar
- `database.sql`  script de la base de datos
- `estilos.css`   diseño
