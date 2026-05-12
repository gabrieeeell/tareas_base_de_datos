<?php
try {
    // Definimos la consulta con todos los JOINs que proporcionaste
    // Usamos una variable para que el código sea más legible
    $sql = "SELECT 
                P.Numero_postulacion, 
                P.Nombre_iniciativa,
                E.Nombre_empresa,
                R_O.Nombre_region AS Region_origen,
                R_I.Nombre_region AS Region_impacto,
                P.Presupuesto,
                EST.Nombre_estado
            FROM POSTULACION P
            INNER JOIN TIPO_INICIATIVA T ON P.ID_tipo_iniciativa = T.ID_tipo
            INNER JOIN SEDE S ON P.ID_sede = S.ID_sede
            INNER JOIN REGION R_I ON P.ID_region_impacto = R_I.ID_region 
            INNER JOIN REGION R_O ON P.ID_region_origen = R_O.ID_region
            INNER JOIN EMPRESA E ON P.Rut_Empresa = E.Rut_Empresa
            INNER JOIN ESTADO_POSTULACION EST ON P.ID_postulacion = EST.ID_estado";

    $stmt = $conexion->query($sql);
    $postulaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error en la consulta: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Evaluador - Postulaciones</title>
    <style>
        body { font-family: sans-serif; background-color: #f4f4f9; padding: 20px; }
        .container { max-width: 800px; margin: auto; }
        .postulacion-card {
            border: 1px solid #ddd;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 12px;
            background-color: #fff;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .postulacion-card h3 { margin-top: 0; color: #004b8d; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; font-size: 0.9em; }
        .label { font-weight: bold; color: #555; }
        .badge { 
            display: inline-block; 
            padding: 4px 8px; 
            background: #e1f5fe; 
            color: #01579b; 
            border-radius: 4px; 
            font-weight: bold; 
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Listado Detallado de Postulaciones</h2>

        <?php if (count($postulaciones) > 0): ?>
            <?php foreach ($postulaciones as $fila): ?>
                <div class="postulacion-card">
                    <h3><?php echo htmlspecialchars($fila['Nombre_iniciativa']); ?></h3>
                    
                    <div class="info-grid">
                        <div><span class="label">Nº Postulación:</span> <?php echo htmlspecialchars($fila['Numero_postulacion']); ?></div>
                        <div><span class="label">Empresa:</span> <?php echo htmlspecialchars($fila['Nombre_empresa']); ?></div>
                        <div><span class="label">Región Origen:</span> <?php echo htmlspecialchars($fila['Region_origen']); ?></div>
                        <div><span class="label">Región Impacto:</span> <?php echo htmlspecialchars($fila['Region_impacto']); ?></div>
                        <div><span class="label">Presupuesto:</span> $<?php echo number_format($fila['Presupuesto'], 0, ',', '.'); ?></div>
                        <div><span class="label">Estado:</span> <span class="badge"><?php echo htmlspecialchars($fila['Nombre_estado']); ?></span></div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No se encontraron postulaciones con los criterios de búsqueda.</p>
        <?php endif; ?>
    </div>

</body>
</html>
