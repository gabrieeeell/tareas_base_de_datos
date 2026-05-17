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
	ID_departamento INT NOT NULL AUTO_INCREMENT,
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
    ID_jefe INT NOT NULL AUTO_INCREMENT,
    Nombre_jefe VARCHAR(50) NOT NULL,
    PRIMARY KEY (ID_jefe)
) ENGINE=InnoDB;

CREATE TABLE COORDINADOR (
    ID_coordinador INT NOT NULL AUTO_INCREMENT,
    rut_coordinador VARCHAR(20) UNIQUE NOT NULL,
    Nombre_coordinador VARCHAR(50) NOT NULL,
    PRIMARY KEY (ID_coordinador)
) ENGINE=InnoDB;

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

INSERT INTO JEFE_CARRERA (ID_jefe, Nombre_jefe) VALUES (0, 'Vacío');
INSERT INTO JEFE_CARRERA (ID_jefe, Nombre_jefe) VALUES (1, 'Lionel Valenzuela');
INSERT INTO JEFE_CARRERA (ID_jefe, Nombre_jefe)VALUES (2, 'Andrea Urrutia');
INSERT INTO JEFE_CARRERA (ID_jefe, Nombre_jefe) VALUES (3, 'Claudio Acuña');
INSERT INTO JEFE_CARRERA (ID_jefe, Nombre_jefe) VALUES (4, 'José Luis Martí');
INSERT INTO JEFE_CARRERA (ID_jefe, Nombre_jefe) VALUES (5, 'Marcelo Villena');
INSERT INTO JEFE_CARRERA (ID_jefe, Nombre_jefe) VALUES (6, 'Agustín González');


INSERT INTO COORDINADOR (ID_coordinador, Nombre_coordinador, rut_coordinador) VALUES (0, 'Sin Coordinador', '22.222.222-0');
INSERT INTO COORDINADOR (Nombre_coordinador, rut_coordinador) VALUES ('Alejandro Fuentes', '22.222.222-1');
INSERT INTO COORDINADOR (Nombre_coordinador, rut_coordinador) VALUES ('María José Campos','22.222.222-2');
INSERT INTO COORDINADOR (Nombre_coordinador, rut_coordinador) VALUES ('Ricardo Morales','22.222.222-3');
INSERT INTO COORDINADOR (Nombre_coordinador, rut_coordinador) VALUES ('Carolina Pizarro','22.222.222-4');
INSERT INTO COORDINADOR (Nombre_coordinador, rut_coordinador) VALUES ('Felipe Contreras','22.222.222-5');
INSERT INTO COORDINADOR (Nombre_coordinador, rut_coordinador) VALUES ('Daniela Espinoza','22.222.222-6');
INSERT INTO COORDINADOR (Nombre_coordinador, rut_coordinador) VALUES ('Jorge Valenzuela','22.222.222-7');


INSERT INTO POSTULACION VALUES (
'MartinGabriel-1', NULL, '2025-11-10',
'Sistema de reciclaje inteligente en campus',
'Implementar estaciones inteligentes para mejorar el reciclaje en campus universitarios',
'Desarrollo de contenedores con sensores IoT que separan residuos automáticamente',
'Reducción del 30% de residuos mal clasificados en el campus',
25000000, '76.123.456-7', 1, 1, 5, 5, 1, 2, 3,NULL
);

INSERT INTO POSTULACION VALUES (
'MartinGabriel-2', NULL, '2026-01-15',
'Plataforma de gestión de transporte estudiantil',
'Optimizar los tiempos de traslado de estudiantes mediante rutas eficientes',
'Desarrollo de una app que sugiere rutas en tiempo real usando datos de tráfico',
'Disminución de un 20% en tiempos de traslado promedio',
18000000, '77.234.567-8', 2, 2, 13, 13, 1, 1, 4,NULL
);

INSERT INTO POSTULACION VALUES (
'MartinGabriel-3', NULL, '2025-08-03',
'Sistema de monitoreo energético en edificios',
'Reducir el consumo energético en infraestructura universitaria',
'Instalación de sensores para medir consumo y plataforma de análisis',
'Ahorro energético del 15% en edificios monitoreados',
32000000, '78.345.678-9', 3, 1, 7, 7, 1, 3, 2,NULL
);

INSERT INTO POSTULACION VALUES (
'MartinGabriel-4', NULL, '2026-02-20',
'Mejora de sistema ERP existente',
'Optimizar procesos administrativos mediante actualización de ERP',
'Refactorización de módulos críticos y mejora de interfaz de usuario',
'Reducción de tiempos de gestión en un 25%',
12000000, '79.456.789-K', 4, 3, 6, 6, 2, 4, 1,NULL
);

INSERT INTO POSTULACION VALUES (
'MartinGabriel-5', NULL, '2025-09-12',
'Aplicación de salud mental para estudiantes',
'Apoyar el bienestar emocional mediante herramientas digitales',
'Desarrollo de app con seguimiento emocional y contacto con profesionales',
'Aumento en acceso a apoyo psicológico en un 40%',
22000000, '75.567.890-1', 5, 1, 13, 13, 1, 5, 2,NULL
);

INSERT INTO POSTULACION VALUES (
'MartinGabriel-6', NULL, '2025-07-25',
'Sistema de riego automatizado agrícola',
'Optimizar uso de agua en cultivos mediante automatización',
'Sensores de humedad conectados a sistema de riego inteligente',
'Reducción del consumo de agua en un 35%',
27000000, '74.678.901-2', 1, 2, 8, 8, 1, 6, 5,NULL
);

INSERT INTO POSTULACION VALUES (
'MartinGabriel-7', NULL, '2026-03-05',
'Plataforma e-learning mejorada',
'Mejorar la experiencia de aprendizaje virtual',
'Incorporación de analíticas de aprendizaje y contenido interactivo',
'Aumento del rendimiento académico en cursos online',
15000000, '76.123.456-7', 2, 1, 9, 9, 2, 2, 6,NULL
);

INSERT INTO POSTULACION VALUES (
'MartinGabriel-8', NULL, '2025-10-18',
'Sistema de gestión de residuos industriales',
'Reducir impacto ambiental de residuos industriales',
'Implementación de software de trazabilidad de residuos',
'Disminución de residuos peligrosos no tratados',
35000000, '77.234.567-8', 3, 4, 2, 2, 1, 3, 4,NULL
);

INSERT INTO POSTULACION VALUES (
'MartinGabriel-9', NULL, '2026-02-01',
'App de seguridad en campus',
'Mejorar la seguridad mediante reportes en tiempo real',
'Aplicación móvil para reportar incidentes con geolocalización',
'Reducción de incidentes no reportados en un 50%',
14000000, '78.345.678-9', 4, 2, 5, 5, 1, 1, 3,NULL
);

INSERT INTO POSTULACION VALUES (
'MartinGabriel-10', NULL, '2025-12-05',
'Optimización de logística en bodegas',
'Reducir tiempos de despacho en centros de distribución',
'Implementación de sistema de picking automatizado',
'Disminución del tiempo de despacho en un 30%',
28000000, '79.456.789-K', 5, 1, 13, 13, 1, 4, 2,NULL
);

INSERT INTO POSTULACION VALUES (
'MartinGabriel-11', NULL, '2025-11-20',
'Sistema inteligente de gestión de residuos urbanos',
'Mejorar eficiencia en recolección de basura',
'Implementación de sensores IoT en contenedores',
'Reducción de costos operativos en un 25%',
28000000, '78.345.678-9', 1, 2, 6, 6, 1, 3, 4,NULL
);

INSERT INTO POSTULACION VALUES (
'MartinGabriel-12', NULL, '2025-10-15',
'Plataforma de monitoreo energético en edificios',
'Optimizar consumo energético en infraestructura institucional',
'Desarrollo de dashboard de consumo en tiempo real',
'Ahorro energético del 20% anual',
30000000, '77.234.567-8', 2, 2, 8, 2, 1, 2, 5,NULL
);


INSERT INTO POSTULACION VALUES (
'MartinGabriel-13', NULL, '2025-09-10',
'Sistema predictivo de fallas en maquinaria industrial',
'Disminuir fallas inesperadas en equipos productivos',
'Aplicación de machine learning para mantenimiento predictivo',
'Reducción de fallas en un 35%',
32000000, '76.123.456-7', 3, 1, 1, 1, 2, 1, 2,NULL
);


-- 1) Sistema de reciclaje inteligente
INSERT INTO CRONOGRAMA (Etapa, Plazos_Semanas, Entregable, ID_postulacion) VALUES
('Diagnostico reciclaje', 6, 'Informe de puntos críticos y oportunidades', 'MartinGabriel-1'),
('Desarrollo prototipo', 12, 'Prototipo funcional de contenedor inteligente', 'MartinGabriel-1'),
('Implementacion piloto', 8, 'Sistema instalado en campus y reporte de resultados', 'MartinGabriel-1');

-- 2) Transporte estudiantil
INSERT INTO CRONOGRAMA VALUES
(NULL, 'Analisis de rutas', 5, 'Mapa de rutas optimizadas', 'MartinGabriel-2'),
(NULL, 'Desarrollo aplicacion', 10, 'App funcional con geolocalización', 'MartinGabriel-2'),
(NULL, 'Pruebas y ajustes', 6, 'Reporte de rendimiento y mejoras', 'MartinGabriel-2');

-- 3) Monitoreo energético
INSERT INTO CRONOGRAMA VALUES
(NULL, 'Levantamiento datos', 14, 'Registro inicial de consumo energético', 'MartinGabriel-3'),
(NULL, 'Instalacion sensores', 11, 'Sensores instalados y operativos', 'MartinGabriel-3'),
(NULL, 'Analisis consumo', 12, 'Informe de optimización energética', 'MartinGabriel-3');

-- 4) ERP existente
INSERT INTO CRONOGRAMA VALUES
(NULL, 'Revision sistema actual', 4, 'Diagnóstico de fallas y mejoras', 'MartinGabriel-4'),
(NULL, 'Refactorizacion modulos', 9, 'Módulos optimizados', 'MartinGabriel-4'),
(NULL, 'Implementacion mejoras', 6, 'Sistema actualizado en producción', 'MartinGabriel-4');

-- 5) Salud mental
INSERT INTO CRONOGRAMA VALUES
(NULL, 'Investigacion usuarios', 5, 'Perfil de necesidades estudiantiles', 'MartinGabriel-5'),
(NULL, 'Desarrollo app', 11, 'Aplicación funcional', 'MartinGabriel-5'),
(NULL, 'Validacion clinica', 7, 'Informe de efectividad y uso', 'MartinGabriel-5');

-- 6) Riego automatizado
INSERT INTO CRONOGRAMA VALUES
(NULL, 'Estudio terreno', 12, 'Análisis de condiciones agrícolas', 'MartinGabriel-6'),
(NULL, 'Implementacion sensores', 10, 'Sistema de sensores activo', 'MartinGabriel-6'),
(NULL, 'Optimización riego', 16, 'Reporte de ahorro hídrico', 'MartinGabriel-6');

-- 7) E-learning
INSERT INTO CRONOGRAMA VALUES
(NULL, 'Analisis plataforma', 4, 'Informe de mejoras necesarias', 'MartinGabriel-7'),
(NULL, 'Desarrollo funcionalidades', 9, 'Nuevas herramientas implementadas', 'MartinGabriel-7'),
(NULL, 'Evaluacion usuarios', 6, 'Resultados de experiencia de usuario', 'MartinGabriel-7');

-- 8) Residuos industriales
INSERT INTO CRONOGRAMA VALUES
(NULL, 'Levantamiento procesos', 6, 'Mapa de generación de residuos', 'MartinGabriel-8'),
(NULL, 'Desarrollo sistema', 11, 'Software de trazabilidad operativo', 'MartinGabriel-8'),
(NULL, 'Implementacion empresa', 9, 'Sistema en funcionamiento', 'MartinGabriel-8');

-- 9) Seguridad campus
INSERT INTO CRONOGRAMA VALUES
(NULL, 'Analisis riesgos', 4, 'Informe de zonas críticas', 'MartinGabriel-9'),
(NULL, 'Desarrollo app seguridad', 9, 'Aplicación con sistema de alertas', 'MartinGabriel-9'),
(NULL, 'Pruebas en terreno', 6, 'Reporte de incidentes gestionados', 'MartinGabriel-9');

-- 10) Logística bodegas
INSERT INTO CRONOGRAMA VALUES
(NULL, 'Diagnostico logístico', 5, 'Análisis de procesos actuales', 'MartinGabriel-10'),
(NULL, 'Implementacion sistema picking', 10, 'Sistema automatizado activo', 'MartinGabriel-10'),
(NULL, 'Optimización operativa', 8, 'Informe de reducción de tiempos', 'MartinGabriel-10');


INSERT INTO CRONOGRAMA (Etapa, Plazos_Semanas, Entregable, ID_postulacion) VALUES
('analisis de sistema actual de recoleccion', 4, 'informe de diagnostico operacional', 'MartinGabriel-11');

INSERT INTO CRONOGRAMA (Etapa, Plazos_Semanas, Entregable, ID_postulacion) VALUES
('desarrollo e integracion de sensores IoT', 12, 'prototipo funcional de sensores en contenedores', 'MartinGabriel-11');

INSERT INTO CRONOGRAMA (Etapa, Plazos_Semanas, Entregable, ID_postulacion) VALUES
('implementacion piloto y evaluacion', 8, 'reporte de eficiencia y optimizacion lograda', 'MartinGabriel-11');

INSERT INTO CRONOGRAMA (Etapa, Plazos_Semanas, Entregable, ID_postulacion) VALUES
('levantamiento de requerimientos energeticos', 5, 'documento de variables y puntos de medicion', 'MartinGabriel-12');

INSERT INTO CRONOGRAMA (Etapa, Plazos_Semanas, Entregable, ID_postulacion) VALUES
('desarrollo de plataforma de monitoreo', 10, 'dashboard funcional con datos en tiempo real', 'MartinGabriel-12');

INSERT INTO CRONOGRAMA (Etapa, Plazos_Semanas, Entregable, ID_postulacion) VALUES
('analisis de consumo y optimizacion', 7, 'informe con recomendaciones de ahorro', 'MartinGabriel-12');

INSERT INTO CRONOGRAMA (Etapa, Plazos_Semanas, Entregable, ID_postulacion) VALUES
('recoleccion y preparacion de datos de maquinaria', 6, 'dataset estructurado para analisis', 'MartinGabriel-13');

INSERT INTO CRONOGRAMA (Etapa, Plazos_Semanas, Entregable, ID_postulacion) VALUES
('entrenamiento de modelos predictivos', 12, 'modelo de machine learning entrenado', 'MartinGabriel-13');

INSERT INTO CRONOGRAMA (Etapa, Plazos_Semanas, Entregable, ID_postulacion) VALUES
('validacion e implementacion en entorno real', 9, 'reporte de reduccion de fallas', 'MartinGabriel-13');

-- ===================== 1) RECICLAJE =====================
INSERT INTO PERSONA_POSTULACION VALUES
('20.234.567-2','MartinGabriel-1','Responsable'),
('22.456.789-4','MartinGabriel-1','analisis impacto ambiental residuos'),
('21.345.678-3','MartinGabriel-1','diseño estructura contenedores'),
('20.789.012-7','MartinGabriel-1','integracion sensores mecanicos'),
('19.678.901-6','MartinGabriel-1','analisis fisico separacion residuos'),
('7.234.567-8','MartinGabriel-1','supervision tecnica del sistema'),
('9.456.789-0','MartinGabriel-1','asesoria procesos quimicos reciclaje'),
('11.678.901-2','MartinGabriel-1','evaluacion sensores y medicion');

-- ===================== 2) TRANSPORTE =====================
INSERT INTO PERSONA_POSTULACION VALUES
('20.303.404-2','MartinGabriel-2','Responsable'),
('20.808.909-7','MartinGabriel-2','optimizacion algoritmos rutas'),
('21.890.123-8','MartinGabriel-2','analisis matematico trafico'),
('22.901.234-9','MartinGabriel-2','levantamiento datos viales'),
('21.333.444-3','MartinGabriel-2','analisis materiales transporte'),
('8.345.678-9','MartinGabriel-2','direccion desarrollo software'),
('14.901.234-5','MartinGabriel-2','asesoria infraestructura vial');

-- ===================== 3) ENERGIA =====================
INSERT INTO PERSONA_POSTULACION VALUES
('21.404.505-3','MartinGabriel-3','Responsable'),
('21.909.010-8','MartinGabriel-3','modelos fisicos consumo'),
('20.818.919-7','MartinGabriel-3','integracion sensores energeticos'),
('13.890.123-4','MartinGabriel-3','modelos matematicos de optimizacion'),
('22.444.555-4','MartinGabriel-3','dashboard monitoreo energia'),
('18.555.666-5','MartinGabriel-3','evaluacion impacto ambiental'),
('11.678.901-2','MartinGabriel-3','supervision medicion energia'),
('16.123.456-7','MartinGabriel-3','asesoria mecanica sensores'),
('8.901.234-5','MartinGabriel-3','validacion procesos energeticos');

-- ===================== 4) ERP =====================
INSERT INTO PERSONA_POSTULACION VALUES
('20.313.414-2','MartinGabriel-4','Responsable'),
('18.616.717-5','MartinGabriel-4','desarrollo modulos sistema'),
('22.444.555-4','MartinGabriel-4','interfaz usuario ERP'),
('19.111.222-1','MartinGabriel-4','optimizacion procesos internos'),
('21.505.606-4','MartinGabriel-4','analisis sistemas existentes'),
('8.345.678-9','MartinGabriel-4','direccion tecnica software'),
('17.234.567-8','MartinGabriel-4','asesoria procesos organizacionales'),
('7.890.123-4','MartinGabriel-4','evaluacion arquitectura sistema');

-- ===================== 5) SALUD MENTAL =====================
INSERT INTO PERSONA_POSTULACION VALUES
('20.222.333-2','MartinGabriel-5','Responsable'),
('21.890.123-8','MartinGabriel-5','modelos estadisticos bienestar'),
('22.010.111-9','MartinGabriel-5','interfaz app emocional'),
('19.666.777-6','MartinGabriel-5','diseño espacios digitales'),
('21.404.505-3','MartinGabriel-5','analisis social usuarios'),
('17.234.567-8','MartinGabriel-5','guia metodologica proyecto'),
('13.890.123-4','MartinGabriel-5','analisis datos bienestar'),
('10.567.890-1','MartinGabriel-5','asesoria diseño experiencia');

-- ===================== 6) RIEGO =====================
INSERT INTO PERSONA_POSTULACION VALUES
('18.567.890-5','MartinGabriel-6','Responsable'),
('20.789.012-7','MartinGabriel-6','diseño sistema riego mecanico'),
('18.555.666-5','MartinGabriel-6','analisis quimico suelo'),
('22.901.234-9','MartinGabriel-6','levantamiento terreno'),
('19.707.808-6','MartinGabriel-6','analisis materiales sensores'),
('12.789.012-3','MartinGabriel-6','supervision sistema mecanico'),
('9.456.789-0','MartinGabriel-6','asesoria quimica riego'),
('18.345.678-9','MartinGabriel-6','validacion sistema sensores');

-- ===================== 7) E-LEARNING =====================
INSERT INTO PERSONA_POSTULACION VALUES
('20.234.567-2','MartinGabriel-7','Responsable'),
('20.222.333-2','MartinGabriel-7','contenido humanistico digital'),
('21.345.678-3','MartinGabriel-7','diseño interfaz educativa'),
('21.909.010-8','MartinGabriel-7','analisis fisico interaccion'),
('8.345.678-9','MartinGabriel-7','direccion desarrollo plataforma'),
('17.234.567-8','MartinGabriel-7','asesoria pedagogica'),
('13.890.123-4','MartinGabriel-7','analisis estadistico aprendizaje');

-- ===================== 8) RESIDUOS INDUSTRIALES =====================
INSERT INTO PERSONA_POSTULACION VALUES
('18.555.666-5','MartinGabriel-8','Responsable'),
('19.707.808-6','MartinGabriel-8','propiedades materiales residuos'),
('18.616.717-5','MartinGabriel-8','desarrollo software trazabilidad'),
('22.901.234-9','MartinGabriel-8','levantamiento procesos industriales'),
('21.890.123-8','MartinGabriel-8','modelos matematicos de simulación'),
('21.333.444-3','MartinGabriel-8','analisis metalurgico residuos'),
('9.456.789-0','MartinGabriel-8','supervision procesos quimicos'),
('6.789.012-3','MartinGabriel-8','asesoria materiales'),
('8.901.234-5','MartinGabriel-8','validacion quimica sistema');

-- ===================== 9) SEGURIDAD =====================
INSERT INTO PERSONA_POSTULACION VALUES
('20.234.567-2','MartinGabriel-9','Responsable'),
('22.444.555-4','MartinGabriel-9','interfaz reportes'),
('21.890.123-8','MartinGabriel-9','modelos matematicos riesgo'),
('22.901.234-9','MartinGabriel-9','analisis infraestructura'),
('21.909.010-8','MartinGabriel-9','analisis sensores fisicos'),
('8.345.678-9','MartinGabriel-9','direccion software'),
('14.901.234-5','MartinGabriel-9','asesoria seguridad infraestructura'),
('11.678.901-2','MartinGabriel-9','validacion sensores');

-- ===================== 10) LOGISTICA =====================
INSERT INTO PERSONA_POSTULACION VALUES
('20.789.012-7','MartinGabriel-10','Responsable'),
('21.890.123-8','MartinGabriel-10','modelos matematicos logistica'),
('22.901.234-9','MartinGabriel-10','analisis obras civiles bodega'),
('20.303.404-2','MartinGabriel-10','procesos mecanicos'),
('21.333.444-3','MartinGabriel-10','analisis materiales'),
('12.789.012-3','MartinGabriel-10','supervision mecanica'),
('14.901.234-5','MartinGabriel-10','asesoria infraestructura'),
('13.890.123-4','MartinGabriel-10','analisis optimizacion');

-- 11

INSERT INTO PERSONA_POSTULACION VALUES ('20.234.567-2','MartinGabriel-11','Responsable');
INSERT INTO PERSONA_POSTULACION VALUES ('21.345.678-3','MartinGabriel-11','diseño de infraestructura urbana para ubicacion de sensores');
INSERT INTO PERSONA_POSTULACION VALUES ('22.456.789-4','MartinGabriel-11','analisis de impacto ambiental del sistema');
INSERT INTO PERSONA_POSTULACION VALUES ('20.789.012-7','MartinGabriel-11','integracion de hardware y sensores IoT');
INSERT INTO PERSONA_POSTULACION VALUES ('21.890.123-8','MartinGabriel-11','modelamiento matematico de optimizacion de rutas');
INSERT INTO PERSONA_POSTULACION VALUES ('7.234.567-8','MartinGabriel-11','supervision tecnica de sensores y materiales');
INSERT INTO PERSONA_POSTULACION VALUES ('8.345.678-9','MartinGabriel-11','asesoria en desarrollo de plataforma informatica');
INSERT INTO PERSONA_POSTULACION VALUES ('9.456.789-0','MartinGabriel-11','guia en evaluacion ambiental del proyecto');

-- 12 

INSERT INTO PERSONA_POSTULACION VALUES ('22.444.555-4','MartinGabriel-12','Responsable');
INSERT INTO PERSONA_POSTULACION VALUES ('18.555.666-5','MartinGabriel-12','analisis de eficiencia energetica en sistemas quimicos');
INSERT INTO PERSONA_POSTULACION VALUES ('20.777.888-7','MartinGabriel-12','procesamiento de datos de consumo energetico');
INSERT INTO PERSONA_POSTULACION VALUES ('21.888.999-8','MartinGabriel-12','simulacion de comportamiento energetico');
INSERT INTO PERSONA_POSTULACION VALUES ('20.808.909-7','MartinGabriel-12','desarrollo de backend para almacenamiento de datos');
INSERT INTO PERSONA_POSTULACION VALUES ('13.890.123-4','MartinGabriel-12','orientacion en modelamiento matematico');
INSERT INTO PERSONA_POSTULACION VALUES ('12.789.012-3','MartinGabriel-12','supervision en sistemas mecanicos y consumo');
INSERT INTO PERSONA_POSTULACION VALUES ('8.901.234-5','MartinGabriel-12','asesoria en eficiencia energetica aplicada');

-- 13

INSERT INTO PERSONA_POSTULACION VALUES ('19.123.456-1','MartinGabriel-13','Responsable');
INSERT INTO PERSONA_POSTULACION VALUES ('19.678.901-6','MartinGabriel-13','procesamiento de señales de sensores');
INSERT INTO PERSONA_POSTULACION VALUES ('20.303.404-2','MartinGabriel-13','modelamiento de sistemas mecanicos');
INSERT INTO PERSONA_POSTULACION VALUES ('21.909.010-8','MartinGabriel-13','analisis fisico de comportamiento de maquinaria');
INSERT INTO PERSONA_POSTULACION VALUES ('22.010.111-9','MartinGabriel-13','simulacion de escenarios de falla');
INSERT INTO PERSONA_POSTULACION VALUES ('16.123.456-7','MartinGabriel-13','supervision de sistemas mecanicos');
INSERT INTO PERSONA_POSTULACION VALUES ('11.678.901-2','MartinGabriel-13','asesoria en analisis fisico de fallas');
INSERT INTO PERSONA_POSTULACION VALUES ('6.789.012-3','MartinGabriel-13','guia en comportamiento de materiales');


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