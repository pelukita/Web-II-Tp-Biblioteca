<?php

include_once 'app/modelos/ficha.modelo.php';
include_once 'app/modelos/libro.modelo.php';
include_once 'app/vistas/fichas.vista.php';

class FichasControlador {
  private $modelo;
  private $libroModelo;
  private $vista;

  public function __construct() {
    $this->modelo = new FichaModelo();
    $this->libroModelo = new LibroModelo();
    $this->vista = new FichaVista();
  }

  public function showFichas($fichaEditar = null, $mensaje = null) {
    $fichas = $this->modelo->getFichas();
    $libros = $this->libroModelo->getLibros();
    $this->vista->showFichas($fichas, $fichaEditar, $mensaje, $libros);
  }

  public function crearFicha() {
    if (!$this->estaLogueado()) {
      $this->redirigirLogin();
    }

    $datos = $this->recolectarDatosFormulario();
    $resultado = $this->validarDatos($datos);

    if (!empty($resultado['errores'])) {
      $this->showFichas(null, implode(' ', $resultado['errores']));
      return;
    }

    $d = $resultado['datos'];
    $this->modelo->insertFicha($d['libro_id'], $d['fecha_prestamo'], $d['fecha_devolucion'], $d['estado']);
    $this->redirigirListado();
  }

  public function editarFicha($idParam) {
    $id = $this->normalizarId($idParam);
    if ($id === null) {
      $this->redirigirListado();
    }

    $ficha = $this->modelo->getFicha($id);
    if (!$ficha) {
      $this->redirigirListado();
    }

    if (!$this->estaLogueado()) {
      $this->redirigirLogin();
    }

    $this->showFichas($ficha);
  }

  public function actualizarFicha($idParam) {
    if (!$this->estaLogueado()) {
      $this->redirigirLogin();
    }

    $id = $this->normalizarId($idParam);
    if ($id === null) {
      $this->redirigirListado();
    }

    $fichaOriginal = $this->modelo->getFicha($id);
    if (!$fichaOriginal) {
      $this->redirigirListado();
    }

    $datos = $this->recolectarDatosFormulario();
    $resultado = $this->validarDatos($datos);

    if (!empty($resultado['errores'])) {
      $fichaEditada = (object) array_merge(['id' => $id], $resultado['datos']);
      $this->showFichas($fichaEditada, implode(' ', $resultado['errores']));
      return;
    }

    $d = $resultado['datos'];
    $this->modelo->updateFicha($id, $d['libro_id'], $d['fecha_prestamo'], $d['fecha_devolucion'], $d['estado']);
    $this->redirigirListado();
  }

  public function eliminarFicha($idParam) {
    if (!$this->estaLogueado()) {
      $this->redirigirLogin();
    }

    $id = $this->normalizarId($idParam);
    if ($id === null) {
      $this->redirigirListado();
    }

    $this->modelo->deleteFicha($id);
    $this->redirigirListado();
  }

  public function verFicha($idParam) {
    $id = $this->normalizarId($idParam);
    if ($id === null) {
      $this->redirigirListado();
    }

    $ficha = $this->modelo->getFicha($id);
    if (!$ficha) {
      $this->showFichas(null, 'No encontramos la ficha solicitada.');
      return;
    }

    $this->vista->showDetalle($ficha);
  }

  private function recolectarDatosFormulario() {
    return [
      'libro_id' => trim((string)($_POST['libro_id'] ?? '')),
      'fecha_prestamo' => trim($_POST['fecha_prestamo'] ?? ''),
      'fecha_devolucion' => trim($_POST['fecha_devolucion'] ?? ''),
      'estado' => trim($_POST['estado'] ?? ''),
    ];
  }

  private function validarDatos(array $datos) {
    $errores = [];
    $libroId = filter_var($datos['libro_id'], FILTER_VALIDATE_INT);
    if ($libroId === false || $libroId <= 0) {
      $errores[] = 'Seleccioná un libro válido.';
    } else if (!$this->modelo->existeLibro($libroId)) {
      $errores[] = 'El libro elegido no existe.';
    }

    $fechaPrestamo = $datos['fecha_prestamo'];
    if ($fechaPrestamo === '' || !$this->esFechaValida($fechaPrestamo)) {
      $errores[] = 'La fecha de préstamo es obligatoria y debe tener formato AAAA-MM-DD.';
    }

    $estadoValido = in_array($datos['estado'], ['Prestado','Devuelto'], true);
    if (!$estadoValido) {
      $errores[] = 'Estado inválido.';
    }

    $fechaDevolucion = $datos['fecha_devolucion'];
    if ($datos['estado'] === 'Devuelto') {
      if ($fechaDevolucion === '' || !$this->esFechaValida($fechaDevolucion)) {
        $errores[] = 'La fecha de devolución es obligatoria al marcar Devuelto.';
      }
    } else {
      if ($fechaDevolucion !== '' && !$this->esFechaValida($fechaDevolucion)) {
        $errores[] = 'La fecha de devolución debe tener formato AAAA-MM-DD o quedar vacía.';
      }
    }

    return [
      'errores' => $errores,
      'datos' => [
        'libro_id' => $libroId !== false ? $libroId : null,
        'fecha_prestamo' => $fechaPrestamo,
        'fecha_devolucion' => ($fechaDevolucion !== '' ? $fechaDevolucion : null),
        'estado' => $estadoValido ? $datos['estado'] : null,
      ],
    ];
  }

  private function esFechaValida($fecha) {
    $momento = \DateTime::createFromFormat('Y-m-d', $fecha);
    return $momento && $momento->format('Y-m-d') === $fecha;
  }

  private function normalizarId($id) {
    $valor = filter_var($id, FILTER_VALIDATE_INT);
    return $valor === false ? null : (int)$valor;
  }

  private function redirigirListado() {
    header('Location: ' . BASE_URL . 'fichas');
    exit;
  }

  private function redirigirLogin() {
    header('Location: ' . BASE_URL . 'login');
    exit;
  }

  private function estaLogueado() {
    return isset($_SESSION['usuario']);
  }
}
