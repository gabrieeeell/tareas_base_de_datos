<?php
// 1. Iniciar el motor de sesiones (DEBE SER LO PRIMERO)
session_start();

require_once("../BDT1.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 2. Si viene el rol por POST (es el login inicial), lo guardamos en la sesión
    if (isset($_POST['rol_ingreso'])) {
        $_SESSION['rol'] = $_POST['rol_ingreso'];
    }

    // 3. Recuperamos el rol desde la sesión (esté recién llegando o ya guardado de antes)
    $rol = isset($_SESSION['rol']) ? $_SESSION['rol'] : null;

    if (!empty($rol)) {
        // Al usar require, la vista hereda la variable $conexion y $rol
        require("../Templates/vista_lista_postulaciones.php");
    } else {
        echo "Error: Sesión expirada o rol no identificado.";
    }
} else {
    echo "Acceso no permitido.";
}
?>
