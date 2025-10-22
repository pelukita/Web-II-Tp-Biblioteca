<?php

class LibroVista {

  public function showLibros($libros, $libroEditar = null, $mensaje = null) {
    include_once 'templates/header.plantilla.phtml';

    $estaEditando = $libroEditar !== null;
    $idEditar = $estaEditando && isset($libroEditar->id) ? (int) $libroEditar->id : null;
    $tituloValor = $estaEditando && isset($libroEditar->titulo) ? (string) $libroEditar->titulo : '';
    $autorValor = $estaEditando && isset($libroEditar->autor) ? (string) $libroEditar->autor : '';
    $fechaValor = '';
    if ($estaEditando && isset($libroEditar->fecha_publicacion) && $libroEditar->fecha_publicacion !== null) {
      $fechaCadena = (string) $libroEditar->fecha_publicacion;
      $fechaValor = substr($fechaCadena, 0, 10);
    }
    $generoValor = $estaEditando && isset($libroEditar->genero) && $libroEditar->genero !== null
      ? (string) $libroEditar->genero
      : '';
    $stockValor = '';
    if ($estaEditando && isset($libroEditar->stock) && $libroEditar->stock !== null) {
      $stockValor = (string) $libroEditar->stock;
    }
    ?>
    <section class="container">
      <h1>Libros</h1>

      <?php if ($mensaje): ?>
        <p><?php echo htmlspecialchars($mensaje); ?></p>
      <?php endif; ?>

      <?php if (isset($_SESSION['usuario'])): ?>
        <form action="<?php echo BASE_URL . ($estaEditando && $idEditar !== null ? 'libros/actualizar/' . $idEditar : 'libros/crear'); ?>" method="post">
          <div>
            <label for="titulo">Título</label>
            <input
              id="titulo"
              name="titulo"
              type="text"
              placeholder="Título del libro"
              value="<?php echo htmlspecialchars($tituloValor); ?>"
              required
            >
          </div>
          <div>
            <label for="autor">Autor</label>
            <input
              id="autor"
              name="autor"
              type="text"
              placeholder="Autor"
              value="<?php echo htmlspecialchars($autorValor); ?>"
              required
            >
          </div>
            <div>
              <label for="fecha_publicacion">Fecha de publicación</label>
              <input
                id="fecha_publicacion"
                name="fecha_publicacion"
                type="date"
                value="<?php echo htmlspecialchars($fechaValor); ?>"
              >
            </div>
            <div>
              <label for="genero">Género</label>
              <input
                id="genero"
                name="genero"
                type="text"
                placeholder="Género"
                value="<?php echo htmlspecialchars($generoValor); ?>"
              >
            </div>
            <div>
              <label for="stock">Stock</label>
              <input
                id="stock"
                name="stock"
                type="number"
                min="0"
                step="1"
                placeholder="Cantidad disponible"
                value="<?php echo htmlspecialchars($stockValor); ?>"
              >
            </div>
            <div>
              <button type="submit"><?php echo $estaEditando ? 'Actualizar' : 'Agregar'; ?></button>
              <?php if ($estaEditando): ?>
                <a href="<?php echo BASE_URL; ?>libros">Cancelar</a>
              <?php endif; ?>
            </div>
        </form>
      <?php endif; ?>

      <ul>
        <?php if (!empty($libros)): ?>
          <?php foreach ($libros as $libro): ?>
            <?php $idLibro = isset($libro->id) ? (int) $libro->id : null; ?>
            <?php if ($idLibro === null) { continue; } ?>
            <li>
              <div>
                <strong><?php echo isset($libro->titulo) ? htmlspecialchars((string) $libro->titulo) : 'Sin título'; ?></strong>
                <div><?php echo 'Autor: ' . htmlspecialchars(isset($libro->autor) && $libro->autor !== null ? (string) $libro->autor : 'No especificado'); ?></div>
                <?php
                  $meta = [];
                  if (isset($libro->fecha_publicacion) && $libro->fecha_publicacion !== null && $libro->fecha_publicacion !== '') {
                    $meta[] = 'Publicación: ' . htmlspecialchars(substr((string) $libro->fecha_publicacion, 0, 10));
                  }
                  if (isset($libro->genero) && $libro->genero !== null && $libro->genero !== '') {
                    $meta[] = 'Género: ' . htmlspecialchars((string) $libro->genero);
                  }
                  if (isset($libro->stock) && $libro->stock !== null && $libro->stock !== '') {
                    $meta[] = 'Stock: ' . htmlspecialchars((string) $libro->stock);
                  }
                  if (!empty($meta)) {
                    echo '<div>' . implode(' · ', $meta) . '</div>';
                  }
                ?>
              </div>
              <div>
                <a href="<?php echo BASE_URL . 'libros/detalle/' . $idLibro; ?>">Ver detalle</a>
                <?php if (isset($_SESSION['usuario'])): ?>
                  <a href="<?php echo BASE_URL . 'libros/editar/' . $idLibro; ?>">Editar</a>
                  <form action="<?php echo BASE_URL . 'libros/eliminar/' . $idLibro; ?>" method="post">
                    <button type="submit">Eliminar</button>
                  </form>
                <?php endif; ?>
              </div>
            </li>
          <?php endforeach; ?>
        <?php else: ?>
          <li>Todavía no cargaste libros.</li>
        <?php endif; ?>
      </ul>
    </section>
    <?php
    include_once 'templates/footer.plantilla.phtml';
  }

  public function showDetalle($libro) {
    include_once 'templates/header.plantilla.phtml';

    ?>
    <section class="container libro-detalle">
      <h1>Detalle del libro</h1>
      <article class="libro-detalle-card">
        <dl>
          <div>
            <dt>Título</dt>
            <dd><?php echo $this->valorLegible($libro->titulo ?? null); ?></dd>
          </div>
          <div>
            <dt>Autor</dt>
            <dd><?php echo $this->valorLegible($libro->autor ?? null); ?></dd>
          </div>
          <div>
            <dt>Fecha de publicación</dt>
            <dd><?php echo $this->valorLegible(isset($libro->fecha_publicacion) ? substr((string) $libro->fecha_publicacion, 0, 10) : null); ?></dd>
          </div>
          <div>
            <dt>Género</dt>
            <dd><?php echo $this->valorLegible($libro->genero ?? null); ?></dd>
          </div>
          <div>
            <dt>Stock</dt>
            <dd><?php echo $this->valorLegible(isset($libro->stock) ? (string) $libro->stock : null, 'Sin datos'); ?></dd>
          </div>
        </dl>
      </article>
      <a class="enlace-secundario" href="<?php echo BASE_URL; ?>libros">Volver al listado</a>
    </section>
    <?php

    include_once 'templates/footer.plantilla.phtml';
  }

  private function valorLegible($valor, $fallback = 'No disponible') {
    if ($valor === null || $valor === '') {
      return htmlspecialchars($fallback);
    }

    return htmlspecialchars((string) $valor);
  }
}