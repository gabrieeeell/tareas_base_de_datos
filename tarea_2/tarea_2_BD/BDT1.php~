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
    // Cada vez que se encuentre un error relacioado a la base de datos se lanza una expecion (por ejemplo si
    // se hace referencia a alguna tabla que no existe)
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Para evitar las inyecciones SQL se debe desactivar la simulacion de sentencias que envia PHP
    // a MySQL, esto quiere decir que se fuerza a que se separe la interpretación de los datos que se ingresan
    // con los comandos SQL. De esta forma los datos se tratan como texto plano y no sucederia algo como que
    // en usuario se ingrese "admin' --" y en el codigo SQL se haga un SELECT * FROM tipo_usuario WHERE usuario = 'usuario' AND password..
    // entonces si no se desactivan las emulaciones, al ingresar "admin' --" cualquier usuario podria ingresar al 
    // perfil de admin sin poner contraseña. En cambio si se desacrivan las emulaciones, se toma como texto plano y
    // no se produciría eso.
    $conexion->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    
} catch (PDOException $e) {
    // Aqui se captura el error y se muestra el mensaje
    die("Error: " . $e->getMessage());
}

// Ahora hay que usar $conexion en ootros archivos pq es el objeto que se usa para vincular la pagina con al BD pa la mente
?>
