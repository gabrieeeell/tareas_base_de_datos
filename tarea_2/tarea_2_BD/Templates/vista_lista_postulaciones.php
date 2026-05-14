<?php
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
echo $f_reg_impacto;
try {
    $sql = "SELECT 
                P.Numero_postulacion, 
                P.Nombre_iniciativa,
                E.Nombre_empresa,
                R_O.Nombre_region AS Region_origen,
                R_I.Nombre_region AS Region_impacto,
                P.Presupuesto,
                EST.Nombre_estado
            FROM POSTULACION P
            LEFT JOIN TIPO_INICIATIVA T ON P.ID_tipo_iniciativa = T.ID_tipo
            LEFT JOIN SEDE S ON P.ID_sede = S.ID_sede
            LEFT JOIN REGION R_I ON P.ID_region_impacto = R_I.ID_region 
            LEFT JOIN REGION R_O ON P.ID_region_origen = R_O.ID_region
            LEFT JOIN EMPRESA E ON P.Rut_Empresa = E.Rut_Empresa
            LEFT JOIN TAMANO_EMPRESA T_E ON T_E.ID_tamano = E.ID_tamano
            LEFT JOIN ESTADO_POSTULACION EST ON P.ID_estado = EST.ID_estado";

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
<body class="bg-light py-4">

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
                <div class="card shadow-sm border-0 rounded-4 mb-3">
                    <div class="card-body p-4">
                        <h4 class="card-title text-primary mb-3 fw-semibold">
                            <?php echo htmlspecialchars($fila['Nombre_iniciativa']); ?>
                        </h4>
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
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-warning shadow-sm border-0 rounded-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                No se encontraron resultados para "<strong><?php echo htmlspecialchars($busqueda); ?></strong>".
            </div>
        <?php endif; ?>
    </div> 
    </div>
    <!-- 1. En el <head>, junto al otro CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- 2. Al final del <body>, justo antes de cerrar </body> -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Resultados (Mantener tu estructura de cards foreach) -->
    <!-- ... -->
