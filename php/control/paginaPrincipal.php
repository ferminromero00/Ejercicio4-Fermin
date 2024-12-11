<?php
// Verifica si se ha recibido una acción
if (isset($_REQUEST["accionPagina"])) {
    // se pone en minúsculas y elimina espacios en blanco
    $accion = str_replace(" ", "", strtolower($_REQUEST["accionPagina"]));

    switch ($accion) {
        case "publicar":
            // Intenta publicar el contenido y guarda el resultado en $publicacion
            $publicacion = publicar($_REQUEST["contenido"]);

            // Verifica si la publicación funciono o no
            if ($publicacion) {
                $_SESSION["mensaje"] = "Publicación registrada con éxito";
            } else {
                $_SESSION["mensaje"] = "No se ha podido subir la publicación";
            }
            header("Location: index.php");
            exit();

        case "borrar":
            if (isset($_POST["borrar_id"])) {
                // Llama a la función para borrar la publicación
                borrarPublicacion($_POST["borrar_id"]);
                $_SESSION["mensaje"] = "Publicación borrada con éxito";
            } else {
                $_SESSION["mensaje"] = "Error al intentar borrar la publicación";
            }
            header("Location: index.php");
            exit();

        case "comentar":
            // Verifica que el contenido del comentario, el ID de la publicación, y el usuario objetivo estén presentes
            if (isset($_POST["comentario"]) && isset($_POST["comentario_id"]) && isset($_POST["usuario_visto"])) {
                // Recogemos los valores del formulario
                $comentario = $_POST["comentario"];
                $idPublicacion = $_POST["comentario_id"];
                $usuarioVisto = $_POST["usuario_visto"];
                
                // Llama a la función para agregar el comentario
                agregarComentario($idPublicacion, $comentario, $usuarioVisto);
                $_SESSION["mensaje"] = "Comentario añadido con éxito";
            } else {
                $_SESSION["mensaje"] = "Error al añadir el comentario";
            }
            header("Location: index.php?usuario=$usuarioVisto");
            exit();
    }
}
?>
