<?php
// 1. Iniciamos la sesión para poder leer el RUT y el Rol
session_start();

// 2. Seguridad: Si no hay un RUT en la sesión, o no es ROL 1, lo echamos al login
if (!isset($_SESSION['rut_usuario']) || $_SESSION['rol_usuario'] != '1') {
    header("Location: ../index.php");
    exit();
}

// 3. Importamos la conexión a la base de datos (subiendo un nivel con ../)
require_once("../BDT1.php");

// 4. Capturamos el RUT en una variable más corta por comodidad
$Rut_resp = $_SESSION['rut_usuario'];

// 5. Buscamos los datos de esta persona para el "Autocompletado"
try {
    $sql = "SELECT Nombre FROM Persona WHERE Rut_persona = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$Rut_resp]);
    
    // Guardamos los resultados en un arreglo
    $datos_persona = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Si la consulta fue exitosa, guardamos el nombre
    $nombre_responsable = $datos_persona ? $datos_persona['Nombre'] : '';

} catch(PDOException $e) {
    // Si hay un error, lo mostramos (útil para la depuración)
    die("Error al buscar datos del usuario: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Postulación</title>
    </head>
<body>
