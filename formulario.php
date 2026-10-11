<?php
require __DIR__ . '/config.php';

$campos = ['nombre', 'direccion', 'telefono', 'correo', 'cargo', 'usuario'];
$id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
$editando = $id > 0;
$datos = array_fill_keys($campos, '');

// Cargar datos existentes al editar
if ($editando && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $st = db()->prepare('SELECT * FROM usuario WHERE id = ?');
    $st->execute([$id]);
    $fila = $st->fetch();
    if (!$fila) {
        mensaje('error', 'El usuario no existe.');
        header('Location: index.php');
        exit;
    }
    foreach ($campos as $c) $datos[$c] = $fila[$c];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!token_valido()) {
        http_response_code(400);
        exit('Solicitud no válida.');
    }
    foreach ($campos as $c) $datos[$c] = trim($_POST[$c] ?? '');
    $clave = $_POST['clave'] ?? '';
    $errores = [];

    foreach ($campos as $c) {
        if ($datos[$c] === '') $errores[] = "El campo '$c' es obligatorio.";
    }
    if ($datos['correo'] !== '' && !filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'El correo no tiene un formato válido.';
    }
    if ($datos['telefono'] !== '' && !preg_match('/^[0-9+\-\s()]{7,20}$/', $datos['telefono'])) {
        $errores[] = 'El teléfono debe tener entre 7 y 20 dígitos (se permiten + - ( ) y espacios).';
    }
    if (!$editando && $clave === '') $errores[] = 'La clave de acceso es obligatoria.';
    if ($clave !== '' && strlen($clave) < 6) $errores[] = 'La clave debe tener al menos 6 caracteres.';

    // Correo y usuario únicos
    foreach (['correo', 'usuario'] as $c) {
        if ($datos[$c] !== '') {
            $st = db()->prepare("SELECT id FROM usuario WHERE $c = ? AND id <> ?");
            $st->execute([$datos[$c], $id]);
            if ($st->fetch()) $errores[] = "Ya existe un usuario con ese $c.";
        }
    }

    if (!$errores) {
        if ($editando) {
            if ($clave !== '') {
                $st = db()->prepare('UPDATE usuario SET nombre=?, direccion=?, telefono=?, correo=?, cargo=?, usuario=?, clave=? WHERE id=?');
                $st->execute([...array_values($datos), password_hash($clave, PASSWORD_DEFAULT), $id]);
            } else {
                $st = db()->prepare('UPDATE usuario SET nombre=?, direccion=?, telefono=?, correo=?, cargo=?, usuario=? WHERE id=?');
                $st->execute([...array_values($datos), $id]);
            }
            mensaje('ok', 'Datos actualizados correctamente.');
        } else {
            $st = db()->prepare('INSERT INTO usuario (nombre, direccion, telefono, correo, cargo, usuario, clave) VALUES (?,?,?,?,?,?,?)');
            $st->execute([...array_values($datos), password_hash($clave, PASSWORD_DEFAULT)]);
            mensaje('ok', 'Usuario registrado correctamente.');
        }
        header('Location: index.php');
        exit;
    }
    foreach ($errores as $err) mensaje('error', $err);
}

cabecera($editando ? 'Actualizar usuario' : 'Nuevo usuario');
?>
<h2><?= $editando ? 'Actualizar usuario' : 'Registrar nuevo usuario' ?></h2>
<form method="post" class="formulario" autocomplete="off">
  <input type="hidden" name="token" value="<?= e(token()) ?>">
  <input type="hidden" name="id" value="<?= $id ?>">

  <label>Nombre
    <input name="nombre" value="<?= e($datos['nombre']) ?>" required maxlength="100">
  </label>
  <label>Dirección
    <input name="direccion" value="<?= e($datos['direccion']) ?>" required maxlength="150">
  </label>
  <label>Teléfono
    <input name="telefono" type="tel" value="<?= e($datos['telefono']) ?>" required maxlength="20">
  </label>
  <label>Correo
    <input name="correo" type="email" value="<?= e($datos['correo']) ?>" required maxlength="100">
  </label>
  <label>Cargo
    <input name="cargo" value="<?= e($datos['cargo']) ?>" required maxlength="80">
  </label>
  <label>Usuario
    <input name="usuario" value="<?= e($datos['usuario']) ?>" required maxlength="50">
  </label>
  <label>Clave de acceso
    <input name="clave" type="password" minlength="6" <?= $editando ? '' : 'required' ?>
           placeholder="<?= $editando ? 'Dejar en blanco para conservar la actual' : 'Mínimo 6 caracteres' ?>">
  </label>

  <div class="botones">
    <button class="btn" type="submit"><?= $editando ? 'Guardar cambios' : 'Registrar' ?></button>
    <a class="btn gris" href="index.php">Cancelar</a>
  </div>
</form>
<?php pie(); ?>
