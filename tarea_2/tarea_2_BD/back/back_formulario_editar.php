<?php
session_start();
require_once("../BDT1.php");

// 1. Funciones de limpieza de datos de PHP (Mismo idioma que la creación)
function filtrar_texto($valor) {
    return (isset($valor) && trim($valor) !== "") ? trim($valor) : "";
}
function filtrar_id($valor) {
    return (isset($valor) && trim($valor) !== "") ? trim($valor) : 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conexion->beginTransaction();

        // Capturar el ID de la postulación que estamos editando y la acción
        $id_postulacion = $_POST['ID_postulacion'] ?? '';
        $accion = $_POST['accion'] ?? 'borrador';

        if (empty($id_postulacion)) {
            throw new Exception("No se especificó el código de la postulación a editar.");
        }

        // =========================================================
        // 2. RECIBIR DATOS GENERALES DEL FORMULARIO
        // =========================================================
        // Datos de la Iniciativa
        $nombre_in              = filtrar_texto($_POST['Nombre_iniciativa'] ?? '');
        $objetivo               = filtrar_texto($_POST['Objetivo_iniciativa'] ?? '');
        $descripcion_soluciones = filtrar_texto($_POST['Descripcion_soluciones'] ?? ''); 
        $resultados             = filtrar_texto($_POST['Resultados_esperados'] ?? '');
        $presupuesto            = filtrar_id($_POST['Presupuesto'] ?? '');
        
        // Antecedentes (Selects)
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

        // Validación Estricta solo si presionan "Enviar Postulación Definitiva"
        if ($accion === 'enviar') {
            if ($nombre_in === "" || $objetivo === "" || $id_sede === 0 || $tipo_iniciativa === 0) {
                throw new Exception("Para enviar la postulación definitiva, debes completar todos los campos obligatorios.");
            }
            $id_estado = 1; // En Revisión
        } else {
            $id_estado = 5; // Mantiene o cambia a Borrador
        }

        // =========================================================
        // 3. ACTUALIZAR O INSERTAR REPRESENTANTE Y EMPRESA (Upsert)
        // =========================================================
        // Buscamos o actualizamos al representante por su correo
        $sql_buscar_rep = "SELECT ID_Representante FROM REPRESENTANTE_EMPRESA WHERE Mail_representante = ?";
        $stmt_buscar = $conexion->prepare($sql_buscar_rep);
        $stmt_buscar->execute([$email_rep]);
        $rep_existente = $stmt_buscar->fetch(PDO::FETCH_ASSOC);

        if ($rep_existente) {
            $id_representante = $rep_existente['ID_Representante'];
            $sql_update_rep = "UPDATE REPRESENTANTE_EMPRESA SET Nombre = ?, Telefono_representante = ? WHERE ID_Representante = ?";
            $conexion->prepare($sql_update_rep)->execute([$nombre_rep, $telefono_rep, $id_representante]);
        } else {
            $sql_insert_rep = "INSERT INTO REPRESENTANTE_EMPRESA (Nombre, Mail_representante, Telefono_representante) VALUES (?, ?, ?)";
            $stmt_insert_rep = $conexion->prepare($sql_insert_rep);
            $stmt_insert_rep->execute([$nombre_rep, $email_rep, $telefono_rep]);
            $id_representante = $conexion->lastInsertId();
        }

        // Upsert de la Empresa
        $sql_empresa = "INSERT INTO EMPRESA (Rut_empresa, Nombre_empresa, Convenio_USM, ID_tamano, ID_representante) 
                        VALUES (?, ?, ?, ?, ?)
                        ON DUPLICATE KEY UPDATE 
                        Nombre_empresa = VALUES(Nombre_empresa),
                        Convenio_USM = VALUES(Convenio_USM),
                        ID_tamano = VALUES(ID_tamano),
                        ID_representante = VALUES(ID_representante)";
        $conexion->prepare($sql_empresa)->execute([$rut_empresa, $nombre_empresa, $convenio_usm, $id_tamano, $id_representante]);

        // =========================================================
        // 4. ACTUALIZAR LA POSTULACIÓN PRINCIPAL
        // =========================================================
        $sql_update_post = "UPDATE POSTULACION SET 
                                Nombre_iniciativa = ?, Objetivo_iniciativa = ?, Descripcion_soluciones = ?, 
                                Resultados_esperados = ?, Presupuesto = ?, ID_tipo_iniciativa = ?, 
                                ID_sede = ?, ID_Jefe = ?, ID_coordinador = ?, ID_region_origen = ?, 
                                ID_region_impacto = ?, ID_estado = ?, Rut_empresa = ?
                            WHERE ID_postulacion = ?";
        
        $conexion->prepare($sql_update_post)->execute([
            $nombre_in, $objetivo, $descripcion_soluciones, $resultados, $presupuesto, $tipo_iniciativa, 
            $id_sede, $id_jefe, $id_coordinador, $region_origen, $region_impacto, $id_estado, $rut_empresa, 
            $id_postulacion
        ]);

        // =========================================================
        // 5. SINCRONIZAR EQUIPO DE TRABAJO (Borrar y Re-insertar)
        // =========================================================
        // Paso A: Eliminamos los vínculos actuales de esta postulación en la tabla intermedia
        $conexion->prepare("DELETE FROM Persona_postulacion WHERE ID_postulacion = ?")->execute([$id_postulacion]);

        // Paso B: Capturamos los arreglos del formulario
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
            
            if ($rut_actual === "") continue; // Ignora filas completamente vacías

            // Filtros seguros contra índices vacíos en modo borrador
            $nombre_actual = filtrar_texto($nombres[$i] ?? '');
            $email_actual  = filtrar_texto($emails[$i] ?? '');
            $fono_actual   = filtrar_texto($telefonos[$i] ?? '');
            $depto_actual  = filtrar_id($deptos[$i] ?? '');
            $sede_actual   = filtrar_id($sedes_personas[$i] ?? '');
            $cargo_actual  = filtrar_id($cargos[$i] ?? '');
            $rol_actual    = filtrar_texto($roles[$i] ?? '');

            // Actualizar directorio general de personas
            $stmt_persona->execute([
                $rut_actual, $nombre_actual, $email_actual, $fono_actual,
                $depto_actual, $sede_actual, $cargo_actual
            ]);

            // Re-vincular en la tabla intermedia
            $stmt_puente->execute([
                $rut_actual, $id_postulacion, $rol_actual
            ]);
        }

        // =========================================================
        // 6. SINCRONIZAR CRONOGRAMA (Borrar y Re-insertar)
        // =========================================================
        $conexion->prepare("DELETE FROM CRONOGRAMA WHERE ID_postulacion = ?")->execute([$id_postulacion]);

        $etapas      = $_POST['Etapa'] ?? [];
        $plazos      = $_POST['Plazos_semanas'] ?? [];
        $entregables = $_POST['Entregable'] ?? [];

        $sql_cronograma = "INSERT INTO CRONOGRAMA (ID_postulacion, Etapa, Plazos_Semanas, Entregable) VALUES (?, ?, ?, ?)";
        $stmt_crono = $conexion->prepare($sql_cronograma);

        for ($j = 0; $j < count($etapas); $j++) {
            $etapa_actual = filtrar_texto($etapas[$j] ?? '');
            
            if ($etapa_actual === "") continue;

            $plazo_actual      = filtrar_id($plazos[$j] ?? '');
            $entregable_actual = filtrar_texto($entregables[$j] ?? '');

            $stmt_crono->execute([
                $id_postulacion, $etapa_actual, $plazo_actual, $entregable_actual
            ]);
        }

        // =========================================================
        // 7. CONFIRMAR TRANSACCIÓN
        // =========================================================
        $conexion->commit();
        
        echo "<script>
                alert('¡Éxito! Postulación actualizada correctamente.');
                window.location.href = '../Templates/T_rol1.php'; 
              </script>";

    } catch (Exception $e) {
        $conexion->rollBack();
        echo "<script>
                alert('Error al actualizar: " . addslashes($e->getMessage()) . "');
                window.history.back();
              </script>";
    }
} else {
    echo "Acceso no autorizado.";
}
?>