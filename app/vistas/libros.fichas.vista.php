<?php

class LibrosConFichasVista {
  public function showLibrosConFichas($libros, $fichasPorLibro) {
    include_once 'templates/header.plantilla.phtml';
?>
    <section class="container">
      <h1>Libros y sus fichas</h1>
      <?php if (empty($libros)): ?>
        <p>No hay libros cargados.</p>
      <?php else: ?>
        <div>
          <?php foreach ($libros as $libro): ?>
            <?php $idLibro = isset($libro->id) ? (int)$libro->id : null; if ($idLibro === null) continue; ?>
            <article>
              <header>
                <h2><?php echo htmlspecialchars($libro->titulo ?? 'Sin título'); ?></h2>
                <p>
                  <?php
                    $autor = $libro->autor ?? null;
                    $fecha = $libro->fecha_publicacion ?? null;
                    $genero = $libro->genero ?? null;
                    $stock = $libro->stock ?? null;
                    $partes = [];
                    if ($autor) $partes[] = 'Autor: ' . htmlspecialchars($autor);
                    if ($fecha) $partes[] = 'Publicación: ' . htmlspecialchars(substr((string)$fecha,0,10));
                    if ($genero) $partes[] = 'Género: ' . htmlspecialchars($genero);
                    if ($stock !== null && $stock !== '') $partes[] = 'Stock: ' . htmlspecialchars($stock);
                    echo implode(' · ', $partes);
                  ?>
                </p>
                <p><a href="<?php echo BASE_URL . 'libros/detalle/' . $idLibro; ?>">Ver detalle del libro</a></p>
              </header>
              <div>
                <h3>Fichas</h3>
                <?php $fichas = $fichasPorLibro[$idLibro] ?? []; ?>
                <?php if (empty($fichas)): ?>
                  <p>Este libro no tiene fichas.</p>
                <?php else: ?>
                  <ul>
                    <?php foreach ($fichas as $ficha): ?>
                      <?php $idFicha = isset($ficha->id) ? (int)$ficha->id : null; if ($idFicha === null) continue; ?>
                      <li>
                        <span>
                          Préstamo: <?php echo htmlspecialchars(substr((string)($ficha->fecha_prestamo ?? ''),0,10)); ?>
                          <?php if ($ficha->estado === 'Devuelto'): ?> · Devolución: <?php echo htmlspecialchars(substr((string)($ficha->fecha_devolucion ?? ''),0,10)); ?><?php endif; ?>
                        </span>
                        <span>
                          (<?php echo htmlspecialchars($ficha->estado ?? ''); ?>)
                        </span>
                        <a href="<?php echo BASE_URL . 'fichas/detalle/' . $idFicha; ?>">Ver ficha</a>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                <?php endif; ?>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>
<?php
    include_once 'templates/footer.plantilla.phtml';
  }
}
