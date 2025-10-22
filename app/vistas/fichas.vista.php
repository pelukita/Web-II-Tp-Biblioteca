<?php

class FichaVista {
  public function showFichas($fichas, $fichaEditar = null, $mensaje = null, $libros = []) {
    include_once 'templates/header.plantilla.phtml';
    $estaEditando = $fichaEditar !== null;
    $idEditar = $estaEditando && isset($fichaEditar->id) ? (int) $fichaEditar->id : null;

    $libroIdValor = $estaEditando && isset($fichaEditar->libro_id) ? (int) $fichaEditar->libro_id : '';
    $fechaPrestamoValor = $estaEditando && isset($fichaEditar->fecha_prestamo) ? substr((string)$fichaEditar->fecha_prestamo,0,10) : '';
    $fechaDevolucionValor = $estaEditando && isset($fichaEditar->fecha_devolucion) && $fichaEditar->fecha_devolucion !== null ? substr((string)$fichaEditar->fecha_devolucion,0,10) : '';
    $estadoValor = $estaEditando && isset($fichaEditar->estado) ? (string)$fichaEditar->estado : 'Prestado';
?>
    <section class="container">
      <h1>Fichas de préstamo</h1>

      <?php if ($mensaje): ?>
        <p><?php echo htmlspecialchars($mensaje); ?></p>
      <?php endif; ?>

      <?php if (isset($_SESSION['usuario'])): ?>
        <form action="<?php echo BASE_URL . ($estaEditando && $idEditar !== null ? 'fichas/actualizar/' . $idEditar : 'fichas/crear'); ?>" method="post">
          <div>
            <label for="libro_id">Libro</label>
            <select id="libro_id" name="libro_id" required>
              <option value="">-- Seleccionar --</option>
              <?php foreach ($libros as $libro): ?>
                <?php $idLibro = isset($libro->id) ? (int)$libro->id : null; if ($idLibro === null) continue; ?>
                <option value="<?php echo $idLibro; ?>" <?php if ($idLibro === (int)$libroIdValor) echo 'selected'; ?>>
                  <?php echo htmlspecialchars($libro->titulo ?? 'Libro'); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label for="fecha_prestamo">Fecha préstamo</label>
            <input type="date" id="fecha_prestamo" name="fecha_prestamo" value="<?php echo htmlspecialchars($fechaPrestamoValor); ?>" required>
          </div>
          <div>
            <label for="fecha_devolucion">Fecha devolución</label>
            <input type="date" id="fecha_devolucion" name="fecha_devolucion" value="<?php echo htmlspecialchars($fechaDevolucionValor); ?>">
          </div>
          <div>
            <label for="estado">Estado</label>
            <select id="estado" name="estado" required>
              <option value="Prestado" <?php if ($estadoValor === 'Prestado') echo 'selected'; ?>>Prestado</option>
              <option value="Devuelto" <?php if ($estadoValor === 'Devuelto') echo 'selected'; ?>>Devuelto</option>
            </select>
          </div>
          <div>
            <button type="submit"><?php echo $estaEditando ? 'Actualizar' : 'Agregar'; ?></button>
            <?php if ($estaEditando): ?>
              <a href="<?php echo BASE_URL; ?>fichas">Cancelar</a>
            <?php endif; ?>
          </div>
        </form>
      <?php endif; ?>

      <div>
        <?php if (!empty($fichas)): ?>
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Libro</th>
                <th>Fecha préstamo</th>
                <th>Fecha devolución</th>
                <th>Estado</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($fichas as $ficha): ?>
              <?php $idFicha = isset($ficha->id) ? (int)$ficha->id : null; if ($idFicha === null) continue; ?>
              <tr>
                <td><?php echo $idFicha; ?></td>
                <td><?php echo htmlspecialchars($ficha->libro_titulo ?? ''); ?></td>
                <td><?php echo htmlspecialchars(substr((string)($ficha->fecha_prestamo ?? ''),0,10)); ?></td>
                <td><?php echo htmlspecialchars(($ficha->fecha_devolucion !== null && $ficha->fecha_devolucion !== '') ? substr((string)$ficha->fecha_devolucion,0,10) : '-'); ?></td>
                <td><?php echo htmlspecialchars($ficha->estado ?? ''); ?></td>
                <td>
                  <a href="<?php echo BASE_URL . 'fichas/detalle/' . $idFicha; ?>">Ver</a>
                  <?php if (isset($_SESSION['usuario'])): ?>
                    <a href="<?php echo BASE_URL . 'fichas/editar/' . $idFicha; ?>">Editar</a>
                    <form action="<?php echo BASE_URL . 'fichas/eliminar/' . $idFicha; ?>" method="post">
                      <button type="submit">Eliminar</button>
                    </form>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        <?php else: ?>
          <p>No hay fichas cargadas.</p>
        <?php endif; ?>
      </div>
    </section>
<?php
    include_once 'templates/footer.plantilla.phtml';
  }

  public function showDetalle($ficha) {
    include_once 'templates/header.plantilla.phtml';
?>
    <section class="container">
      <h1>Detalle de ficha</h1>
      <article>
        <dl>
          <div>
            <dt>ID</dt>
            <dd><?php echo htmlspecialchars($ficha->id); ?></dd>
          </div>
          <div>
            <dt>Libro</dt>
            <dd><?php echo htmlspecialchars($ficha->libro_titulo ?? ''); ?></dd>
          </div>
          <div>
            <dt>Fecha préstamo</dt>
            <dd><?php echo htmlspecialchars(substr((string)($ficha->fecha_prestamo ?? ''),0,10)); ?></dd>
          </div>
          <div>
            <dt>Fecha devolución</dt>
            <dd><?php echo htmlspecialchars(($ficha->fecha_devolucion !== null && $ficha->fecha_devolucion !== '') ? substr((string)$ficha->fecha_devolucion,0,10) : 'No devuelto'); ?></dd>
          </div>
          <div>
            <dt>Estado</dt>
            <dd><?php echo htmlspecialchars($ficha->estado ?? ''); ?></dd>
          </div>
        </dl>
      </article>
      <a href="<?php echo BASE_URL; ?>fichas">Volver al listado</a>
    </section>
<?php
    include_once 'templates/footer.plantilla.phtml';
  }
}
