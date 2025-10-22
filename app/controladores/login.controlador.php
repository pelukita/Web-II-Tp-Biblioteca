<?php

include_once 'app/vistas/login.vista.php';

class LoginControlador {
    private $vista;

    public function __construct() {
        $this->vista = new LoginVista();
    }

    public function showLogin($mensaje = null) {
        // Si ya está logueado, redirige al inicio
        if ($this->estaAutenticado()) {
            header('Location: ' . BASE_URL . 'inicio');
            exit;
        }
        $this->vista->showLogin($mensaje);
    }

    public function procesarLogin() {
        $usuario = trim($_POST['usuario'] ?? '');
        $password = trim($_POST['password'] ?? '');

        $errores = [];
        if ($usuario === '') {
            $errores[] = 'Falta el nombre de usuario.';
        }
        if ($password === '') {
            $errores[] = 'Falta la contraseña.';
        }
        if (!empty($errores)) {
            $this->vista->showLogin(implode(' ', $errores));
            return;
        }

        // Validación hardcodeada
        if ($usuario === 'admin' && $password === 'admin') {
            session_regenerate_id(true);
            $_SESSION['usuario'] = 'admin';
            header('Location: ' . BASE_URL . 'inicio');
            exit;
        } else {
            $this->vista->showLogin('Credenciales inválidas.');
        }
    }

    public function logout() {
        if ($this->estaAutenticado()) {
            $_SESSION = [];
            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params['path'], $params['domain'],
                    $params['secure'], $params['httponly']
                );
            }
            session_destroy();
        }
        header('Location: ' . BASE_URL . 'login');
        exit;
    }

    private function estaAutenticado() {
        return isset($_SESSION['usuario']) && $_SESSION['usuario'] === 'admin';
    }
}
