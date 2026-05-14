// EASTER EGG cristopher shiny
<?php
// Esto nos ayuda a saber en todo momento que persona está rellenando el formulario, para mas adelante hacer
// un autocompletado de datos y asegurar integridad de datos en la BD
session_start();

// Aqui se guardan los datos de la sesion, para hacer el posterior autocompletado en el caso del rol 1
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['rut_ingreso']) && isset($_POST['rol_ingreso'])) {
    $_SESSION['rut_usuario'] = $_POST['rut_ingreso'];
    $_SESSION['rol_usuario'] = $_POST['rol_ingreso'];
    // Al recargar la página y salir se ingresa con los datos de la sesion
    header("Location: index.php");
    exit();
}

// Si ya existe una sesion abierta en el navegador entonces se redirecciona automaticamente (Se puede eliminar, de momento si se ingresa rol 2 o 3 se queda atrapado en
// un 404), para salir hay que meterse a /cerrar_sesion.php y ahi se destruye la sesion, supongo que cuando ya se tengan todos los casos cubiertos de datos que se puedan ingresar
// va a funcionar bien
if (isset($_SESSION['rol_usuario']) && isset($_SESSION['rut_usuario'])) {
    $rol = $_SESSION['rol_usuario'];
    if ($rol == '1') {
        header("Location: Templates/T_rol1.php");
        exit();
    } elseif ($rol == '2') {
        //require("./Templates/vista_lista_postulaciones.php");
        header("Location: Templates/vista_lista_postulaciones.php");
        exit();
    } elseif ($rol == '3') {
        header("Location: Templates/T_rol3.php");
        exit();
    }
}

// Para usar la conexion a la base de datos
require("BDT1.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CT-USM - Ingreso</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('Imagenes/FondoInicio.png');
            background-size: cover
                }
        .min-vh-100 {
            min-height: 100vh;
        }
    </style>
</head>

<body>

    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="row w-100 justify-content-center">
            
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card shadow-lg border-0 rounded-4 text-center" style="background-color: rgba(255, 255, 255, 0.95);">
                    <div class="card-body p-4 p-md-5">
                        <h2 class="card-title mb-4 fw-bold text-dark">
                            Postulaciones USM
                        </h2>
                        
                        <img src="Imagenes/logoU.jpg" alt="Logo USM" class="img-fluid mb-4" style="max-height: 130px;">
                        
                        <form action="index.php" method="POST">
                            
                            <div class="mb-3 text-start">
                                <label for="rut_ingreso" class="form-label text-secondary fw-semibold">RUT</label>
                                <input 
                                    type="text" 
                                    name="rut_ingreso" 
                                    id="rut_ingreso" 
                                    class="form-control form-control-lg text-center border-secondary"
                                    placeholder="Pruebaprueba"
                                    required
                                >
                            </div>

                            <div class="mb-4 text-start">
                                <label for="rol_ingreso" class="form-label text-secondary fw-semibold">Rol</label>
                                <label class="form-label">
                                </label>

                                <input 
                                    type="text" 
                                    /*valor*/
                                    name="rol_ingreso" 
                                    id="rol_ingreso" 
                                    class="form-control form-control-lg text-center border-secondary"

                                    /*FondoapuntandoatucarpetaImagenes*/
                                    
                                    required
                                >
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold">
                                Entrar
                            </button>
                        </form>

                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
