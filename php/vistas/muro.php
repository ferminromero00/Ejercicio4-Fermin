<?php

// Verifica si el usuario actual está intentando ver el muro de otro usuario
if (isset($_GET['usuario'])) {
    $usuarioVisto = $_GET['usuario'];
} else {
    $usuarioVisto = $_SESSION["usuario"];
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Red Social</title>
</head>

<body style="background-color: grey">
    <!-- HTML de bienvenida que muestra el nombre del usuario que está siendo visto -->
    <h2>Bienvenido a la página de <?php echo $usuarioVisto; ?></h2>

    <!-- Solo muestra el formulario de publicación si el usuario de la sesión está viendo su propio muro -->
    <?php if ($usuarioVisto === $_SESSION["usuario"]): ?>
        <form method="POST">
            <textarea name="contenido" rows="10" cols="40"></textarea>
            <input type="submit" value="Publicar" name="accionPagina">
        </form>
    <?php endif; ?>

    <!-- Obtener y mostrar publicaciones del usuario actualmente visto -->
    <?php
    $publicaciones = mostrarPublicaciones($usuarioVisto);
    foreach ($publicaciones as $publicacion):
        ?>
        <div class="publicacion">
            <p><strong><?php echo $publicacion['fecha']; ?></strong></p>
            <p><?php echo $publicacion['contenido']; ?></p>

            <!-- Muestra el formulario de borrado solo si el usuario propietario de la publicación está viendo su propio muro -->
            <?php if ($usuarioVisto === $_SESSION["usuario"]): ?>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="borrar_id" value="<?php echo $publicacion['id']; ?>">
                    <input type="hidden" name="accionPagina" value="borrar">
                    <input type="submit" value="Borrar">
                </form>
            <?php endif; ?>

            <!-- Verifica si existen comentarios asociados a la publicación y los muestra -->
            <?php if (file_exists($publicacion['rutaComentarios'])): ?>
                <?php
                $comentarios = file($publicacion['rutaComentarios'], FILE_IGNORE_NEW_LINES);
                if ($comentarios):
                    ?>
                    <h4>Comentarios:</h4>
                    <?php foreach ($comentarios as $comentario): ?>
                        <p><?php echo $comentario; ?></p>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Formulario para añadir un comentario en la publicación, disponible para todos los usuarios -->
            <form method="POST">
                <textarea name="comentario" rows="2" cols="40" placeholder="Añade un comentario..."></textarea>
                <input type="hidden" name="comentario_id" value="<?php echo $publicacion['id']; ?>">
                <input type="hidden" name="usuario_visto" value="<?php echo $usuarioVisto; ?>">
                <input type="hidden" name="accionPagina" value="comentar">
                <input type="submit" value="Comentar">
            </form>
        </div>
        <br>
    <?php endforeach; ?>

    <!-- Muestra la lista de usuarios en una sección a la derecha de la página -->
    <?php $usuarios = obtenerUsuarios(); ?>
    <div style="position: absolute; top: 0; right: 0; margin-right: 50px">
        <h3>Usuarios</h3>
        <ul>
            <!-- Enlace al muro del usuario de la sesión actual -->
            <li><a href="index.php?usuario=<?php echo $_SESSION['usuario']; ?>"><?php echo $_SESSION['usuario']; ?></a>
            </li>

            <!-- Mira sobre cada usuario y muestra un enlace a su muro, excepto el usuario de la sesión actual -->
            <?php foreach ($usuarios as $usuario): ?>
                <?php if ($usuario !== $_SESSION["usuario"]): ?>
                    <li><a href="index.php?usuario=<?php echo $usuario; ?>"><?php echo $usuario; ?></a></li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </div>

    <br><br>

    <!-- Botón para cerrar sesión -->
    <form method="POST">
        <input type="submit" value="Cerrar_Sesion" name="accion">
    </form>
</body>

</html>