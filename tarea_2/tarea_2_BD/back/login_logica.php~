<?php

require_once("../BDT1.php");
//las siguientes 2 lineas son para mostrar errores durante desarrollo
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $rol = $_POST['rol_ingreso']; // aca se puede usar htmlspecialchars para evitar ataques XSS básicos
    

    if (!empty($rol)) {
        switch ($rol) {
        case "2":
            require("./vistas_segun_rol/vista_evaluador.php");
            break;
        } 
        
    } else {
        echo "El campo rol está vacío.";
    }
} else {
    // Si alguien intenta entrar al PHP directamente sin enviar el formulario
    echo "Acceso no permitido.";
}

?>
