DROP DATABASE IF EXISTS ct_usm_postulaciones;
CREATE DATABASE ct_usm_postulaciones;
USE ct_usm_postulaciones;


CREATE TABLE REGION (
    ID_region INT NOT NULL,
    Nombre_region VARCHAR(50) NOT NULL,
    PRIMARY KEY (ID_region)
) ENGINE=InnoDB;

INSERT INTO REGION (ID_region, Nombre_region) VALUES
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
(1, 'En Revisión'),
(2, 'Aprobada'),
(3, 'Rechazada'),
(4, 'Cerrada');

CREATE TABLE CARGO_PERSONA (
    ID_cargo INT NOT NULL,
    Nombre_cargo VARCHAR(50) NOT NULL,
    PRIMARY KEY (ID_cargo)
) ENGINE=InnoDB;

INSERT INTO CARGO_PERSONA (ID_cargo, Nombre_cargo) VALUES
(1, 'Estudiante'),
(2, 'Profesor');

CREATE TABLE DEPARTAMENTO (
	ID_departamento INT NOT NULL AUTO_INCREMENT,
    Nombre_departamento VARCHAR(100) NOT NULL,
    PRIMARY KEY (ID_departamento)
) ENGINE=InnoDB;

INSERT INTO DEPARTAMENTO (Nombre_departamento) VALUES
('Aeronáutica'),
('Arquitectura'),
('Ciencia de los Materiales / Metalurgia'),
('Física'),
('Informática'),
('Ingeniería Mecánica'),
('Ingeniería Química y Ambiental'),
('Matemática'),
('Obras Civiles'),
('Construcción y Prevención de Riesgos'),
('Mecánica'),
('Estudios Humanísticos');

CREATE TABLE TIPO_INICIATIVA (
    ID_tipo INT NOT NULL,
    Tipo_iniciativa VARCHAR(9) NOT NULL,
    PRIMARY KEY (ID_tipo)
) ENGINE=InnoDB;

INSERT INTO  TIPO_INICIATIVA(ID_tipo, Tipo_iniciativa) VALUES
(1, 'Nueva'),
(2, 'Existente');

CREATE TABLE JEFE_CARRERA (
    ID_jefe INT NOT NULL AUTO_INCREMENT,
    Nombre_jefe VARCHAR(50) NOT NULL,
    PRIMARY KEY (ID_jefe)
) ENGINE=InnoDB;

CREATE TABLE COORDINADOR (
    ID_coordinador INT NOT NULL AUTO_INCREMENT,
    Nombre_coordinador VARCHAR(50) NOT NULL,
    PRIMARY KEY (ID_coordinador)
) ENGINE=InnoDB;

CREATE TABLE TAMANO_EMPRESA (
    ID_tamano INT NOT NULL,
    Nombre_tamano VARCHAR(15) NOT NULL,
    PRIMARY KEY (ID_tamano)
) ENGINE=InnoDB;

INSERT INTO  TAMANO_EMPRESA (ID_tamano, Nombre_tamano) VALUES
(1, 'Microempresa'),
(2, 'Mediana'),
(3, 'Grande');

CREATE TABLE REPRESENTANTE_EMPRESA (
    ID_representante INT NOT NULL AUTO_INCREMENT,
    Nombre VARCHAR(100) NOT NULL,
    Mail_representante VARCHAR(255) NOT NULL UNIQUE, 
    Telefono_representante VARCHAR(12) NOT NULL UNIQUE, 
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
    eMail VARCHAR(255) NOT NULL UNIQUE,
    Telefono VARCHAR(12) UNIQUE,
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
    FOREIGN KEY (ID_postulacion) REFERENCES POSTULACION(ID_postulacion) ON DELETE CASCADE,
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
    FOREIGN KEY (ID_postulacion) REFERENCES POSTULACION(ID_postulacion) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO REPRESENTANTE_EMPRESA (Nombre, Mail_representante, Telefono_representante)
VALUES ('Carlos Muñoz', 'carlos.munoz@gmail.com', '+56991234567');

INSERT INTO REPRESENTANTE_EMPRESA (Nombre, Mail_representante, Telefono_representante)
VALUES ('Fernanda Rojas', 'fernanda.rojas@gmail.com', '+56992345678');

INSERT INTO REPRESENTANTE_EMPRESA (Nombre, Mail_representante, Telefono_representante)
VALUES ('Diego Pérez', 'diego.perez@gmail.com', '+56993456789');

INSERT INTO REPRESENTANTE_EMPRESA (Nombre, Mail_representante, Telefono_representante)
VALUES ('Valentina Soto', 'valentina.soto@gmail.com', '+56994567890');

INSERT INTO REPRESENTANTE_EMPRESA (Nombre, Mail_representante, Telefono_representante)
VALUES ('Martin Araya', 'martin.araya@sprite.cl', '+56994567897');


INSERT INTO EMPRESA (Rut_Empresa, Nombre_empresa, Convenio_USM, ID_tamano, ID_representante)
VALUES ('76.123.456-7', 'TechSolutions SpA', TRUE, 1, 1);

INSERT INTO EMPRESA (Rut_Empresa, Nombre_empresa, Convenio_USM, ID_tamano, ID_representante)
VALUES ('77.234.567-8', 'Constructora Andes Ltda.', FALSE, 2, 2);

INSERT INTO EMPRESA (Rut_Empresa, Nombre_empresa, Convenio_USM, ID_tamano, ID_representante)
VALUES ('78.345.678-9', 'Servicios Industriales Norte', TRUE, 3, 3);

INSERT INTO EMPRESA (Rut_Empresa, Nombre_empresa, Convenio_USM, ID_tamano, ID_representante)
VALUES ('79.456.789-K', 'AgroChile Exportaciones', FALSE, 1, 4);

INSERT INTO EMPRESA (Rut_Empresa, Nombre_empresa, Convenio_USM, ID_tamano, ID_representante)
VALUES ('75.567.890-1', 'Logística Pacífico', TRUE, 2, 1);

INSERT INTO EMPRESA (Rut_Empresa, Nombre_empresa, Convenio_USM, ID_tamano, ID_representante)
VALUES ('74.678.901-2', 'Innovación Digital Ltda.', FALSE, 3, 2);

INSERT INTO EMPRESA (Rut_Empresa, Nombre_empresa, Convenio_USM, ID_tamano, ID_representante)
VALUES ('75.372.141-2', 'Sprite Chile.', FALSE, 2, 5);


INSERT INTO PERSONA VALUES ('19.123.456-1','Juan Pérez',3,1,'juan.perez@usm.cl','+56991230001',1);
INSERT INTO PERSONA VALUES ('20.234.567-2','Camila Rojas',5,2,'camila.rojas@usm.cl','+56991230002',1);
INSERT INTO PERSONA VALUES ('21.345.678-3','Diego Soto',2,3,'diego.soto@usm.cl','+56991230003',1);
INSERT INTO PERSONA VALUES ('22.456.789-4','Valentina Díaz',7,4,'valentina.diaz@usm.cl','+56991230004',1);
INSERT INTO PERSONA VALUES ('18.567.890-5','Felipe Muñoz',1,5,'felipe.munoz@usm.cl','+56991230005',1);
INSERT INTO PERSONA VALUES ('19.678.901-6','Antonia Silva',4,1,'antonia.silva@usm.cl','+56991230006',1);
INSERT INTO PERSONA VALUES ('20.789.012-7','Matías Torres',6,2,'matias.torres@usm.cl','+56991230007',1);
INSERT INTO PERSONA VALUES ('21.890.123-8','Javiera Morales',8,3,'javiera.morales@usm.cl','+56991230008',1);
INSERT INTO PERSONA VALUES ('22.901.234-9','Benjamín Herrera',9,4,'benjamin.herrera@usm.cl','+56991230009',1);
INSERT INTO PERSONA VALUES ('18.012.345-K','Francisca Castro',10,5,'francisca.castro@usm.cl','+56991230010',1);
INSERT INTO PERSONA VALUES ('19.111.222-1','Sebastián Vargas',11,1,'sebastian.vargas@usm.cl','+56991230011',1);
INSERT INTO PERSONA VALUES ('20.222.333-2','Daniela Fuentes',12,2,'daniela.fuentes@usm.cl','+56991230012',1);
INSERT INTO PERSONA VALUES ('21.333.444-3','Tomás Araya',3,3,'tomas.araya@usm.cl','+56991230013',1);
INSERT INTO PERSONA VALUES ('22.444.555-4','Constanza Bravo',5,4,'constanza.bravo@usm.cl','+56991230014',1);
INSERT INTO PERSONA VALUES ('18.555.666-5','Ignacio Paredes',7,5,'ignacio.paredes@usm.cl','+56991230015',1);
INSERT INTO PERSONA VALUES ('19.666.777-6','Catalina Reyes',2,1,'catalina.reyes@usm.cl','+56991230016',1);
INSERT INTO PERSONA VALUES ('20.777.888-7','Cristóbal Vega',4,2,'cristobal.vega@usm.cl','+56991230017',1);
INSERT INTO PERSONA VALUES ('21.888.999-8','Isidora León',6,3,'isidora.leon@usm.cl','+56991230018',1);
INSERT INTO PERSONA VALUES ('22.999.000-9','Nicolás Salinas',8,4,'nicolas.salinas@usm.cl','+56991230019',1);
INSERT INTO PERSONA VALUES ('18.101.202-0','Paula Godoy',9,5,'paula.godoy@usm.cl','+56991230020',1);
INSERT INTO PERSONA VALUES ('19.202.303-1','Andrés Riquelme',10,1,'andres.riquelme@usm.cl','+56991230021',1);
INSERT INTO PERSONA VALUES ('20.303.404-2','Macarena Figueroa',11,2,'macarena.figueroa@usm.cl','+56991230022',1);
INSERT INTO PERSONA VALUES ('21.404.505-3','Rodrigo Escobar',12,3,'rodrigo.escobar@usm.cl','+56991230023',1);
INSERT INTO PERSONA VALUES ('21.505.606-4','Fernanda Aguilar',1,4,'fernanda.aguilar@usm.cl','+56991230024',1);
INSERT INTO PERSONA VALUES ('18.606.707-5','Álvaro Contreras',2,5,'alvaro.contreras@usm.cl','+56991230025',1);
INSERT INTO PERSONA VALUES ('19.707.808-6','Lucas Navarro',3,1,'lucas.navarro@usm.cl','+56991230026',1);
INSERT INTO PERSONA VALUES ('20.808.909-7','Sofía Méndez',6,2,'sofia.mendez@usm.cl','+56991230027',1);
INSERT INTO PERSONA VALUES ('21.909.010-8','Gabriel Cárdenas',4,3,'gabriel.cardenas@usm.cl','+56991230028',1);
INSERT INTO PERSONA VALUES ('22.010.111-9','Martina Sepúlveda',8,4,'martina.sepulveda@usm.cl','+56991230029',1);
INSERT INTO PERSONA VALUES ('18.111.212-0','Pablo Bustos',10,5,'pablo.bustos@usm.cl','+56991230030',1);
INSERT INTO PERSONA VALUES ('19.212.313-1','Josefa Palma',2,1,'josefa.palma@usm.cl','+56991230031',1);
INSERT INTO PERSONA VALUES ('20.313.414-2','Vicente Saavedra',5,2,'vicente.saavedra@usm.cl','+56991230032',1);
INSERT INTO PERSONA VALUES ('21.414.515-3','Florencia Farías',7,3,'florencia.farias@usm.cl','+56991230033',1);
INSERT INTO PERSONA VALUES ('21.515.616-4','Simón Valdés',9,4,'simon.valdes@usm.cl','+56991230034',1);
INSERT INTO PERSONA VALUES ('18.616.717-5','Trinidad Carrasco',11,5,'trinidad.carrasco@usm.cl','+56991230035',1);
INSERT INTO PERSONA VALUES ('19.717.818-6','Emilio Tapia',12,1,'emilio.tapia@usm.cl','+56991230036',1);
INSERT INTO PERSONA VALUES ('20.818.919-7','Amanda Olivares',1,2,'amanda.olivares@usm.cl','+56991230037',1);
INSERT INTO PERSONA VALUES ('21.919.020-8','Bruno Zamora',3,3,'bruno.zamora@usm.cl','+56991230038',1);
INSERT INTO PERSONA VALUES ('22.020.121-9','Daniela Cuevas',6,4,'daniela.cuevas@usm.cl','+56991230039',1);
INSERT INTO PERSONA VALUES ('18.121.222-0','Esteban Loyola',4,5,'esteban.loyola@usm.cl','+56991230040',1);


INSERT INTO PERSONA VALUES ('7.234.567-8','Héctor Guzmán',3,1,'hector.guzman@usm.cl','+56991230041',2);
INSERT INTO PERSONA VALUES ('8.345.678-9','Patricia Navarrete',5,2,'patricia.navarrete@usm.cl','+56991230042',2);
INSERT INTO PERSONA VALUES ('9.456.789-0','Ricardo Alarcón',7,3,'ricardo.alarcon@usm.cl','+56991230043',2);
INSERT INTO PERSONA VALUES ('10.567.890-1','Verónica Sanhueza',2,4,'veronica.sanhueza@usm.cl','+56991230044',2);
INSERT INTO PERSONA VALUES ('11.678.901-2','Claudio Yáñez',4,5,'claudio.yanez@usm.cl','+56991230045',2);
INSERT INTO PERSONA VALUES ('12.789.012-3','María Eugenia Pino',6,1,'maria.pino@usm.cl','+56991230046',2);
INSERT INTO PERSONA VALUES ('13.890.123-4','Óscar Leiva',8,2,'oscar.leiva@usm.cl','+56991230047',2);
INSERT INTO PERSONA VALUES ('14.901.234-5','Lorena Carvajal',9,3,'lorena.carvajal@usm.cl','+56991230048',2);
INSERT INTO PERSONA VALUES ('15.012.345-6','Eduardo Mardones',10,4,'eduardo.mardones@usm.cl','+56991230049',2);
INSERT INTO PERSONA VALUES ('16.123.456-7','Ximena Henríquez',11,5,'ximena.henriquez@usm.cl','+56991230050',2);
INSERT INTO PERSONA VALUES ('17.234.567-8','Raúl Cofré',12,1,'raul.cofre@usm.cl','+56991230051',2);
INSERT INTO PERSONA VALUES ('18.345.678-9','Teresa Aravena',1,2,'teresa.aravena@usm.cl','+56991230052',2);
INSERT INTO PERSONA VALUES ('6.789.012-3','Sergio Quintana',3,3,'sergio.quintana@usm.cl','+56991230053',2);
INSERT INTO PERSONA VALUES ('7.890.123-4','Gloria Venegas',5,4,'gloria.venegas@usm.cl','+56991230054',2);
INSERT INTO PERSONA VALUES ('8.901.234-5','Hugo Becerra',7,5,'hugo.becerra@usm.cl','+56991230055',2);

INSERT INTO JEFE_CARRERA (Nombre_jefe) VALUES ('Lionel Valenzuela');
INSERT INTO JEFE_CARRERA (Nombre_jefe) VALUES ('Andrea Urrutia');
INSERT INTO JEFE_CARRERA (Nombre_jefe) VALUES ('Claudio Acuña');
INSERT INTO JEFE_CARRERA (Nombre_jefe) VALUES ('José Luis Martí');
INSERT INTO JEFE_CARRERA (Nombre_jefe) VALUES ('Marcelo Villena');
INSERT INTO JEFE_CARRERA (Nombre_jefe) VALUES ('Agustín González');


INSERT INTO COORDINADOR (Nombre_coordinador) VALUES ('Alejandro Fuentes');
INSERT INTO COORDINADOR (Nombre_coordinador) VALUES ('María José Campos');
INSERT INTO COORDINADOR (Nombre_coordinador) VALUES ('Ricardo Morales');
INSERT INTO COORDINADOR (Nombre_coordinador) VALUES ('Carolina Pizarro');
INSERT INTO COORDINADOR (Nombre_coordinador) VALUES ('Felipe Contreras');
INSERT INTO COORDINADOR (Nombre_coordinador) VALUES ('Daniela Espinoza');
INSERT INTO COORDINADOR (Nombre_coordinador) VALUES ('Jorge Valenzuela');


INSERT INTO POSTULACION VALUES (
'LKF1A9XQW8ZP3M2N5R7T', NULL, '2025-11-10',
'Sistema de reciclaje inteligente en campus',
'Implementar estaciones inteligentes para mejorar el reciclaje en campus universitarios',
'Desarrollo de contenedores con sensores IoT que separan residuos automáticamente',
'Reducción del 30% de residuos mal clasificados en el campus',
25000000, '76.123.456-7', 1, 1, 5, 5, 1, 2, 3
);

INSERT INTO POSTULACION VALUES (
'QW9E3RTY6U1IOP4ASD8F', NULL, '2026-01-15',
'Plataforma de gestión de transporte estudiantil',
'Optimizar los tiempos de traslado de estudiantes mediante rutas eficientes',
'Desarrollo de una app que sugiere rutas en tiempo real usando datos de tráfico',
'Disminución de un 20% en tiempos de traslado promedio',
18000000, '77.234.567-8', 2, 2, 13, 13, 1, 1, 4
);

INSERT INTO POSTULACION VALUES (
'ZXCVB7N8M1Q2W3E4R5TY', NULL, '2025-08-03',
'Sistema de monitoreo energético en edificios',
'Reducir el consumo energético en infraestructura universitaria',
'Instalación de sensores para medir consumo y plataforma de análisis',
'Ahorro energético del 15% en edificios monitoreados',
32000000, '78.345.678-9', 3, 1, 7, 7, 1, 3, 2
);

INSERT INTO POSTULACION VALUES (
'PLM9OK8IJ7UH6YG5T4RF', NULL, '2026-02-20',
'Mejora de sistema ERP existente',
'Optimizar procesos administrativos mediante actualización de ERP',
'Refactorización de módulos críticos y mejora de interfaz de usuario',
'Reducción de tiempos de gestión en un 25%',
12000000, '79.456.789-K', 4, 3, 6, 6, 2, 4, 1
);

INSERT INTO POSTULACION VALUES (
'ASDFG1H2J3K4L5Q6W7ER', NULL, '2025-09-12',
'Aplicación de salud mental para estudiantes',
'Apoyar el bienestar emocional mediante herramientas digitales',
'Desarrollo de app con seguimiento emocional y contacto con profesionales',
'Aumento en acceso a apoyo psicológico en un 40%',
22000000, '75.567.890-1', 5, 1, 13, 13, 1, 5, 2
);

INSERT INTO POSTULACION VALUES (
'MNBV2CX3ZAQ4WSX5EDC6', NULL, '2025-07-25',
'Sistema de riego automatizado agrícola',
'Optimizar uso de agua en cultivos mediante automatización',
'Sensores de humedad conectados a sistema de riego inteligente',
'Reducción del consumo de agua en un 35%',
27000000, '74.678.901-2', 1, 2, 8, 8, 1, 6, 5
);

INSERT INTO POSTULACION VALUES (
'YHN7UJM8IK9OL0P1Q2AZ', NULL, '2026-03-05',
'Plataforma e-learning mejorada',
'Mejorar la experiencia de aprendizaje virtual',
'Incorporación de analíticas de aprendizaje y contenido interactivo',
'Aumento del rendimiento académico en cursos online',
15000000, '76.123.456-7', 2, 1, 9, 9, 2, 2, 6
);

INSERT INTO POSTULACION VALUES (
'WSX3EDC4RFV5TGB6YHN7', NULL, '2025-10-18',
'Sistema de gestión de residuos industriales',
'Reducir impacto ambiental de residuos industriales',
'Implementación de software de trazabilidad de residuos',
'Disminución de residuos peligrosos no tratados',
35000000, '77.234.567-8', 3, 4, 2, 2, 1, 3, 4
);

INSERT INTO POSTULACION VALUES (
'IK8OL9P0Q1AZ2SX3EDC4', NULL, '2026-02-01',
'App de seguridad en campus',
'Mejorar la seguridad mediante reportes en tiempo real',
'Aplicación móvil para reportar incidentes con geolocalización',
'Reducción de incidentes no reportados en un 50%',
14000000, '78.345.678-9', 4, 2, 5, 5, 1, 1, 3
);

INSERT INTO POSTULACION VALUES (
'RFV5TGB6YHN7UJM8IK9O', NULL, '2025-12-05',
'Optimización de logística en bodegas',
'Reducir tiempos de despacho en centros de distribución',
'Implementación de sistema de picking automatizado',
'Disminución del tiempo de despacho en un 30%',
28000000, '79.456.789-K', 5, 1, 13, 13, 1, 4, 2
);

INSERT INTO POSTULACION VALUES (
'ZXC1VBN2MAS3DFG4HJK5', NULL, '2025-11-20',
'Sistema inteligente de gestión de residuos urbanos',
'Mejorar eficiencia en recolección de basura',
'Implementación de sensores IoT en contenedores',
'Reducción de costos operativos en un 25%',
28000000, '78.345.678-9', 1, 2, 6, 6, 1, 3, 4
);

INSERT INTO POSTULACION VALUES (
'QAZ9WSX8EDC7RFV6TGB5', NULL, '2025-10-15',
'Plataforma de monitoreo energético en edificios',
'Optimizar consumo energético en infraestructura institucional',
'Desarrollo de dashboard de consumo en tiempo real',
'Ahorro energético del 20% anual',
30000000, '77.234.567-8', 2, 2, 8, 2, 1, 2, 5
);


INSERT INTO POSTULACION VALUES (
'PLK4OIJ5UHY6TGB7RFV8', NULL, '2025-09-10',
'Sistema predictivo de fallas en maquinaria industrial',
'Disminuir fallas inesperadas en equipos productivos',
'Aplicación de machine learning para mantenimiento predictivo',
'Reducción de fallas en un 35%',
32000000, '76.123.456-7', 3, 1, 1, 1, 2, 1, 2
);


-- 1) Sistema de reciclaje inteligente
INSERT INTO CRONOGRAMA (Etapa, Plazos_Semanas, Entregable, ID_postulacion) VALUES
('Diagnostico reciclaje', 6, 'Informe de puntos críticos y oportunidades', 'LKF1A9XQW8ZP3M2N5R7T'),
('Desarrollo prototipo', 12, 'Prototipo funcional de contenedor inteligente', 'LKF1A9XQW8ZP3M2N5R7T'),
('Implementacion piloto', 8, 'Sistema instalado en campus y reporte de resultados', 'LKF1A9XQW8ZP3M2N5R7T');

-- 2) Transporte estudiantil
INSERT INTO CRONOGRAMA VALUES
(NULL, 'Analisis de rutas', 5, 'Mapa de rutas optimizadas', 'QW9E3RTY6U1IOP4ASD8F'),
(NULL, 'Desarrollo aplicacion', 10, 'App funcional con geolocalización', 'QW9E3RTY6U1IOP4ASD8F'),
(NULL, 'Pruebas y ajustes', 6, 'Reporte de rendimiento y mejoras', 'QW9E3RTY6U1IOP4ASD8F');

-- 3) Monitoreo energético
INSERT INTO CRONOGRAMA VALUES
(NULL, 'Levantamiento datos', 14, 'Registro inicial de consumo energético', 'ZXCVB7N8M1Q2W3E4R5TY'),
(NULL, 'Instalacion sensores', 11, 'Sensores instalados y operativos', 'ZXCVB7N8M1Q2W3E4R5TY'),
(NULL, 'Analisis consumo', 12, 'Informe de optimización energética', 'ZXCVB7N8M1Q2W3E4R5TY');

-- 4) ERP existente
INSERT INTO CRONOGRAMA VALUES
(NULL, 'Revision sistema actual', 4, 'Diagnóstico de fallas y mejoras', 'PLM9OK8IJ7UH6YG5T4RF'),
(NULL, 'Refactorizacion modulos', 9, 'Módulos optimizados', 'PLM9OK8IJ7UH6YG5T4RF'),
(NULL, 'Implementacion mejoras', 6, 'Sistema actualizado en producción', 'PLM9OK8IJ7UH6YG5T4RF');

-- 5) Salud mental
INSERT INTO CRONOGRAMA VALUES
(NULL, 'Investigacion usuarios', 5, 'Perfil de necesidades estudiantiles', 'ASDFG1H2J3K4L5Q6W7ER'),
(NULL, 'Desarrollo app', 11, 'Aplicación funcional', 'ASDFG1H2J3K4L5Q6W7ER'),
(NULL, 'Validacion clinica', 7, 'Informe de efectividad y uso', 'ASDFG1H2J3K4L5Q6W7ER');

-- 6) Riego automatizado
INSERT INTO CRONOGRAMA VALUES
(NULL, 'Estudio terreno', 12, 'Análisis de condiciones agrícolas', 'MNBV2CX3ZAQ4WSX5EDC6'),
(NULL, 'Implementacion sensores', 10, 'Sistema de sensores activo', 'MNBV2CX3ZAQ4WSX5EDC6'),
(NULL, 'Optimización riego', 16, 'Reporte de ahorro hídrico', 'MNBV2CX3ZAQ4WSX5EDC6');

-- 7) E-learning
INSERT INTO CRONOGRAMA VALUES
(NULL, 'Analisis plataforma', 4, 'Informe de mejoras necesarias', 'YHN7UJM8IK9OL0P1Q2AZ'),
(NULL, 'Desarrollo funcionalidades', 9, 'Nuevas herramientas implementadas', 'YHN7UJM8IK9OL0P1Q2AZ'),
(NULL, 'Evaluacion usuarios', 6, 'Resultados de experiencia de usuario', 'YHN7UJM8IK9OL0P1Q2AZ');

-- 8) Residuos industriales
INSERT INTO CRONOGRAMA VALUES
(NULL, 'Levantamiento procesos', 6, 'Mapa de generación de residuos', 'WSX3EDC4RFV5TGB6YHN7'),
(NULL, 'Desarrollo sistema', 11, 'Software de trazabilidad operativo', 'WSX3EDC4RFV5TGB6YHN7'),
(NULL, 'Implementacion empresa', 9, 'Sistema en funcionamiento', 'WSX3EDC4RFV5TGB6YHN7');

-- 9) Seguridad campus
INSERT INTO CRONOGRAMA VALUES
(NULL, 'Analisis riesgos', 4, 'Informe de zonas críticas', 'IK8OL9P0Q1AZ2SX3EDC4'),
(NULL, 'Desarrollo app seguridad', 9, 'Aplicación con sistema de alertas', 'IK8OL9P0Q1AZ2SX3EDC4'),
(NULL, 'Pruebas en terreno', 6, 'Reporte de incidentes gestionados', 'IK8OL9P0Q1AZ2SX3EDC4');

-- 10) Logística bodegas
INSERT INTO CRONOGRAMA VALUES
(NULL, 'Diagnostico logístico', 5, 'Análisis de procesos actuales', 'RFV5TGB6YHN7UJM8IK9O'),
(NULL, 'Implementacion sistema picking', 10, 'Sistema automatizado activo', 'RFV5TGB6YHN7UJM8IK9O'),
(NULL, 'Optimización operativa', 8, 'Informe de reducción de tiempos', 'RFV5TGB6YHN7UJM8IK9O');


INSERT INTO CRONOGRAMA (Etapa, Plazos_Semanas, Entregable, ID_postulacion) VALUES
('analisis de sistema actual de recoleccion', 4, 'informe de diagnostico operacional', 'ZXC1VBN2MAS3DFG4HJK5');

INSERT INTO CRONOGRAMA (Etapa, Plazos_Semanas, Entregable, ID_postulacion) VALUES
('desarrollo e integracion de sensores IoT', 12, 'prototipo funcional de sensores en contenedores', 'ZXC1VBN2MAS3DFG4HJK5');

INSERT INTO CRONOGRAMA (Etapa, Plazos_Semanas, Entregable, ID_postulacion) VALUES
('implementacion piloto y evaluacion', 8, 'reporte de eficiencia y optimizacion lograda', 'ZXC1VBN2MAS3DFG4HJK5');


INSERT INTO CRONOGRAMA (Etapa, Plazos_Semanas, Entregable, ID_postulacion) VALUES
('levantamiento de requerimientos energeticos', 5, 'documento de variables y puntos de medicion', 'QAZ9WSX8EDC7RFV6TGB5');

INSERT INTO CRONOGRAMA (Etapa, Plazos_Semanas, Entregable, ID_postulacion) VALUES
('desarrollo de plataforma de monitoreo', 10, 'dashboard funcional con datos en tiempo real', 'QAZ9WSX8EDC7RFV6TGB5');

INSERT INTO CRONOGRAMA (Etapa, Plazos_Semanas, Entregable, ID_postulacion) VALUES
('analisis de consumo y optimizacion', 7, 'informe con recomendaciones de ahorro', 'QAZ9WSX8EDC7RFV6TGB5');


INSERT INTO CRONOGRAMA (Etapa, Plazos_Semanas, Entregable, ID_postulacion) VALUES
('recoleccion y preparacion de datos de maquinaria', 6, 'dataset estructurado para analisis', 'PLK4OIJ5UHY6TGB7RFV8');

INSERT INTO CRONOGRAMA (Etapa, Plazos_Semanas, Entregable, ID_postulacion) VALUES
('entrenamiento de modelos predictivos', 12, 'modelo de machine learning entrenado', 'PLK4OIJ5UHY6TGB7RFV8');

INSERT INTO CRONOGRAMA (Etapa, Plazos_Semanas, Entregable, ID_postulacion) VALUES
('validacion e implementacion en entorno real', 9, 'reporte de reduccion de fallas', 'PLK4OIJ5UHY6TGB7RFV8');
-- ===================== 1) RECICLAJE =====================
INSERT INTO PERSONA_POSTULACION VALUES
('20.234.567-2','LKF1A9XQW8ZP3M2N5R7T','desarrollo backend sistema IoT'),
('22.456.789-4','LKF1A9XQW8ZP3M2N5R7T','analisis impacto ambiental residuos'),
('21.345.678-3','LKF1A9XQW8ZP3M2N5R7T','diseño estructura contenedores'),
('20.789.012-7','LKF1A9XQW8ZP3M2N5R7T','integracion sensores mecanicos'),
('19.678.901-6','LKF1A9XQW8ZP3M2N5R7T','analisis fisico separacion residuos'),
('7.234.567-8','LKF1A9XQW8ZP3M2N5R7T','supervision tecnica del sistema'),
('9.456.789-0','LKF1A9XQW8ZP3M2N5R7T','asesoria procesos quimicos reciclaje'),
('11.678.901-2','LKF1A9XQW8ZP3M2N5R7T','evaluacion sensores y medicion');

-- ===================== 2) TRANSPORTE =====================
INSERT INTO PERSONA_POSTULACION VALUES
('20.303.404-2','QW9E3RTY6U1IOP4ASD8F','modelado rutas transporte'),
('20.808.909-7','QW9E3RTY6U1IOP4ASD8F','optimizacion algoritmos rutas'),
('21.890.123-8','QW9E3RTY6U1IOP4ASD8F','analisis matematico trafico'),
('22.901.234-9','QW9E3RTY6U1IOP4ASD8F','levantamiento datos viales'),
('21.333.444-3','QW9E3RTY6U1IOP4ASD8F','analisis materiales transporte'),
('8.345.678-9','QW9E3RTY6U1IOP4ASD8F','direccion desarrollo software'),
('14.901.234-5','QW9E3RTY6U1IOP4ASD8F','asesoria infraestructura vial');

-- ===================== 3) ENERGIA =====================
INSERT INTO PERSONA_POSTULACION VALUES
('21.404.505-3','ZXCVB7N8M1Q2W3E4R5TY','analisis consumo energetico'),
('21.909.010-8','ZXCVB7N8M1Q2W3E4R5TY','modelos fisicos consumo'),
('20.818.919-7','ZXCVB7N8M1Q2W3E4R5TY','integracion sensores energeticos'),
('13.890.123-4','ZXCVB7N8M1Q2W3E4R5TY','modelos matematicos de optimizacion'),
('22.444.555-4','ZXCVB7N8M1Q2W3E4R5TY','dashboard monitoreo energia'),
('18.555.666-5','ZXCVB7N8M1Q2W3E4R5TY','evaluacion impacto ambiental'),
('11.678.901-2','ZXCVB7N8M1Q2W3E4R5TY','supervision medicion energia'),
('16.123.456-7','ZXCVB7N8M1Q2W3E4R5TY','asesoria mecanica sensores'),
('8.901.234-5','ZXCVB7N8M1Q2W3E4R5TY','validacion procesos energeticos');

-- ===================== 4) ERP =====================
INSERT INTO PERSONA_POSTULACION VALUES
('20.313.414-2','PLM9OK8IJ7UH6YG5T4RF','refactorizacion backend'),
('18.616.717-5','PLM9OK8IJ7UH6YG5T4RF','desarrollo modulos sistema'),
('22.444.555-4','PLM9OK8IJ7UH6YG5T4RF','interfaz usuario ERP'),
('19.111.222-1','PLM9OK8IJ7UH6YG5T4RF','optimizacion procesos internos'),
('21.505.606-4','PLM9OK8IJ7UH6YG5T4RF','analisis sistemas existentes'),
('8.345.678-9','PLM9OK8IJ7UH6YG5T4RF','direccion tecnica software'),
('17.234.567-8','PLM9OK8IJ7UH6YG5T4RF','asesoria procesos organizacionales'),
('7.890.123-4','PLM9OK8IJ7UH6YG5T4RF','evaluacion arquitectura sistema');

-- ===================== 5) SALUD MENTAL =====================
INSERT INTO PERSONA_POSTULACION VALUES
('20.222.333-2','ASDFG1H2J3K4L5Q6W7ER','analisis experiencia usuario'),
('21.890.123-8','ASDFG1H2J3K4L5Q6W7ER','modelos estadisticos bienestar'),
('22.010.111-9','ASDFG1H2J3K4L5Q6W7ER','interfaz app emocional'),
('19.666.777-6','ASDFG1H2J3K4L5Q6W7ER','diseño espacios digitales'),
('21.404.505-3','ASDFG1H2J3K4L5Q6W7ER','analisis social usuarios'),
('17.234.567-8','ASDFG1H2J3K4L5Q6W7ER','guia metodologica proyecto'),
('13.890.123-4','ASDFG1H2J3K4L5Q6W7ER','analisis datos bienestar'),
('10.567.890-1','ASDFG1H2J3K4L5Q6W7ER','asesoria diseño experiencia');

-- ===================== 6) RIEGO =====================
INSERT INTO PERSONA_POSTULACION VALUES
('18.567.890-5','MNBV2CX3ZAQ4WSX5EDC6','analisis sistemas aeronauticos sensores'),
('20.789.012-7','MNBV2CX3ZAQ4WSX5EDC6','diseño sistema riego mecanico'),
('18.555.666-5','MNBV2CX3ZAQ4WSX5EDC6','analisis quimico suelo'),
('22.901.234-9','MNBV2CX3ZAQ4WSX5EDC6','levantamiento terreno'),
('19.707.808-6','MNBV2CX3ZAQ4WSX5EDC6','analisis materiales sensores'),
('12.789.012-3','MNBV2CX3ZAQ4WSX5EDC6','supervision sistema mecanico'),
('9.456.789-0','MNBV2CX3ZAQ4WSX5EDC6','asesoria quimica riego'),
('18.345.678-9','MNBV2CX3ZAQ4WSX5EDC6','validacion sistema sensores');

-- ===================== 7) E-LEARNING =====================
INSERT INTO PERSONA_POSTULACION VALUES
('20.234.567-2','YHN7UJM8IK9OL0P1Q2AZ','backend plataforma educativa'),
('20.222.333-2','YHN7UJM8IK9OL0P1Q2AZ','contenido humanistico digital'),
('21.345.678-3','YHN7UJM8IK9OL0P1Q2AZ','diseño interfaz educativa'),
('21.909.010-8','YHN7UJM8IK9OL0P1Q2AZ','analisis fisico interaccion'),
('8.345.678-9','YHN7UJM8IK9OL0P1Q2AZ','direccion desarrollo plataforma'),
('17.234.567-8','YHN7UJM8IK9OL0P1Q2AZ','asesoria pedagogica'),
('13.890.123-4','YHN7UJM8IK9OL0P1Q2AZ','analisis estadistico aprendizaje');

-- ===================== 8) RESIDUOS INDUSTRIALES =====================
INSERT INTO PERSONA_POSTULACION VALUES
('18.555.666-5','WSX3EDC4RFV5TGB6YHN7','analisis quimico residuos'),
('19.707.808-6','WSX3EDC4RFV5TGB6YHN7','propiedades materiales residuos'),
('18.616.717-5','WSX3EDC4RFV5TGB6YHN7','desarrollo software trazabilidad'),
('22.901.234-9','WSX3EDC4RFV5TGB6YHN7','levantamiento procesos industriales'),
('21.890.123-8','WSX3EDC4RFV5TGB6YHN7','modelos matematicos de simulación'),
('21.333.444-3','WSX3EDC4RFV5TGB6YHN7','analisis metalurgico residuos'),
('9.456.789-0','WSX3EDC4RFV5TGB6YHN7','supervision procesos quimicos'),
('6.789.012-3','WSX3EDC4RFV5TGB6YHN7','asesoria materiales'),
('8.901.234-5','WSX3EDC4RFV5TGB6YHN7','validacion quimica sistema');

-- ===================== 9) SEGURIDAD =====================
INSERT INTO PERSONA_POSTULACION VALUES
('20.234.567-2','IK8OL9P0Q1AZ2SX3EDC4','backend sistema seguridad'),
('22.444.555-4','IK8OL9P0Q1AZ2SX3EDC4','interfaz reportes'),
('21.890.123-8','IK8OL9P0Q1AZ2SX3EDC4','modelos matematicos riesgo'),
('22.901.234-9','IK8OL9P0Q1AZ2SX3EDC4','analisis infraestructura'),
('21.909.010-8','IK8OL9P0Q1AZ2SX3EDC4','analisis sensores fisicos'),
('8.345.678-9','IK8OL9P0Q1AZ2SX3EDC4','direccion software'),
('14.901.234-5','IK8OL9P0Q1AZ2SX3EDC4','asesoria seguridad infraestructura'),
('11.678.901-2','IK8OL9P0Q1AZ2SX3EDC4','validacion sensores');

-- ===================== 10) LOGISTICA =====================
INSERT INTO PERSONA_POSTULACION VALUES
('20.789.012-7','RFV5TGB6YHN7UJM8IK9O','optimizacion mecanica picking'),
('21.890.123-8','RFV5TGB6YHN7UJM8IK9O','modelos matematicos logistica'),
('22.901.234-9','RFV5TGB6YHN7UJM8IK9O','analisis obras civiles bodega'),
('20.303.404-2','RFV5TGB6YHN7UJM8IK9O','procesos mecanicos'),
('21.333.444-3','RFV5TGB6YHN7UJM8IK9O','analisis materiales'),
('12.789.012-3','RFV5TGB6YHN7UJM8IK9O','supervision mecanica'),
('14.901.234-5','RFV5TGB6YHN7UJM8IK9O','asesoria infraestructura'),
('13.890.123-4','RFV5TGB6YHN7UJM8IK9O','analisis optimizacion');

-- 11

INSERT INTO PERSONA_POSTULACION VALUES ('20.234.567-2','ZXC1VBN2MAS3DFG4HJK5','desarrollo de dashboard de monitoreo de contenedores');
INSERT INTO PERSONA_POSTULACION VALUES ('21.345.678-3','ZXC1VBN2MAS3DFG4HJK5','diseño de infraestructura urbana para ubicacion de sensores');
INSERT INTO PERSONA_POSTULACION VALUES ('22.456.789-4','ZXC1VBN2MAS3DFG4HJK5','analisis de impacto ambiental del sistema');
INSERT INTO PERSONA_POSTULACION VALUES ('20.789.012-7','ZXC1VBN2MAS3DFG4HJK5','integracion de hardware y sensores IoT');
INSERT INTO PERSONA_POSTULACION VALUES ('21.890.123-8','ZXC1VBN2MAS3DFG4HJK5','modelamiento matematico de optimizacion de rutas');

INSERT INTO PERSONA_POSTULACION VALUES ('7.234.567-8','ZXC1VBN2MAS3DFG4HJK5','supervision tecnica de sensores y materiales');
INSERT INTO PERSONA_POSTULACION VALUES ('8.345.678-9','ZXC1VBN2MAS3DFG4HJK5','asesoria en desarrollo de plataforma informatica');
INSERT INTO PERSONA_POSTULACION VALUES ('9.456.789-0','ZXC1VBN2MAS3DFG4HJK5','guia en evaluacion ambiental del proyecto');

-- 12 

INSERT INTO PERSONA_POSTULACION VALUES ('22.444.555-4','QAZ9WSX8EDC7RFV6TGB5','desarrollo de interfaz de usuario para monitoreo energetico');
INSERT INTO PERSONA_POSTULACION VALUES ('18.555.666-5','QAZ9WSX8EDC7RFV6TGB5','analisis de eficiencia energetica en sistemas quimicos');
INSERT INTO PERSONA_POSTULACION VALUES ('20.777.888-7','QAZ9WSX8EDC7RFV6TGB5','procesamiento de datos de consumo energetico');
INSERT INTO PERSONA_POSTULACION VALUES ('21.888.999-8','QAZ9WSX8EDC7RFV6TGB5','simulacion de comportamiento energetico');
INSERT INTO PERSONA_POSTULACION VALUES ('20.808.909-7','QAZ9WSX8EDC7RFV6TGB5','desarrollo de backend para almacenamiento de datos');

INSERT INTO PERSONA_POSTULACION VALUES ('13.890.123-4','QAZ9WSX8EDC7RFV6TGB5','orientacion en modelamiento matematico');
INSERT INTO PERSONA_POSTULACION VALUES ('12.789.012-3','QAZ9WSX8EDC7RFV6TGB5','supervision en sistemas mecanicos y consumo');
INSERT INTO PERSONA_POSTULACION VALUES ('8.901.234-5','QAZ9WSX8EDC7RFV6TGB5','asesoria en eficiencia energetica aplicada');

-- 13

INSERT INTO PERSONA_POSTULACION VALUES ('19.123.456-1','PLK4OIJ5UHY6TGB7RFV8','analisis de datos historicos de fallas');
INSERT INTO PERSONA_POSTULACION VALUES ('19.678.901-6','PLK4OIJ5UHY6TGB7RFV8','procesamiento de señales de sensores');
INSERT INTO PERSONA_POSTULACION VALUES ('20.303.404-2','PLK4OIJ5UHY6TGB7RFV8','modelamiento de sistemas mecanicos');
INSERT INTO PERSONA_POSTULACION VALUES ('21.909.010-8','PLK4OIJ5UHY6TGB7RFV8','analisis fisico de comportamiento de maquinaria');
INSERT INTO PERSONA_POSTULACION VALUES ('22.010.111-9','PLK4OIJ5UHY6TGB7RFV8','simulacion de escenarios de falla');

INSERT INTO PERSONA_POSTULACION VALUES ('16.123.456-7','PLK4OIJ5UHY6TGB7RFV8','supervision de sistemas mecanicos');
INSERT INTO PERSONA_POSTULACION VALUES ('11.678.901-2','PLK4OIJ5UHY6TGB7RFV8','asesoria en analisis fisico de fallas');
INSERT INTO PERSONA_POSTULACION VALUES ('6.789.012-3','PLK4OIJ5UHY6TGB7RFV8','guia en comportamiento de materiales');

-- Querys

-- Query 1
/* Entrega un listado general de las postulaciones incluyendo su número, fecha, tipo de iniciativa, 
sede, regiones (de impacto y origen), empresa y presupuesto. Esto se logra vinculando la tabla 
de postulaciones con todas las tablas que contienen informacion relevante de esta mediante inner 
joins para obtener los nombres descriptivos asociados a cada ID. */
SELECT 
    P.Numero_postulacion, 
    P.Fecha_postulacion, 
    T.Tipo_iniciativa,
    S.Nombre_Sede,
    R_I.Nombre_region,
    R_O.Nombre_region,
    E.Nombre_empresa,
    P.Presupuesto
FROM POSTULACION P
INNER JOIN TIPO_INICIATIVA T ON P.ID_tipo_iniciativa = T.ID_tipo
INNER JOIN SEDE S ON P.ID_sede = S.ID_sede
INNER JOIN REGION R_I ON P.ID_region_impacto = R_I.ID_region
INNER JOIN REGION R_O ON P.ID_region_origen = R_O.ID_region
INNER JOIN EMPRESA E ON P.Rut_Empresa = E.Rut_Empresa;


-- Query 2
/* Lista las postulaciones que se ejecutan específicamente en la región de Valparaíso (o la región 
especificada), mostrando la empresa, la sede y el presupuesto. Se logra vinculando las tablas que 
contienen la información necesaria sobre las empresas (como region impacto y origen) y aplicando 
un filtro where sobre el nombre de la región de impacto para limitar los resultados. 
*/
SELECT 
    E.Nombre_empresa,
    S.Nombre_Sede,
    P.Presupuesto
FROM POSTULACION P
INNER JOIN SEDE S ON P.ID_sede = S.ID_sede
INNER JOIN REGION R_I ON P.ID_region_impacto = R_I.ID_region
INNER JOIN EMPRESA E ON P.Rut_Empresa = E.Rut_Empresa
WHERE R_I.Nombre_region = 'Valparaíso';


-- Query 3
/* Cuenta cuántas postulaciones existen para cada tipo de iniciativa (Nueva o Existente). 
Esto se logra mediante un inner join con la tabla de tipos y el uso de contadores 
condicionales (count con case) para clasificar cada registro según su tipo. */

SELECT
    COUNT(CASE WHEN T.Tipo_iniciativa = 'Nueva' THEN 1 END) AS total_nuevas,
    COUNT(CASE WHEN T.Tipo_iniciativa = 'Existente' THEN 1 END) AS existentes
FROM POSTULACION P
INNER JOIN TIPO_INICIATIVA T ON P.ID_tipo_iniciativa = T.ID_tipo;


-- Query 4
/* Entrega la información detallada del equipo de trabajo (rut, nombre, cargo, sede, email y rol) 
que se especifique según la ID_postulación o Numero de postulación. Esto se consigue vinculando el campo 
Rut_Persona de la tabla intermedia de integrantes el mismo campo de la tabla PERSONA */
SELECT
    PP.RUT_Persona,
    P.Nombre,
    C.Nombre_cargo,
    S.Nombre_sede,
    P.eMail,
    PP.Rol
FROM PERSONA_POSTULACION PP
INNER JOIN PERSONA P ON P.RUT_Persona = PP.RUT_Persona
INNER JOIN CARGO_PERSONA C ON C.ID_cargo = P.ID_cargo 
INNER JOIN SEDE S ON S.ID_sede = P.ID_sede
INNER JOIN POSTULACION PO ON PO.ID_postulacion = PP.ID_postulacion
WHERE PP.ID_postulacion = 'ASDFG1H2J3K4L5Q6W7ER' OR PO.Numero_postulacion = 5;


-- Query 5
/* Muestra el tamaño de las empresas, su estado de convenio y la cantidad total de postulaciones 
asociadas, ordenadas de mayor a menor actividad. Se utiliza un left join para no excluir a 
las empresas sin postulaciones y un group by para totalizar los registros por empresa.
En el pdf no especificaba listar el nombre asi que no se incluye
 */
SELECT
    T.Nombre_tamano,
    E.Convenio_USM,
    COUNT(P.ID_postulacion) AS Total_Postulaciones
FROM EMPRESA E
INNER JOIN TAMANO_EMPRESA T ON E.ID_tamano = T.ID_tamano
LEFT JOIN POSTULACION P ON E.Rut_Empresa = P.Rut_Empresa
GROUP BY 
    T.Nombre_tamano, 
    E.Convenio_USM
ORDER BY Total_Postulaciones DESC;


-- Query 6
/* Lista las postulaciones cuyo presupuesto es mayor al promedio general de todas las registradas. 
Para ello se usa una subconsulta select dentro el filtro where, la cual calcula el 
promedio global antes de comparar cada fila individualmente. */
SELECT
    P.Numero_postulacion,
    E.Nombre_empresa,
    P.Presupuesto
FROM POSTULACION P
INNER JOIN EMPRESA E ON E.Rut_Empresa = P.Rut_Empresa
WHERE P.Presupuesto > (SELECT AVG(Presupuesto) FROM POSTULACION);


-- Query 7
/* Entrega la cantidad de profesores y estudiantes por cada postulación, generando una fila 
distinta para cada cargo. Esto se hace utilizando grpup by con el número de postulación 
y el nombre del cargo, lo que permite realizar el conteo separado por cada tipo de integrante. */
SELECT
    P.Numero_postulacion,
    C.Nombre_cargo,
    COUNT(PP.Rut_Persona) AS Cantidad_integrantes
FROM POSTULACION P
INNER JOIN PERSONA_POSTULACION PP ON PP.ID_postulacion = P.ID_postulacion
INNER JOIN PERSONA PE ON PP.RUT_Persona = PE.RUT_Persona
INNER JOIN CARGO_PERSONA C ON C.ID_cargo = PE.ID_cargo
GROUP BY 
    P.Numero_postulacion, 
    C.Nombre_cargo
ORDER BY P.Numero_postulacion;


-- Query 8
/* Identifica las postulaciones que no cumplen con el equipo mínimo de 5 estudiantes o 3 profesores. 
Para ello se agrupa por postulación y usando contadores condicionales para obtener los 
totales de cada cargo en una misma fila, filtrando el resultado final con having. */
SELECT 
    P.Numero_postulacion,
    COUNT(CASE WHEN C.Nombre_cargo = 'Estudiante' THEN 1 END) AS num_estudiantes,
    COUNT(CASE WHEN C.Nombre_cargo = 'Profesor' THEN 1 END) AS num_profesores
FROM POSTULACION P
INNER JOIN PERSONA_POSTULACION PP ON P.ID_postulacion = PP.ID_postulacion
INNER JOIN PERSONA PE ON PP.Rut_Persona = PE.Rut_Persona
INNER JOIN CARGO_PERSONA C ON PE.ID_cargo = C.ID_cargo
GROUP BY P.Numero_postulacion
HAVING num_estudiantes < 5 OR num_profesores < 3;


-- Query 9
/* Lista las empresas registradas que actualmente no tienen ninguna postulación asociada. 
Se logra mediante un left join (naturalmente ya que se quieren considerar las sin coincidencias) 
hacia la tabla de postulaciones y un filtro where que selecciona únicamente los registros donde 
no existe una coincidencia (valores nulos). */
SELECT
    E.Nombre_empresa,
    E.Rut_Empresa,
    T.Nombre_tamano
FROM EMPRESA E
INNER JOIN TAMANO_EMPRESA T ON E.ID_tamano = T.ID_tamano
LEFT JOIN POSTULACION P ON E.Rut_Empresa = P.Rut_Empresa
WHERE P.Rut_Empresa IS NULL;


-- Query 10
/* Entrega el numero de postulación, ID de postulación, la suma de cronogramas asociados a esta 
postulación (numero de etapas) y la sumatoria de las semanas totales. Esto se logra vinculando 
las tablas mediante un inner join y usando group by para agrupar en un mismo registro los 
cronogramas de cada postulación, aplicando después el filtro having para las que superan 36 semanas. */
SELECT
    P.Numero_postulacion,
    P.ID_postulacion,
    COUNT(C.ID_postulacion) AS Total_Etapas,
    SUM(C.Plazos_Semanas) AS Total_Semanas
FROM POSTULACION P
INNER JOIN CRONOGRAMA C ON C.ID_postulacion = P.ID_postulacion
GROUP BY 
    P.Numero_postulacion, 
    P.ID_postulacion
HAVING SUM(C.Plazos_Semanas) > 36;