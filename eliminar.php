<?php
require __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !token_valido()) {
    http_response_code(400);
    exit('Solicitud no válida.');
}

$st = db()->prepare('DELETE FROM usuario WHERE id = ?');
$st->execute([(int)($_POST['id'] ?? 0)]);

if ($st->rowCount() > 0) mensaje('ok', 'Usuario eliminado.');
else mensaje('error', 'El usuario no existe.');

header('Location: index.php');
exit;
