<?php

// Para cerrar la sesión se debe poner primero session_start() para que se reconozca la sesión, luego con destroy se eliminan los datos de la sesion (que era el rut del 
// responsable para rol 1 por ejemplo) y luego se redirecciona directamente a la página principal
session_start();
session_destroy();
header("Location: ../index.php");
exit();
?>