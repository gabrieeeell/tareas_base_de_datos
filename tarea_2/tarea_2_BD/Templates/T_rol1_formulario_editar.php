<?php
// 1. Iniciamos la sesión
session_start();

// 2. Seguridad
if (!isset($_SESSION['rut_usuario']) || $_SESSION['rol_usuario'] != '1') {
    header("Location: ../index.php");
    exit();
}

// 3. Importamos la conexión
require_once("../BDT1.php"); 
var_dump(get_defined_vars());

// 4. Capturamos el ID de la postulación desde la URL
$id_postulacion_edit = $_GET['id'] ?? null;

if (!$id_postulacion_edit) {
    echo "<script>alert('No se especificó una postulación'); window.location.href='T_rol1.php';</script>";
    exit;
}

try {
    // 5. BUSCAR DATOS PRINCIPALES (Postulación + Empresa + Representante)
    $sql_main = "SELECT p.*, e.*, r.Nombre as Nombre_rep, r.Mail_representante, r.Telefono_representante 
                 FROM POSTULACION p
                 JOIN EMPRESA e ON p.Rut_empresa = e.Rut_empresa
                 JOIN REPRESENTANTE_EMPRESA r ON e.ID_representante = r.ID_Representante
                 WHERE p.ID_postulacion = ?";
    $stmt_main = $conexion->prepare($sql_main);
    $stmt_main->execute([$id_postulacion_edit]);
    $postulacion = $stmt_main->fetch(PDO::FETCH_ASSOC);

    if (!$postulacion) {
        die("La postulación no existe.");
    }

    // 6. BUSCAR INTEGRANTES DEL EQUIPO
    $sql_equipo = "SELECT per.*, pp.rol 
                   FROM Persona per
                   JOIN Persona_postulacion pp ON per.Rut_persona = pp.Rut_persona
                   WHERE pp.ID_postulacion = ?";
    $stmt_equipo = $conexion->prepare($sql_equipo);
    $stmt_equipo->execute([$id_postulacion_edit]);
    $integrantes = $stmt_equipo->fetchAll(PDO::FETCH_ASSOC);

    // 7. BUSCAR CRONOGRAMA
    $sql_crono = "SELECT * FROM CRONOGRAMA WHERE ID_postulacion = ?";
    $stmt_crono = $conexion->prepare($sql_crono);
    $stmt_crono->execute([$id_postulacion_edit]);
    $etapas = $stmt_crono->fetchAll(PDO::FETCH_ASSOC);

    // 8. (Opcional) Cargar catálogos para los SELECTS (Sedes, Regiones, etc.)
    $sedes = $conexion->query("SELECT * FROM SEDE")->fetchAll(PDO::FETCH_ASSOC);
    $regiones = $conexion->query("SELECT * FROM REGION")->fetchAll(PDO::FETCH_ASSOC);
    $tamanos = $conexion->query("SELECT * FROM TAMANO_EMPRESA")->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    die("Error al cargar datos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Postulación - <?php echo $id_postulacion_edit; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">Editar Postulación: <?php echo htmlspecialchars($postulacion['Nombre_iniciativa']); ?></h3>
        </div>
        <div class="card-body">
            <form action="../back/back_formulario_editar.php" method="POST">
                
                <input type="hidden" name="ID_postulacion" value="<?php echo $postulacion['ID_postulacion']; ?>">

                <h4 class="text-primary border-bottom pb-2">1. Información de la Iniciativa</h4>
                <div class="row g-3 mb-4">
                    <div class="col-md-12">
                        <label class="form-label">Nombre de la Iniciativa</label>
                        <input type="text" name="Nombre_iniciativa" class="form-control" value="<?php echo htmlspecialchars($postulacion['Nombre_iniciativa']); ?>" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Objetivo</label>
                        <textarea name="Objetivo_iniciativa" class="form-control" rows="2"><?php echo htmlspecialchars($postulacion['Objetivo_iniciativa']); ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Presupuesto ($)</label>
                        <input type="number" name="Presupuesto" class="form-control" value="<?php echo $postulacion['Presupuesto']; ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Sede</label>
                        <select name="ID_sede" class="form-select">
                            <?php foreach($sedes as $s): ?>
                                <option value="<?php echo $s['ID_sede']; ?>" <?php echo ($s['ID_sede'] == $postulacion['ID_sede']) ? 'selected' : ''; ?>>
                                    <?php echo $s['Nombre_Sede']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <h4 class="text-primary border-bottom pb-2">2. Entidad Externa (Empresa)</h4>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">RUT Empresa</label>
                        <input type="text" name="Rut_Empresa" class="form-control" value="<?php echo $postulacion['Rut_Empresa']; ?>" readonly>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Nombre Empresa</label>
                        <input type="text" name="Nombre_empresa" class="form-control" value="<?php echo htmlspecialchars($postulacion['Nombre_empresa']); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Representante</label>
                        <input type="text" name="Nombre_representante" class="form-control" value="<?php echo htmlspecialchars($postulacion['Nombre_rep']); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email Representante</label>
                        <input type="email" name="Mail_representante" class="form-control" value="<?php echo $postulacion['Mail_representante']; ?>">
                    </div>
                    <div class="col-md-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="Convenio_USM" id="convenio" <?php echo ($postulacion['Convenio_USM'] == 1) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="convenio">Cuenta con Convenio USM</label>
                        </div>
                    </div>
                </div>

                <h4 class="text-primary border-bottom pb-2">3. Equipo de Trabajo</h4>
<div id="contenedor-integrantes" class="mb-4">
    <?php foreach($integrantes as $integ): ?>
        <?php 
            // Comparamos el RUT del integrante con el del usuario logueado
            $es_responsable = ($integ['Rut_persona'] == $_SESSION['rut_usuario']);
            $readonly_attr = $es_responsable ? 'readonly style="background-color: #e9ecef;"' : '';
        ?>
        <div class="card mb-2 border-start border-primary border-4 p-3">
            <div class="row g-2">
                <div class="col-md-3">
                    <label class="small">RUT</label>
                    <input type="text" name="Rut_Persona[]" class="form-control form-control-sm" 
                           value="<?php echo htmlspecialchars($integ['RUT_Persona']); ?>" 
                           <?php echo $readonly_attr; ?>>
                </div>
                <div class="col-md-4">
                    <label class="small">Nombre</label>
                    <input type="text" name="Nombre_persona[]" class="form-control form-control-sm" 
                           value="<?php echo htmlspecialchars($integ['Nombre']); ?>">
                </div>
                <div class="col-md-3">
                    <label class="small">Cargo</label>
                    <select name="ID_cargo[]" class="form-select form-select-sm">
                        <option value="1" <?php echo ($integ['ID_cargo'] == 1) ? 'selected' : ''; ?>>Estudiante</option>
                        <option value="2" <?php echo ($integ['ID_cargo'] == 2) ? 'selected' : ''; ?>>Profesor</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="small">Rol</label>
                    <input type="text" name="Rol[]" class="form-control form-control-sm" 
                           value="<?php echo htmlspecialchars($integ['rol']); ?>" 
                           <?php echo $readonly_attr; ?>>
                </div>
                
                <input type="hidden" name="eMail[]" value="<?php echo $integ['eMail']; ?>">
                <input type="hidden" name="Telefono[]" value="<?php echo $integ['Telefono']; ?>">
                <input type="hidden" name="ID_departamento[]" value="<?php echo $integ['ID_departamento']; ?>">
                <input type="hidden" name="ID_sede_persona[]" value="<?php echo $integ['ID_sede']; ?>">
            </div>
        </div>
    <?php endforeach; ?>
</div>

                <h4 class="text-primary border-bottom pb-2">4. Cronograma de Actividades</h4>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Etapa</th>
                            <th>Semanas</th>
                            <th>Entregable</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($etapas as $e): ?>
                        <tr>
                            <td><input type="text" name="Etapa[]" class="form-control form-control-sm" value="<?php echo htmlspecialchars($e['Etapa']); ?>"></td>
                            <td><input type="number" name="Plazos_Semanas[]" class="form-control form-control-sm" value="<?php echo $e['Plazos_Semanas']; ?>"></td>
                            <td><input type="text" name="Entregable[]" class="form-control form-control-sm" value="<?php echo htmlspecialchars($e['Entregable']); ?>"></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="d-flex justify-content-between mt-4">
                    <a href="T_rol1.php" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-success px-5">Guardar Cambios</button>
                </div>

            </form>
        </div>
    </div>
</div>

</body>
</html>