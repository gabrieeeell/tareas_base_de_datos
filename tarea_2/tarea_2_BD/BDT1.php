<?php
 // Estos son los parametros para hacer la conexión de la pagina con la base de datos
$host     = "localhost";
$dbname   = "ct_usm_postulaciones";
$usuario = "root";
$contraseña = "";
try {
    // Se instancia el objeto PDO y conectar los datos anteriores con el host
    // con atributos que ayudan a identificar errores
    $x = "mysql:host=$host;dbname=$dbname;charset=utf8";
    $conexion = new PDO($x, $usuario, $contraseña);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Desactivar emulaciones para usar las nativas de MySQl (para las inyecciones SQL)
    $conexion->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    
} catch (PDOException $e) {
    // Aqui se captura el error y se muestra el mensaje ()
    die("Error: " . $e->getMessage());
}

// Ahora hay que usar $conexion en ootros archivos pq es el objeto que se usa para vincular la pagina con al BD pa la mente
?>