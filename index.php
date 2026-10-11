<?php
require __DIR__ . '/config.php';

$q = trim($_GET['q'] ?? '');

if ($q !== '') {
    $like = '%' . $q . '%';
    $sql = 'SELECT * FROM usuario
            WHERE nombre LIKE ? OR direccion LIKE ? OR telefono LIKE ?
               OR correo LIKE ? OR cargo LIKE ? OR usuario LIKE ?
            ORDER BY nombre';
    $st = db()->prepare($sql);
    $st->execute(array_fill(0, 6, $like));
} else {
    $st = db()->query('SELECT * FROM usuario ORDER BY nombre');
}
$usuarios = $st->fetchAll();

cabecera();
?>
<form method="get" class="busqueda">
  <input type="search" name="q" value="<?= e($q) ?>" placeholder="Buscar por nombre, correo, cargo, usuario, teléfono...">
  <button class="btn" type="submit">Buscar</button>
  <?php if ($q !== ''): ?><a href="index.php" class="btn gris">Limpiar</a><?php endif; ?>
</form>

<p class="conteo">
  <?php if ($q !== ''): ?>
    <?= count($usuarios) ?> resultado(s) para "<strong><?= e($q) ?></strong>"
  <?php else: ?>
    <?= count($usuarios) ?> usuario(s) registrado(s)
  <?php endif; ?>
</p>

<?php if ($usuarios): ?>
<div class="tabla-wrap">
<table>
  <thead>
    <tr><th>#</th><th>Nombre</th><th>Dirección</th><th>Teléfono</th><th>Correo</th><th>Cargo</th><th>Usuario</th><th>Acciones</th></tr>
  </thead>
  <tbody>
  <?php foreach ($usuarios as $u): ?>
    <tr>
      <td><?= (int)$u['id'] ?></td>
      <td><?= e($u['nombre']) ?></td>
      <td><?= e($u['direccion']) ?></td>
      <td><?= e($u['telefono']) ?></td>
      <td><?= e($u['correo']) ?></td>
      <td><?= e($u['cargo']) ?></td>
      <td><?= e($u['usuario']) ?></td>
      <td class="acciones">
        <a class="btn pequeno" href="formulario.php?id=<?= (int)$u['id'] ?>">Editar</a>
        <form method="post" action="eliminar.php"
              data-msg="¿Eliminar al usuario <?= e($u['nombre']) ?>? Esta acción no se puede deshacer."
              onsubmit="return confirm(this.dataset.msg);">
          <input type="hidden" name="token" value="<?= e(token()) ?>">
          <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
          <button class="btn pequeno rojo" type="submit">Eliminar</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
</div>
<?php else: ?>
  <p class="vacio">No hay usuarios para mostrar.</p>
<?php endif; ?>
<?php pie(); ?>
