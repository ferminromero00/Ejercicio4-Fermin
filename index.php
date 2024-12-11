<?php
// Verifica si la sesión ya ha sido iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Establece la variable con el directorio de las vistas
$ruta = "vistas" . DIRECTORY_SEPARATOR;

// Verifica si el usuario está autenticado para cargar la vista correspondiente
$vista = isset($_SESSION["usuario"]) ? "muro.php" : "identificacion.php";

// Incluye los archivos de funciones y controladores necesarios para la aplicación
require("modelo" . DIRECTORY_SEPARATOR . "funciones.php");
require("control" . DIRECTORY_SEPARATOR . "inicioSesion.php");
require("control" . DIRECTORY_SEPARATOR . "paginaPrincipal.php");

// Si el usuario está autenticado y se especifica un usuario en la URL, carga la vista del muro de ese usuario
if (isset($_SESSION["usuario"]) && isset($_GET["usuario"])) {
    $vista = "muro.php";
}

?>

<html>

<head>
    <title>Red Social</title>
</head>

<body style="background-color: grey">
    <?php
    // Muestra el mensaje almacenado en la sesión y luego lo elimina
    $mensaje = $_SESSION["mensaje"] ?? "";
    unset($_SESSION["mensaje"]);

    echo $mensaje;
    ?>

    <br><br>

    <?php
    // Incluye la vista especificada (muro o identificación) según el estado de autenticación
    require $ruta . $vista;
    ?>

    <button><a href="docs/index.html" target="_blank">Documentacion PHP</a></button>

</body>

</html>