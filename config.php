<?php
// ---------- Configuración (valores por defecto de XAMPP) ----------
const DB_HOST = 'localhost';
const DB_NAME = 'registro_usuarios';
const DB_USER = 'root';
const DB_PASS = '';          // XAMPP trae root sin contraseña

session_start();

// ---------- Conexión PDO (crea la BD y la tabla si no existen) ----------
function db(): PDO {
    static $pdo = null;
    if ($pdo) return $pdo;

    $opciones = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];
    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';charset=utf8mb4', DB_USER, DB_PASS, $opciones);
        $pdo->exec('CREATE DATABASE IF NOT EXISTS `' . DB_NAME . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        $pdo->exec('USE `' . DB_NAME . '`');
        $pdo->exec('CREATE TABLE IF NOT EXISTS usuario (
            id        INT AUTO_INCREMENT PRIMARY KEY,
            nombre    VARCHAR(100) NOT NULL,
            direccion VARCHAR(150) NOT NULL,
            telefono  VARCHAR(20)  NOT NULL,
            correo    VARCHAR(100) NOT NULL UNIQUE,
            cargo     VARCHAR(80)  NOT NULL,
            usuario   VARCHAR(50)  NOT NULL UNIQUE,
            clave     VARCHAR(255) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
    } catch (PDOException $ex) {
        http_response_code(500);
        exit('<h3>No se pudo conectar a MySQL.</h3><p>Verifica que MySQL esté iniciado en XAMPP y que los datos de config.php sean correctos.</p>');
    }
    return $pdo;
}

// ---------- Utilidades ----------
function e(?string $texto): string {          // escapar HTML
    return htmlspecialchars($texto ?? '', ENT_QUOTES, 'UTF-8');
}

function mensaje(string $tipo, string $texto): void {   // mensaje flash
    $_SESSION['flash'][] = [$tipo, $texto];
}

function mensajes(): array {
    $m = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $m;
}

function token(): string {                    // protección CSRF
    if (empty($_SESSION['token'])) $_SESSION['token'] = bin2hex(random_bytes(32));
    return $_SESSION['token'];
}

function token_valido(): bool {
    return isset($_POST['token'], $_SESSION['token']) && hash_equals($_SESSION['token'], $_POST['token']);
}

function cabecera(string $titulo = 'Registro de usuarios'): void { ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($titulo) ?></title>
  <link rel="stylesheet" href="estilos.css">
</head>
<body>
  <header>
    <h1>Registro de usuarios</h1>
    <nav>
      <a href="index.php">Listado</a>
      <a href="formulario.php" class="btn">+ Nuevo usuario</a>
    </nav>
  </header>
  <main>
    <?php foreach (mensajes() as [$tipo, $texto]): ?>
      <div class="alerta <?= e($tipo) ?>"><?= e($texto) ?></div>
    <?php endforeach; ?>
<?php }

function pie(): void { ?>
  </main>
</body>
</html>
<?php }
