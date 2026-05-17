<?php
session_start();
require_once("../BDT1.php");
function filtrar_texto($valor) {
    return (isset($valor) && trim($valor) !== "") ? trim($valor) : "";
}
function filtrar_id($valor) {
    return (isset($valor) && trim($valor) !== "") ? trim($valor) : 0;
}
function filtrar_fecha($valor) {
    return (isset($valor) && trim($valor) !== "") ? trim($valor) : '0000-00-00';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conexion->beginTransaction();
        $id_postulacion = $_POST['ID_postulacion'] ?? '';

        if (isset($_POST['accion']) && $_POST['accion'] === 'enviar') {
            $accion = 'enviar';
        } elseif (isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
            $accion = 'eliminar';
        } elseif (isset($_POST['accion']) && $_POST['accion'] === 'borrador') {
            $accion = 'borrador';
        } else {
            $accion = 'borrador'; 
        }
        if (empty($id_postulacion)) {
            throw new Exception("No se especificó el código de la postulación a editar.");
        }






        // TRIGGER
        if ($accion === 'eliminar') {
            $sql_delete = "DELETE FROM POSTULACION WHERE ID_postulacion = ?";
            $conexion->prepare($sql_delete)->execute([$id_postulacion]);
        
            $conexion->commit(); 
            
            header("Location: ../Templates/T_rol1.php");
            exit(); 
        }






        if ($accion === 'enviar') {
            $id_estado = 1; 
        } else {
            $id_estado = 5; 
        }


        $fecha_postulacion     = filtrar_fecha($_POST['Fecha_postulacion'] ?? '');
        $nombre_in              = filtrar_texto($_POST['Nombre_iniciativa'] ?? '');
        $objetivo               = filtrar_texto($_POST['Objetivo_iniciativa'] ?? '');
        $descripcion_soluciones = filtrar_texto($_POST['Descripcion_soluciones'] ?? ''); 
        $resultados             = filtrar_texto($_POST['Resultados_esperados'] ?? '');
        $presupuesto            = filtrar_id($_POST['Presupuesto'] ?? '');
        $tipo_iniciativa        = filtrar_id($_POST['ID_tipo_iniciativa'] ?? '');
        $id_sede                = filtrar_id($_POST['ID_sede'] ?? '');
        $id_jefe                = filtrar_id($_POST['ID_Jefe'] ?? $_POST['ID_jefe'] ?? '');
        $id_coordinador         = filtrar_id($_POST['ID_coordinador'] ?? '');
        $region_origen          = filtrar_id($_POST['ID_region_origen'] ?? '');
        $region_impacto         = filtrar_id($_POST['ID_region_impacto'] ?? '');
        $nombre_empresa         = filtrar_texto($_POST['Nombre_empresa'] ?? '');
        $rut_empresa            = filtrar_texto($_POST['Rut_empresa'] ?? $_POST['Rut_Empresa'] ?? '');
        $id_tamano              = filtrar_id($_POST['ID_tamano'] ?? '');
        $convenio_usm           = filtrar_id($_POST['Convenio_USM'] ?? '');
        $nombre_rep             = filtrar_texto($_POST['Nombre_representante'] ?? '');
        $email_rep              = filtrar_texto($_POST['Mail_representante'] ?? '');
        $telefono_rep           = filtrar_texto($_POST['Telefono_representante'] ?? '');


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


        $sql_empresa = "INSERT INTO EMPRESA (Rut_empresa, Nombre_empresa, Convenio_USM, ID_tamano, ID_representante) 
                        VALUES (?, ?, ?, ?, ?)
                        ON DUPLICATE KEY UPDATE 
                        Nombre_empresa = VALUES(Nombre_empresa),
                        Convenio_USM = VALUES(Convenio_USM),
                        ID_tamano = VALUES(ID_tamano),
                        ID_representante = VALUES(ID_representante)";
        $conexion->prepare($sql_empresa)->execute([$rut_empresa, $nombre_empresa, $convenio_usm, $id_tamano, $id_representante]);

        $sql_update_post = "UPDATE POSTULACION SET 
                                Nombre_iniciativa = ?, Objetivo_iniciativa = ?, Descripcion_soluciones = ?, 
                                Resultados_esperados = ?, Presupuesto = ?, ID_tipo_iniciativa = ?, 
                                ID_sede = ?, ID_Jefe = ?, ID_coordinador = ?, ID_region_origen = ?, 
                                ID_region_impacto = ?, ID_estado = ?, Rut_empresa = ?, Fecha_postulacion = ?
                            WHERE ID_postulacion = ?";
        
        $conexion->prepare($sql_update_post)->execute([
            $nombre_in, $objetivo, $descripcion_soluciones, $resultados, $presupuesto, $tipo_iniciativa, 
            $id_sede, $id_jefe, $id_coordinador, $region_origen, $region_impacto, $id_estado, $rut_empresa, 
            $fecha_postulacion, 
            $id_postulacion     
        ]);


        $conexion->prepare("DELETE FROM PERSONA_POSTULACION WHERE ID_postulacion = ?")->execute([$id_postulacion]);

        $rut_personas   = $_POST['Rut_Persona'] ?? [];
        $nombres        = $_POST['Nombre_persona'] ?? [];
        $deptos         = $_POST['ID_departamento'] ?? [];
        $sedes_personas = $_POST['ID_sede_persona'] ?? [];
        $emails         = $_POST['eMail'] ?? [];
        $telefonos      = $_POST['Telefono'] ?? [];
        $roles          = $_POST['Rol'] ?? [];
        $cargos         = $_POST['ID_cargo'] ?? [];

        $sql_upsert_persona = "INSERT INTO PERSONA (Rut_persona, Nombre, email, telefono, ID_departamento, ID_sede, ID_cargo) 
                               VALUES (?, ?, ?, ?, ?, ?, ?) 
                               ON DUPLICATE KEY UPDATE 
                               Nombre = VALUES(Nombre), 
                               email = VALUES(email), 
                               telefono = VALUES(telefono),
                               ID_departamento = VALUES(ID_departamento), 
                               ID_sede = VALUES(ID_sede),
                               ID_cargo = VALUES(ID_cargo)";
        $stmt_persona = $conexion->prepare($sql_upsert_persona);

        $sql_puente = "INSERT INTO PERSONA_POSTULACION (Rut_persona, ID_postulacion, rol) VALUES (?, ?, ?)";
        $stmt_puente = $conexion->prepare($sql_puente);

        for ($i = 0; $i < count($rut_personas); $i++) {
            $rut_actual = filtrar_texto($rut_personas[$i] ?? '');
            
            if ($rut_actual === "") continue; 

            $nombre_actual = filtrar_texto($nombres[$i] ?? '');
            $email_actual  = filtrar_texto($emails[$i] ?? '');
            $fono_actual   = filtrar_texto($telefonos[$i] ?? '');
            $depto_actual  = filtrar_id($deptos[$i] ?? '');
            $sede_actual   = filtrar_id($sedes_personas[$i] ?? '');
            $cargo_actual  = filtrar_id($cargos[$i] ?? '');
            $rol_actual    = filtrar_texto($roles[$i] ?? '');

            $stmt_persona->execute([
                $rut_actual, $nombre_actual, $email_actual, $fono_actual,
                $depto_actual, $sede_actual, $cargo_actual
            ]);

            $stmt_puente->execute([
                $rut_actual, $id_postulacion, $rol_actual
            ]);
        }


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


        $conexion->commit();
        
        echo "<script>
                alert('¡Éxito! Postulación actualizada correctamente.');
                window.location.href = '../Templates/T_rol1.php'; 
              </script>";

    } catch (Exception $e) {
        $conexion->rollBack();
        echo "<script>
                alert('Error en el sistema: " . addslashes($e->getMessage()) . "');
                window.history.back();
              </script>";
    }
} else {
    echo "Acceso no autorizado.";
}
?>
