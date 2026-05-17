<?php
session_start();

if (!isset($_SESSION['rut_usuario']) || $_SESSION['rol_usuario'] != '1') {
    header("Location: ../index.php");
    exit();
}
require_once("../BDT1.php");
$Rut_resp = $_SESSION['rut_usuario'];
$id_postulacion_view = $_GET['id'] ?? null;
if (!$id_postulacion_view) {
    echo "<script>alert(''); window.location.href='T_rol1.php';</script>";
    exit;
}
try {
    $SQ_main = "SELECT p.*, e.Nombre_empresa, e.Convenio_USM, e.ID_tamano, 
                        r.Nombre as Nombre_rep, r.Mail_representante, r.Telefono_representante 
                 FROM POSTULACION p
                 LEFT JOIN EMPRESA e ON p.Rut_Empresa = e.Rut_Empresa
                 LEFT JOIN REPRESENTANTE_EMPRESA r ON e.ID_representante = r.ID_Representante
                 WHERE p.ID_postulacion = ?";
    $s_main = $conexion->prepare($SQ_main);
    $s_main->execute([$id_postulacion_view]);
    $postulacion = $s_main->fetch(PDO::FETCH_ASSOC);

    if (!$postulacion) {
        die("La postulación no existe.");
    }


    $sql_equipo = "SELECT per.*, pp.rol 
                   FROM Persona per
                   JOIN Persona_postulacion pp ON per.Rut_persona = pp.Rut_persona
                   WHERE pp.ID_postulacion = ?";


    $stmt_equipo = $conexion->prepare($sql_equipo);
    $stmt_equipo->execute([$id_postulacion_view]);

    $integrantes = $stmt_equipo->fetchAll(PDO::FETCH_ASSOC);
    $sql_crono = "SELECT * FROM CRONOGRAMA WHERE ID_postulacion = ?";
    $stmt_crono = $conexion->prepare($sql_crono);
    $stmt_crono->execute([$id_postulacion_view]);
    $etapas = $stmt_crono->fetchAll(PDO::FETCH_ASSOC);
    
    $sedes = $conexion->query("SELECT * FROM SEDE WHERE ID_sede > 0")->fetchAll(PDO::FETCH_ASSOC);
    $tipos_iniciativa = $conexion->query("SELECT * FROM TIPO_INICIATIVA WHERE ID_tipo > 0")->fetchAll(PDO::FETCH_ASSOC);
    $regiones = $conexion->query("SELECT * FROM REGION WHERE ID_region > 0")->fetchAll(PDO::FETCH_ASSOC);
    $jefes = $conexion->query("SELECT * FROM JEFE_CARRERA WHERE ID_Jefe > 0")->fetchAll(PDO::FETCH_ASSOC); 
    $coordinadores = $conexion->query("SELECT * FROM COORDINADOR WHERE ID_coordinador > 0")->fetchAll(PDO::FETCH_ASSOC);
    $tamanos = $conexion->query("SELECT * FROM TAMANO_EMPRESA WHERE ID_tamano > 0")->fetchAll(PDO::FETCH_ASSOC);
    $departamentos = $conexion->query("SELECT * FROM DEPARTAMENTO WHERE ID_departamento > 0")->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    die("Error al cargar datos: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Postulación - CT-USM</title> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color: #f4f6f9;">
    <div class="container mt-5">
        <a href="T_rol1.php" class="btn btn-outline-secondary mb-4 shadow-sm">
            &larr; Volver
        </a>
        <div class="d-flex align-items-center mb-4">
            <h2 class="text-dark fw-bold m-0">Postulación: <?php echo htmlspecialchars($postulacion['ID_postulacion']); ?></h2>
            <span class="badge bg-secondary ms-3 fs-6">Modo Vista (Solo Lectura)</span>
        </div>
        <div>
            <div class="card shadow-sm border-0 mb-5">
                <div class="card-body p-4">
                    <h5 class="text-primary border-bottom pb-2 mb-4">Información General</h5>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Fecha de Postulación</label>
                        <input type="date" class="form-control bg-light" value="<?php echo ($postulacion['Fecha_postulacion'] !== '0000-00-00' && !empty($postulacion['Fecha_postulacion'])) ? $postulacion['Fecha_postulacion'] : ''; ?>" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre_iniciativa</label>
                        <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($postulacion['Nombre_iniciativa'] ?? ''); ?>" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Objetivo_iniciativa</label>
                        <textarea class="form-control bg-light" rows="2" disabled><?php echo htmlspecialchars($postulacion['Objetivo_iniciativa'] ?? ''); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Descripcion_soluciones</label>
                        <textarea class="form-control bg-light" rows="3" disabled><?php echo htmlspecialchars($postulacion['Descripcion_soluciones'] ?? ''); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Resultados_esperados</label>
                        <textarea class="form-control bg-light" rows="2" disabled><?php echo htmlspecialchars($postulacion['Resultados_esperados'] ?? ''); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Presupuesto</label>
                        <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($postulacion['Presupuesto'] ?? ''); ?>" disabled>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm border-0 mb-5">
                <div class="card-body p-4">
                    <h5 class="text-primary border-bottom pb-2 mb-4">Antecedentes de la Postulación</h5>

                    <div class="mb-3">
                        <label class="form-label fw-bold">ID_sede</label>
                        <select class="form-select bg-light" disabled>

                            <option value="0" <?php echo ((int)$postulacion['ID_sede'] === 0) ? 'selected' : ''; ?>>Seleccione una sede</option>
                            <?php foreach($sedes as $s): ?>
                                <option value="<?php echo $s['ID_sede']; ?>" <?php echo ($s['ID_sede'] == ($postulacion['ID_sede'] ?? '')) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($s['Nombre_Sede'] ?? $s['nombre_sede']); ?>
                                </option>

                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">ID_tipo_iniciativa</label>

                        <select class="form-select bg-light" disabled>

                            <option value="0" <?php echo ((int)$postulacion['ID_tipo_iniciativa'] === 0) ? 'selected' : ''; ?>>Seleccione un tipo</option>
                            <?php foreach($tipos_iniciativa as $t): ?>
                                <option value="<?php echo $t['ID_tipo'] ?? $t['id_tipo']; ?>" <?php echo (($t['ID_tipo'] ?? $t['id_tipo']) == ($postulacion['ID_tipo_iniciativa'] ?? '')) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($t['Tipo_iniciativa'] ?? $t['Tipo_iniciativa']); ?>
                                </option>
                            <?php endforeach; ?>

                        </select>

                    </div>

                    <div class="mb-3">

                        <label class="form-label fw-bold">ID_region_origen</label>
                        <select class="form-select bg-light" disabled>
                            <option value="0" <?php echo ((int)$postulacion['ID_region_origen'] === 0) ? 'selected' : ''; ?>>Seleccione una region de origen</option>
                            <?php foreach($regiones as $r): ?>
                                <option value="<?php echo $r['ID_region']; ?>" <?php echo ($r['ID_region'] == ($postulacion['ID_region_origen'] ?? '')) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($r['Nombre_region'] ?? $r['nombre_region']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                    </div>

                    <div class="mb-3">


                        <label class="form-label fw-bold">ID_region_Impacto</label>
                        <select class="form-select bg-light" disabled>
                            <option value="0" <?php echo ((int)$postulacion['ID_region_impacto'] === 0) ? 'selected' : ''; ?>>Seleccione una region de impacto</option>
                            <?php foreach($regiones as $r): ?>
                                <option value="<?php echo $r['ID_region']; ?>" <?php echo ($r['ID_region'] == ($postulacion['ID_region_impacto'] ?? '')) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($r['Nombre_region'] ?? $r['nombre_region']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>


                    </div>

                    <div class="mb-3">

                        <label class="form-label fw-bold">ID_Jefe</label>
                        <select class="form-select bg-light" disabled>
                            <option value="0" <?php echo ((int)$postulacion['ID_jefe'] === 0) ? 'selected' : ''; ?>>Seleccione un jefe de carrera</option>
                            <?php foreach($jefes as $j): ?>
                                <option value="<?php echo $j['ID_jefe'] ?? $j['id_jefe']; ?>" <?php echo (($j['ID_jefe'] ?? $j['id_jefe']) == ($postulacion['ID_Jefe'] ?? $postulacion['ID_jefe'] ?? '')) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($j['Nombre_jefe'] ?? $j['nombre_jefe']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>



                    <div class="mb-3">
                        <label class="form-label fw-bold">ID_coordinador</label>
                        <select class="form-select bg-light" disabled>
                             <option value="0" <?php echo ((int)$postulacion['ID_coordinador'] === 0) ? 'selected' : ''; ?>>Seleccione un coordinador</option>
                            <?php foreach($coordinadores as $c): ?>
                                <option value="<?php echo $c['ID_coordinador'] ?? $c['id_coordinador']; ?>" <?php echo (($c['ID_coordinador'] ?? $c['id_coordinador']) == ($postulacion['ID_coordinador'] ?? '')) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($c['Nombre_coordinador'] ?? $c['nombre_coordinador']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-5">
                <div class="card-body p-4">
                    <h5 class="text-primary border-bottom pb-2 mb-4">Antecedentes Entidad Externa</h5>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre_empresa</label>
                        <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($postulacion['Nombre_empresa'] ?? ''); ?>" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Rut_Empresa</label>
                        <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($postulacion['Rut_Empresa'] ?? ''); ?>" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">ID_tamano</label>
                        <select class="form-select bg-light" disabled>
                             <option value="0" <?php echo ((int)$postulacion['ID_tamano'] === 0) ? 'selected' : ''; ?>>Seleccione un tamaño</option>
                            <?php foreach($tamanos as $t): ?>
                                <option value="<?php echo $t['ID_tamano'] ?? $t['id_tamano']; ?>" <?php echo (($t['ID_tamano'] ?? $t['id_tamano']) == ($postulacion['ID_tamano'] ?? '')) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($t['Descripcion'] ?? $t['descripcion'] ?? $t['Nombre_tamano']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Convenio-USM</label>
                        <select class="form-select bg-light" disabled>
                            <option value="0" <?php echo (($postulacion['Convenio_USM'] ?? 0) == 0) ? 'selected' : ''; ?>>Seleccione si tiene convenio USM</option>
                            <option value="2" <?php echo (($postulacion['Convenio_USM'] ?? 0) == 2) ? 'selected' : ''; ?>>Sí</option>
                            <option value="1" <?php echo (($postulacion['Convenio_USM'] ?? 0) == 1) ? 'selected' : ''; ?>>No</option>
                        </select>
                    </div>

                    <hr class="my-4">
                    <h6 class="fw-bold mb-3">Datos del Representante</h6>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre (Representante)</label>
                        <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($postulacion['Nombre_rep'] ?? ''); ?>" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Mail_representante</label>
                        <input type="email" class="form-control bg-light" value="<?php echo htmlspecialchars($postulacion['Mail_representante'] ?? ''); ?>" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Telefono_representante</label>
                        <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($postulacion['Telefono_representante'] ?? ''); ?>" disabled>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-5">
                <div class="card-body p-4">
                    <h5 class="text-primary border-bottom pb-2 mb-4">Equipo de Trabajo</h5>

                    <div id="contenedor_integrantes">
                        <?php foreach($integrantes as $index => $integ): ?>
                            <?php 
                                $rut_actual = $integ['RUT_Persona'] ?? $integ['rut_persona'] ?? '';
                                $rol_actual = $integ['rol'] ?? $integ['Rol'] ?? '';
                                $es_responsable = (strtoupper(trim($rut_actual)) == strtoupper(trim($_SESSION['rut_usuario'])));
                            ?>
                            
                            <div class="integrante-item border <?php echo $es_responsable ? 'border-primary bg-light' : 'rounded bg-white'; ?> p-3 mb-4">
                                
                                <h6 class="fw-bold text-primary mb-3">
                                    Integrante <?php echo $es_responsable ? '(Responsable)' : ''; ?>
                                </h6>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Rut_Persona</label>
                                    <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($rut_actual); ?>" disabled>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nombre</label>
                                    <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($integ['Nombre'] ?? $integ['nombre'] ?? ''); ?>" disabled>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">ID_departamento</label>
                                    <select class="form-select bg-light" disabled>
                                        <option value="0" <?php echo ((int)($integ['ID_departamento'] ?? 0) === 0) ? 'selected' : ''; ?>>Seleccione un departamento</option>
                                        <?php foreach ($departamentos as $depto): ?>
                                            <option value="<?php echo $depto['ID_departamento']; ?>" <?php echo ($depto['ID_departamento'] == ($integ['ID_departamento'] ?? '')) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($depto['Nombre_departamento'] ?? $depto['nombre_departamento']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">ID_Sede</label>
                                    <select class="form-select bg-light" disabled>
                                        <option value="0" <?php echo ((int)($integ['ID_sede_persona'] ?? 0) === 0) ? 'selected' : ''; ?>>Seleccione una sede</option>
                                        <?php foreach ($sedes as $sede): ?>
                                            <option value="<?php echo $sede['ID_sede'] ?? $sede['ID_Sede']; ?>" <?php echo (($sede['ID_sede'] ?? $sede['ID_Sede']) == ($integ['ID_Sede'] ?? $integ['id_sede'] ?? '')) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($sede['Nombre_Sede'] ?? $sede['nombre_sede']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">eMail</label>
                                    <input type="email" class="form-control bg-light" value="<?php echo htmlspecialchars($integ['eMail'] ?? $integ['Email'] ?? ''); ?>" disabled>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Telefono</label>
                                    <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($integ['telefono'] ?? $integ['Telefono'] ?? ''); ?>" disabled>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Cargo</label>
                                    <select class="form-select bg-light" disabled>
                                        <?php $id_cargo = $integ['ID_cargo'] ?? $integ['id_cargo'] ?? 1; ?>
                                        <option value="0" <?php echo ((int)($integ['ID_cargo'] ?? 0) === 0) ? 'selected' : ''; ?>>Seleccione un cargo</option>
                                        <option value="1" <?php echo ($id_cargo == 1) ? 'selected' : ''; ?>>Estudiante</option>
                                        <option value="2" <?php echo ($id_cargo == 2) ? 'selected' : ''; ?>>Profesor</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Rol en el proyecto</label>
                                    <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($rol_actual); ?>" disabled>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div> 
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-5">
                <div class="card-body p-4">
                    <h5 class="text-primary border-bottom pb-2 mb-4">Cronograma de Trabajo</h5>

                    <div id="contenedor_cronograma">
                        <?php if (!empty($etapas)): ?>
                            <?php foreach($etapas as $e): ?>
                                <div class="etapa-item border border-info rounded p-3 mb-4 bg-light">
                                    <h6 class="fw-bold text-info mb-3">Etapa</h6>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Etapa</label>
                                        <input type="text" class="form-control bg-white" value="<?php echo htmlspecialchars($e['Etapa'] ?? $e['etapa'] ?? ''); ?>" disabled>
                                    </div>


                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Plazos_semanas</label>
                                        <input type="text" class="form-control bg-white" value="<?php echo htmlspecialchars($e['Plazos_semanas'] ?? $e['Plazos_Semanas'] ?? $e['plazos_semanas'] ?? ''); ?>" disabled>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Entregable</label>
                                        <input type="text" class="form-control bg-white" value="<?php echo htmlspecialchars($e['Entregable'] ?? $e['entregable'] ?? ''); ?>" disabled>
                                    </div>
                                </div>




                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div> 

                </div>


            </div>
        </div>

    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>