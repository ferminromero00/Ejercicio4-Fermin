<?php

/**
 * Registra un nuevo usuario.
 * TEXTO DEMOSTRACION QUE FUNCIONA LA DOCUMENTACION
 *
 * @param string $usuario El nombre de usuario.
 * @param string $contraseña La contraseña del usuario.
 * @return bool True si el registro fue exitoso, false en caso contrario.
 */ function registrar($usuario, $contraseña)
{
    $ok = false;
    // Obtener la fecha actual
    $fecha = date("Y-m-d H:i:s");

    // Verificar si el directorio del usuario no existe y crearlo
    if (!is_dir("usuarios/$usuario")) {
        mkdir("usuarios/$usuario"); // Crear el directorio del usuario
    }

    // Abrir el archivo de usuarios en modo de escritura para agregar el nuevo usuario
    $f = fopen("usuarios/usuarios.ini", "a+");
    if ($f != NULL) {
        // Guardar usuario y contraseña en el archivo
        $ok = fwrite($f, "$usuario=$contraseña" . PHP_EOL);
        fclose($f);
    }

    // Registrar el evento de registro en un archivo de log
    $f2 = fopen("registros.log", "a+");
    if ($f2 != NULL) {
        fwrite($f2, "Fecha: [$fecha] . El usuario $usuario se ha registrado" . PHP_EOL);
        fclose($f2);
    }

    return $ok;
}

/**
 * Verifica las credenciales de inicio de sesión.
 *
 * @param string $usuario El nombre de usuario.
 * @param string $contraseña La contraseña del usuario.
 * @return bool True si las credenciales son correctas, false en caso contrario.
 */
function comprobacion($usuario, $contraseña)
{
    $ok = false;
    // Cargar los usuarios registrados
    $usuarios = parse_ini_file("usuarios/usuarios.ini");
    // Verificar si el usuario existe y si la contraseña coincide
    if (isset($usuarios[$usuario]) && $usuarios[$usuario] === $contraseña) {
        $ok = true;
    }

    return $ok;
}

/**
 * Publica contenido en el muro del usuario.
 *
 * @param string $contenido El contenido a publicar.
 * @return bool True si la publicación fue exitosa, false en caso contrario.
 */
function publicar($contenido)
{
    $usuario = $_SESSION["usuario"];
    $contador = 1;
    $directorio = "usuarios/$usuario/";

    // Buscar el próximo número de publicación disponible
    $publicacion = $directorio . "publicacion$contador";
    while (is_dir($publicacion)) {
        $contador++;
        $publicacion = $directorio . "publicacion$contador";
    }

    mkdir($publicacion);

    $ruta = $publicacion . "/publicacion$contador.txt";
    $fecha = date("Y-m-d H:i:s");

    // Registrar el evento de publicación en un archivo de log
    $f2 = fopen("registros.log", "a+");
    if ($f2 != NULL) {
        fwrite($f2, "Fecha: [$fecha] . El usuario $usuario ha subido una publicacion a su muro" . PHP_EOL);
        fclose($f2);
    }

    // Crear el archivo de la publicación y escribir la fecha y contenido
    $f = fopen($ruta, "w");
    if ($f) {
        fwrite($f, "Fecha: $fecha" . PHP_EOL);
        fwrite($f, "Contenido: $contenido" . PHP_EOL);
        fclose($f);
        return true;
    }
    return false;
}

/**
 * Borra una publicación de un usuario.
 *
 * @param int $id El ID de la publicación a borrar.
 * @return bool True si la publicación fue borrada, false en caso contrario.
 */
function borrarPublicacion($id)
{
    $usuario = $_SESSION["usuario"];
    $directorio = "usuarios/$usuario/publicacion$id";

    $fecha = date("Y-m-d H:i:s");

    // Registrar el evento de borrado en un archivo de log
    $f2 = fopen("registros.log", "a+");
    if ($f2 != NULL) {
        fwrite($f2, "Fecha: [$fecha] . El usuario $usuario ha borrado una publicacion de su muro" . PHP_EOL);
        fclose($f2);
    }

    // Borrar el directorio de la publicación y sus archivos
    if (is_dir($directorio)) {

        array_map('unlink', glob("$directorio/*.*"));
        // Eliminar el directorio
        rmdir($directorio);
        return true;
    }

    return false;
}

/**
 * Agrega un comentario a una publicación de un usuario.
 *
 * @param int $idPublicacion El ID de la publicación.
 * @param string $comentario El comentario a agregar.
 * @param string $usuarioVisto El usuario dueño de la publicación.
 * @return bool True si el comentario fue agregado, false en caso contrario.
 */
function agregarComentario($idPublicacion, $comentario, $usuarioVisto)
{
    $usuario = $_SESSION["usuario"];
    $directorio = "usuarios/$usuarioVisto/";
    $rutaComentarios = $directorio . "publicacion$idPublicacion/comentarios.txt";

    $fecha = date("Y-m-d H:i:s");

    // Registrar el evento de comentario en un archivo de log
    $f2 = fopen("registros.log", "a+");
    if ($f2 != NULL) {
        if ($usuario === $usuarioVisto) {
            fwrite($f2, "Fecha: [$fecha] . El usuario $usuario ha hecho un comentario en su propio muro" . PHP_EOL);
        } else {
            fwrite($f2, "Fecha: [$fecha] . El usuario $usuario ha hecho un comentario en el muro de $usuarioVisto" . PHP_EOL);
        }
        fclose($f2);
    }

    // Añadir el comentario al archivo de comentarios
    $f = fopen($rutaComentarios, "a");
    if ($f) {
        fwrite($f, "$usuario: $comentario" . PHP_EOL);
        fclose($f);
        return true;
    }
    return false;
}

/**
 * Muestra las publicaciones de un usuario.
 *
 * @param string $usuario El nombre del usuario.
 * @return array Un array con las publicaciones del usuario.
 */
function mostrarPublicaciones($usuario)
{
    $directorio = "usuarios/$usuario/";
    $contador = 1;
    // Array para almacenar publicaciones
    $publicaciones = [];

    // Explora los directorios de publicaciones del usuario para cargar cada publicación existente
    // Las publicaciones se guardan en directorios numerados en orden, asi : (publicacion1, publicacion2, ...).
    // Utilizamos un contador para construir el nombre de cada directorio y verificamos su existencia con is_dir.
    // Si un directorio existe, asumimos que contiene una publicación válida.

    while (is_dir($directorio . "publicacion$contador")) {
        $archivoPublicacion = $directorio . "publicacion$contador/publicacion$contador.txt";
        $rutaComentarios = $directorio . "publicacion$contador/comentarios.txt";

        if (file_exists($archivoPublicacion)) {
            $contenidoArchivo = file($archivoPublicacion, FILE_IGNORE_NEW_LINES);
            $fecha = isset($contenidoArchivo[0]) ? $contenidoArchivo[0] : '';
            $contenido = isset($contenidoArchivo[1]) ? $contenidoArchivo[1] : '';

            // Agregar la publicación al array de publicaciones
            $publicaciones[] = [
                'fecha' => $fecha,
                'contenido' => $contenido,
                'id' => $contador,
                'rutaComentarios' => $rutaComentarios
            ];
        }
        $contador++;
    }

    return $publicaciones; // Retornar las publicaciones encontradas
}

/**
 * Obtiene la lista de usuarios registrados.
 *
 * @return array Un array con los nombres de los usuarios registrados.
 */
function obtenerUsuarios()
{
    // Obtener los nombres de usuarios como array
    return array_keys(parse_ini_file("usuarios/usuarios.ini"));
}

?>
