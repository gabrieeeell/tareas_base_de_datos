<?php
session_start();

if (!isset($_SESSION['rut_usuario']) || $_SESSION['rol_usuario'] != '1') {
    header("Location: ../index.php");
    exit();
}

require_once("../BDT1.php");
$Rut_resp = $_SESSION['rut_usuario'];
$id_postulacion_edit = $_GET['id'] ?? null;

if (!$id_postulacion_edit) {
    echo "<script>alert('No se especificó una postulación'); window.location.href='T_rol1.php';</script>";
    exit;
}

try {
    // 1. OBTENER DATOS DE LA POSTULACIÓN, EMPRESA Y REPRESENTANTE
    $sql_main = "SELECT p.*, e.Nombre_empresa, e.Convenio_USM, e.ID_tamano, 
                        r.Nombre as Nombre_rep, r.Mail_representante, r.Telefono_representante 
                 FROM POSTULACION p
                 LEFT JOIN EMPRESA e ON p.Rut_Empresa = e.Rut_Empresa
                 LEFT JOIN REPRESENTANTE_EMPRESA r ON e.ID_representante = r.ID_Representante
                 WHERE p.ID_postulacion = ?";
    $stmt_main = $conexion->prepare($sql_main);
    $stmt_main->execute([$id_postulacion_edit]);
    $postulacion = $stmt_main->fetch(PDO::FETCH_ASSOC);

    if (!$postulacion) {
        die("La postulación no existe.");
    }

    // 2. OBTENER INTEGRANTES
    $sql_equipo = "SELECT per.*, pp.rol 
                   FROM Persona per
                   JOIN Persona_postulacion pp ON per.Rut_persona = pp.Rut_persona
                   WHERE pp.ID_postulacion = ?";
    $stmt_equipo = $conexion->prepare($sql_equipo);
    $stmt_equipo->execute([$id_postulacion_edit]);
    $integrantes = $stmt_equipo->fetchAll(PDO::FETCH_ASSOC);

    // 3. OBTENER CRONOGRAMA
    $sql_crono = "SELECT * FROM CRONOGRAMA WHERE ID_postulacion = ?";
    $stmt_crono = $conexion->prepare($sql_crono);
    $stmt_crono->execute([$id_postulacion_edit]);
    $etapas = $stmt_crono->fetchAll(PDO::FETCH_ASSOC);

    // 4. CARGAR CATÁLOGOS PARA LOS SELECTS (Filtrando el ID 0 para ocultar "Vacío")
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
    <title>Editar Postulación - CT-USM</title> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color: #f4f6f9;">

    <div class="container mt-5">
        
        <a href="T_rol1.php" class="btn btn-outline-secondary mb-4 shadow-sm">
            &larr; Volver
        </a>

        <h2 class="mb-4 text-dark fw-bold">Editar Postulación: <?php echo htmlspecialchars($postulacion['ID_postulacion']); ?></h2>
            
        <form action="../back/back_formulario_editar.php" method="POST">
            
            <input type="hidden" name="ID_postulacion" value="<?php echo $postulacion['ID_postulacion']; ?>">

            <div class="card shadow-sm border-0 mb-5">
                <div class="card-body p-4">
                    <h5 class="text-primary border-bottom pb-2 mb-4">Información General</h5>

                    <div class="mb-3">
                        <label for="Fecha_postulacion" class="form-label fw-bold">Fecha de Postulación</label>
                        <input type="date" class="form-control" id="Fecha_postulacion" name="Fecha_postulacion" 
                            value="<?php echo ($postulacion['Fecha_postulacion'] !== '0000-00-00' && !empty($postulacion['Fecha_postulacion'])) ? $postulacion['Fecha_postulacion'] : ''; ?>" requiered>
                    </div>

                    <div class="mb-3">
                        <label for="Nombre_iniciativa" class="form-label fw-bold">Nombre_iniciativa (100)*</label>
                        <input type="text" class="form-control" id="Nombre_iniciativa" name="Nombre_iniciativa" value="<?php echo htmlspecialchars($postulacion['Nombre_iniciativa'] ?? ''); ?>" required maxlength="100">
                    </div>

                    <div class="mb-3">
                        <label for="Objetivo_iniciativa" class="form-label fw-bold">Objetivo_iniciativa (255)*</label>
                        <textarea class="form-control" id="Objetivo_iniciativa" name="Objetivo_iniciativa" rows="2" required maxlength="255"><?php echo htmlspecialchars($postulacion['Objetivo_iniciativa'] ?? ''); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="Descripcion_soluciones" class="form-label fw-bold">Descripcion_soluciones (255)*</label>
                        <textarea class="form-control" id="Descripcion_soluciones" name="Descripcion_soluciones" rows="3" required maxlength="255"><?php echo htmlspecialchars($postulacion['Descripcion_soluciones'] ?? ''); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="Resultados_esperados" class="form-label fw-bold">Resultados_esperados (255)*</label>
                        <textarea class="form-control" id="Resultados_esperados" name="Resultados_esperados" rows="2" required maxlength="255"><?php echo htmlspecialchars($postulacion['Resultados_esperados'] ?? ''); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="Presupuesto" class="form-label fw-bold">Presupuesto (Entero)*</label>
                        <input type="text" inputmode="numeric" pattern="[0-9]+" class="form-control" id="Presupuesto" name="Presupuesto" value="<?php echo $postulacion['Presupuesto'] ?? ''; ?>" required>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-5">
                <div class="card-body p-4">
                    <h5 class="text-primary border-bottom pb-2 mb-4">Antecedentes de la Postulación</h5>

                    <div class="mb-3">
                        <label for="ID_sede" class="form-label fw-bold">ID_sede (31)*</label>
                        <select class="form-select" id="ID_sede" name="ID_sede" required>
                            <option value="0" <?php echo ((int)$postulacion['ID_sede'] === 0) ? 'selected' : ''; ?>>Seleccione una sede</option>
                            <?php foreach($sedes as $s): ?>
                                <option value="<?php echo $s['ID_sede']; ?>" <?php echo ($s['ID_sede'] == ($postulacion['ID_sede'] ?? $postulacion['ID_sede'] ?? '')) ? 'selected' : ''; ?>>
                                    <?php echo $s['Nombre_Sede'] ?? $s['nombre_sede']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="ID_tipo_iniciativa" class="form-label fw-bold">ID_tipo_iniciativa (9)*</label>
                        <select class="form-select" id="ID_tipo_iniciativa" name="ID_tipo_iniciativa" required>
                            <option value="0" <?php echo ((int)$postulacion['ID_tipo_iniciativa'] === 0) ? 'selected' : ''; ?>>Seleccione un tipo</option>
                            <?php foreach($tipos_iniciativa as $t): ?>
                                <option value="<?php echo $t['ID_tipo'] ?? $t['id_tipo']; ?>" <?php echo (($t['ID_tipo'] ?? $t['id_tipo']) == ($postulacion['ID_tipo_iniciativa'] ?? '')) ? 'selected' : ''; ?>>
                                    <?php echo $t['Tipo_iniciativa'] ?? $t['Tipo_iniciativa']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="ID_region_origen" class="form-label fw-bold">ID_region_origen (36)*</label>
                        <select class="form-select" id="ID_region_origen" name="ID_region_origen" required>
                            <option value="0" <?php echo ((int)$postulacion['ID_region_origen'] === 0) ? 'selected' : ''; ?>>Seleccione una region de origen</option>
                            <?php foreach($regiones as $r): ?>
                                <option value="<?php echo $r['ID_region']; ?>" <?php echo ($r['ID_region'] == ($postulacion['ID_region_origen'] ?? '')) ? 'selected' : ''; ?>>
                                    <?php echo $r['Nombre_region'] ?? $r['nombre_region']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="ID_region_Impacto" class="form-label fw-bold">ID_region_Impacto (36)*</label>
                        <select class="form-select" id="ID_region_Impacto" name="ID_region_impacto" required>
                            <option value="0" <?php echo ((int)$postulacion['ID_region_impacto'] === 0) ? 'selected' : ''; ?>>Seleccione una region de impacto</option>
                            <?php foreach($regiones as $r): ?>
                                <option value="<?php echo $r['ID_region']; ?>" <?php echo ($r['ID_region'] == ($postulacion['ID_region_impacto'] ?? '')) ? 'selected' : ''; ?>>
                                    <?php echo $r['Nombre_region'] ?? $r['nombre_region']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="ID_Jefe" class="form-label fw-bold">ID_Jefe (50)*</label>
                        <select class="form-select" id="ID_Jefe" name="ID_Jefe" required>
                            <option value="0" <?php echo ((int)$postulacion['ID_jefe'] === 0) ? 'selected' : ''; ?>>Seleccione un jefe de carrera</option>
                            <?php foreach($jefes as $j): ?>
                                <option value="<?php echo $j['ID_jefe'] ?? $j['id_jefe']; ?>" <?php echo (($j['ID_jefe'] ?? $j['id_jefe']) == ($postulacion['ID_Jefe'] ?? $postulacion['ID_jefe'] ?? '')) ? 'selected' : ''; ?>>
                                    <?php echo $j['Nombre_jefe'] ?? $j['nombre_jefe']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="ID_coordinador" class="form-label fw-bold">ID_coordinador (50)*</label>
                        <select class="form-select" id="ID_coordinador" name="ID_coordinador" required>
                             <option value="0" <?php echo ((int)$postulacion['ID_coordinador'] === 0) ? 'selected' : ''; ?>>Seleccione un coordinador</option>
                            <?php foreach($coordinadores as $c): ?>
                                <option value="<?php echo $c['ID_coordinador'] ?? $c['id_coordinador']; ?>" <?php echo (($c['ID_coordinador'] ?? $c['id_coordinador']) == ($postulacion['ID_coordinador'] ?? '')) ? 'selected' : ''; ?>>
                                    <?php echo $c['Nombre_coordinador'] ?? $c['nombre_coordinador']; ?>
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
                        <label for="Nombre_empresa" class="form-label fw-bold">Nombre_empresa (100)*</label>
                        <input type="text" class="form-control" id="Nombre_empresa" name="Nombre_empresa" value="<?php echo htmlspecialchars($postulacion['Nombre_empresa'] ?? ''); ?>" required maxlength="100">
                    </div>

                    <div class="mb-3">
                        <label for="Rut_Empresa" class="form-label fw-bold">Rut_Empresa (12)*</label>
                        <input type="text" class="form-control" id="Rut_Empresa" name="Rut_Empresa" value="<?php echo htmlspecialchars($postulacion['Rut_Empresa'] ?? $postulacion['Rut_Empresa'] ?? ''); ?>" required maxlength="12">
                    </div>

                    <div class="mb-3">
                        <label for="ID_tamano" class="form-label fw-bold">ID_tamano (15)*</label>
                        <select class="form-select" id="ID_tamano" name="ID_tamano" required>
                             <option value="0" <?php echo ((int)$postulacion['ID_tamano'] === 0) ? 'selected' : ''; ?>>Seleccione un tamaño</option>
                            <?php foreach($tamanos as $t): ?>
                                <option value="<?php echo $t['ID_tamano'] ?? $t['id_tamano']; ?>" <?php echo (($t['ID_tamano'] ?? $t['id_tamano']) == ($postulacion['ID_tamano'] ?? '')) ? 'selected' : ''; ?>>
                                    <?php echo $t['Descripcion'] ?? $t['descripcion'] ?? $t['Nombre_tamano']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="Convenio_USM" class="form-label fw-bold">Convenio-USM (Booleano)*</label>
                        <select class="form-select" id="Convenio_USM" name="Convenio_USM" required>
                            <option value="0" <?php echo (($postulacion['Convenio_USM'] ?? 0) == 0) ? 'selected' : ''; ?>>Seleccione si tiene convenio USM</option>
                            <option value="2" <?php echo (($postulacion['Convenio_USM'] ?? 0) == 2) ? 'selected' : ''; ?>>Sí</option>
                            <option value="1" <?php echo (($postulacion['Convenio_USM'] ?? 0) == 1) ? 'selected' : ''; ?>>No</option>
                        </select>
                    </div>

                    <hr class="my-4">
                    <h6 class="fw-bold mb-3">Datos del Representante</h6>

                    <div class="mb-3">
                        <label for="Nombre_representante" class="form-label fw-bold">Nombre (Representante) (100)*</label>
                        <input type="text" class="form-control" id="Nombre_representante" name="Nombre_representante" value="<?php echo htmlspecialchars($postulacion['Nombre_rep'] ?? ''); ?>" required maxlength="100">
                    </div>

                    <div class="mb-3">
                        <label for="Mail_representante" class="form-label fw-bold">Mail_representante (255)*</label>
                        <input type="email" class="form-control" id="Mail_representante" name="Mail_representante" value="<?php echo htmlspecialchars($postulacion['Mail_representante'] ?? ''); ?>" required maxlength="255">
                    </div>

                    <div class="mb-3">
                        <label for="Telefono_representante" class="form-label fw-bold">Telefono_representante (12)*</label>
                        <input type="text" class="form-control" id="Telefono_representante" name="Telefono_representante" value="<?php echo htmlspecialchars($postulacion['Telefono_representante'] ?? ''); ?>" required maxlength="12">
                    </div>
                </div>
            </div>



<div class="card shadow-sm border-0 mb-5">
                <div class="card-body p-4">
                    <h5 class="text-primary border-bottom pb-2 mb-4">Equipo de Trabajo</h5>

                    <div id="contenedor_integrantes">
                        <?php foreach($integrantes as $index => $integ): ?>
                            <?php 
                                // Limpieza y validación de llaves para evitar Warnings
                                $rut_actual = $integ['RUT_Persona'] ?? $integ['RUT_Persona'] ?? $integ['RUT_Persona'] ?? $integ['rut_persona'] ?? '';
                                $rol_actual = $integ['rol'] ?? $integ['Rol'] ?? '';
                                
                                // Verificación de si es el responsable (Comparación limpia)
                                $es_responsable = (strtoupper(trim($rut_actual)) == strtoupper(trim($_SESSION['rut_usuario'])));
                                $num_integrante = $index + 1;
                            ?>
                            
                            <div class="integrante-item border <?php echo $es_responsable ? 'border-primary bg-light' : 'rounded bg-white position-relative'; ?> p-3 mb-4">
                                
                                <?php if (!$es_responsable): ?>
                                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 btn-eliminar">X</button>
                                <?php endif; ?>

                                <h6 class="fw-bold text-primary mb-3">
                                        Integrante <?php echo $es_responsable ? '(Responsable)' : ''; ?>
                                </h6>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Rut_Persona (12)*</label>
                                    <?php if ($es_responsable): ?>
                                        <input type="text" class="form-control bg-white" value="<?php echo htmlspecialchars($rut_actual); ?>" readonly required>
                                        <input type="hidden" name="Rut_Persona[]" value="<?php echo htmlspecialchars($rut_actual); ?>">
                                    <?php else: ?>
                                        <input type="text" name="Rut_Persona[]" class="form-control" value="<?php echo htmlspecialchars($rut_actual); ?>" required maxlength="12">
                                    <?php endif; ?>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nombre (100)*</label>
                                    <input type="text" class="form-control" name="Nombre_persona[]" value="<?php echo htmlspecialchars($integ['Nombre'] ?? $integ['nombre'] ?? ''); ?>" required maxlength="100">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">ID_departamento*</label>
                                    <select class="form-select" name="ID_departamento[]" required>
                                        <option value="0" <?php echo ((int)($integ['ID_departamento'] ?? 0) === 0) ? 'selected' : ''; ?>>Seleccione un departamento</option>
                                        
                                        <?php foreach ($departamentos as $depto): ?>
                                            <option value="<?php echo $depto['ID_departamento']; ?>" <?php echo ($depto['ID_departamento'] == ($integ['ID_departamento'] ?? '')) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($depto['Nombre_departamento'] ?? $depto['nombre_departamento']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">ID_Sede*</label>
                                    <select class="form-select" name="ID_sede_persona[]" required>
                                        <option value="0" <?php echo ((int)($integ['ID_sede_persona'] ?? 0) === 0) ? 'selected' : ''; ?>>Seleccione una sede</option>
                                        <?php foreach ($sedes as $sede): ?>
                                            <option value="<?php echo $sede['ID_sede'] ?? $sede['ID_Sede']; ?>" <?php echo (($sede['ID_sede'] ?? $sede['ID_Sede']) == ($integ['ID_Sede'] ?? $integ['id_sede'] ?? '')) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($sede['Nombre_Sede'] ?? $sede['nombre_sede']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">eMail (255)*</label>
                                    <input type="email" class="form-control" name="eMail[]" value="<?php echo htmlspecialchars($integ['eMail'] ?? $integ['Email'] ?? ''); ?>" required maxlength="255">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Telefono (12)</label>
                                    <input type="text" class="form-control" name="Telefono[]" value="<?php echo htmlspecialchars($integ['telefono'] ?? $integ['Telefono'] ?? ''); ?>" maxlength="12">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Cargo*</label>
                                    <select class="form-select" name="ID_cargo[]" required>
                                        <?php $id_cargo = $integ['ID_cargo'] ?? $integ['id_cargo'] ?? 1; ?>
                                        <option value="0" <?php echo ((int)($integ['ID_cargo'] ?? 0) === 0) ? 'selected' : ''; ?>>Seleccione un cargo</option>
                                        <option value="1" <?php echo ($id_cargo == 1) ? 'selected' : ''; ?>>Estudiante</option>
                                        <option value="2" <?php echo ($id_cargo == 2) ? 'selected' : ''; ?>>Profesor</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Rol en el proyecto (60)*</label>
                                    <?php if ($es_responsable): ?>
                                        <input type="text" class="form-control bg-white" value="<?php echo htmlspecialchars($rol_actual); ?>" readonly required>
                                        <input type="hidden" name="Rol[]" value="<?php echo htmlspecialchars($rol_actual); ?>">
                                    <?php else: ?>
                                        <input type="text" class="form-control" name="Rol[]" value="<?php echo htmlspecialchars($rol_actual); ?>" required maxlength="60">
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div> 

                    <button type="button" id="btn_agregar_integrante" class="btn btn-outline-success w-100 fw-bold shadow-sm">
                        + Agregar Integrante
                    </button>
                </div>
            </div>

           <div class="card shadow-sm border-0 mb-5">
    <div class="card-body p-4">
        <h5 class="text-primary border-bottom pb-2 mb-4">Cronograma de Trabajo</h5>

        <?php 
        if (empty($etapas)) {
            $etapas = [ [] ]; 
        }
        ?>

        <div id="contenedor_cronograma">
            <?php foreach($etapas as $e): ?>
                <div class="etapa-item border border-info rounded p-3 mb-4 bg-light position-relative">
                    
                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 btn-eliminar-etapa">X</button>

                    <h6 class="fw-bold text-info mb-3">Etapa</h6>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Etapa (100)*</label>
                        <input type="text" class="form-control" name="Etapa[]" value="<?php echo htmlspecialchars($e['Etapa'] ?? $e['etapa'] ?? ''); ?>" required maxlength="100">
                    </div>
                    
                    <div class="mb-3">
    <label class="form-label fw-bold">Plazos_semanas (Entero)*</label>
    <input type="text" inputmode="numeric" pattern="[0-9]+" class="form-control" name="Plazos_semanas[]" value="<?php echo htmlspecialchars($e['Plazos_semanas'] ?? $e['Plazos_Semanas'] ?? $e['plazos_semanas'] ?? ''); ?>" required>
</div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Entregable (100)*</label>
                        <input type="text" class="form-control" name="Entregable[]" value="<?php echo htmlspecialchars($e['Entregable'] ?? $e['entregable'] ?? ''); ?>" required maxlength="100">
                    </div>
                </div>
            <?php endforeach; ?>
        </div> 

        <button type="button" id="btn_agregar_etapa" class="btn btn-outline-info w-100 fw-bold shadow-sm">
            + Agregar Etapa
        </button>
    </div>
</div>

           <div class="card shadow-sm border-0 mb-5 bg-transparent">
                <div class="card-body p-0 d-flex justify-content-end gap-3">
                    
                    <a href="T_rol1.php" class="btn btn-outline-secondary px-4 py-2 fw-bold shadow-sm">
                        Cancelar
                    </a>

                    <button type="submit" name="accion" value="eliminar" class="btn btn-danger me-auto" 
                            onclick="return confirm('¿Seguro que quieres borrar la postulación?');"
                            formnovalidate> Eliminar Postulacion
                    </button>
                    
                    <button type="submit" name="accion" value="borrador" class="btn btn-secondary px-4 py-2 fw-bold shadow-sm" formnovalidate>
                        Guardar como Borrador
                    </button>
                    
                    <button type="submit" name="accion" value="enviar" class="btn btn-primary px-4 py-2 fw-bold shadow-sm">
                        Enviar Postulación Definitiva
                    </button>
                    
                </div>
            </div>

        </form>
    </div>

    <template id="template_integrante">
        <div class="integrante-item border rounded p-3 mb-4 position-relative bg-white">
            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 btn-eliminar">X</button>
            <h6 class="fw-bold mb-3 titulo-integrante">Integrante Nuevo</h6>
            
            <div class="mb-3">
                <label class="form-label fw-bold">Rut_Persona (12)*</label>
                <input type="text" class="form-control" name="Rut_Persona[]" required maxlength="12">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Nombre (100)*</label>
                <input type="text" class="form-control" name="Nombre_persona[]" required maxlength="100">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">ID_departamento*</label>
                <select class="form-select" name="ID_departamento[]" required>
                    <option value="" selected disabled>Seleccione un departamento</option>
                    <?php foreach ($departamentos as $depto): ?>
                        <option value="<?php echo $depto['ID_departamento']; ?>"><?php echo htmlspecialchars($depto['Nombre_departamento']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">ID_Sede*</label>
                <select class="form-select" name="ID_sede_persona[]" required>
                    <option value="" selected disabled>Seleccione una sede</option>
                    <?php foreach ($sedes as $sede): ?>
                        <option value="<?php echo $sede['ID_sede'] ?? $sede['ID_Sede']; ?>"><?php echo htmlspecialchars($sede['Nombre_Sede']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">eMail (255)*</label>
                <input type="email" class="form-control" name="eMail[]" required maxlength="255">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Telefono (12)</label>
                <input type="text" class="form-control" name="Telefono[]" maxlength="12">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Cargo*</label>
                <select class="form-select" name="ID_cargo[]" required>
                    <option value="" selected disabled>Seleccione un cargo</option>
                    <option value="1">Estudiante</option>
                    <option value="2">Profesor</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Rol en el proyecto (60)*</label>
                <input type="text" class="form-control" name="Rol[]" required maxlength="60">
            </div>
        </div>
    </template>

    <template id="template_etapa">
        <div class="etapa-item border rounded p-3 mb-4 position-relative bg-white">
            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 btn-eliminar-etapa">X</button>
            <h6 class="fw-bold mb-3 titulo-etapa">Nueva Etapa</h6>
            <div class="mb-3">
                <label class="form-label fw-bold">Etapa (100)*</label>
                <input type="text" class="form-control" name="Etapa[]" required maxlength="100">
            </div>
            <div class="mb-3">
    <label class="form-label fw-bold">Plazos_semanas (Entero)*</label>
    <input type="text" inputmode="numeric" pattern="[0-9]+" class="form-control" name="Plazos_semanas[]" value="<?php echo htmlspecialchars($e['Plazos_semanas'] ?? $e['Plazos_Semanas'] ?? $e['plazos_semanas'] ?? ''); ?>" required>
</div>
            <div class="mb-3">
                <label class="form-label fw-bold">Entregable (100)*</label>
                <input type="text" class="form-control" name="Entregable[]" required maxlength="100">
            </div>
        </div>
    </template>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="formulario_PC.js"></script>


</body>
</html>