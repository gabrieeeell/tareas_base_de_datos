<?php
include '../BDT1.php'; 

try {
    $conexion->beginTransaction();

    // =========================================================
    // 1. CAPTURA DE DATOS BÁSICOS (POSTULACIÓN)
    // =========================================================
    $id_postulacion = $_POST['ID_postulacion']; // Campo oculto en tu formulario
    $nombre_iniciativa = $_POST['Nombre_iniciativa'];
    $objetivo = $_POST['Objetivo_iniciativa'];
    $descripcion = $_POST['Descripcion_soluciones'];
    $resultados = $_POST['Resultados_esperados'];
    $presupuesto = $_POST['Presupuesto'];
    
    // IDs de selectores
    $tipo_iniciativa = $_POST['ID_tipo_iniciativa'];
    $id_sede = $_POST['ID_sede'];
    $id_jefe = $_POST['ID_jefe'];
    $id_coordinador = $_POST['ID_coordinador'];
    $region_origen = $_POST['ID_region_origen'];
    $region_impacto = $_POST['ID_region_impacto'];
    $id_estado = $_POST['ID_estado'];

    // =========================================================
    // 2. DATOS DE EMPRESA Y REPRESENTANTE
    // =========================================================
    $rut_empresa = $_POST['Rut_empresa'];
    $nombre_empresa = $_POST['Nombre_empresa'];
    $convenio_usm = isset($_POST['Convenio_USM']) ? 1 : 0;
    $id_tamano = $_POST['ID_tamano'];
    
    $nombre_rep = $_POST['Nombre_representante'];
    $email_rep = $_POST['Mail_representante'];
    $telefono_rep = $_POST['Telefono_representante'];

    // 2.1 UPSERT REPRESENTANTE (Igual que en crear)
    $sql_rep = "INSERT INTO REPRESENTANTE_EMPRESA (Nombre, Mail_representante, Telefono_representante) 
                VALUES (?, ?, ?) 
                ON DUPLICATE KEY UPDATE Nombre = VALUES(Nombre), Telefono_representante = VALUES(Telefono_representante)";
    $stmt_rep = $conexion->prepare($sql_rep);
    $stmt_rep->execute([$nombre_rep, $email_rep, $telefono_rep]);

    // Obtenemos el ID (si era nuevo o si ya existía)
    $sql_get_rep = "SELECT ID_Representante FROM REPRESENTANTE_EMPRESA WHERE Mail_representante = ?";
    $stmt_get = $conexion->prepare($sql_get_rep);
    $stmt_get->execute([$email_rep]);
    $id_rep = $stmt_get->fetchColumn();

    // 2.2 UPDATE EMPRESA
    $sql_upd_empresa = "UPDATE EMPRESA SET 
                        Nombre_empresa = ?, Convenio_USM = ?, ID_tamano = ?, ID_representante = ? 
                        WHERE Rut_empresa = ?";
    $conexion->prepare($sql_upd_empresa)->execute([$nombre_empresa, $convenio_usm, $id_tamano, $id_rep, $rut_empresa]);

    // =========================================================
    // 3. ACTUALIZAR POSTULACIÓN PRINCIPAL
    // =========================================================
    $sql_upd_post = "UPDATE POSTULACION SET 
                     Nombre_iniciativa = ?, Objetivo_iniciativa = ?, Descripcion_soluciones = ?, 
                     Resultados_esperados = ?, Presupuesto = ?, ID_tipo_iniciativa = ?, 
                     ID_sede = ?, ID_Jefe = ?, ID_coordinador = ?, ID_region_origen = ?, 
                     ID_region_impacto = ?, ID_estado = ?, Rut_empresa = ?
                     WHERE ID_postulacion = ?";
    
    $conexion->prepare($sql_upd_post)->execute([
        $nombre_iniciativa, $objetivo, $descripcion, $resultados, $presupuesto, 
        $tipo_iniciativa, $id_sede, $id_jefe, $id_coordinador, $region_origen, 
        $region_impacto, $id_estado, $rut_empresa, $id_postulacion
    ]);

    // =========================================================
    // 4. ACTUALIZAR EQUIPO (Sincronización M:N)
    // =========================================================
    
    // 4.1 Borramos los vínculos antiguos en la tabla puente
    $sql_del_vinculos = "DELETE FROM Persona_postulacion WHERE ID_postulacion = ?";
    $conexion->prepare($sql_del_vinculos)->execute([$id_postulacion]);

    // 4.2 Procesamos los integrantes (Igual que en crear)
    $ruts = $_POST['Rut_Persona'] ?? [];
    $nombres = $_POST['Nombre_persona'] ?? [];
    $emails = $_POST['eMail'] ?? [];
    $telefonos = $_POST['Telefono'] ?? [];
    $deptos = $_POST['ID_departamento'] ?? [];
    $sedes_p = $_POST['ID_sede_persona'] ?? [];
    $cargos = $_POST['ID_cargo'] ?? [];
    $roles = $_POST['Rol'] ?? [];

    $sql_per = "INSERT INTO Persona (Rut_persona, Nombre, email, telefono, ID_departamento, ID_sede, ID_cargo) 
                VALUES (?, ?, ?, ?, ?, ?, ?) 
                ON DUPLICATE KEY UPDATE Nombre=VALUES(Nombre), email=VALUES(email), telefono=VALUES(telefono), 
                ID_departamento=VALUES(ID_departamento), ID_sede=VALUES(ID_sede), ID_cargo=VALUES(ID_cargo)";
    $stmt_per = $conexion->prepare($sql_per);

    $sql_pue = "INSERT INTO Persona_postulacion (Rut_persona, ID_postulacion, rol) VALUES (?, ?, ?)";
    $stmt_pue = $conexion->prepare($sql_pue);

    for ($i = 0; $i < count($ruts); $i++) {
        $rut_limpio = strtoupper(str_replace(['.', '-'], '', trim($ruts[$i])));
        if (empty($rut_limpio)) continue;

        $stmt_per->execute([$rut_limpio, $nombres[$i], $emails[$i], $telefonos[$i], $deptos[$i], $sedes_p[$i], $cargos[$i]]);
        $stmt_pue->execute([$rut_limpio, $id_postulacion, $roles[$i]]);
    }

    // =========================================================
    // 5. ACTUALIZAR CRONOGRAMA
    // =========================================================
    
    // 5.1 Borramos las etapas viejas
    $sql_del_crono = "DELETE FROM CRONOGRAMA WHERE ID_postulacion = ?";
    $conexion->prepare($sql_del_crono)->execute([$id_postulacion]);

    // 5.2 Insertamos las nuevas etapas
    $etapas = $_POST['Etapa'] ?? [];
    $semanas = $_POST['Plazos_Semanas'] ?? [];
    $entregables = $_POST['Entregable'] ?? [];

    $sql_ins_crono = "INSERT INTO CRONOGRAMA (Etapa, Plazos_Semanas, Entregable, ID_postulacion) VALUES (?, ?, ?, ?)";
    $stmt_crono = $conexion->prepare($sql_ins_crono);

    for ($i = 0; $i < count($etapas); $i++) {
        if (!empty($etapas[$i])) {
            $stmt_crono->execute([$etapas[$i], $semanas[$i], $entregables[$i], $id_postulacion]);
        }
    }

    $conexion->commit();
    echo "<script>alert('Postulación actualizada con éxito'); window.location.href='../Templates/T_rol1_dashboard.php';</script>";

} catch (Exception $e) {
    $conexion->rollBack();
    echo "Error al actualizar: " . $e->getMessage();
}