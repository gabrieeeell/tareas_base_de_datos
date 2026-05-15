<?php
session_start();
require_once("../BDT1.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conexion->beginTransaction();

        // =========================================================
        // 1. GENERAR LA ID PERSONALIZADA (Ej: MartinGabriel-1)
        // =========================================================
        $sql_ultima = "SELECT MAX(CAST(SUBSTRING_INDEX(ID_postulacion, '-', -1) AS UNSIGNED)) AS ultimo_num 
                       FROM POSTULACION 
                       WHERE ID_postulacion LIKE 'MartinGabriel-%'";

        $query_id = $conexion->query($sql_ultima);
        $ultima_fila = $query_id->fetch(PDO::FETCH_ASSOC);

        if ($ultima_fila && $ultima_fila['ultimo_num'] !== null) {
            $nuevo_numero = $ultima_fila['ultimo_num'] + 1;
        } else {
            $nuevo_numero = 1;
        }

        // Nuestra variable PHP oficial para usar en todo este archivo
        $id_postulacion = "MartinGabriel-" . $nuevo_numero;


        // =========================================================
        // 2. RECIBIR DATOS GENERALES DEL FORMULARIO
        // =========================================================
        // =========================================================
        // 2. RECIBIR DATOS GENERALES DEL FORMULARIO
        // =========================================================
        // Datos de la Iniciativa
        $nombre_in = $_POST['Nombre_iniciativa'] ?? null;
        $objetivo = $_POST['Objetivo_iniciativa'] ?? null;
        $descripcion_soluciones = $_POST['Descripcion_soluciones'] ?? null; 
        $resultados = $_POST['Resultados_esperados'] ?? null;
        $presupuesto = $_POST['Presupuesto'] ?? null;
        
        // Antecedentes
        $tipo_iniciativa = $_POST['ID_tipo_iniciativa'] ?? null;
        $id_sede = $_POST['ID_sede'] ?? null;
        $id_jefe = $_POST['ID_Jefe'] ?? null;
        $id_coordinador = $_POST['ID_coordinador'] ?? null;
        $region_origen = $_POST['ID_region_origen'] ?? null;
        $region_impacto = $_POST['ID_region_impacto'] ?? null;

        // Entidad Externa y Representante
        $nombre_empresa = $_POST['Nombre_empresa'] ?? null;
        $rut_empresa = $_POST['Rut_empresa'] ?? null;
        $id_tamano = $_POST['ID_tamano'] ?? null;
        $convenio_usm = $_POST['Convenio_USM'] ?? null;
        
        $nombre_rep = $_POST['Nombre_representante'] ?? null;
        $email_rep = $_POST['Mail_representante'] ?? null;
        $telefono_rep = $_POST['Telefono_representante'] ?? null;

        if ($telefono_rep === "") {
            $telefono_rep = null;
        }

        // Lógica de Estados
        $accion = $_POST['accion'] ?? 'borrador';
        if ($accion === 'enviar') {
            $id_estado = 1; // En Revisión
        } else {
            $id_estado = 5; // Borrador
        }


       // =========================================================
        // 2.4 GUARDAR O BUSCAR AL REPRESENTANTE
        // =========================================================
        // Primero buscamos si el representante ya existe por su email
        $sql_buscar_rep = "SELECT ID_Representante FROM REPRESENTANTE_EMPRESA WHERE Mail_representante = ?";
        $stmt_buscar = $conexion->prepare($sql_buscar_rep);
        $stmt_buscar->execute([$email_rep]);
        $rep_existente = $stmt_buscar->fetch(PDO::FETCH_ASSOC);

        if ($rep_existente) {
            // Si ya existe, usamos su ID y actualizamos su teléfono y nombre por si cambiaron
            $id_representante = $rep_existente['ID_Representante'];
            
            $sql_update_rep = "UPDATE REPRESENTANTE_EMPRESA SET Nombre = ?, Telefono_representante = ? WHERE ID_Representante = ?";
            $stmt_update_rep = $conexion->prepare($sql_update_rep);
            $stmt_update_rep->execute([$nombre_rep, $telefono_rep, $id_representante]);
        } else {
            // Si no existe, lo creamos nuevo
            $sql_insert_rep = "INSERT INTO REPRESENTANTE_EMPRESA (Nombre, Mail_representante, Telefono_representante) VALUES (?, ?, ?)";
            $stmt_insert_rep = $conexion->prepare($sql_insert_rep);
            $stmt_insert_rep->execute([$nombre_rep, $email_rep, $telefono_rep]);
            
            // Atrapamos el ID que la base de datos le acaba de asignar
            $id_representante = $conexion->lastInsertId();
        }

        // =========================================================
        // 2.5 GUARDAR O ACTUALIZAR LA EMPRESA
        // =========================================================
        // Ahora conectamos la Empresa con el ID del Representante que acabamos de conseguir
        $sql_empresa = "INSERT INTO EMPRESA (Rut_empresa, Nombre_empresa, Convenio_USM, ID_tamano, ID_representante) 
                        VALUES (?, ?, ?, ?, ?)
                        ON DUPLICATE KEY UPDATE 
                        Nombre_empresa = VALUES(Nombre_empresa),
                        Convenio_USM = VALUES(Convenio_USM),
                        ID_tamano = VALUES(ID_tamano),
                        ID_representante = VALUES(ID_representante)";
        
        $stmt_emp = $conexion->prepare($sql_empresa);
        $stmt_emp->execute([
            $rut_empresa, $nombre_empresa, $convenio_usm, $id_tamano, $id_representante
        ]);

        // =========================================================
        // 3. INSERTAR LA POSTULACIÓN PRINCIPAL
        // =========================================================
        // (Asegúrate de que estas columnas coincidan exacto con tu tabla POSTULACION)
        $sql_postulacion = "INSERT INTO POSTULACION 
        (ID_postulacion, Nombre_iniciativa, Objetivo_iniciativa, Descripcion_soluciones, Resultados_esperados, Presupuesto, ID_tipo_iniciativa, ID_sede, ID_Jefe, ID_coordinador, ID_region_origen, ID_region_impacto, ID_estado, Rut_empresa, Comentario_coordinador) 
        VALUES 
        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt_post = $conexion->prepare($sql_postulacion);
        
        $stmt_post->execute([
            $id_postulacion, $nombre_in, $objetivo, $descripcion_soluciones, $resultados, $presupuesto, 
            $tipo_iniciativa, $id_sede, $id_jefe, $id_coordinador, $region_origen, $region_impacto, 
            $id_estado, $rut_empresa, null
        ]);


// =========================================================
// 4. PROCESAR EQUIPO DE TRABAJO
// =========================================================

// 4.1 Capturamos los arreglos del formulario
$rut_personas = $_POST['Rut_Persona'] ?? [];
$nombres = $_POST['Nombre_persona'] ?? [];
$deptos = $_POST['ID_departamento'] ?? [];
$sedes_personas = $_POST['ID_sede_persona'] ?? [];
$emails = $_POST['eMail'] ?? [];
$telefonos = $_POST['Telefono'] ?? [];
$roles = $_POST['Rol'] ?? [];
$cargos = $_POST['ID_cargo'] ?? []; // Nuevo campo del formulario

// 4.2 Preparamos la sentencia para la tabla PERSONA (UPSERT)
// Usamos "Nombre" a secas y "Rut_persona" según tu estructura [cite: 1, 3]
$sql_upsert_persona = "INSERT INTO Persona (Rut_persona, Nombre, email, telefono, ID_departamento, ID_sede, ID_cargo) 
                       VALUES (?, ?, ?, ?, ?, ?, ?) 
                       ON DUPLICATE KEY UPDATE 
                       Nombre = VALUES(Nombre), 
                       email = VALUES(email), 
                       telefono = VALUES(telefono),
                       ID_departamento = VALUES(ID_departamento), 
                       ID_sede = VALUES(ID_sede),
                       ID_cargo = VALUES(ID_cargo)";
$stmt_persona = $conexion->prepare($sql_upsert_persona);

// 4.3 Preparamos la sentencia para la tabla intermedia
// Según tu modelo: Persona_postulacion(Rut_persona, ID_postulacion, rol) [cite: 6]
$sql_puente = "INSERT INTO Persona_postulacion (Rut_persona, ID_postulacion, rol) VALUES (?, ?, ?)";
$stmt_puente = $conexion->prepare($sql_puente);

// 4.4 Ejecutamos el ciclo para cada integrante
for ($i = 0; $i < count($rut_personas); $i++) {
    $rut_actual = trim($rut_personas[$i]);
    
    // Saltamos si el RUT viene vacío
    if (empty($rut_actual)) continue; 

    // Limpiamos datos opcionales
    $fono_actual = empty($telefonos[$i]) ? null : $telefonos[$i];
    $cargo_actual = $cargos[$i] ?? 1; // Por defecto Estudiante (1) si no viene nada

    // A. Guardar o actualizar datos básicos del integrante
    $stmt_persona->execute([
        $rut_actual, 
        $nombres[$i], 
        $emails[$i], 
        $fono_actual,
        $deptos[$i], 
        $sedes_personas[$i], 
        $cargo_actual
    ]);

    // B. Crear el vínculo entre esta persona y la postulación actual
    $stmt_puente->execute([
        $rut_actual, 
        $id_postulacion, 
        $roles[$i]
    ]);
}
        // =========================================================
        // 5. PROCESAR CRONOGRAMA (Con $id_postulacion)
        // =========================================================
        $etapas = $_POST['Etapa'] ?? [];
        $plazos = $_POST['Plazos_semanas'] ?? [];
        $entregables = $_POST['Entregable'] ?? [];

        $sql_cronograma = "INSERT INTO CRONOGRAMA (ID_postulacion, Etapa, Plazos_Semanas, Entregable) VALUES (?, ?, ?, ?)";
        $stmt_crono = $conexion->prepare($sql_cronograma);

        for ($j = 0; $j < count($etapas); $j++) {
            $etapa_actual = trim($etapas[$j]);
            if (empty($etapa_actual)) continue;

            $stmt_crono->execute([
                $id_postulacion, $etapa_actual, $plazos[$j], $entregables[$j]
            ]);
        }

        // =========================================================
        // 6. CONFIRMAR TODO Y REDIRIGIR
        // =========================================================
        $conexion->commit();
        
        echo "<script>
                alert('¡Éxito! Postulación guardada con el ID: $id_postulacion');
                window.location.href = '../Templates/T_rol1.php'; 
              </script>";

    } catch (Exception $e) {
        // SI ALGO FALLA, DESHACEMOS TODO
        $conexion->rollBack();
        echo "Error al guardar la postulación: " . $e->getMessage();
    }
} else {
    echo "Acceso no autorizado. Debe ingresar mediante el formulario.";
}
?>