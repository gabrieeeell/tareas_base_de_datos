<?php
//para poder acceder al rol del usuario
session_start();
// Iniciamos sesión para no perder el rol del usuario
require_once("../BDT1.php");
// Capturamos el término de búsqueda (soporta POST para el buscador y GET para limpiar)
$busqueda = $_POST['buscar'] ?? ($_GET['buscar'] ?? '');
$f_reg_origen = $_POST['reg_origen'] ?? '';
$f_reg_impacto = $_POST['reg_impacto'] ?? '';
$f_sede = $_POST['sede'] ?? '';
$f_tipo = $_POST['tipo_iniciativa'] ?? '';
$f_tamano = $_POST['tamano_empresa'] ?? '';
$f_convenio = $_POST['convenio_usm'] ?? '';
$f_estado = $_POST['estado_postulacion'] ?? '';
$f_id_coordinador = $_POST['id_coordinador'] ?? '';

// Obtener la lista de coordinadores para el select
$stmt_c = $conexion->query("SELECT Nombre_coordinador, ID_coordinador FROM COORDINADOR");
$coordinadores_lista = $stmt_c->fetchAll(PDO::FETCH_ASSOC);

// Procesar ELIMINAR Coordinador
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_eliminar_coordinador'])) {
    $id_borrar = $_POST['id_coordinador_eliminar'];
    try {
        $conexion->beginTransaction();
        // 1. Setear a 0 en postulaciones (para no perder la integridad si no hay ON DELETE SET NULL)
        $stmt1 = $conexion->prepare("UPDATE POSTULACION SET ID_coordinador = 0 WHERE ID_coordinador = :id");
        $stmt1->execute([':id' => $id_borrar]);
        
        // 2. Eliminar de la tabla coordinador
        $stmt2 = $conexion->prepare("DELETE FROM COORDINADOR WHERE ID_coordinador = :id");
        $stmt2->execute([':id' => $id_borrar]);
        
        $conexion->commit();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } catch (Exception $e) {
        $conexion->rollBack();
        echo "<div class='alert alert-danger'>Error al eliminar: " . $e->getMessage() . "</div>";
    }
}

// Procesar AGREGAR Coordinador
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_agregar_coordinador'])) {
    $nom = $_POST['nuevo_nombre_coordinador'];
    $rut = $_POST['nuevo_rut_coordinador'];
    try {
        $ins = $conexion->prepare("INSERT INTO COORDINADOR (Nombre_coordinador, rut_coordinador) VALUES (:nom, :rut)");
        $ins->execute([':nom' => $nom, ':rut' => $rut]);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Error al agregar: " . $e->getMessage() . "</div>";
    }
}

// Procesar actualización de Coordinador
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_actualizar_coordinador'])) {
    $id_postulacion = $_POST['post_id_nuevo_coordinador'];
    $id_coord_nuevo = $_POST['nuevo_coordinador_id'];

    try {
        $upd_coord = $conexion->prepare("UPDATE POSTULACION SET ID_coordinador = :coord WHERE Numero_postulacion = :id");
        $upd_coord->execute([
            ':coord' => $id_coord_nuevo,
            ':id'    => $id_postulacion
        ]);
        
        // Refrescar para ver cambios
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Error al reasignar: " . $e->getMessage() . "</div>";
    }
}

try {
    $sql = "SELECT 
                P.Numero_postulacion,
                P.Fecha_postulacion,
                P.Objetivo_iniciativa,
                P.Descripcion_soluciones,
                P.Resultados_esperados,
                P.Nombre_iniciativa,
                P.Rut_Empresa,
                E.Nombre_empresa,
                P.ID_coordinador,
                R_O.Nombre_region AS Region_origen,
                R_I.Nombre_region AS Region_impacto,
                P.Presupuesto,
                EST.Nombre_estado,
                C.rut_coordinador,
                S.Nombre_Sede,
                T.Tipo_iniciativa,
                J.Nombre_jefe,
                C.Nombre_coordinador
            FROM POSTULACION P
            LEFT JOIN TIPO_INICIATIVA T ON P.ID_tipo_iniciativa = T.ID_tipo
            LEFT JOIN SEDE S ON P.ID_sede = S.ID_sede
            LEFT JOIN REGION R_I ON P.ID_region_impacto = R_I.ID_region 
            LEFT JOIN REGION R_O ON P.ID_region_origen = R_O.ID_region
            LEFT JOIN EMPRESA E ON P.Rut_Empresa = E.Rut_Empresa
            LEFT JOIN TAMANO_EMPRESA T_E ON T_E.ID_tamano = E.ID_tamano
            LEFT JOIN ESTADO_POSTULACION EST ON P.ID_estado = EST.ID_estado
            LEFT JOIN COORDINADOR C ON P.ID_coordinador = C.ID_coordinador
            LEFT JOIN JEFE_CARRERA J ON J.ID_jefe = P.ID_jefe";

    $condiciones = [];
    $params = [];
    // Búsqueda general (corregido con marcadores únicos)
    if ($busqueda !== '') {
        $condiciones[] = "(P.Nombre_iniciativa LIKE :b1 OR E.Nombre_empresa LIKE :b2 OR P.Numero_postulacion LIKE :b3)";
        $term = "%$busqueda%";
        $params[':b1'] = $term;
        $params[':b2'] = $term;
        $params[':b3'] = $term;
    }

    // Filtros avanzados - Ajustados según tu descripción
    if ($f_reg_origen !== '') {
        $condiciones[] = "R_O.Nombre_region = :reg_o";
        $params[':reg_o'] = $f_reg_origen;
    }
    if ($f_reg_impacto !== '') {
        $condiciones[] = "R_I.Nombre_region = :reg_i";
        $params[':reg_i'] = $f_reg_impacto;
    }
    if ($f_sede !== '') {
        $condiciones[] = "S.Nombre_sede = :sede"; // Antes tenías S.Nombre_Sede
        $params[':sede'] = $f_sede;
    }
    if ($f_tipo !== '') {
        $condiciones[] = "T.Tipo_iniciativa = :tipo"; // Corregido de Nombre_tipo a Tipo_iniciativa
        $params[':tipo'] = $f_tipo;
    }
    if ($f_tamano !== '') {
        $condiciones[] = "T_E.Nombre_tamano = :tam";
        $params[':tam'] = $f_tamano;
    }
    if ($f_convenio !== '') {
        $condiciones[] = "E.Convenio_USM = :conv";
        $params[':conv'] = ($f_convenio === 'Sí') ? 1 : 0;
    }
    if ($f_estado !== '') {
        $condiciones[] = "EST.Nombre_estado = :est";
        $params[':est'] = $f_estado;
    }

    if ($f_id_coordinador !== '') {
        $condiciones[] = "P.ID_coordinador = :cor";
        $params[':cor'] = $f_id_coordinador;
    }

    if (!empty($condiciones)) {
        $sql .= " WHERE " . implode(" AND ", $condiciones);
    }


    $stmt = $conexion->prepare($sql);
    $stmt->execute($params);
    $postulaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Si hay un error de SQL, lo veremos aquí
    die("Error en la consulta: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Evaluador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light py-4 d-flex">

<div class="container" style="max-width: 800px;">
    
    <!-- Título y Barra Principal -->
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
        <h2 class="text-dark fw-bold mb-3 text-center">Postulaciones</h2>
        
        <form action="" method="POST">
            <div class="input-group mb-2">
                <input type="text" name="buscar" class="form-control" placeholder="Búsqueda rápida..." value="<?php echo htmlspecialchars($busqueda); ?>">
                <button class="btn btn-primary px-4 fw-bold" type="submit">Buscar</button>
            </div>
            
            <!-- Botón que abre el Modal -->
            <div class="d-flex justify-content-between align-items-center">
                <button type="button" class="btn btn-link text-decoration-none p-0" data-bs-toggle="modal" data-bs-target="#modalBusqueda">
                    <i class="bi bi-sliders"></i> Búsqueda Avanzada
                </button>
                <?php if ($busqueda || $f_reg_origen || $f_reg_impacto || $f_sede || $f_tipo || $f_tamano || $f_convenio || $f_estado): ?>
                    <a href="?" class="small text-muted">Limpiar filtros</a>
                <?php endif; ?>
            </div>
            <!-- Boton para crear postulacion y "Mis postulaciones si corresponde"-->
                <div class="w-100 d-flex flex-row" style="height: 5rem;"> 
                    <!-- Asumimos que el evaluador no podia crear postulaciones -->
                    <!--
                    <a href="T_rol1_formulario_crear.php" class="mx-2 my-3 btn btn-success px-4 py-2 rounded-4 fw-bold shadow-sm d-flex align-items-center">
                        <i class="bi bi-plus-lg me-2"></i> Crear 
                    </a>
                    -->
                    <?php if ($_SESSION['rol_usuario'] == '3'): ?>
                    <button type="button" class="mx-2 my-3 btn btn-info px-4 py-2 rounded-4 fw-bold shadow-sm d-flex align-items-center text-white" data-bs-toggle="modal" data-bs-target="#modalGestionCoordinadores">
                        <i class="bi bi-people-fill me-2"></i> Coordinadores
                    </button>
                    <?php endif; ?>
                    <?php if ($_SESSION['rol_usuario'] == '1'): ?>
                    <a href="T_rol1.php" class="mx-2 my-3 btn btn-outline-primary px-4 py-2 rounded-4 fw-bold shadow-sm d-flex align-items-center">
                        <i class="bi bi-person-lines-fill me-2"></i> Mis Postulaciones
                    </a>
                    <?php endif; ?>
                </div>
            <!-- MODAL DE BÚSQUEDA AVANZADA -->
            <div class="modal fade" id="modalBusqueda" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content rounded-4 border-0 shadow">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-bold">Filtros Avanzados</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <!-- Región Origen -->
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Región Ejecución</label>
                                    <select name="reg_origen" class="form-select">
                                        <option value="">Todas</option>
                                        <?php foreach($regiones as $r): ?>
                                            <option value="<?php echo $r; ?>" <?php echo ($f_reg_origen == $r) ? 'selected' : ''; ?>><?php echo $r; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <!-- Región Impacto -->
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Región Impacto</label>
                                    <select name="reg_impacto" class="form-select">
                                        <option value="">Todas</option>
                                        <?php foreach($regiones as $r): ?>
                                            <option value="<?php echo $r; ?>" <?php echo ($f_reg_impacto == $r) ? 'selected' : ''; ?>><?php echo $r; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <!-- Sede -->
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Sede / Campus</label>
                                    <select name="sede" class="form-select">
                                        <option value="">Todas</option>
                                        <?php 
                                        $sedes = ['Campus Casa Central Valparaíso', 'Campus San Joaquín', 'Campus Vitacura', 'Sede Viña del Mar', 'Sede Concepción'];
                                        foreach($sedes as $s): ?>
                                            <option value="<?php echo $s; ?>" <?php echo ($f_sede == $s) ? 'selected' : ''; ?>><?php echo $s; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <!-- Tipo -->
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Tipo de Iniciativa</label>
                                    <select name="tipo_iniciativa" class="form-select">
                                        <option value="">Todos</option>
                                        <option value="Nueva" <?php echo ($f_tipo == 'Nueva') ? 'selected' : ''; ?>>Nueva</option>
                                        <option value="Existente" <?php echo ($f_tipo == 'Existente') ? 'selected' : ''; ?>>Existente</option>
                                    </select>
                                </div>
                                <!-- Tamaño Empresa -->
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Tamaño Empresa</label>
                                    <select name="tamano_empresa" class="form-select">
                                        <option value="">Todos</option>
                                        <option value="Microempresa" <?php echo ($f_tamano == 'Microempresa') ? 'selected' : ''; ?>>Microempresa</option>
                                        <option value="Mediana" <?php echo ($f_tamano == 'Mediana') ? 'selected' : ''; ?>>Mediana</option>
                                        <option value="Grande" <?php echo ($f_tamano == 'Grande') ? 'selected' : ''; ?>>Grande</option>
                                    </select>
                                </div>
                                <!-- Convenio -->
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Convenio Marco</label>
                                    <select name="convenio_usm" class="form-select">
                                        <option value="">Ambos</option>
                                        <option value="Sí" <?php echo ($f_convenio == 'Sí') ? 'selected' : ''; ?>>Sí</option>
                                        <option value="No" <?php echo ($f_convenio == 'No') ? 'selected' : ''; ?>>No</option>
                                    </select>
                                </div>
                                <!-- Estado -->
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Estado Postulación</label>
                                    <select name="estado_postulacion" class="form-select">
                                        <option value="">Todos</option>
                                        <option value="En Revisión" <?php echo ($f_estado == 'En Revisión') ? 'selected' : ''; ?>>En Revisión</option>
                                        <option value="Aprobada" <?php echo ($f_estado == 'Aprobada') ? 'selected' : ''; ?>>Aprobada</option>
                                        <option value="Rechazada" <?php echo ($f_estado == 'Rechazada') ? 'selected' : ''; ?>>Rechazada</option>
                                        <option value="Cerrada" <?php echo ($f_estado == 'Cerrada') ? 'selected' : ''; ?>>Cerrada</option>
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label small fw-bold">Coordinador Asignado</label>
                                    <select name="id_coordinador" class="form-select">
                                        <option value="">Todos los coordinadores</option>
                                        <?php foreach($coordinadores_lista as $c): ?>
                                            <option value="<?php echo $c['ID_coordinador']; ?>" <?php echo ($f_id_coordinador == $c['ID_coordinador']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($c['Nombre_coordinador']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary px-4 fw-bold">Aplicar Filtros</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    <!-- Listado de Resultados -->
        <?php if (count($postulaciones) > 0): ?>
            <?php foreach ($postulaciones as $fila): ?>
                <?php if ($fila["rut_coordinador"] === $_SESSION["rut_usuario"] && $_SESSION["rol_usuario"] === "2" || $_SESSION["rol_usuario"] !== "2"): ?> 
                    
                <?php if ($_SESSION["rol_usuario"] === "2"): ?> 
                <!-- Modal para la Postulación Nº <?php echo $fila['Numero_postulacion']; ?> -->
                <div class="modal fade" id="modalEditar<?php echo $fila['Numero_postulacion']; ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content rounded-4 border-0 shadow-lg">
                            
                            <!-- Encabezado -->
                            <div class="modal-header bg-light border-0 py-3 px-4">
                                <h5 class="modal-title fw-bold text-primary">
                                    <i class="bi bi-file-earmark-text me-2"></i> Detalle de Postulación
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body p-4">
                                <!-- Información General (Fila 1) -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-4">
                                        <label class="small text-muted fw-bold d-block">Número Postulación</label>
                                        <span class="text-dark">#<?php echo htmlspecialchars($fila['Numero_postulacion']); ?></span>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="small text-muted fw-bold d-block">Fecha Postulación</label>
                                        <span class="text-dark"><?php echo htmlspecialchars($fila['Fecha_postulacion'] ?? 'No registrada'); ?></span>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="small text-muted fw-bold d-block">Estado</label>
                                        <span class="badge bg-info text-dark rounded-pill">
                                            <?php echo htmlspecialchars($fila['Nombre_estado']); ?>
                                        </span>
                                    </div>
                                </div>

                                <!-- Título e Iniciativa -->
                                <div class="mb-4">
                                    <label class="small text-muted fw-bold d-block">Nombre de la Iniciativa</label>
                                    <h4 class="text-primary fw-semibold"><?php echo htmlspecialchars($fila['Nombre_iniciativa']); ?></h4>
                                </div>

                                <!-- Textos Largos (Estilo Tarjeta Interna) -->
                                <div class="bg-light rounded-3 p-3 mb-4">
                                    <div class="mb-3">
                                        <label class="small text-muted fw-bold d-block">Objetivo de la Iniciativa</label>
                                        <p class="mb-0 text-dark"><?php echo nl2br(htmlspecialchars($fila['Objetivo_iniciativa'] ?? 'Sin objetivo definido')); ?></p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="small text-muted fw-bold d-block">Descripción de Soluciones</label>
                                        <p class="mb-0 text-dark small"><?php echo nl2br(htmlspecialchars($fila['Descripcion_soluciones'] ?? 'Sin descripción')); ?></p>
                                    </div>
                                    <div>
                                        <label class="small text-muted fw-bold d-block">Resultados Esperados</label>
                                        <p class="mb-0 text-dark small italic">"<?php echo htmlspecialchars($fila['Resultados_esperados'] ?? 'No especificados'); ?>"</p>
                                    </div>
                                </div>

                                <!-- Detalles Técnicos (Grilla) -->
                                <div class="row g-3">
                                    <div class="col-md-6 border-end">
                                        <p class="mb-1 small"><span class="fw-bold text-muted">Rut Empresa:</span> <?php echo htmlspecialchars($fila['Rut_Empresa']); ?></p>
                                        <p class="mb-1 small"><span class="fw-bold text-muted">Sede:</span> <?php echo htmlspecialchars($fila['Nombre_Sede']); ?></p>
                                        <p class="mb-1 small"><span class="fw-bold text-muted">Tipo:</span> <?php echo htmlspecialchars($fila['Tipo_iniciativa']); ?></p>
                                        <p class="mb-1 small"><span class="fw-bold text-muted">Presupuesto:</span> <span class="text-success fw-bold">$<?php echo number_format($fila['Presupuesto'], 0, ',', '.'); ?></span></p>
                                    </div>
                                    <div class="col-md-6 ps-md-4">
                                        <p class="mb-1 small"><span class="fw-bold text-muted">Región Origen:</span> <?php echo htmlspecialchars($fila['Region_origen']); ?></p>
                                        <p class="mb-1 small"><span class="fw-bold text-muted">Región Impacto:</span> <?php echo htmlspecialchars($fila['Region_impacto']); ?></p>
                                        <hr class="my-2">
                                        <p class="mb-1 small"><span class="fw-bold text-muted">Jefe Carrera:</span> <?php echo htmlspecialchars($fila['Nombre_jefe'] ?? 'No asignado'); ?></p>
                                        <p class="mb-1 small"><span class="fw-bold text-muted">Coordinador:</span> <?php echo htmlspecialchars($fila['Nombre_coordinador'] ?? 'No asignado'); ?></p>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer border-0 bg-light py-3">
                                <button type="button" class="btn btn-secondary rounded-3 px-4" data-bs-dismiss="modal">Cerrar</button>
                                <button type="button" class="btn btn-primary rounded-3 px-4 fw-bold">Guardar Cambios</button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                    <!-- tengo que incluir el boton de editar si es que es rol 2 --> 
                    <div class="card shadow-sm border-0 rounded-4 mb-3">
                        <div class="card-body p-4">
                            <div class="w-100 d-flex flex-row" style="height: 4rem;">
                                <h4 class="w-75 card-title text-primary my-1 fw-semibold">
                                    <?php echo htmlspecialchars($fila['Nombre_iniciativa']); ?>
                                </h4>
                                <?php if ($_SESSION["rol_usuario"] === "2"): ?>
                                <a class="btn btn-warning h-75 px-3 my-1 rounded-4 fw-bold shadow-sm d-inline-flex align-items-center text-dark bg-light border-secondary" data-bs-toggle="modal" 
        data-bs-target="#modalEditar<?php echo $fila['Numero_postulacion']; ?>">
                                    <i class="bi bi-pencil-square me-2"></i> Editar
                                </a>
                                <?php endif; ?>
                            </div>
                            <div class="row row-cols-1 row-cols-md-2 g-3 small">
                                <div class="col">
                                    <span class="text-muted fw-bold">Nº Postulación:</span> 
                                    <span class="text-dark"><?php echo htmlspecialchars($fila['Numero_postulacion']); ?></span>
                                </div>
                                <div class="col">
                                    <span class="text-muted fw-bold">Empresa:</span> 
                                    <span class="text-dark"><?php echo htmlspecialchars($fila['Nombre_empresa']); ?></span>
                                </div>
                                <div class="col">
                                    <span class="text-muted fw-bold">Región Origen:</span> 
                                    <span class="text-dark"><?php echo htmlspecialchars($fila['Region_origen']); ?></span>
                                </div>
                                <div class="col">
                                    <span class="text-muted fw-bold">Región Impacto:</span> 
                                    <span class="text-dark"><?php echo htmlspecialchars($fila['Region_impacto']); ?></span>
                                </div>
                                <div class="col">
                                    <span class="text-muted fw-bold">Presupuesto:</span> 
                                    <span class="text-success fw-bold">
                                        $<?php echo number_format($fila['Presupuesto'], 0, ',', '.'); ?>
                                    </span>
                                </div>
                                <div class="col d-flex align-items-center">
                                    <span class="text-muted fw-bold me-2">Estado:</span> 
                                    <span class="badge rounded-pill bg-info text-dark">
                                        <?php echo htmlspecialchars($fila['Nombre_estado']); ?>
                                    </span>
                                </div>
                                <div class="col">
                                    <span class="text-muted fw-bold">Sede:</span> 
                                    <span class="text-dark"><?php echo htmlspecialchars($fila['Nombre_Sede']); ?></span>
                                </div>
                            </div>
                            <?php if ($_SESSION["rol_usuario"] == "3"): ?>
                            <div class="mt-3 pt-3 border-top">
                                <form action="" method="POST" class="row g-2 align-items-center">
                                    <input type="hidden" name="post_id_nuevo_coordinador" value="<?php echo $fila['Numero_postulacion']; ?>">
                                    
                                    <div class="col-auto">
                                        <label class="small fw-bold text-muted"><i class="bi bi-person-badge me-1"></i> Coordinador:</label>
                                    </div>
                                    
                                    <div class="col">
                                        <select name="nuevo_coordinador_id" class="form-select form-select-sm rounded-3 shadow-sm">
                                            <?php foreach($coordinadores_lista as $c): ?>
                                                <option value="<?php echo $c['ID_coordinador']; ?>" 
                                                    <?php echo (isset($fila['ID_coordinador']) && $fila['ID_coordinador'] == $c['ID_coordinador']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($c['Nombre_coordinador']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="col-auto">
                                        <button type="submit" name="btn_actualizar_coordinador" class="btn btn-sm btn-primary rounded-4 px-3 fw-bold shadow-sm">
                                            Guardar
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else: ?>
        <div class="alert alert-warning shadow-sm border-0 rounded-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            No se encontraron resultados para "<strong><?php echo htmlspecialchars($busqueda); ?></strong>".
        </div>
        <?php endif; ?>
    </div> 
    </div>
    <!-- Modal gestoin coordinadores -->
    <div class="modal fade" id="modalGestionCoordinadores" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold"><i class="bi bi-person-gear me-2"></i> Gestión de Coordinadores</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase">Eliminar Coordinador</label>
                        <form action="" method="POST" class="row g-2 shadow-sm p-3 rounded-3 bg-light border">
                            <div class="col">
                                <select name="id_coordinador_eliminar" class="form-select border-0 shadow-sm" required>
                                    <option value="" disabled selected>Seleccione para eliminar...</option>
                                    <!-- el slice es para no incluir el "sin coordinador" en la lista-->
                                    <?php foreach(array_slice($coordinadores_lista, 1)as $c): ?>
                                        <option value="<?php echo $c['ID_coordinador']; ?>">
                                            <?php echo htmlspecialchars($c['Nombre_coordinador']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-auto">
                                <button type="submit" name="btn_eliminar_coordinador" class="btn btn-danger rounded-3" onclick="return confirm('¿Está seguro? Las postulaciones asociadas quedarán sin coordinador asignado.')">
                                    <i class="bi bi-trash"></i> Eliminar
                                </button>
                            </div>
                        </form>
                    </div>

                    <hr class="my-4">

                    <div>
                        <label class="form-label small fw-bold text-muted text-uppercase">Agregar Nuevo Coordinador</label>
                        <form action="" method="POST" class="p-3 rounded-3 bg-light border">
                            <div class="mb-3">
                                <label class="small fw-bold">Nombre Completo</label>
                                <input type="text" name="nuevo_nombre_coordinador" class="form-control border-0 shadow-sm" placeholder="Ej: Juan Pérez" required>
                            </div>
                            <div class="mb-3">
                                <label class="small fw-bold">RUT (Con puntos y guion)</label>
                                <input type="text" name="nuevo_rut_coordinador" class="form-control border-0 shadow-sm" placeholder="12.345.678-9" required>
                            </div>
                            <button type="submit" name="btn_agregar_coordinador" class="btn btn-primary w-100 rounded-4 fw-bold shadow-sm py-2">
                                <i class="bi bi-person-plus-fill me-2"></i> Agregar Coordinador
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- 1. En el <head>, junto al otro CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- 2. Al final del <body>, justo antes de cerrar </body> -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Resultados (Mantener tu estructura de cards foreach) -->
    <!-- ... -->
