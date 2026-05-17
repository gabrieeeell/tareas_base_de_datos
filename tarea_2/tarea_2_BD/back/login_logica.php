<?php

session_start();

require_once("../BDT1.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['rol_ingreso'])) {
        $_SESSION['rol'] = $_POST['rol_ingreso'];
    }
    $rol = isset($_SESSION['rol']) ? $_SESSION['rol'] : null;
    if (!empty($rol)) {
        require("../Templates/vista_lista_postulaciones.php");
    } else {
        echo "Error: Sesión expirada o rol no identificado.";
    }
} else {
    echo "Acceso no permitido.";
}
?>
