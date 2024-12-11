<?php
if (isset($_REQUEST["accion"])) {
    $accion = str_replace(" ", "", strtolower($_REQUEST["accion"]));
    // Obtiene la fecha y hora actuales
    $fecha = date("Y-m-d H:i:s");

    switch ($accion) {
        case "acceder":
            $comprobar = comprobacion($_REQUEST["nombre"], $_REQUEST["contraseña"]);

            if ($comprobar) {
                $_SESSION["usuario"] = $_REQUEST["nombre"];

                // Registra el inicio de sesión en el archivo de log
                $f2 = fopen("registros.log", "a+");
                if ($f2 != NULL) {
                    fwrite($f2, "Fecha: [$fecha] . Se ha iniciado sesión correctamente con el usuario $_REQUEST[nombre]" . PHP_EOL);
                    fclose($f2);
                }

                header("Location: index.php");
                exit();
            } else {
                $_SESSION["mensaje"] = "Usuario no existe o contraseña incorrecta";

                // Registra el intento fallido de inicio de sesión en el archivo de log
                $f2 = fopen("registros.log", "a+");
                if ($f2 != NULL) {
                    fwrite($f2, "Fecha: [$fecha] . Se ha intentado iniciar sesión con el usuario $_REQUEST[nombre], inicio de sesión incorrecto" . PHP_EOL);
                    fclose($f2);
                }

                header("Location: index.php");
                exit();
            }

        case "registrarse":
            $registro = registrar($_REQUEST["nombre"], $_REQUEST["contraseña"]);

            if ($registro) {
                $_SESSION["mensaje"] = "Usuario registrado con éxito";
            } else {
                $_SESSION["mensaje"] = "No se ha podido registrar el usuario";
            }

            header("Location: index.php");
            exit();

        case "cerrar_sesion":
            $usuario = $_SESSION["usuario"];
            // Elimina las variables de la sesión
            session_unset();
            // Destruye la sesión
            session_destroy();

            // Registra el cierre de sesión en el archivo de log
            $f2 = fopen("registros.log", "a+");
            if ($f2 != NULL) {
                fwrite($f2, "Fecha: [$fecha] . El usuario $usuario ha cerrado la sesión" . PHP_EOL);
                fclose($f2);
            }

            header("Location: index.php");
            exit();
    }
}
?>