<?php
session_start();
require_once("../BDT1.php");

function filtrar_texto($valor) {
    return (isset($valor) && trim($valor) !== "") ? trim($valor) : "";
}
function filtrar_id($valor) {
    return (isset($valor) && trim($valor) !== "") ? trim($valor) : 0;
}

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


        // 2. RECIBIR DATOS GENERALES DEL FORMULARIO
        // =========================================================
        // Datos de la Iniciativa
        $nombre_in              = filtrar_texto($_POST['Nombre_iniciativa'] ?? '');
        $objetivo               = filtrar_texto($_POST['Objetivo_iniciativa'] ?? '');
        $descripcion_soluciones = filtrar_texto($_POST['Descripcion_soluciones'] ?? ''); 
        $resultados             = filtrar_texto($_POST['Resultados_esperados'] ?? '');
        $presupuesto            = filtrar_id($_POST['Presupuesto'] ?? '');
        
        // Antecedentes
        $tipo_iniciativa = filtrar_id($_POST['ID_tipo_iniciativa'] ?? '');
        $id_sede         = filtrar_id($_POST['ID_sede'] ?? '');
        $id_jefe         = filtrar_id($_POST['ID_Jefe'] ?? $_POST['ID_jefe'] ?? '');
        $id_coordinador  = filtrar_id($_POST['ID_coordinador'] ?? '');
        $region_origen   = filtrar_id($_POST['ID_region_origen'] ?? '');
        $region_impacto  = filtrar_id($_POST['ID_region_impacto'] ?? '');

        // Entidad Externa y Representante
        $nombre_empresa = filtrar_texto($_POST['Nombre_empresa'] ?? '');
        $rut_empresa    = filtrar_texto($_POST['Rut_empresa'] ?? $_POST['Rut_Empresa'] ?? '');
        $id_tamano      = filtrar_id($_POST['ID_tamano'] ?? '');
        $convenio_usm   = filtrar_id($_POST['Convenio_USM'] ?? '');
        
        $nombre_rep     = filtrar_texto($_POST['Nombre_representante'] ?? '');
        $email_rep      = filtrar_texto($_POST['Mail_representante'] ?? '');
        $telefono_rep   = filtrar_texto($_POST['Telefono_representante'] ?? '');

        // Lógica de Estados y Validación Estricta
        $accion = $_POST['accion'] ?? 'borrador';
        
        if ($accion === 'enviar') {
            if ($nombre_in === "" || $objetivo === "" || $id_sede === 0 || $tipo_iniciativa === 0) {
                throw new Exception("Para enviar la postulación definitiva, debes completar todos los campos obligatorios.");
            }
            $id_estado = 1; // En Revisión
        } else {
            $id_estado = 5; // Borrador
        }


        // =========================================================
        // 2.4 GUARDAR O BUSCAR AL REPRESENTANTE
        // =========================================================
        $sql_buscar_rep = "SELECT ID_Representante FROM REPRESENTANTE_EMPRESA WHERE Mail_representante = ?";
        $stmt_buscar = $conexion->prepare($sql_buscar_rep);
        $stmt_buscar->execute([$email_rep]);
        $rep_existente = $stmt_buscar->fetch(PDO::FETCH_ASSOC);

        if ($rep_existente) {
            $id_representante = $rep_existente['ID_Representante'];
            
            $sql_update_rep = "UPDATE REPRESENTANTE_EMPRESA SET Nombre = ?, Telefono_representante = ? WHERE ID_Representante = ?";
            $stmt_update_rep = $conexion->prepare($sql_update_rep);
            $stmt_update_rep->execute([$nombre_rep, $telefono_rep, $id_representante]);
        } else {
            $sql_insert_rep = "INSERT INTO REPRESENTANTE_EMPRESA (Nombre, Mail_representante, Telefono_representante) VALUES (?, ?, ?)";
            $stmt_insert_rep = $conexion->prepare($sql_insert_rep);
            $stmt_insert_rep->execute([$nombre_rep, $email_rep, $telefono_rep]);
            
            $id_representante = $conexion->lastInsertId();
        }

        // =========================================================
        // 2.5 GUARDAR O ACTUALIZAR LA EMPRESA
        // =========================================================
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
        // 4. PROCESAR EQUIPO DE TRABAJO (SEGURO PARA ELEMENTOS VACÍOS)
        // =========================================================
        $rut_personas   = $_POST['Rut_Persona'] ?? [];
        $nombres        = $_POST['Nombre_persona'] ?? [];
        $deptos         = $_POST['ID_departamento'] ?? [];
        $sedes_personas = $_POST['ID_sede_persona'] ?? [];
        $emails         = $_POST['eMail'] ?? [];
        $telefonos      = $_POST['Telefono'] ?? [];
        $roles          = $_POST['Rol'] ?? [];
        $cargos         = $_POST['ID_cargo'] ?? [];

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

        $sql_puente = "INSERT INTO Persona_postulacion (Rut_persona, ID_postulacion, rol) VALUES (?, ?, ?)";
        $stmt_puente = $conexion->prepare($sql_puente);

        for ($i = 0; $i < count($rut_personas); $i++) {
            $rut_actual = filtrar_texto($rut_personas[$i] ?? '');
            
            // Saltamos la fila completa si el RUT viene vacío
            if ($rut_actual === "") continue; 

            // Aplicamos los filtros correspondientes usando protección de índices
            $nombre_actual = filtrar_texto($nombres[$i] ?? '');
            $email_actual  = filtrar_texto($emails[$i] ?? '');
            $fono_actual   = filtrar_texto($telefonos[$i] ?? '');
            $depto_actual  = filtrar_id($deptos[$i] ?? '');          // Retorna 0 si falta
            $sede_actual   = filtrar_id($sedes_personas[$i] ?? '');   // Retorna 0 si falta
            $cargo_actual  = filtrar_id($cargos[$i] ?? '');          // Retorna 0 si falta
            $rol_actual    = filtrar_texto($roles[$i] ?? '');

            // A. Guardar o actualizar datos básicos del integrante
            $stmt_persona->execute([
                $rut_actual, $nombre_actual, $email_actual, $fono_actual,
                $depto_actual, $sede_actual, $cargo_actual
            ]);

            // B. Crear el vínculo entre esta persona y la postulación actual
            $stmt_puente->execute([
                $rut_actual, $id_postulacion, $rol_actual
            ]);
        }


        // =========================================================
        // 5. PROCESAR CRONOGRAMA (SEGURO PARA ELEMENTOS VACÍOS)
        // =========================================================
        $etapas      = $_POST['Etapa'] ?? [];
        $plazos      = $_POST['Plazos_semanas'] ?? [];
        $entregables = $_POST['Entregable'] ?? [];

        $sql_cronograma = "INSERT INTO CRONOGRAMA (ID_postulacion, Etapa, Plazos_Semanas, Entregable) VALUES (?, ?, ?, ?)";
        $stmt_crono = $conexion->prepare($sql_cronograma);

        for ($j = 0; $j < count($etapas); $j++) {
            $etapa_actual = filtrar_texto($etapas[$j] ?? '');
            
            // Saltamos la fila completa si la descripción de la etapa viene vacía
            if ($etapa_actual === "") continue;

            // Procesamos plazos y entregables con resguardo de índices
            $plazo_actual      = filtrar_id($plazos[$j] ?? '');      // Retorna 0 si falta
            $entregable_actual = filtrar_texto($entregables[$j] ?? '');

            $stmt_crono->execute([
                $id_postulacion, $etapa_actual, $plazo_actual, $entregable_actual
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
        $conexion->rollBack();
        echo "Error al guardar la postulación: " . $e->getMessage();
    }
} else {
    echo "Acceso no autorizado. Debe ingresar mediante el formulario.";
}
?>