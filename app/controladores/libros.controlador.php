<?php

include_once 'app/modelos/libro.modelo.php';
include_once 'app/modelos/ficha.modelo.php';
include_once 'app/vistas/libros.fichas.vista.php';
include_once 'app/vistas/libros.lista.vista.php';

class LibrosControlador {

  private $modelo;
  private $fichaModelo;
  private $vista;
  private $vistaLibrosConFichas;

  public function __construct() {
    $this->modelo = new LibroModelo();
    $this->fichaModelo = new FichaModelo();
    $this->vista = new LibroVista();
    $this->vistaLibrosConFichas = new LibrosConFichasVista();
  }

  public function showLibros($libroEditar = null, $mensaje = null) {
    $libros = $this->modelo->getLibros();
    $this->vista->showLibros($libros, $libroEditar, $mensaje);
  }

  public function crearLibro() {
    $datosFormulario = $this->recolectarDatosFormulario();
    $resultado = $this->validarYNormalizarDatos($datosFormulario);

    if (!empty($resultado['errores'])) {
      $this->showLibros(null, implode(' ', $resultado['errores']));
      return;
    }

    $datos = $resultado['datos'];

    $this->modelo->insertLibro(
      $datos['titulo'],
      $datos['autor'],
      $datos['fecha_publicacion'],
      $datos['genero'],
      $datos['stock']
    );
    $this->redirigirListado();
  }

  public function editarLibro($idParam) {
    $id = $this->normalizarId($idParam);

    if ($id === null) {
      $this->redirigirListado();
    }

    $libro = $this->modelo->getLibro($id);

    if (!$libro) {
      $this->redirigirListado();
    }

    $this->showLibros($libro);
  }

  public function actualizarLibro($idParam) {
    $id = $this->normalizarId($idParam);

    if ($id === null) {
      $this->redirigirListado();
    }

    $datosFormulario = $this->recolectarDatosFormulario();
    $resultado = $this->validarYNormalizarDatos($datosFormulario);

    if (!empty($resultado['errores'])) {
      $libroEditado = (object) array_merge(['id' => $id], $resultado['datos']);
      $this->showLibros($libroEditado, implode(' ', $resultado['errores']));
      return;
    }

    $datos = $resultado['datos'];

    $this->modelo->updateLibro(
      $id,
      $datos['titulo'],
      $datos['autor'],
      $datos['fecha_publicacion'],
      $datos['genero'],
      $datos['stock']
    );
    $this->redirigirListado();
  }

  public function verLibro($idParam) {
    $id = $this->normalizarId($idParam);

    if ($id === null) {
      $this->redirigirListado();
    }

    $libro = $this->modelo->getLibro($id);

    if (!$libro) {
      $this->showLibros(null, 'No encontramos el libro solicitado.');
      return;
    }

    $this->vista->showDetalle($libro);
  }

  public function eliminarLibro($idParam) {
    $id = $this->normalizarId($idParam);

    if ($id === null) {
      $this->redirigirListado();
    }

    $this->modelo->deleteLibro($id);
    $this->redirigirListado();
  }

  public function showLibrosConFichas() {
    $libros = $this->modelo->getLibros();
    $todasFichas = $this->fichaModelo->getFichas();
    $fichasPorLibro = [];
    foreach ($todasFichas as $ficha) {
      if (!isset($ficha->libro_id)) continue;
      $lid = (int) $ficha->libro_id;
      if (!isset($fichasPorLibro[$lid])) {
        $fichasPorLibro[$lid] = [];
      }
      $fichasPorLibro[$lid][] = $ficha;
    }
    $this->vistaLibrosConFichas->showLibrosConFichas($libros, $fichasPorLibro);
  }

  private function normalizarId($id) {
    $valor = filter_var($id, FILTER_VALIDATE_INT);
    return $valor === false ? null : (int) $valor;
  }

  private function recolectarDatosFormulario() {
    return [
      'titulo' => trim($_POST['titulo'] ?? ''),
      'autor' => trim($_POST['autor'] ?? ''),
      'fecha_publicacion' => trim($_POST['fecha_publicacion'] ?? ''),
      'genero' => trim($_POST['genero'] ?? ''),
      'stock' => trim((string) ($_POST['stock'] ?? '')),
    ];
  }

  private function validarYNormalizarDatos(array $datos) {
    $errores = [];

    if ($datos['titulo'] === '') {
      $errores[] = 'Es necesario completar el título.';
    }

    if ($datos['autor'] === '') {
      $errores[] = 'Es necesario completar el autor.';
    }

    $fecha = $datos['fecha_publicacion'] !== '' ? $datos['fecha_publicacion'] : null;
    if ($fecha !== null && !$this->esFechaValida($fecha)) {
      $errores[] = 'La fecha debe tener el formato AAAA-MM-DD.';
    }

    $stock = null;
    if ($datos['stock'] !== '') {
      $stockFiltrado = filter_var($datos['stock'], FILTER_VALIDATE_INT);
      if ($stockFiltrado === false || $stockFiltrado < 0) {
        $errores[] = 'El stock debe ser un número entero mayor o igual a 0.';
      } else {
        $stock = $stockFiltrado;
      }
    }

    return [
      'errores' => $errores,
      'datos' => [
        'titulo' => $datos['titulo'],
        'autor' => $datos['autor'],
        'fecha_publicacion' => $fecha,
        'genero' => $datos['genero'] !== '' ? $datos['genero'] : null,
        'stock' => $stock,
      ],
    ];
  }

  private function esFechaValida($fecha) {
  $momento = \DateTime::createFromFormat('Y-m-d', $fecha);
    return $momento && $momento->format('Y-m-d') === $fecha;
  }

  private function redirigirListado() {
    header('Location: ' . BASE_URL . 'libros');
    exit;
  }
}