<?php
//Con session_start() podemos recordar los datos del usuario postulante para esa sesion, esto es para que no existan responsables con datos contradictorios, como
//responsables con el mismo rut pero diferente nombre o que cada vez se creen mas responsables
session_start();
// Para asegurar que existe un rut y su rol es 1, de esta forma no ocurre que se le da permisos de rol 1 a alguien que puso otra cosa
if (!isset($_SESSION['rut_usuario']) || $_SESSION['rol_usuario'] != '1') {
    header("Location: ../index.php");
    exit();
}

//Esto es para importar la conexión de la BD y asignar variable al rut del usuario responsable
require_once("../BDT1.php");
$Rut_resp = $_SESSION['rut_usuario'];

//Buscamos si existe una persona con ese rut en la BD para el autocompletado de los datos, guardamos una consutla SQL para encontrar los datos de ese rut, pero 
// ponemos signo de "?" ya que de esta forma se espera un dato, que luego se rellena con execute(dato) y asi se envian por separado, lo que evita que se pueda hacer una
// inyección SQL del tipo "rut_persona= 1" OR "1" = "1" " lo que haría que se mostraran todos los datos de todos los usuarios
try {
    $sql = "SELECT Nombre FROM Persona WHERE Rut_persona = ?";
    // 
    $BD_conexion = $conexion->prepare($sql);
    $BD_conexion->execute([$Rut_resp]);
    // Esto es para guardar los datos de la persona en un arreglo (fetch extrae la primera fila)
    $Datos_persona = $BD_conexion->fetch(PDO::FETCH_ASSOC);
    
    // Ahora si hay datos, entonces se guardan, pero sin o hay, se guardan datos vacios
    if ($Datos_persona) {
        $Nombre_resp = $Datos_persona['Nombre'];
    } 
    // Si no existe
    else {
        $Nombre_resp = '';
    }

} catch(PDOException $e) {
    //Por si hay errores, hay q borrarlo para la entrega
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Postulación</title>
    </head>
<body>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Postulación - CT-USM</title> <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color: #f4f6f9;">



    <div class="container mt-5">
        
    <a href="T_rol1.php" class="btn btn-outline-secondary mb-4 shadow-sm">
        &larr; Volver
    </a>

    <h2 class="mb-4 text-dark fw-bold">Crear Nueva Postulación</h2>
        
    <form action="../back/back_formulario_crear.php" method="POST">
        
        <div class="card shadow-sm border-0 mb-5">
            <div class="card-body p-4">
                <h5 class="text-primary border-bottom pb-2 mb-4">Información General</h5>
            

                        <div class="mb-3">
                            <label for="Nombre_iniciativa" class="form-label fw-bold">Nombre_iniciativa (100)*</label>
                            <input type="text" class="form-control" id="Nombre_iniciativa" name="Nombre_iniciativa" required maxlength="100">
                        </div>

                        <div class="mb-3">
                            <label for="Objetivo_iniciativa" class="form-label fw-bold">Objetivo_iniciativa (255)*</label>
                            <textarea class="form-control" id="Objetivo_iniciativa" name="Objetivo_iniciativa" rows="2" required maxlength="255"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="Descripcion_soluciones" class="form-label fw-bold">Descripcion_soluciones (255)*</label>
                            <textarea class="form-control" id="Descripcion_soluciones" name="Descripcion_soluciones" rows="3" required maxlength="255" ></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="Resultados_esperados" class="form-label fw-bold">Resultados_esperados (255)*</label>
                            <textarea class="form-control" id="Resultados_esperados" name="Resultados_esperados" rows="2" required maxlength="255" ></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="Presupuesto" class="form-label fw-bold">Presupuesto (Entero)*</label>
                            <input type="text" inputmode="numeric" pattern="[0-9]+" class="form-control" id="Presupuesto" name="Presupuesto" required>
                        </div>
                        <div class="card shadow-sm border-0 mb-5">

                        <div class="card-body p-4">
                            <h5 class="text-primary border-bottom pb-2 mb-4">Antecedentes de la Postulación</h5>

                            <div class="mb-3">
                                <label for="ID_sede" class="form-label fw-bold">ID_sede (31)*</label>
                                <select class="form-select" id="ID_sede" name="ID_sede" required>
                                    <option value="" selected disabled>Seleccione Sede...</option>
                                    </select>
                            </div>

                            <div class="mb-3">
                                <label for="ID_tipo_iniciativa" class="form-label fw-bold">ID_tipo_iniciativa (9)*</label>
                                <select class="form-select" id="ID_tipo_iniciativa" name="ID_tipo_iniciativa" required>
                                    <option value="" selected disabled>Seleccione Tipo...</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="ID_region_origen" class="form-label fw-bold">ID_region_origen (36)*</label>
                                <select class="form-select" id="ID_region_origen" name="ID_region_origen" required>
                                    <option value="" selected disabled>Seleccione Región...</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="ID_region_Impacto" class="form-label fw-bold">ID_region_Impacto (36)*</label>
                                <select class="form-select" id="ID_region_Impacto" name="ID_region_Impacto" required>
                                    <option value="" selected disabled>Seleccione Región de Impacto...</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="ID_Jefe" class="form-label fw-bold">ID_Jefe (50)*</label>
                                <select class="form-select" id="ID_Jefe" name="ID_Jefe" required>
                                    <option value="" selected disabled>Seleccione Jefe de Carrera...</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="ID_coordinador" class="form-label fw-bold">ID_coordinador (50)*</label>
                                <select class="form-select" id="ID_coordinador" name="ID_coordinador" required>
                                    <option value="" selected disabled>Seleccione Coordinador...</option>
                                </select>
                            </div>

                        </div>
                    </div>


                    <div class="card shadow-sm border-0 mb-5">
    <div class="card-body p-4">
        <h5 class="text-primary border-bottom pb-2 mb-4">Antecedentes Entidad Externa</h5>

        <div class="mb-3">
            <label for="Nombre_empresa" class="form-label fw-bold">Nombre_empresa (100)*</label>
            <input type="text" class="form-control" id="Nombre_empresa" name="Nombre_empresa" required maxlength="100" >
        </div>

        <div class="mb-3">
            <label for="Rut_empresa" class="form-label fw-bold">Rut_empresa (12)*</label>
            <input type="text" class="form-control" id="Rut_empresa" name="Rut_empresa" required maxlength="12" >
        </div>

        <div class="mb-3">
            <label for="ID_tamano" class="form-label fw-bold">ID_tamano (15)*</label>
            <select class="form-select" id="ID_tamano" name="ID_tamano" required>
                <option value="" selected disabled>Seleccione Tamaño...</option>
                </select>
        </div>

        <div class="mb-3">
            <label for="Convenio_USM" class="form-label fw-bold">Convenio-USM (Booleano)*</label>
            <select class="form-select" id="Convenio_USM" name="Convenio_USM" required>
                <option value="" selected disabled>¿Posee convenio vigente?</option>
                <option value="1">Sí</option>
                <option value="0">No</option>
            </select>
        </div>

        <hr class="my-4">
        <h6 class="fw-bold mb-3">Datos del Representante</h6>

        <div class="mb-3">
            <label for="Nombre_representante" class="form-label fw-bold">Nombre (Representante) (100)*</label>
            <input type="text" class="form-control" id="Nombre_representante" name="Nombre_representante" required maxlength="100" >
        </div>

        <div class="mb-3">
            <label for="Mail_representante" class="form-label fw-bold">Mail_representante (255)*</label>
            <input type="email" class="form-control" id="Mail_representante" name="Mail_representante" required maxlength="255" >
        </div>

        <div class="mb-3">
            <label for="Telefono_representante" class="form-label fw-bold">Telefono_representante (12)*</label>
            <input type="text" class="form-control" id="Telefono_representante" name="Telefono_representante" required maxlength="12" >
        </div>

    </div>
</div>
                
            </div>
        </div>
        



    </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
