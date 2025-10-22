<?php

class LoginVista {
    public function showLogin($mensaje = null) {
        include_once 'templates/header.plantilla.phtml';
        ?>
        <section class="container login-view">
            <h1>Iniciar sesión</h1>
            <?php if ($mensaje): ?>
                <p class="alerta-minima"><?php echo htmlspecialchars($mensaje); ?></p>
            <?php endif; ?>
            <form action="<?php echo BASE_URL; ?>login/ingresar" method="post" class="login-form">
                <div class="campo-formulario">
                    <label for="usuario">Usuario</label>
                    <input type="text" id="usuario" name="usuario" placeholder="admin" required>
                </div>
                <div class="campo-formulario">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" placeholder="admin" required>
                </div>
                <div class="form-acciones">
                    <button type="submit">Entrar</button>
                </div>
            </form>
        </section>
        <?php
        include_once 'templates/footer.plantilla.phtml';
    }
}
