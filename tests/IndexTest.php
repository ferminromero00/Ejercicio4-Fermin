<?php

use PHPUnit\Framework\TestCase;

class IndexTest extends TestCase
{
    public function testSessionStart()
    {
        $_SESSION = []; // Simula una sesión vacía
        $_SESSION["usuario"] = "testUser"; // Añade datos de prueba
        $this->assertTrue(isset($_SESSION["usuario"]));
    }

    public function testVistaSeleccionada()
    {
        // Simula una sesión de usuario autenticado
        $_SESSION["usuario"] = "testUser";
        $ruta = "vistas" . DIRECTORY_SEPARATOR;
        $vista = isset($_SESSION["usuario"]) ? "muro.php" : "identificacion.php";
        $this->assertEquals("muro.php", $vista);
    }
}