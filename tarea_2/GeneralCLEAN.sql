DROP DATABASE IF EXISTS ct_usm_postulaciones;
CREATE DATABASE ct_usm_postulaciones;
USE ct_usm_postulaciones;


CREATE TABLE REGION (
    ID_region INT NOT NULL,
    Nombre_region VARCHAR(50) NOT NULL,
    PRIMARY KEY (ID_region)
) ENGINE=InnoDB;

INSERT INTO REGION (ID_region, Nombre_region) VALUES
(0, 'Vacío'),
(1, 'Arica y Parinacota'),
(2, 'Tarapacá'),
(3, 'Antofagasta'),
(4, 'Atacama'),
(5, 'Coquimbo'),
(6, 'Valparaíso'),
(7, 'Metropolitana de Santiago'),
(8, 'Libertador General Bernardo O''Higgins'),
(9, 'Maule'),
(10, 'Ñuble'),
(11, 'Biobío'),
(12, 'La Araucanía'),
(13, 'Los Ríos'),
(14, 'Los Lagos'),
(15, 'Aysén del General Carlos Ibáñez del Campo'),
(16, 'Magallanes y de la Antártica Chilena');

CREATE TABLE SEDE (
    ID_sede INT NOT NULL,
    Nombre_Sede VARCHAR(31) NOT NULL,
    PRIMARY KEY (ID_sede)
) ENGINE=InnoDB;

INSERT INTO SEDE (ID_sede, Nombre_Sede) VALUES
(0, 'Vacío'),
(1, 'Campus Casa Central Valparaíso'),
(2, 'Campus San Joaquín'),
(3, 'Campus Vitacura'),
(4, 'Sede Viña del Mar'),
(5, 'Sede Concepción');

CREATE TABLE ESTADO_POSTULACION (
    ID_estado INT NOT NULL,
    Nombre_estado VARCHAR(11) NOT NULL,
    PRIMARY KEY (ID_estado)
) ENGINE=InnoDB;

INSERT INTO ESTADO_POSTULACION (ID_estado, Nombre_estado) VALUES
(0, 'Vacío'),
(1, 'En Revisión'),
(2, 'Aprobada'),
(3, 'Rechazada'),
(4, 'Cerrada'),
(5, 'Borrador');


CREATE TABLE CARGO_PERSONA (
    ID_cargo INT NOT NULL,
    Nombre_cargo VARCHAR(50) NOT NULL,
    PRIMARY KEY (ID_cargo)
) ENGINE=InnoDB;

INSERT INTO CARGO_PERSONA (ID_cargo, Nombre_cargo) VALUES
(0, 'Vacío'),
(1, 'Estudiante'),
(2, 'Profesor');

CREATE TABLE DEPARTAMENTO (
	ID_departamento INT NOT NULL,
    Nombre_departamento VARCHAR(100) NOT NULL,
    PRIMARY KEY (ID_departamento)
) ENGINE=InnoDB;

INSERT INTO DEPARTAMENTO (ID_departamento, Nombre_departamento) VALUES
(0, 'Vacío'),
(1, 'Aeronáutica'),
(2, 'Arquitectura'),
(3, 'Ciencia de los Materiales / Metalurgia'),
(4, 'Física'),
(5, 'Informática'),
(6, 'Ingeniería Mecánica'),
(7, 'Ingeniería Química y Ambiental'),
(8, 'Matemática'),
(9, 'Obras Civiles'),
(10, 'Construcción y Prevención de Riesgos'),
(11, 'Mecánica'),
(12, 'Estudios Humanísticos');

CREATE TABLE TIPO_INICIATIVA (
    ID_tipo INT NOT NULL,
    Tipo_iniciativa VARCHAR(9) NOT NULL,
    PRIMARY KEY (ID_tipo)
) ENGINE=InnoDB;

INSERT INTO  TIPO_INICIATIVA(ID_tipo, Tipo_iniciativa) VALUES
(0, 'Vacío'),
(1, 'Nueva'),
(2, 'Existente');

CREATE TABLE JEFE_CARRERA (
    ID_jefe INT NOT NULL,
    Nombre_jefe VARCHAR(50) NOT NULL,
    PRIMARY KEY (ID_jefe)
) ENGINE=InnoDB;
INSERT INTO JEFE_CARRERA (ID_jefe, Nombre_jefe) VALUES (0, 'Vacío');
INSERT INTO JEFE_CARRERA (ID_jefe, Nombre_jefe) VALUES (1, 'Lionel Valenzuela');
INSERT INTO JEFE_CARRERA (ID_jefe, Nombre_jefe)VALUES (2, 'Andrea Urrutia');
INSERT INTO JEFE_CARRERA (ID_jefe, Nombre_jefe) VALUES (3, 'Claudio Acuña');
INSERT INTO JEFE_CARRERA (ID_jefe, Nombre_jefe) VALUES (4, 'José Luis Martí');
INSERT INTO JEFE_CARRERA (ID_jefe, Nombre_jefe) VALUES (5, 'Marcelo Villena');
INSERT INTO JEFE_CARRERA (ID_jefe, Nombre_jefe) VALUES (6, 'Agustín González');

CREATE TABLE COORDINADOR (
    ID_coordinador INT NOT NULL,
    rut_coordinador VARCHAR(20) UNIQUE NOT NULL,
    Nombre_coordinador VARCHAR(50) NOT NULL,
    PRIMARY KEY (ID_coordinador)
) ENGINE=InnoDB;


INSERT INTO COORDINADOR (ID_coordinador, Nombre_coordinador, rut_coordinador) VALUES (0, 'Sin Coordinador', '22.222.222-0');
INSERT INTO COORDINADOR (ID_coordinador, Nombre_coordinador, rut_coordinador) VALUES (1, 'Alejandro Fuentes', '22.222.222-1');
INSERT INTO COORDINADOR (ID_coordinador, Nombre_coordinador, rut_coordinador) VALUES (2, 'María José Campos','22.222.222-2');
INSERT INTO COORDINADOR (ID_coordinador, Nombre_coordinador, rut_coordinador) VALUES (3, 'Ricardo Morales','22.222.222-3');
INSERT INTO COORDINADOR (ID_coordinador, Nombre_coordinador, rut_coordinador) VALUES (4, 'Carolina Pizarro','22.222.222-4');
INSERT INTO COORDINADOR (ID_coordinador, Nombre_coordinador, rut_coordinador) VALUES (5, 'Felipe Contreras','22.222.222-5');
INSERT INTO COORDINADOR (ID_coordinador, Nombre_coordinador, rut_coordinador) VALUES (6, 'Daniela Espinoza','22.222.222-6');
INSERT INTO COORDINADOR (ID_coordinador, Nombre_coordinador, rut_coordinador) VALUES (7, 'Jorge Valenzuela','22.222.222-7');

CREATE TABLE TAMANO_EMPRESA (
    ID_tamano INT NOT NULL,
    Nombre_tamano VARCHAR(15) NOT NULL,
    PRIMARY KEY (ID_tamano)
) ENGINE=InnoDB;

INSERT INTO  TAMANO_EMPRESA (ID_tamano, Nombre_tamano) VALUES
(0, 'Vacío'),
(1, 'Microempresa'),
(2, 'Mediana'),
(3, 'Grande');

CREATE TABLE REPRESENTANTE_EMPRESA (
    ID_representante INT NOT NULL AUTO_INCREMENT,
    Nombre VARCHAR(100) NOT NULL,
    Mail_representante VARCHAR(255) NOT NULL, 
    Telefono_representante VARCHAR(12) NOT NULL, 
    PRIMARY KEY (ID_representante)
) ENGINE=InnoDB;


CREATE TABLE EMPRESA (
    Rut_Empresa VARCHAR(12) NOT NULL,
    Nombre_empresa VARCHAR(100) NOT NULL,
    Convenio_USM BOOLEAN NOT NULL,
    ID_tamano INT NOT NULL,
    ID_representante INT NOT NULL,
    PRIMARY KEY (Rut_Empresa),
    FOREIGN KEY (ID_tamano) REFERENCES TAMANO_EMPRESA(ID_tamano),
    FOREIGN KEY (ID_representante) REFERENCES REPRESENTANTE_EMPRESA(ID_representante)
) ENGINE=InnoDB;

CREATE TABLE PERSONA (
    RUT_Persona VARCHAR(12) NOT NULL,
    Nombre VARCHAR(100) NOT NULL,
    ID_departamento INT NOT NULL,
    ID_sede INT NOT NULL,
    eMail VARCHAR(255) NOT NULL,
    Telefono VARCHAR(12),
    ID_cargo INT NOT NULL,
    PRIMARY KEY (RUT_Persona),
    FOREIGN KEY (ID_sede) REFERENCES SEDE(ID_sede),
    FOREIGN KEY (ID_cargo) REFERENCES CARGO_PERSONA(ID_cargo),
    FOREIGN KEY (ID_departamento) REFERENCES DEPARTAMENTO(ID_departamento)
) ENGINE=InnoDB;

-- Se puede solo 1 auto increment por entidad, para tenerlo en cuenta
CREATE TABLE POSTULACION (
    ID_postulacion VARCHAR(20) NOT NULL,
    Numero_postulacion INT NOT NULL UNIQUE AUTO_INCREMENT,
    Fecha_postulacion DATE NOT NULL,
    Nombre_iniciativa VARCHAR(100) NOT NULL,
    Objetivo_iniciativa VARCHAR(255) NOT NULL,
    Descripcion_soluciones VARCHAR(255) NOT NULL,
    Resultados_esperados VARCHAR(255) NOT NULL,
    Presupuesto INT NOT NULL,
    Rut_Empresa VARCHAR(12) NOT NULL,
    ID_sede INT NOT NULL,
    ID_estado INT NOT NULL,
    ID_region_impacto INT NOT NULL,
    ID_region_origen INT NOT NULL,
    ID_tipo_iniciativa INT NOT NULL,
    ID_jefe INT NOT NULL,
    ID_coordinador INT NOT NULL,
    Comentario_coordinador VARCHAR(255) NULL,
    PRIMARY KEY (ID_postulacion),
    FOREIGN KEY (Rut_Empresa) REFERENCES EMPRESA(Rut_Empresa),
    FOREIGN KEY (ID_sede) REFERENCES SEDE(ID_sede),
    FOREIGN KEY (ID_estado) REFERENCES ESTADO_POSTULACION(ID_estado),
    FOREIGN KEY (ID_region_impacto) REFERENCES REGION(ID_region),
    FOREIGN KEY (ID_region_origen) REFERENCES REGION(ID_region),
    FOREIGN KEY (ID_tipo_iniciativa) REFERENCES TIPO_INICIATIVA(ID_tipo),
    FOREIGN KEY (ID_jefe) REFERENCES JEFE_CARRERA(ID_jefe),
    FOREIGN KEY (ID_coordinador) REFERENCES COORDINADOR(ID_coordinador)
) ENGINE=InnoDB;

CREATE TABLE CRONOGRAMA (
    ID_cronograma INT NOT NULL AUTO_INCREMENT,
    Etapa VARCHAR(100) NOT NULL,
    Plazos_Semanas INT NOT NULL,
    Entregable VARCHAR(100) NOT NULL,
    ID_postulacion VARCHAR(20) NOT NULL,
    PRIMARY KEY (ID_cronograma),
    FOREIGN KEY (ID_postulacion) REFERENCES POSTULACION(ID_postulacion),
    CHECK (Plazos_Semanas <= 36)
) ENGINE=InnoDB;

CREATE TABLE DOCUMENTO (
    ID_documento INT NOT NULL AUTO_INCREMENT,
    Archivo BLOB NOT NULL,
    ID_postulacion VARCHAR(20) NOT NULL,
    Tipo VARCHAR(10) NOT NULL,
    PRIMARY KEY (ID_documento),
    FOREIGN KEY (ID_postulacion) REFERENCES POSTULACION(ID_postulacion) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE PERSONA_POSTULACION (
    RUT_Persona VARCHAR(12) NOT NULL,
    ID_postulacion VARCHAR(20) NOT NULL,
    Rol VARCHAR(60) NOT NULL,
    PRIMARY KEY (RUT_Persona, ID_postulacion),
    FOREIGN KEY (RUT_Persona) REFERENCES PERSONA(RUT_Persona),
    FOREIGN KEY (ID_postulacion) REFERENCES POSTULACION(ID_postulacion) 
) ENGINE=InnoDB;

DELIMITER //

CREATE TRIGGER trg_eliminar_cascada_postulacion
BEFORE DELETE ON POSTULACION
FOR EACH ROW
BEGIN
    DELETE FROM CRONOGRAMA WHERE ID_postulacion = OLD.ID_postulacion;
    DELETE FROM PERSONA_POSTULACION WHERE ID_postulacion = OLD.ID_postulacion;
END //

DELIMITER ;

CREATE OR REPLACE VIEW vista_postulaciones_responsables AS
SELECT 
    P.ID_Postulacion, 
    P.Nombre_iniciativa, 
    E.Nombre_estado AS Estado,
    PP.Rut_persona,
    PP.Rol
FROM POSTULACION P
JOIN ESTADO_POSTULACION E ON P.ID_estado = E.ID_estado
JOIN PERSONA_POSTULACION PP ON P.ID_Postulacion = PP.ID_postulacion;

DELIMITER //

CREATE PROCEDURE sp_actualizar_coordinador_postulacion(
    IN p_id_coordinador INT,
    IN p_numero_postulacion INT
)
BEGIN
    UPDATE POSTULACION 
    SET ID_coordinador = p_id_coordinador 
    WHERE Numero_postulacion = p_numero_postulacion;
END //

DELIMITER ;

