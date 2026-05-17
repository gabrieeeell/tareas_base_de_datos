<?php

$host     = "localhost";
$dbname   = "ct_usm_postulaciones";
$usuario = "root";
$contraseña = "";
try {
    $x = "mysql:host=$host;dbname=$dbname;charset=utf8";
    $conexion = new PDO($x, $usuario, $contraseña);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conexion->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

// Ahora hay que usar $conexion en otros archivos pq es el objeto que se usa para vincular la pagina con al BD pa la mente
?>
