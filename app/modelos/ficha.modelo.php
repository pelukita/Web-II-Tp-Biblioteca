<?php

class FichaModelo {

  public function __construct() {
    $this->_deploy();
  }

  private function getConexion() {
    $db = new PDO('mysql:host=localhost;dbname=biblioteca_db;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $db;
  }

  private function _deploy() {
    $db = $this->getConexion();
    $query = $db->query('SHOW TABLES');
    $tables = $query->fetchAll();
    if(count($tables) == 0) {
      $sql =<<<'SQL'
        CREATE DATABASE IF NOT EXISTS biblioteca_db
          DEFAULT CHARACTER SET utf8mb4
          COLLATE utf8mb4_unicode_ci;
        USE biblioteca_db;

        CREATE TABLE IF NOT EXISTS libro (
          id INT AUTO_INCREMENT PRIMARY KEY,
          titulo VARCHAR(200) NOT NULL,
          autor VARCHAR(100) NOT NULL,
          fecha_publicacion DATE NULL,
          genero VARCHAR(50) NULL,
          stock INT NOT NULL DEFAULT 0,
          INDEX (autor),
          INDEX (genero)
        ) ENGINE=InnoDB;

        CREATE TABLE IF NOT EXISTS ficha (
          id INT AUTO_INCREMENT PRIMARY KEY,
          libro_id INT NOT NULL,
          fecha_prestamo DATE NOT NULL,
          fecha_devolucion DATE NULL,
          estado ENUM('Prestado','Devuelto') NOT NULL DEFAULT 'Prestado',
          CONSTRAINT fk_ficha_libro
            FOREIGN KEY (libro_id) REFERENCES libro(id)
            ON DELETE CASCADE
            ON UPDATE CASCADE,
          INDEX (estado),
          INDEX (fecha_prestamo)
        ) ENGINE=InnoDB;

        INSERT INTO libro (titulo, autor, fecha_publicacion, genero, stock) VALUES
        ('El Principito','Antoine de Saint-Exupéry','1943-04-06','Fábula',5),
        ('Cien años de soledad','Gabriel García Márquez','1967-06-05','Realismo mágico',3);

        INSERT INTO ficha (libro_id, fecha_prestamo, fecha_devolucion, estado) VALUES
        (1, CURDATE(), NULL, 'Prestado'),
        (2, DATE_SUB(CURDATE(), INTERVAL 10 DAY), DATE_SUB(CURDATE(), INTERVAL 2 DAY), 'Devuelto');
        SQL;
        $db->query($sql);
      }
    }


  public function getFichas() {
    $db = $this->getConexion();
    $query = $db->prepare('SELECT f.*, l.titulo AS libro_titulo FROM ficha f JOIN libro l ON f.libro_id = l.id ORDER BY f.fecha_prestamo DESC');
    $query->execute();
    return $query->fetchAll(PDO::FETCH_OBJ);
  }

  public function getFicha($id) {
    $db = $this->getConexion();
    $query = $db->prepare('SELECT f.*, l.titulo AS libro_titulo FROM ficha f JOIN libro l ON f.libro_id = l.id WHERE f.id = ? LIMIT 1');
    $query->execute([$id]);
    return $query->fetch(PDO::FETCH_OBJ) ?: null;
  }

  public function insertFicha($libroId, $fechaPrestamo, $fechaDevolucion, $estado) {
    $db = $this->getConexion();
    $query = $db->prepare('INSERT INTO ficha (libro_id, fecha_prestamo, fecha_devolucion, estado) VALUES (?, ?, ?, ?)');
    $query->execute([
      $libroId,
      $fechaPrestamo,
      $fechaDevolucion !== '' ? $fechaDevolucion : null,
      $estado,
    ]);
    return $db->lastInsertId();
  }

  public function updateFicha($id, $libroId, $fechaPrestamo, $fechaDevolucion, $estado) {
    $db = $this->getConexion();
    $query = $db->prepare('UPDATE ficha SET libro_id = ?, fecha_prestamo = ?, fecha_devolucion = ?, estado = ? WHERE id = ?');
    $query->execute([
      $libroId,
      $fechaPrestamo,
      $fechaDevolucion !== '' ? $fechaDevolucion : null,
      $estado,
      $id,
    ]);
  }

  public function deleteFicha($id) {
    $db = $this->getConexion();
    $query = $db->prepare('DELETE FROM ficha WHERE id = ?');
    $query->execute([$id]);
  }

  public function existeLibro($libroId) {
    $db = $this->getConexion();
    $query = $db->prepare('SELECT id FROM libro WHERE id = ? LIMIT 1');
    $query->execute([$libroId]);
    return (bool) $query->fetch(PDO::FETCH_ASSOC);
  }
}
