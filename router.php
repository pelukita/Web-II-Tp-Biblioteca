<?php
require_once 'configuracion/configuracion.php';
require_once 'app/controladores/inicio.controlador.php';
require_once 'app/controladores/libros.controlador.php';
require_once 'app/controladores/fichas.controlador.php';
require_once 'app/controladores/login.controlador.php';

if (!empty($_GET["action"])){
    $action = $_GET["action"];
} else {
    $action = "inicio";
}

$params = explode("/", $action);

switch ($params[0]) {
    case "inicio":
        $controlador = new InicioControlador();
        $controlador->showInicio();
        break;
//------------------------------------------------
    case "libros":
        $controlador = new LibrosControlador();
        if (!isset($params[1])) {
            $controlador->showLibros();
            break;
        }

        switch ($params[1]) {
            case 'crear':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controlador->crearLibro();
                } else {
                    $controlador->showLibros();
                }
                break;
            case 'detalle':
                if (!empty($params[2])) {
                    $controlador->verLibro($params[2]);
                } else {
                    $controlador->showLibros();
                }
                break;
            case 'editar':
                if (!empty($params[2])) {
                    $controlador->editarLibro($params[2]);
                } else {
                    $controlador->showLibros();
                }
                break;
            case 'actualizar':
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($params[2])) {
                    $controlador->actualizarLibro($params[2]);
                } else {
                    $controlador->showLibros();
                }
                break;
            case 'eliminar':
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($params[2])) {
                    $controlador->eliminarLibro($params[2]);
                } else {
                    $controlador->showLibros();
                }
                break;
            case 'fichas':
                $controlador->showLibrosConFichas();
                break;
            default:
                $controlador->showLibros();
                break;
        }
        break;
//------------------------------------------------
    case "fichas":
        $controlador = new FichasControlador();
        if (!isset($params[1])) {
            $controlador->showFichas();
            break;
        }
        switch ($params[1]) {
            case 'crear':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controlador->crearFicha();
                } else {
                    $controlador->showFichas();
                }
                break;
            case 'editar':
                if (!empty($params[2])) {
                    $controlador->editarFicha($params[2]);
                } else {
                    $controlador->showFichas();
                }
                break;
            case 'actualizar':
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($params[2])) {
                    $controlador->actualizarFicha($params[2]);
                } else {
                    $controlador->showFichas();
                }
                break;
            case 'eliminar':
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($params[2])) {
                    $controlador->eliminarFicha($params[2]);
                } else {
                    $controlador->showFichas();
                }
                break;
            case 'detalle':
                if (!empty($params[2])) {
                    $controlador->verFicha($params[2]);
                } else {
                    $controlador->showFichas();
                }
                break;
            default:
                $controlador->showFichas();
                break;
        }
        break;
//------------------------------------------------
    case "login":
        $controlador = new LoginControlador();
        if (!isset($params[1])) {
            $controlador->showLogin();
            break;
        }
        switch ($params[1]) {
            case 'ingresar':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controlador->procesarLogin();
                } else {
                    $controlador->showLogin();
                }
                break;
            case 'salir':
                $controlador->logout();
                break;
            default:
                $controlador->showLogin();
                break;
        }
        break;
    default:
        echo "Error 404 - Página no encontrada";
        break;
}