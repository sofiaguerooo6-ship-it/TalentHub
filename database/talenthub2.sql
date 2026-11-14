-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: talenthub2
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `talenthub2`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `talenthub2` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `talenthub2`;

--
-- Table structure for table `auditoria_estado`
--

DROP TABLE IF EXISTS `auditoria_estado`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auditoria_estado` (
  `id_auditoria` int(11) NOT NULL AUTO_INCREMENT,
  `nro_solicitud` int(11) NOT NULL,
  `cod_estado_anterior` varchar(10) DEFAULT NULL,
  `cod_estado_nuevo` varchar(10) NOT NULL,
  `legajo_responsable` int(11) DEFAULT NULL,
  `fecha_cambio` datetime NOT NULL DEFAULT current_timestamp(),
  `observacion` text DEFAULT NULL,
  PRIMARY KEY (`id_auditoria`),
  KEY `nro_solicitud` (`nro_solicitud`),
  KEY `cod_estado_anterior` (`cod_estado_anterior`),
  KEY `cod_estado_nuevo` (`cod_estado_nuevo`),
  KEY `legajo_responsable` (`legajo_responsable`),
  CONSTRAINT `auditoria_estado_ibfk_1` FOREIGN KEY (`nro_solicitud`) REFERENCES `solicitud_licencia` (`nro_solicitud`),
  CONSTRAINT `auditoria_estado_ibfk_2` FOREIGN KEY (`cod_estado_anterior`) REFERENCES `estado_solicitud` (`cod_estado`),
  CONSTRAINT `auditoria_estado_ibfk_3` FOREIGN KEY (`cod_estado_nuevo`) REFERENCES `estado_solicitud` (`cod_estado`),
  CONSTRAINT `auditoria_estado_ibfk_4` FOREIGN KEY (`legajo_responsable`) REFERENCES `empleado` (`legajo`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auditoria_estado`
--

LOCK TABLES `auditoria_estado` WRITE;
/*!40000 ALTER TABLE `auditoria_estado` DISABLE KEYS */;
INSERT INTO `auditoria_estado` VALUES (1,1,NULL,'APRO',4,'2026-06-27 13:28:33','Solicitud creada'),(2,2,NULL,'APRO',5,'2026-06-27 13:28:33','Solicitud creada'),(3,3,NULL,'APRO',6,'2026-06-27 13:28:33','Solicitud creada'),(4,4,NULL,'APRO',7,'2026-06-27 13:28:33','Solicitud creada'),(5,5,NULL,'APRO',8,'2026-06-27 13:28:33','Solicitud creada'),(6,6,NULL,'PEND',9,'2026-06-27 13:28:33','Solicitud creada'),(7,7,NULL,'APRO',10,'2026-06-27 13:28:33','Solicitud creada'),(8,8,NULL,'APRO',11,'2026-06-27 13:28:33','Solicitud creada'),(9,9,NULL,'RECH',12,'2026-06-27 13:28:33','Solicitud creada'),(10,10,NULL,'APRO',13,'2026-06-27 13:28:33','Solicitud creada'),(11,11,NULL,'APRO',14,'2026-06-27 13:28:33','Solicitud creada'),(12,12,NULL,'APRO',15,'2026-06-27 13:28:33','Solicitud creada'),(13,13,NULL,'APRO',16,'2026-06-27 13:28:33','Solicitud creada'),(14,14,NULL,'PEND',17,'2026-06-27 13:28:33','Solicitud creada'),(15,15,NULL,'APRO',18,'2026-06-27 13:28:33','Solicitud creada'),(16,16,NULL,'APRO',19,'2026-06-27 13:28:33','Solicitud creada'),(17,17,NULL,'APRO',20,'2026-06-27 13:28:33','Solicitud creada'),(18,18,NULL,'APRO',21,'2026-06-27 13:28:33','Solicitud creada'),(19,19,NULL,'RECH',22,'2026-06-27 13:28:33','Solicitud creada'),(20,20,NULL,'APRO',23,'2026-06-27 13:28:33','Solicitud creada'),(21,21,NULL,'APRO',4,'2026-06-27 13:28:33','Solicitud creada'),(22,22,NULL,'PEND',6,'2026-06-27 13:28:33','Solicitud creada'),(23,23,NULL,'APRO',25,'2026-06-27 13:28:33','Solicitud creada'),(24,24,NULL,'APRO',24,'2026-06-27 13:28:33','Solicitud creada'),(25,25,NULL,'APRO',26,'2026-06-27 13:40:24','Solicitud creada'),(26,26,NULL,'APRO',27,'2026-06-27 13:40:24','Solicitud creada'),(27,27,NULL,'PEND',28,'2026-06-27 13:40:24','Solicitud creada'),(28,28,NULL,'APRO',29,'2026-06-27 13:40:24','Solicitud creada'),(29,29,NULL,'APRO',30,'2026-06-27 13:40:24','Solicitud creada'),(30,30,NULL,'APRO',31,'2026-06-27 13:40:24','Solicitud creada'),(31,31,NULL,'APRO',32,'2026-06-27 13:40:24','Solicitud creada'),(32,32,NULL,'APRO',33,'2026-06-27 13:40:24','Solicitud creada'),(33,33,NULL,'RECH',34,'2026-06-27 13:40:24','Solicitud creada'),(34,34,NULL,'APRO',35,'2026-06-27 13:40:24','Solicitud creada'),(35,35,NULL,'CANC',36,'2026-06-27 13:40:24','Solicitud creada'),(36,36,NULL,'APRO',37,'2026-06-27 13:40:24','Solicitud creada'),(37,37,NULL,'APRO',38,'2026-06-27 13:40:24','Solicitud creada'),(38,38,NULL,'PEND',39,'2026-06-27 13:40:24','Solicitud creada'),(39,39,NULL,'APRO',40,'2026-06-27 13:40:24','Solicitud creada'),(40,40,NULL,'APRO',41,'2026-06-27 13:40:24','Solicitud creada'),(41,41,NULL,'APRO',42,'2026-06-27 13:40:24','Solicitud creada'),(42,42,NULL,'APRO',43,'2026-06-27 13:40:24','Solicitud creada'),(43,43,NULL,'APRO',44,'2026-06-27 13:40:24','Solicitud creada'),(44,44,NULL,'APRO',45,'2026-06-27 13:40:24','Solicitud creada'),(45,45,NULL,'APRO',46,'2026-06-27 13:40:24','Solicitud creada'),(46,46,NULL,'APRO',47,'2026-06-27 13:40:24','Solicitud creada'),(47,47,NULL,'APRO',48,'2026-06-27 13:40:24','Solicitud creada'),(48,48,NULL,'PEND',49,'2026-06-27 13:40:24','Solicitud creada'),(49,49,NULL,'APRO',50,'2026-06-27 13:40:24','Solicitud creada'),(50,50,NULL,'PEND',1,'2026-06-30 09:33:45','Solicitud creada'),(56,50,'PEND','APRO',NULL,'2026-06-30 09:45:02',''),(58,50,'APRO','PEND',NULL,'2026-06-30 10:04:31',''),(60,50,'PEND','APRO',NULL,'2026-06-30 10:06:26',''),(61,51,NULL,'PEND',52,'2026-06-30 10:23:51','Solicitud creada'),(63,51,'PEND','APRO',NULL,'2026-06-30 10:25:16',''),(64,48,'PEND','APRO',NULL,'2026-06-30 11:01:35','test admin');
/*!40000 ALTER TABLE `auditoria_estado` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cargo`
--

DROP TABLE IF EXISTS `cargo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cargo` (
  `cod_cargo` varchar(10) NOT NULL,
  `nombre_cargo` varchar(100) NOT NULL,
  `banda_min` decimal(12,2) NOT NULL,
  `banda_max` decimal(12,2) NOT NULL,
  `cod_nivel` varchar(10) NOT NULL,
  PRIMARY KEY (`cod_cargo`),
  KEY `cod_nivel` (`cod_nivel`),
  CONSTRAINT `cargo_ibfk_1` FOREIGN KEY (`cod_nivel`) REFERENCES `nivel_jerarquico` (`cod_nivel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cargo`
--

LOCK TABLES `cargo` WRITE;
/*!40000 ALTER TABLE `cargo` DISABLE KEYS */;
INSERT INTO `cargo` VALUES ('ADM01','Asistente Administrativo',110000.00,190000.00,'ADM'),('ADM02','Recepcionista',100000.00,170000.00,'ADM'),('ADM03','Asistente de RRHH',115000.00,195000.00,'ADM'),('ADM04','Asistente Contable',120000.00,200000.00,'ADM'),('ANL01','Analista de RRHH',150000.00,260000.00,'ANL'),('ANL02','Analista de Sistemas',160000.00,280000.00,'ANL'),('ANL03','Analista Contable',155000.00,270000.00,'ANL'),('ANL04','Analista de Marketing',150000.00,260000.00,'ANL'),('ANL05','Analista de Datos',165000.00,285000.00,'ANL'),('ANL06','Analista Financiero',158000.00,275000.00,'ANL'),('GER01','Director General',800000.00,1500000.00,'GER'),('GER02','Gerente de Recursos Humanos',500000.00,900000.00,'GER'),('GER03','Gerente de Tecnología',550000.00,950000.00,'GER'),('GER04','Gerente Financiero',550000.00,950000.00,'GER'),('GER05','Gerente Comercial',500000.00,900000.00,'GER'),('GER06','Gerente de Operaciones',500000.00,900000.00,'GER'),('JEF01','Jefe de Reclutamiento',300000.00,500000.00,'JEF'),('JEF02','Jefe de Sistemas',320000.00,520000.00,'JEF'),('JEF03','Jefe de Contabilidad',310000.00,510000.00,'JEF'),('JEF04','Jefe de Ventas',300000.00,500000.00,'JEF'),('JEF05','Jefe de Logística',280000.00,480000.00,'JEF'),('JEF06','Jefe de Marketing',290000.00,490000.00,'JEF'),('OPE01','Vendedor Senior',130000.00,220000.00,'OPE'),('OPE02','Vendedor Junior',100000.00,170000.00,'OPE'),('OPE03','Operario de Logística',90000.00,150000.00,'OPE'),('OPE04','Repositor',85000.00,140000.00,'OPE'),('SUP01','Supervisor de Personal',200000.00,350000.00,'SUP'),('SUP02','Supervisor de Proyectos TI',220000.00,370000.00,'SUP'),('SUP03','Supervisor de Ventas',190000.00,340000.00,'SUP'),('SUP04','Supervisor de Operaciones',190000.00,330000.00,'SUP'),('SUP05','Supervisor de Atención',185000.00,320000.00,'SUP'),('TEC01','Desarrollador Backend',170000.00,300000.00,'TEC'),('TEC02','Desarrollador Frontend',165000.00,290000.00,'TEC'),('TEC03','Técnico de Soporte',120000.00,200000.00,'TEC'),('TEC04','Diseñador UX/UI',155000.00,270000.00,'TEC'),('TEC05','Administrador de Base de Datos',175000.00,310000.00,'TEC');
/*!40000 ALTER TABLE `cargo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `departamento`
--

DROP TABLE IF EXISTS `departamento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `departamento` (
  `cod_depto` varchar(10) NOT NULL,
  `nombre_depto` varchar(100) NOT NULL,
  PRIMARY KEY (`cod_depto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departamento`
--

LOCK TABLES `departamento` WRITE;
/*!40000 ALTER TABLE `departamento` DISABLE KEYS */;
INSERT INTO `departamento` VALUES ('FIN','Finanzas y Contabilidad'),('LEG','Legales'),('LOG','Logística'),('MKT','Marketing'),('OPE','Operaciones'),('RRHH','Recursos Humanos'),('SAC','Servicio al Cliente'),('TI','Tecnología e Innovación'),('VEN','Ventas y Comercial');
/*!40000 ALTER TABLE `departamento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `departamento_localidad`
--

DROP TABLE IF EXISTS `departamento_localidad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `departamento_localidad` (
  `cod_depto` varchar(10) NOT NULL,
  `cod_postal` varchar(10) NOT NULL,
  `es_sede_principal` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`cod_depto`,`cod_postal`),
  KEY `cod_postal` (`cod_postal`),
  CONSTRAINT `departamento_localidad_ibfk_1` FOREIGN KEY (`cod_depto`) REFERENCES `departamento` (`cod_depto`),
  CONSTRAINT `departamento_localidad_ibfk_2` FOREIGN KEY (`cod_postal`) REFERENCES `localidad` (`cod_postal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departamento_localidad`
--

LOCK TABLES `departamento_localidad` WRITE;
/*!40000 ALTER TABLE `departamento_localidad` DISABLE KEYS */;
INSERT INTO `departamento_localidad` VALUES ('FIN','1000',1),('LEG','1000',1),('LOG','1000',1),('LOG','2000',0),('MKT','1000',1),('OPE','1000',1),('OPE','5500',0),('RRHH','1000',1),('SAC','1000',1),('SAC','5000',0),('TI','1000',1),('TI','5000',0),('VEN','1000',1),('VEN','2000',0),('VEN','5000',0);
/*!40000 ALTER TABLE `departamento_localidad` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `documento_adjunto`
--

DROP TABLE IF EXISTS `documento_adjunto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `documento_adjunto` (
  `id_doc` int(11) NOT NULL AUTO_INCREMENT,
  `nro_solicitud` int(11) NOT NULL,
  `cod_tipo_doc` varchar(10) NOT NULL,
  `nombre_archivo` varchar(255) NOT NULL,
  `fecha_carga` date NOT NULL,
  PRIMARY KEY (`id_doc`),
  KEY `nro_solicitud` (`nro_solicitud`),
  KEY `cod_tipo_doc` (`cod_tipo_doc`),
  CONSTRAINT `documento_adjunto_ibfk_1` FOREIGN KEY (`nro_solicitud`) REFERENCES `solicitud_licencia` (`nro_solicitud`),
  CONSTRAINT `documento_adjunto_ibfk_2` FOREIGN KEY (`cod_tipo_doc`) REFERENCES `tipo_documento` (`cod_tipo_doc`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documento_adjunto`
--

LOCK TABLES `documento_adjunto` WRITE;
/*!40000 ALTER TABLE `documento_adjunto` DISABLE KEYS */;
INSERT INTO `documento_adjunto` VALUES (1,50,'CMED','certificado_gripe.pdf','2026-06-30'),(2,51,'CMED','certificado_gripe.pdf','2026-06-30');
/*!40000 ALTER TABLE `documento_adjunto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empleado`
--

DROP TABLE IF EXISTS `empleado`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `empleado` (
  `legajo` int(11) NOT NULL AUTO_INCREMENT,
  `dni` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `telefono` varchar(30) DEFAULT NULL,
  `fecha_ingreso` date NOT NULL,
  PRIMARY KEY (`legajo`),
  UNIQUE KEY `dni` (`dni`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empleado`
--

LOCK TABLES `empleado` WRITE;
/*!40000 ALTER TABLE `empleado` DISABLE KEYS */;
INSERT INTO `empleado` VALUES (1,'20111001','Martina','González','mgonzalez@talenthub.com','1134567890','2019-03-01'),(2,'22222002','Carlos','Rodríguez','crodriguez@talenthub.com','1145678901','2018-06-15'),(3,'24333003','Laura','Martínez','lmartinez@talenthub.com','1156789012','2020-01-10'),(4,'26444004','Diego','López','dlopez@talenthub.com','1167890123','2021-04-20'),(5,'28555005','Valentina','Pérez','vperez@talenthub.com','1178901234','2021-07-05'),(6,'30666006','Sebastián','Sánchez','ssanchez@talenthub.com','1189012345','2022-02-14'),(7,'32777007','Florencia','Ramírez','framirez@talenthub.com','1190123456','2022-05-03'),(8,'34888008','Matías','Torres','mtorres@talenthub.com','1101234567','2020-09-11'),(9,'36999009','Camila','Flores','cflores@talenthub.com','1112345678','2023-01-16'),(10,'38000010','Nicolás','Gómez','ngomez@talenthub.com','1123456789','2023-03-22'),(11,'27111011','Ana','Fernández','afernandez@talenthub.com','1134561111','2019-11-01'),(12,'29222012','Lucas','Díaz','ldiaz@talenthub.com','1145672222','2020-04-07'),(13,'31333013','Sofía','Morales','smorales@talenthub.com','1156783333','2021-08-18'),(14,'33444014','Agustín','Romero','aromero@talenthub.com','1167894444','2022-01-09'),(15,'35555015','Lucía','Álvarez','lalvarez@talenthub.com','1178905555','2022-06-27'),(16,'37666016','Tomás','Ruiz','truiz@talenthub.com','1189016666','2023-02-13'),(17,'39777017','Julieta','Herrera','jherrera@talenthub.com','1190127777','2023-05-04'),(18,'21888018','Pablo','Medina','pmedina@talenthub.com','1101238888','2019-07-22'),(19,'23999019','Daniela','Castro','dcastro@talenthub.com','1112349999','2020-10-30'),(20,'25000020','Federico','Vargas','fvargas@talenthub.com','1123450000','2021-12-01'),(21,'26100021','Micaela','Ramos','mramos@talenthub.com','1134560101','2022-03-15'),(22,'28200022','Rodrigo','Gutiérrez','rgutierrez@talenthub.com','1145670202','2022-08-08'),(23,'30300023','Belén','Ortega','bortega@talenthub.com','1156780303','2023-01-03'),(24,'32400024','Hernán','Molina','hmolina@talenthub.com','1167890404','2023-04-17'),(25,'34500025','Natalia','Silva','nsilva@talenthub.com','1178900505','2019-09-09'),(26,'40111026','Ramiro','Acosta','racosta@talenthub.com','1134560026','2022-03-07'),(27,'41222027','Pilar','Benítez','pbenitez@talenthub.com','1145670027','2022-07-11'),(28,'42333028','Ezequiel','Cabrera','ecabrera@talenthub.com','1156780028','2023-01-23'),(29,'38444029','Valeria','Delgado','vdelgado@talenthub.com','1167890029','2021-05-17'),(30,'36555030','Ignacio','Espinosa','iespinosa@talenthub.com','1178900030','2020-11-03'),(31,'34666031','Melina','Figueroa','mfigueroa@talenthub.com','1189010031','2019-08-14'),(32,'32777032','Patricio','Godoy','pgodoy@talenthub.com','1190120032','2022-09-29'),(33,'30888033','Antonella','Ibáñez','aibanez@talenthub.com','1101230033','2023-02-06'),(34,'28999034','Leandro','Juárez','ljuarez@talenthub.com','1112340034','2020-06-22'),(35,'27000035','Romina','Lastra','rlastra@talenthub.com','1123450035','2021-10-08'),(36,'25100036','Facundo','Mansilla','fmansilla@talenthub.com','1134560036','2023-06-19'),(37,'23200037','Gabriela','Navarro','gnavarro@talenthub.com','1145670037','2022-04-25'),(38,'21300038','Maximiliano','Ojeda','mojeda@talenthub.com','1156780038','2019-12-02'),(39,'43400039','Sabrina','Pereyra','spereyra@talenthub.com','1167890039','2024-01-15'),(40,'44500040','Bruno','Quiroga','bquiroga@talenthub.com','1178900040','2024-02-20'),(41,'39600041','Ximena','Reyes','xreyes@talenthub.com','1189010041','2021-03-11'),(42,'37700042','Gonzalo','Salinas','gsalinas@talenthub.com','1190120042','2020-08-27'),(43,'35800043','Luciana','Tapia','ltapia@talenthub.com','1101230043','2022-11-14'),(44,'33900044','Cristian','Urbano','curbano@talenthub.com','1112340044','2023-07-03'),(45,'31000045','Mariana','Vega','mvega@talenthub.com','1123450045','2021-01-19'),(46,'29100046','Alejandro','Aguirre','aaguirre@talenthub.com','1134560046','2020-03-04'),(47,'27200047','Cecilia','Barrios','cbarrios@talenthub.com','1145670047','2024-03-12'),(48,'26300048','Damián','Cáceres','dcaceres@talenthub.com','1156780048','2019-05-28'),(49,'24400049','Eliana','Domínguez','edominguez@talenthub.com','1167890049','2023-08-07'),(50,'22500050','Fernando','Estrada','festrada@talenthub.com','1178900050','2022-10-16'),(51,'40000051','Tomßs','Herrera','therrera@talenthub.com','2610000051','2026-06-27'),(52,'40000052','Valentina','RÝos','vrios@talenthub.com','2610000052','2026-06-30'),(53,'40000053','Camila','Ortiz','cortiz@talenthub.com','2610000053','2026-06-30');
/*!40000 ALTER TABLE `empleado` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = cp850 */ ;
/*!50003 SET character_set_results = cp850 */ ;
/*!50003 SET collation_connection  = cp850_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_desactivar_usuario_al_eliminar_empleado
BEFORE DELETE ON empleado
FOR EACH ROW
BEGIN
  UPDATE usuario
  SET estado     = 0,
      fecha_baja = CURDATE(),
      legajo     = NULL
  WHERE legajo = OLD.legajo;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `empleado_direccion`
--

DROP TABLE IF EXISTS `empleado_direccion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `empleado_direccion` (
  `id_direccion` int(11) NOT NULL AUTO_INCREMENT,
  `legajo` int(11) NOT NULL,
  `cod_postal` varchar(10) NOT NULL,
  `calle` varchar(150) NOT NULL,
  `altura` varchar(10) NOT NULL,
  `es_principal` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_direccion`),
  KEY `legajo` (`legajo`),
  KEY `cod_postal` (`cod_postal`),
  CONSTRAINT `empleado_direccion_ibfk_1` FOREIGN KEY (`legajo`) REFERENCES `empleado` (`legajo`),
  CONSTRAINT `empleado_direccion_ibfk_2` FOREIGN KEY (`cod_postal`) REFERENCES `localidad` (`cod_postal`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empleado_direccion`
--

LOCK TABLES `empleado_direccion` WRITE;
/*!40000 ALTER TABLE `empleado_direccion` DISABLE KEYS */;
INSERT INTO `empleado_direccion` VALUES (1,1,'1000','Av. Corrientes','1234',1),(2,2,'1000','Av. Santa Fe','890',1),(3,3,'5000','Av. Colón','450',1),(4,4,'1000','Calle Florida','222',1),(5,5,'1900','Calle 7','555',1),(6,6,'2000','Bv. Oroño','780',1),(7,7,'5000','Av. Vélez Sársfield','300',1),(8,8,'1000','Av. de Mayo','1500',1),(9,9,'1000','Lavalle','640',1),(10,10,'5500','Av. San Martín','990',1),(11,11,'1000','Talcahuano','320',1),(12,12,'1900','Diagonal 80','100',1),(13,13,'2000','Córdoba','2100',1),(14,14,'1000','Reconquista','458',1),(15,15,'5000','Bv. San Juan','600',1),(16,16,'1000','Av. Rivadavia','3300',1),(17,17,'2000','San Luis','1200',1),(18,18,'1000','Montevideo','750',1),(19,19,'5500','Av. Las Heras','488',1),(20,20,'1000','Suipacha','210',1),(21,21,'1000','Esmeralda','980',1),(22,22,'5000','Obispo Trejo','150',1),(23,23,'1000','Callao','560',1),(24,24,'2000','Mendoza','870',1),(25,25,'1000','Uruguay','430',1),(26,26,'1000','Av. Belgrano','1890',1),(27,27,'5000','Av. Hipólito Yrigoyen','750',1),(28,28,'2000','Maipú','430',1),(29,29,'1000','Tucumán','1100',1),(30,30,'1900','Calle 50','780',1),(31,31,'5000','Av. Rafael Núñez','2300',1),(32,32,'1000','Viamonte','890',1),(33,33,'2000','Italia','1650',1),(34,34,'1000','Av. Entre Ríos','450',1),(35,35,'5500','Av. España','1200',1),(36,36,'1000','Paraguay','670',1),(37,37,'1000','Junín','340',1),(38,38,'5000','Bv. Chacabuco','980',1),(39,39,'1000','Lavalle','1220',1),(40,40,'1000','Av. Pueyrredón','560',1),(41,41,'2000','Zeballos','2200',1),(42,42,'1900','Calle 13','445',1),(43,43,'5000','Duarte Quirós','760',1),(44,44,'1000','Av. Corrientes','3400',1),(45,45,'1000','Bartolomé Mitre','870',1),(46,46,'5500','Av. San Martín','2100',1),(47,47,'1000','Cerrito','690',1),(48,48,'5000','Av. Marcelo T. Alvear','400',1),(49,49,'2000','San Lorenzo','1340',1),(50,50,'1000','Av. Callao','1500',1),(51,51,'5500','Calle Ficticia','151',0),(52,51,'2345','Calle Ficticia','152',1),(53,52,'1900','Calle Ficticia','153',1),(54,53,'5000','Calle Ficticia','154',1),(55,52,'3600','Calle Ficticia','155',0),(56,52,'3600','Calle Ficticia','156',0);
/*!40000 ALTER TABLE `empleado_direccion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estado_solicitud`
--

DROP TABLE IF EXISTS `estado_solicitud`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `estado_solicitud` (
  `cod_estado` varchar(10) NOT NULL,
  `nombre_estado` varchar(50) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`cod_estado`),
  UNIQUE KEY `nombre_estado` (`nombre_estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estado_solicitud`
--

LOCK TABLES `estado_solicitud` WRITE;
/*!40000 ALTER TABLE `estado_solicitud` DISABLE KEYS */;
INSERT INTO `estado_solicitud` VALUES ('APRO','APROBADA','Solicitud aprobada por el supervisor'),('CANC','CANCELADA','Solicitud cancelada por el empleado'),('PEND','PENDIENTE','Solicitud ingresada, pendiente de revisión'),('RECH','RECHAZADA','Solicitud rechazada');
/*!40000 ALTER TABLE `estado_solicitud` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `evaluacion_desempeno`
--

DROP TABLE IF EXISTS `evaluacion_desempeno`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `evaluacion_desempeno` (
  `id_evaluacion` int(11) NOT NULL AUTO_INCREMENT,
  `legajo_evaluado` int(11) NOT NULL,
  `legajo_evaluador` int(11) DEFAULT NULL,
  `periodo` varchar(20) NOT NULL,
  `puntuacion` int(11) NOT NULL CHECK (`puntuacion` between 0 and 100),
  `comentario` text DEFAULT NULL,
  PRIMARY KEY (`id_evaluacion`),
  KEY `legajo_evaluado` (`legajo_evaluado`),
  KEY `legajo_evaluador` (`legajo_evaluador`),
  CONSTRAINT `evaluacion_desempeno_ibfk_1` FOREIGN KEY (`legajo_evaluado`) REFERENCES `empleado` (`legajo`),
  CONSTRAINT `evaluacion_desempeno_ibfk_2` FOREIGN KEY (`legajo_evaluador`) REFERENCES `empleado` (`legajo`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `evaluacion_desempeno`
--

LOCK TABLES `evaluacion_desempeno` WRITE;
/*!40000 ALTER TABLE `evaluacion_desempeno` DISABLE KEYS */;
INSERT INTO `evaluacion_desempeno` VALUES (1,4,1,'2023-S2',4,'Buen desempeño en gestión de legajos y procesos internos.'),(2,5,1,'2023-S2',3,'Cumple con sus tareas. Debe mejorar comunicación escrita.'),(3,6,2,'2023-S2',5,'Excelente desarrollador, entrega en tiempo y forma.'),(4,7,2,'2023-S2',4,'Muy buena calidad en el diseño frontend.'),(5,8,2,'2023-S2',4,'Buen analista, proactivo en la resolución de problemas.'),(6,9,2,'2023-S2',3,'Nuevo en el equipo, aprendizaje acelerado.'),(7,10,3,'2023-S2',5,'Superó los objetivos de ventas del semestre.'),(8,11,1,'2023-S2',5,'Impecable gestión contable, sin errores durante el período.'),(9,12,1,'2023-S2',4,'Buen soporte al área financiera.'),(10,13,3,'2023-S2',4,'Buenas habilidades de negociación y cierre.'),(11,14,3,'2023-S2',3,'En proceso de desarrollo, promisorio.'),(12,15,3,'2023-S2',5,'Excelente campaña de marketing digital ejecutada.'),(13,18,1,'2023-S2',5,'Análisis financiero muy preciso. Muy recomendable.'),(14,20,3,'2023-S2',4,'Diseños creativos y alineados con la identidad de marca.'),(15,21,2,'2023-S2',4,'Muy buen trabajo con los datos del sistema.'),(16,25,2,'2023-S2',5,'DBA de referencia en el área, gestión impecable.'),(17,4,1,'2024-S1',5,'Ascenso merecido. Excelente gestión del equipo RRHH.'),(18,6,2,'2024-S1',5,'Lideró la migración del sistema con éxito.'),(19,10,3,'2024-S1',4,'Sigue superando metas comerciales.'),(20,11,1,'2024-S1',5,'Rol clave en el cierre contable anual.'),(21,13,3,'2024-S1',5,'Mejor vendedora del semestre.'),(22,15,3,'2024-S1',4,'Lanzamiento exitoso de nueva campaña.'),(23,8,2,'2024-S1',5,'Implementó nuevo sistema de monitoreo.'),(24,25,2,'2024-S1',5,'Optimizó queries críticas reduciendo tiempos a la mitad.'),(25,26,3,'2023-S2',4,'Buen desempeño comercial, cumple cuotas.'),(26,27,2,'2023-S2',3,'Aprendizaje constante, aún le falta velocidad.'),(27,28,2,'2023-S2',4,'Eficiente en tareas administrativas de operaciones.'),(28,29,3,'2023-S2',5,'Excelente trabajo en campañas digitales.'),(29,30,2,'2023-S2',5,'Uno de los mejores desarrolladores del equipo.'),(30,32,31,'2023-S2',3,'Atención al cliente correcta. Debe mejorar tiempos.'),(31,33,38,'2023-S2',4,'Puntual y ordenado. Buena actitud.'),(32,34,1,'2023-S2',4,'Sólido conocimiento en gestión de personal.'),(33,35,3,'2023-S2',4,'Diseños creativos y bien ejecutados.'),(34,36,3,'2023-S2',3,'Nuevo en el equipo, en período de adaptación.'),(35,37,1,'2023-S2',5,'Excelente gestión de legajos y procesos.'),(36,39,31,'2023-S2',3,'Reciente ingreso. Mucho potencial.'),(37,40,2,'2023-S2',4,'Rápida adaptación al equipo TI.'),(38,41,1,'2023-S2',4,'Análisis financiero riguroso y puntual.'),(39,42,3,'2023-S2',5,'Superó objetivos de ventas en un 20%.'),(40,43,1,'2023-S2',4,'Excelente manejo de documentación legal.'),(41,44,2,'2023-S2',4,'Buena calidad de código. Entrega puntual.'),(42,45,1,'2023-S2',5,'Contabilidad impecable. Sin observaciones.'),(43,46,38,'2023-S2',3,'Cumple funciones. Debe mejorar rendimiento.'),(44,47,31,'2023-S2',4,'Muy buena atención. Puntualidad destacada.'),(45,48,2,'2023-S2',5,'Base de datos optimizada y bien documentada.'),(46,49,38,'2023-S2',3,'Nuevo ingreso, sigue aprendiendo los procesos.'),(47,50,2,'2024-S1',5,'Proyecto de migración finalizado exitosamente.'),(48,30,2,'2024-S1',5,'Referente técnico del equipo. Muy recomendado.'),(49,42,3,'2024-S1',5,'Primer puesto en ranking de ventas del semestre.'),(50,52,1,'2026',80,'Buen trabajo en equipo');
/*!40000 ALTER TABLE `evaluacion_desempeno` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `historial_cargo`
--

DROP TABLE IF EXISTS `historial_cargo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `historial_cargo` (
  `id_hist` int(11) NOT NULL AUTO_INCREMENT,
  `legajo` int(11) NOT NULL,
  `cod_cargo` varchar(10) NOT NULL,
  `cod_depto` varchar(10) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date DEFAULT NULL,
  PRIMARY KEY (`id_hist`),
  KEY `legajo` (`legajo`),
  KEY `cod_cargo` (`cod_cargo`),
  KEY `cod_depto` (`cod_depto`),
  CONSTRAINT `historial_cargo_ibfk_1` FOREIGN KEY (`legajo`) REFERENCES `empleado` (`legajo`),
  CONSTRAINT `historial_cargo_ibfk_2` FOREIGN KEY (`cod_cargo`) REFERENCES `cargo` (`cod_cargo`),
  CONSTRAINT `historial_cargo_ibfk_3` FOREIGN KEY (`cod_depto`) REFERENCES `departamento` (`cod_depto`)
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `historial_cargo`
--

LOCK TABLES `historial_cargo` WRITE;
/*!40000 ALTER TABLE `historial_cargo` DISABLE KEYS */;
INSERT INTO `historial_cargo` VALUES (1,4,'ADM03','RRHH','2019-11-15','2021-04-19'),(2,8,'TEC03','TI','2018-01-10','2020-09-10'),(3,11,'ADM04','FIN','2017-05-01','2019-10-31'),(4,18,'ADM04','FIN','2016-03-01','2019-07-21'),(5,25,'ANL02','TI','2017-02-01','2019-09-08'),(6,1,'SUP01','RRHH','2019-03-01',NULL),(7,2,'SUP02','TI','2018-06-15',NULL),(8,3,'SUP03','VEN','2020-01-10',NULL),(9,4,'ANL01','RRHH','2021-04-20',NULL),(10,5,'ADM03','RRHH','2021-07-05',NULL),(11,6,'TEC01','TI','2022-02-14',NULL),(12,7,'TEC02','TI','2022-05-03',NULL),(13,8,'ANL02','TI','2020-09-11',NULL),(14,9,'TEC03','TI','2023-01-16',NULL),(15,10,'OPE01','VEN','2023-03-22',NULL),(16,11,'ANL03','FIN','2019-11-01',NULL),(17,12,'ADM04','FIN','2020-04-07',NULL),(18,13,'OPE01','VEN','2021-08-18',NULL),(19,14,'OPE02','VEN','2022-01-09',NULL),(20,15,'ANL04','MKT','2022-06-27',NULL),(21,16,'ADM01','OPE','2023-02-13',NULL),(22,17,'OPE03','LOG','2023-05-04',NULL),(23,18,'ANL06','FIN','2019-07-22',NULL),(24,19,'ADM02','SAC','2020-10-30',NULL),(25,20,'TEC04','MKT','2021-12-01',NULL),(26,21,'ANL05','TI','2022-03-15',NULL),(27,22,'OPE02','VEN','2022-08-08',NULL),(28,23,'ADM02','SAC','2023-01-03',NULL),(29,24,'OPE03','LOG','2023-04-17',NULL),(30,25,'TEC05','TI','2019-09-09',NULL),(31,30,'TEC03','TI','2018-06-01','2020-10-31'),(32,34,'ADM03','RRHH','2018-09-01','2020-06-21'),(33,42,'OPE02','VEN','2018-01-15','2020-08-26'),(34,45,'ADM04','FIN','2018-03-01','2020-12-31'),(35,48,'TEC03','TI','2017-01-10','2019-05-27'),(36,26,'OPE02','VEN','2022-03-07',NULL),(37,27,'TEC03','TI','2022-07-11',NULL),(38,28,'ADM01','OPE','2023-01-23',NULL),(39,29,'ANL04','MKT','2021-05-17',NULL),(40,30,'TEC01','TI','2020-11-03',NULL),(41,31,'SUP05','SAC','2019-08-14',NULL),(42,32,'ADM02','SAC','2022-09-29',NULL),(43,33,'OPE04','LOG','2023-02-06',NULL),(44,34,'ANL01','RRHH','2020-06-22',NULL),(45,35,'TEC04','MKT','2021-10-08',NULL),(46,36,'OPE02','VEN','2023-06-19',NULL),(47,37,'ADM03','RRHH','2022-04-25',NULL),(48,38,'JEF05','LOG','2019-12-02',NULL),(49,39,'ADM02','SAC','2024-01-15','2026-06-30'),(50,40,'TEC03','TI','2024-02-20',NULL),(51,41,'ANL06','FIN','2021-03-11',NULL),(52,42,'OPE01','VEN','2020-08-27',NULL),(53,43,'ADM01','LEG','2022-11-14',NULL),(54,44,'TEC02','TI','2023-07-03',NULL),(55,45,'ANL03','FIN','2021-01-19',NULL),(56,46,'OPE03','LOG','2020-03-04',NULL),(57,47,'ADM02','SAC','2024-03-12',NULL),(58,48,'ANL05','TI','2019-05-28',NULL),(59,49,'OPE04','LOG','2023-08-07',NULL),(60,50,'TEC01','TI','2022-10-16',NULL),(61,51,'TEC05','TI','2026-06-27',NULL),(62,52,'ANL02','TI','2026-06-30','2026-06-30'),(63,53,'TEC01','SAC','2026-06-30','2026-07-04'),(64,53,'TEC01','TI','2026-07-06',NULL),(65,39,'GER03','TI','2026-07-01',NULL);
/*!40000 ALTER TABLE `historial_cargo` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = cp850 */ ;
/*!50003 SET character_set_results = cp850 */ ;
/*!50003 SET collation_connection  = cp850_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_validar_cargo_unico_activo
BEFORE INSERT ON historial_cargo
FOR EACH ROW
BEGIN
  DECLARE cnt INT;
  SELECT COUNT(*) INTO cnt
  FROM historial_cargo
  WHERE legajo = NEW.legajo AND fecha_fin IS NULL;
  IF cnt > 0 THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'El empleado ya tiene un cargo activo. Debe darlo de baja antes de asignar uno nuevo.';
  END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `localidad`
--

DROP TABLE IF EXISTS `localidad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `localidad` (
  `cod_postal` varchar(10) NOT NULL,
  `nombre_ciudad` varchar(100) NOT NULL,
  `cod_provincia` varchar(10) NOT NULL,
  PRIMARY KEY (`cod_postal`),
  KEY `cod_provincia` (`cod_provincia`),
  CONSTRAINT `localidad_ibfk_1` FOREIGN KEY (`cod_provincia`) REFERENCES `provincia` (`cod_provincia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `localidad`
--

LOCK TABLES `localidad` WRITE;
/*!40000 ALTER TABLE `localidad` DISABLE KEYS */;
INSERT INTO `localidad` VALUES ('1000','Buenos Aires','CABA'),('1900','La Plata','BA'),('2000','Rosario','SF'),('2300','Rafaela','SF'),('2345','Rodeo de la Cruz','MEN'),('2700','Pergamino','BA'),('2900','San Nicolás de los Arroyos','BA'),('3000','Santa Fe','SF'),('3100','Paraná','ER'),('3260','Concordia','ER'),('3300','Posadas','MIS'),('3400','Corrientes','CTES'),('3500','Resistencia','CHA'),('3600','Formosa','FOR'),('4000','San Miguel de Tucumán','TUC'),('4200','Santiago del Estero','SE'),('4400','Salta','SAL'),('4600','San Salvador de Jujuy','JUJ'),('4700','San Fernando del Valle','CAT'),('5000','Córdoba','COR'),('5152','Villa Carlos Paz','COR'),('5300','La Rioja','LR'),('5400','San Juan','SJ'),('5500','Mendoza','MEN'),('5600','San Rafael','MEN'),('5700','San Luis','SL'),('5900','Villa María','COR'),('6000','Junín','BA'),('6300','Santa Rosa','LP'),('7600','Mar del Plata','BA'),('8000','Bahía Blanca','BA'),('8300','Neuquén','NEU'),('8400','Bariloche','RN'),('9000','Comodoro Rivadavia','CHU'),('9400','Río Gallegos','SC'),('9410','Ushuaia','TF');
/*!40000 ALTER TABLE `localidad` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nivel_jerarquico`
--

DROP TABLE IF EXISTS `nivel_jerarquico`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nivel_jerarquico` (
  `cod_nivel` varchar(10) NOT NULL,
  `nombre_nivel` varchar(80) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`cod_nivel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nivel_jerarquico`
--

LOCK TABLES `nivel_jerarquico` WRITE;
/*!40000 ALTER TABLE `nivel_jerarquico` DISABLE KEYS */;
INSERT INTO `nivel_jerarquico` VALUES ('ADM','Administrativo','Soporte administrativo y gestión documental'),('ANL','Analista','Análisis, planificación y ejecución de procesos'),('GER','Gerencia','Nivel de dirección y toma de decisiones estratégicas'),('JEF','Jefatura','Coordinación de equipos y áreas funcionales'),('OPE','Operativo','Tareas operativas y de producción'),('SUP','Supervisión','Supervisión operativa de equipos de trabajo'),('TEC','Técnico','Ejecución técnica especializada');
/*!40000 ALTER TABLE `nivel_jerarquico` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `provincia`
--

DROP TABLE IF EXISTS `provincia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `provincia` (
  `cod_provincia` varchar(10) NOT NULL,
  `nombre_provincia` varchar(100) NOT NULL,
  PRIMARY KEY (`cod_provincia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `provincia`
--

LOCK TABLES `provincia` WRITE;
/*!40000 ALTER TABLE `provincia` DISABLE KEYS */;
INSERT INTO `provincia` VALUES ('BA','Buenos Aires'),('CABA','Ciudad Autónoma de Buenos Aires'),('CAT','Catamarca'),('CHA','Chaco'),('CHU','Chubut'),('COR','Córdoba'),('CTES','Corrientes'),('ER','Entre Ríos'),('FOR','Formosa'),('JUJ','Jujuy'),('LP','La Pampa'),('LR','La Rioja'),('MEN','Mendoza'),('MIS','Misiones'),('NEU','Neuquén'),('RN','Río Negro'),('SAL','Salta'),('SC','Santa Cruz'),('SE','Santiago del Estero'),('SF','Santa Fe'),('SJ','San Juan'),('SL','San Luis'),('TF','Tierra del Fuego'),('TUC','Tucumán');
/*!40000 ALTER TABLE `provincia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rol`
--

DROP TABLE IF EXISTS `rol`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rol` (
  `id_rol` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_rol` varchar(50) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_rol`),
  UNIQUE KEY `nombre_rol` (`nombre_rol`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rol`
--

LOCK TABLES `rol` WRITE;
/*!40000 ALTER TABLE `rol` DISABLE KEYS */;
INSERT INTO `rol` VALUES (1,'ADMINISTRADOR','Acceso total al sistema'),(2,'SUPERVISOR','Gesti?n de su equipo'),(3,'EMPLEADO','Acceso a su perfil y solicitudes');
/*!40000 ALTER TABLE `rol` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `solicitud_licencia`
--

DROP TABLE IF EXISTS `solicitud_licencia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `solicitud_licencia` (
  `nro_solicitud` int(11) NOT NULL AUTO_INCREMENT,
  `legajo` int(11) NOT NULL,
  `cod_tipo` varchar(10) NOT NULL,
  `cod_estado` varchar(10) NOT NULL,
  `fecha_solicitud` date NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `observacion` text DEFAULT NULL,
  PRIMARY KEY (`nro_solicitud`),
  KEY `cod_tipo` (`cod_tipo`),
  KEY `cod_estado` (`cod_estado`),
  KEY `idx_solicitud_fechas` (`legajo`,`fecha_inicio`,`fecha_fin`),
  CONSTRAINT `solicitud_licencia_ibfk_1` FOREIGN KEY (`legajo`) REFERENCES `empleado` (`legajo`),
  CONSTRAINT `solicitud_licencia_ibfk_2` FOREIGN KEY (`cod_tipo`) REFERENCES `tipo_licencia` (`cod_tipo`),
  CONSTRAINT `solicitud_licencia_ibfk_3` FOREIGN KEY (`cod_estado`) REFERENCES `estado_solicitud` (`cod_estado`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `solicitud_licencia`
--

LOCK TABLES `solicitud_licencia` WRITE;
/*!40000 ALTER TABLE `solicitud_licencia` DISABLE KEYS */;
INSERT INTO `solicitud_licencia` VALUES (1,4,'VAC','APRO','2024-01-05','2024-01-15','2024-01-29','Vacaciones de verano'),(2,5,'ENF','APRO','2024-03-10','2024-03-10','2024-03-14','Gripe estacional'),(3,6,'VAC','APRO','2024-02-01','2024-02-05','2024-02-16',NULL),(4,7,'EST','APRO','2024-06-20','2024-06-24','2024-06-28','Exámenes finales'),(5,8,'CAS','APRO','2024-04-08','2024-04-15','2024-04-24','Casamiento'),(6,9,'ENF','PEND','2024-11-01','2024-11-04','2024-11-08',NULL),(7,10,'VAC','APRO','2024-07-01','2024-07-08','2024-07-19','Vacaciones invierno'),(8,11,'MAT','APRO','2024-05-01','2024-05-01','2024-05-28','Licencia por maternidad'),(9,12,'ENF','RECH','2024-03-20','2024-03-20','2024-03-25','Sin certificado médico'),(10,13,'VAC','APRO','2024-08-05','2024-08-12','2024-08-23',NULL),(11,14,'DUE','APRO','2024-09-02','2024-09-03','2024-09-05','Fallecimiento familiar'),(12,15,'EST','APRO','2024-06-15','2024-06-20','2024-06-24','Cursada universitaria'),(13,16,'ENF','APRO','2024-10-14','2024-10-14','2024-10-18','Intervención quirúrgica'),(14,17,'VAC','PEND','2024-12-01','2024-12-23','2025-01-06',NULL),(15,18,'VAC','APRO','2024-01-20','2024-02-01','2024-02-15','Vacaciones de verano'),(16,19,'PAT','APRO','2024-07-15','2024-07-16','2024-07-17','Nacimiento de hijo'),(17,20,'ENF','APRO','2024-04-22','2024-04-22','2024-04-26',NULL),(18,21,'VAC','APRO','2024-02-12','2024-02-19','2024-03-01',NULL),(19,22,'SIN','RECH','2024-05-06','2024-05-13','2024-05-17','Sin goce de sueldo no autorizado'),(20,23,'ENF','APRO','2024-08-20','2024-08-20','2024-08-23','Reposo médico'),(21,4,'ENF','APRO','2024-09-09','2024-09-09','2024-09-11','Cuadro gripal'),(22,6,'EST','PEND','2024-11-18','2024-11-25','2024-11-29','Exámenes parciales'),(23,25,'CAS','APRO','2023-11-10','2023-11-20','2023-11-29','Casamiento en Mendoza'),(24,24,'VAC','APRO','2024-01-08','2024-01-15','2024-01-26',NULL),(25,26,'VAC','APRO','2024-01-10','2024-01-22','2024-02-02',NULL),(26,27,'ENF','APRO','2024-03-05','2024-03-05','2024-03-09','Cuadro febril'),(27,28,'VAC','PEND','2024-11-20','2024-12-02','2024-12-13',NULL),(28,29,'EST','APRO','2024-06-10','2024-06-17','2024-06-21','Defensa de tesis'),(29,30,'VAC','APRO','2024-07-15','2024-07-22','2024-08-02',NULL),(30,31,'ENF','APRO','2024-04-03','2024-04-03','2024-04-07','Cirugía ambulatoria'),(31,32,'CAS','APRO','2024-09-10','2024-09-16','2024-09-25',NULL),(32,33,'VAC','APRO','2024-02-05','2024-02-12','2024-02-23',NULL),(33,34,'ENF','RECH','2024-05-08','2024-05-08','2024-05-12','Certificado vencido'),(34,35,'MAT','APRO','2024-06-01','2024-06-01','2024-06-28','Licencia por maternidad'),(35,36,'VAC','CANC','2024-08-01','2024-08-12','2024-08-23','Cancelado por el empleado'),(36,37,'DUE','APRO','2024-10-07','2024-10-08','2024-10-10','Fallecimiento abuelo'),(37,38,'VAC','APRO','2024-01-15','2024-01-22','2024-02-02',NULL),(38,39,'ENF','PEND','2024-11-25','2024-11-25','2024-11-29',NULL),(39,40,'EST','APRO','2024-07-08','2024-07-15','2024-07-19','Primer examen universitario'),(40,41,'VAC','APRO','2024-03-18','2024-03-25','2024-04-05',NULL),(41,42,'PAT','APRO','2024-08-19','2024-08-20','2024-08-21','Nacimiento de hija'),(42,43,'ENF','APRO','2024-09-30','2024-09-30','2024-10-04','Reposo por lumbalgia'),(43,44,'VAC','APRO','2024-02-19','2024-02-26','2024-03-08',NULL),(44,45,'EST','APRO','2024-06-24','2024-06-28','2024-07-02',NULL),(45,46,'MUD','APRO','2024-05-13','2024-05-14','2024-05-14','Mudanza de domicilio'),(46,47,'ENF','APRO','2024-10-21','2024-10-21','2024-10-25',NULL),(47,48,'VAC','APRO','2024-01-08','2024-01-15','2024-01-26',NULL),(48,49,'VAC','APRO','2024-12-10','2024-12-16','2024-12-27','test admin'),(49,50,'ENF','APRO','2024-04-15','2024-04-15','2024-04-19','Gripe con fiebre'),(50,1,'ENF','APRO','2026-06-30','2026-07-03','2026-07-08',''),(51,52,'ENF','APRO','2026-06-30','2026-07-03','2026-07-08','');
/*!40000 ALTER TABLE `solicitud_licencia` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = cp850 */ ;
/*!50003 SET character_set_results = cp850 */ ;
/*!50003 SET collation_connection  = cp850_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_validar_solapamiento_licencia
BEFORE INSERT ON solicitud_licencia
FOR EACH ROW
BEGIN
  DECLARE cnt INT;
  SELECT COUNT(*) INTO cnt
  FROM solicitud_licencia sl
  JOIN estado_solicitud es ON sl.cod_estado = es.cod_estado
  WHERE sl.legajo        = NEW.legajo
    AND es.nombre_estado NOT IN ('RECHAZADA', 'CANCELADA')
    AND sl.fecha_inicio  <= NEW.fecha_fin
    AND sl.fecha_fin     >= NEW.fecha_inicio;

  IF cnt > 0 THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'El empleado ya tiene una licencia en ese período de fechas.';
  END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_auditoria_nueva_solicitud
AFTER INSERT ON solicitud_licencia
FOR EACH ROW
BEGIN
  INSERT INTO auditoria_estado (
    nro_solicitud,
    cod_estado_anterior,
    cod_estado_nuevo,
    legajo_responsable,
    observacion
  ) VALUES (
    NEW.nro_solicitud,
    NULL,
    NEW.cod_estado,
    NEW.legajo,
    'Solicitud creada'
  );
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `supervisor_historial`
--

DROP TABLE IF EXISTS `supervisor_historial`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `supervisor_historial` (
  `id_hist` int(11) NOT NULL,
  `legajo_supervisor` int(11) NOT NULL,
  PRIMARY KEY (`id_hist`,`legajo_supervisor`),
  KEY `legajo_supervisor` (`legajo_supervisor`),
  CONSTRAINT `supervisor_historial_ibfk_1` FOREIGN KEY (`id_hist`) REFERENCES `historial_cargo` (`id_hist`),
  CONSTRAINT `supervisor_historial_ibfk_2` FOREIGN KEY (`legajo_supervisor`) REFERENCES `empleado` (`legajo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supervisor_historial`
--

LOCK TABLES `supervisor_historial` WRITE;
/*!40000 ALTER TABLE `supervisor_historial` DISABLE KEYS */;
INSERT INTO `supervisor_historial` VALUES (9,1),(10,1),(11,2),(12,2),(13,2),(14,2),(15,3),(16,1),(17,1),(18,3),(19,3),(20,3),(21,2),(22,2),(23,1),(24,1),(25,3),(26,2),(27,3),(28,1),(29,2),(30,2),(36,3),(37,2),(38,2),(39,3),(40,2),(42,31),(43,38),(44,1),(45,3),(46,3),(47,1),(49,31),(50,2),(51,1),(52,3),(53,1),(54,2),(55,1),(56,38),(57,31),(58,2),(59,38),(60,2),(61,38),(62,1),(64,1);
/*!40000 ALTER TABLE `supervisor_historial` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipo_documento`
--

DROP TABLE IF EXISTS `tipo_documento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipo_documento` (
  `cod_tipo_doc` varchar(10) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  PRIMARY KEY (`cod_tipo_doc`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_documento`
--

LOCK TABLES `tipo_documento` WRITE;
/*!40000 ALTER TABLE `tipo_documento` DISABLE KEYS */;
INSERT INTO `tipo_documento` VALUES ('CACC','Certificado de Accidente Laboral'),('CDEF','Certificado de Defunci?n'),('CEST','Certificado de Estudios'),('CMAT','Certificado de Maternidad'),('CMAT2','Certificado de Matrimonio'),('CPAT','Certificado de Paternidad'),('CMED','Certificado M?dico'),('CMUD','Constancia de Mudanza'),('OTRO','Otro');
/*!40000 ALTER TABLE `tipo_documento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipo_licencia`
--

DROP TABLE IF EXISTS `tipo_licencia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipo_licencia` (
  `cod_tipo` varchar(10) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `dias_max` int(11) NOT NULL,
  `requiere_certificado` tinyint(1) NOT NULL DEFAULT 0,
  `remunerada` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`cod_tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_licencia`
--

LOCK TABLES `tipo_licencia` WRITE;
/*!40000 ALTER TABLE `tipo_licencia` DISABLE KEYS */;
INSERT INTO `tipo_licencia` VALUES ('ART','Accidente de trabajo',30,1,1),('CAS','Casamiento',10,1,1),('DUE','Duelo familiar',3,1,1),('ENF','Enfermedad',15,1,1),('EST','Estudio / Examen',10,1,1),('MAT','Maternidad',30,1,1),('MUD','Mudanza',1,0,1),('PAT','Paternidad',2,1,1),('SIN','Sin goce de sueldo',30,0,0),('VAC','Vacaciones anuales',30,0,1);
/*!40000 ALTER TABLE `tipo_licencia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario`
--

DROP TABLE IF EXISTS `usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `legajo` int(11) DEFAULT NULL,
  `id_rol` int(11) NOT NULL,
  `nombre_usuario` varchar(50) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_alta` date NOT NULL,
  `fecha_baja` date DEFAULT NULL,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `nombre_usuario` (`nombre_usuario`),
  UNIQUE KEY `legajo` (`legajo`),
  KEY `id_rol` (`id_rol`),
  CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`legajo`) REFERENCES `empleado` (`legajo`),
  CONSTRAINT `usuario_ibfk_2` FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id_rol`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--

LOCK TABLES `usuario` WRITE;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
INSERT INTO `usuario` VALUES (1,NULL,1,'admin','$2y$10$puQxKCtG7Y7hE30oCg36T.FTN4OTcBTgNY7bgco5oyvx6jf523V6G',1,'2026-06-27',NULL),(2,1,2,'mgonzalez','$2y$10$tNi4gxitvUjDTdbS0fYISeL0iCa0fArGmMGjV3QpDR.IaLc.xUGD6',1,'2019-03-01',NULL),(3,2,2,'crodriguez','$2y$10$tNi4gxitvUjDTdbS0fYISeL0iCa0fArGmMGjV3QpDR.IaLc.xUGD6',1,'2018-06-15',NULL),(4,3,2,'lmartinez','$2y$10$tNi4gxitvUjDTdbS0fYISeL0iCa0fArGmMGjV3QpDR.IaLc.xUGD6',1,'2020-01-10',NULL),(5,4,3,'dlopez','$2y$10$tNi4gxitvUjDTdbS0fYISeL0iCa0fArGmMGjV3QpDR.IaLc.xUGD6',1,'2021-04-20',NULL),(6,5,3,'vperez','$2y$10$tNi4gxitvUjDTdbS0fYISeL0iCa0fArGmMGjV3QpDR.IaLc.xUGD6',1,'2021-07-05',NULL),(7,6,3,'ssanchez','$2y$10$tNi4gxitvUjDTdbS0fYISeL0iCa0fArGmMGjV3QpDR.IaLc.xUGD6',1,'2022-02-14',NULL),(8,7,3,'framirez','$2y$10$tNi4gxitvUjDTdbS0fYISeL0iCa0fArGmMGjV3QpDR.IaLc.xUGD6',1,'2022-05-03',NULL),(9,8,3,'mtorres','$2y$10$tNi4gxitvUjDTdbS0fYISeL0iCa0fArGmMGjV3QpDR.IaLc.xUGD6',1,'2020-09-11',NULL),(10,9,3,'cflores','$2y$10$tNi4gxitvUjDTdbS0fYISeL0iCa0fArGmMGjV3QpDR.IaLc.xUGD6',1,'2023-01-16',NULL),(11,10,3,'ngomez','$2y$10$tNi4gxitvUjDTdbS0fYISeL0iCa0fArGmMGjV3QpDR.IaLc.xUGD6',1,'2023-03-22',NULL),(12,11,3,'afernandez','$2y$10$tNi4gxitvUjDTdbS0fYISeL0iCa0fArGmMGjV3QpDR.IaLc.xUGD6',1,'2019-11-01',NULL),(13,12,3,'ldiaz','$2y$10$tNi4gxitvUjDTdbS0fYISeL0iCa0fArGmMGjV3QpDR.IaLc.xUGD6',1,'2020-04-07',NULL),(14,13,3,'smorales','$2y$10$tNi4gxitvUjDTdbS0fYISeL0iCa0fArGmMGjV3QpDR.IaLc.xUGD6',1,'2021-08-18',NULL),(15,14,3,'aromero','$2y$10$tNi4gxitvUjDTdbS0fYISeL0iCa0fArGmMGjV3QpDR.IaLc.xUGD6',1,'2022-01-09',NULL),(16,15,3,'lalvarez','$2y$10$tNi4gxitvUjDTdbS0fYISeL0iCa0fArGmMGjV3QpDR.IaLc.xUGD6',1,'2022-06-27',NULL),(17,16,3,'truiz','$2y$10$tNi4gxitvUjDTdbS0fYISeL0iCa0fArGmMGjV3QpDR.IaLc.xUGD6',1,'2023-02-13',NULL),(18,17,3,'jherrera','$2y$10$tNi4gxitvUjDTdbS0fYISeL0iCa0fArGmMGjV3QpDR.IaLc.xUGD6',1,'2023-05-04',NULL),(19,18,3,'pmedina','$2y$10$tNi4gxitvUjDTdbS0fYISeL0iCa0fArGmMGjV3QpDR.IaLc.xUGD6',1,'2019-07-22',NULL),(20,19,3,'dcastro','$2y$10$tNi4gxitvUjDTdbS0fYISeL0iCa0fArGmMGjV3QpDR.IaLc.xUGD6',1,'2020-10-30',NULL),(21,20,3,'fvargas','$2y$10$tNi4gxitvUjDTdbS0fYISeL0iCa0fArGmMGjV3QpDR.IaLc.xUGD6',1,'2021-12-01',NULL),(22,21,3,'mramos','$2y$10$tNi4gxitvUjDTdbS0fYISeL0iCa0fArGmMGjV3QpDR.IaLc.xUGD6',1,'2022-03-15',NULL),(23,22,3,'rgutierrez','$2y$10$tNi4gxitvUjDTdbS0fYISeL0iCa0fArGmMGjV3QpDR.IaLc.xUGD6',1,'2022-08-08',NULL),(24,23,3,'bortega','$2y$10$tNi4gxitvUjDTdbS0fYISeL0iCa0fArGmMGjV3QpDR.IaLc.xUGD6',1,'2023-01-03',NULL),(25,24,3,'hmolina','$2y$10$tNi4gxitvUjDTdbS0fYISeL0iCa0fArGmMGjV3QpDR.IaLc.xUGD6',1,'2023-04-17',NULL),(26,25,3,'nsilva','$2y$10$tNi4gxitvUjDTdbS0fYISeL0iCa0fArGmMGjV3QpDR.IaLc.xUGD6',1,'2019-09-09',NULL),(27,26,3,'racosta','$2y$10$tNi4gxitvUjDTdbS0fYISeL0iCa0fArGmMGjV3QpDR.IaLc.xUGD6',1,'2022-03-07',NULL),(28,27,3,'pbenitez','$2y$10$tNi4gxitvUjDTdbS0fYISeL0iCa0fArGmMGjV3QpDR.IaLc.xUGD6',1,'2022-07-11',NULL),(29,28,3,'ecabrera','$2y$10$tNi4gxitvUjDTdbS0fYISeL0iCa0fArGmMGjV3QpDR.IaLc.xUGD6',1,'2023-01-23',NULL),(30,29,3,'vdelgado','$2y$10$tNi4gxitvUjDTdbS0fYISeL0iCa0fArGmMGjV3QpDR.IaLc.xUGD6',1,'2021-05-17',NULL),(31,30,3,'iespinosa','$2y$10$tNi4gxitvUjDTdbS0fYISeL0iCa0fArGmMGjV3QpDR.IaLc.xUGD6',1,'2020-11-03',NULL),(32,31,2,'mfigueroa','$2y$10$tNi4gxitvUjDTdbS0fYISeL0iCa0fArGmMGjV3QpDR.IaLc.xUGD6',1,'2019-08-14',NULL),(33,32,3,'pgodoy','$2y$10$tNi4gxitvUjDTdbS0fYISeL0iCa0fArGmMGjV3QpDR.IaLc.xUGD6',1,'2022-09-29',NULL),(34,33,3,'aibanez','$2y$10$tNi4gxitvUjDTdbS0fYISeL0iCa0fArGmMGjV3QpDR.IaLc.xUGD6',1,'2023-02-06',NULL),(35,34,3,'ljuarez','$2y$10$tNi4gxitvUjDTdbS0fYISeL0iCa0fArGmMGjV3QpDR.IaLc.xUGD6',1,'2020-06-22',NULL),(36,35,3,'rlastra','$2y$10$tNi4gxitvUjDTdbS0fYISeL0iCa0fArGmMGjV3QpDR.IaLc.xUGD6',1,'2021-10-08',NULL),(37,36,3,'fmansilla','$2y$10$tNi4gxitvUjDTdbS0fYISeL0iCa0fArGmMGjV3QpDR.IaLc.xUGD6',1,'2023-06-19',NULL),(38,37,3,'gnavarro','$2y$10$tNi4gxitvUjDTdbS0fYISeL0iCa0fArGmMGjV3QpDR.IaLc.xUGD6',1,'2022-04-25',NULL),(39,38,2,'mojeda','$2y$10$tNi4gxitvUjDTdbS0fYISeL0iCa0fArGmMGjV3QpDR.IaLc.xUGD6',1,'2019-12-02',NULL),(40,39,2,'spereyra','$2y$10$tNi4gxitvUjDTdbS0fYISeL0iCa0fArGmMGjV3QpDR.IaLc.xUGD6',1,'2024-01-15',NULL),(41,40,3,'bquiroga','$2y$10$tNi4gxitvUjDTdbS0fYISeL0iCa0fArGmMGjV3QpDR.IaLc.xUGD6',1,'2024-02-20',NULL),(42,41,3,'xreyes','$2y$10$tNi4gxitvUjDTdbS0fYISeL0iCa0fArGmMGjV3QpDR.IaLc.xUGD6',1,'2021-03-11',NULL),(43,42,3,'gsalinas','$2y$10$tNi4gxitvUjDTdbS0fYISeL0iCa0fArGmMGjV3QpDR.IaLc.xUGD6',1,'2020-08-27',NULL),(44,43,3,'ltapia','$2y$10$tNi4gxitvUjDTdbS0fYISeL0iCa0fArGmMGjV3QpDR.IaLc.xUGD6',1,'2022-11-14',NULL),(45,44,3,'curbano','$2y$10$tNi4gxitvUjDTdbS0fYISeL0iCa0fArGmMGjV3QpDR.IaLc.xUGD6',1,'2023-07-03',NULL),(46,45,3,'mvega','$2y$10$tNi4gxitvUjDTdbS0fYISeL0iCa0fArGmMGjV3QpDR.IaLc.xUGD6',1,'2021-01-19',NULL),(47,46,3,'aaguirre','$2y$10$tNi4gxitvUjDTdbS0fYISeL0iCa0fArGmMGjV3QpDR.IaLc.xUGD6',1,'2020-03-04',NULL),(48,47,3,'cbarrios','$2y$10$tNi4gxitvUjDTdbS0fYISeL0iCa0fArGmMGjV3QpDR.IaLc.xUGD6',1,'2024-03-12',NULL),(49,48,3,'dcaceres','$2y$10$tNi4gxitvUjDTdbS0fYISeL0iCa0fArGmMGjV3QpDR.IaLc.xUGD6',1,'2019-05-28',NULL),(50,49,3,'edominguez','$2y$10$tNi4gxitvUjDTdbS0fYISeL0iCa0fArGmMGjV3QpDR.IaLc.xUGD6',1,'2023-08-07',NULL),(51,50,3,'festrada','$2y$10$tNi4gxitvUjDTdbS0fYISeL0iCa0fArGmMGjV3QpDR.IaLc.xUGD6',1,'2022-10-16',NULL),(52,51,3,'pending_51','$2y$10$yFOPsLIN5HLPNxlYWAyCA.ZAXmxKhWtPN2KH7R1rupznFyBw8tHwi',1,'2026-06-27',NULL),(53,52,3,'vrios','$2y$10$.Hoa1R2dBbVT0fmndydmoe87DgrKuxH1asOS4RkmiBSusNRdygaVa',1,'2026-06-30',NULL),(54,53,3,'cortiz','$2y$10$mlv9f2CizPO4pqs3540Q8e4OgCpAoK9Xzt5tQ8tzPzzVKawQhBArK',1,'2026-06-30',NULL);
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `v_empleado_detallado`
--

DROP TABLE IF EXISTS `v_empleado_detallado`;
/*!50001 DROP VIEW IF EXISTS `v_empleado_detallado`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v_empleado_detallado` AS SELECT
 1 AS `legajo`,
  1 AS `dni`,
  1 AS `nombre`,
  1 AS `apellido`,
  1 AS `email`,
  1 AS `telefono`,
  1 AS `fecha_ingreso`,
  1 AS `calle`,
  1 AS `altura`,
  1 AS `ciudad`,
  1 AS `provincia` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `ve_organigramaactual`
--

DROP TABLE IF EXISTS `ve_organigramaactual`;
/*!50001 DROP VIEW IF EXISTS `ve_organigramaactual`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `ve_organigramaactual` AS SELECT
 1 AS `legajo`,
  1 AS `nombre`,
  1 AS `apellido`,
  1 AS `cargo`,
  1 AS `nivel`,
  1 AS `departamento`,
  1 AS `supervisores` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `ve_reportelicencias`
--

DROP TABLE IF EXISTS `ve_reportelicencias`;
/*!50001 DROP VIEW IF EXISTS `ve_reportelicencias`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `ve_reportelicencias` AS SELECT
 1 AS `nro_solicitud`,
  1 AS `empleado`,
  1 AS `tipo_licencia`,
  1 AS `estado`,
  1 AS `fecha_solicitud`,
  1 AS `fecha_inicio`,
  1 AS `fecha_fin`,
  1 AS `dias_solicitados`,
  1 AS `observacion` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vista_dashboard`
--

DROP TABLE IF EXISTS `vista_dashboard`;
/*!50001 DROP VIEW IF EXISTS `vista_dashboard`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vista_dashboard` AS SELECT
 1 AS `total_empleados`,
  1 AS `licencias_pendientes`,
  1 AS `licencias_aprobadas`,
  1 AS `total_evaluaciones`,
  1 AS `total_departamentos` */;
SET character_set_client = @saved_cs_client;

--
-- Dumping events for database 'talenthub2'
--

--
-- Dumping routines for database 'talenthub2'
--
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP FUNCTION IF EXISTS `fn_antiguedad_anios` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = cp850 */ ;
/*!50003 SET character_set_results = cp850 */ ;
/*!50003 SET collation_connection  = cp850_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_antiguedad_anios`(p_legajo INT) RETURNS int(11)
    DETERMINISTIC
BEGIN
  DECLARE anios INT;
  SELECT TIMESTAMPDIFF(YEAR, fecha_ingreso, CURDATE())
  INTO anios
  FROM empleado
  WHERE legajo = p_legajo;
  RETURN COALESCE(anios, 0);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP FUNCTION IF EXISTS `fn_cargo_actual` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_cargo_actual`(p_legajo INT) RETURNS varchar(100) CHARSET utf8mb4 COLLATE utf8mb4_spanish_ci
    DETERMINISTIC
BEGIN
  DECLARE nombre VARCHAR(100);
  SELECT c.nombre_cargo INTO nombre
  FROM historial_cargo h
  JOIN cargo c ON h.cod_cargo = c.cod_cargo
  WHERE h.legajo = p_legajo AND h.fecha_fin IS NULL
  LIMIT 1;
  RETURN COALESCE(nombre, 'Sin cargo asignado');
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP FUNCTION IF EXISTS `fn_dias_licencia_usados` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = cp850 */ ;
/*!50003 SET character_set_results = cp850 */ ;
/*!50003 SET collation_connection  = cp850_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_dias_licencia_usados`(p_legajo INT, p_anio INT) RETURNS int(11)
    DETERMINISTIC
BEGIN
  DECLARE total INT;
  SELECT COALESCE(SUM(
      (DATEDIFF(sl.fecha_fin, sl.fecha_inicio) + 1)
      - FLOOR((DATEDIFF(sl.fecha_fin, sl.fecha_inicio) + WEEKDAY(sl.fecha_inicio) + 1) / 7)
  ), 0)
  INTO total
  FROM solicitud_licencia sl
  JOIN estado_solicitud es ON sl.cod_estado = es.cod_estado
  WHERE sl.legajo = p_legajo
    AND es.nombre_estado = 'APROBADA'
    AND YEAR(sl.fecha_inicio) = p_anio;
  RETURN total;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP FUNCTION IF EXISTS `fn_promedio_evaluacion` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = cp850 */ ;
/*!50003 SET character_set_results = cp850 */ ;
/*!50003 SET collation_connection  = cp850_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_promedio_evaluacion`(p_legajo INT) RETURNS decimal(4,2)
    DETERMINISTIC
BEGIN
  DECLARE promedio DECIMAL(4,2);
  SELECT COALESCE(ROUND(AVG(puntuacion), 2), 0)
  INTO promedio
  FROM evaluacion_desempeno
  WHERE legajo_evaluado = p_legajo;
  RETURN promedio;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP FUNCTION IF EXISTS `fn_tiene_licencia_pendiente` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_tiene_licencia_pendiente`(p_legajo INT) RETURNS tinyint(4)
    DETERMINISTIC
BEGIN
  DECLARE cantidad INT;
  SELECT COUNT(*) INTO cantidad
  FROM solicitud_licencia sl
  JOIN estado_solicitud es ON sl.cod_estado = es.cod_estado
  WHERE sl.legajo = p_legajo
    AND es.nombre_estado = 'PENDIENTE';
  RETURN IF(cantidad > 0, 1, 0);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_cambiar_estado_licencia` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = cp850 */ ;
/*!50003 SET character_set_results = cp850 */ ;
/*!50003 SET collation_connection  = cp850_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_cambiar_estado_licencia`(
  IN  p_nro_solicitud      INT,
  IN  p_cod_estado_nuevo   VARCHAR(10),
  IN  p_legajo_responsable INT,
  IN  p_observacion        TEXT,
  OUT p_resultado          VARCHAR(200)
)
BEGIN
  DECLARE v_estado_anterior VARCHAR(10);
  DECLARE v_nombre_anterior VARCHAR(50);
  DECLARE v_nombre_nuevo    VARCHAR(50);

  DECLARE EXIT HANDLER FOR SQLEXCEPTION
  BEGIN
    ROLLBACK;
    SET p_resultado = 'ERROR: fallo interno al cambiar el estado.';
  END;

  
  SELECT cod_estado INTO v_estado_anterior
  FROM solicitud_licencia
  WHERE nro_solicitud = p_nro_solicitud;

  IF v_estado_anterior IS NULL THEN
    SET p_resultado = 'ERROR: Solicitud no encontrada.';

  ELSEIF v_estado_anterior = p_cod_estado_nuevo THEN
    SET p_resultado = 'ERROR: La solicitud ya se encuentra en ese estado.';

  ELSEIF v_estado_anterior IN ('CANC') THEN
    SET p_resultado = 'ERROR: No se puede modificar una solicitud cancelada.';

  ELSE
    START TRANSACTION;

    
    UPDATE solicitud_licencia
    SET cod_estado  = p_cod_estado_nuevo,
        observacion = p_observacion
    WHERE nro_solicitud = p_nro_solicitud;

    
    INSERT INTO auditoria_estado
      (nro_solicitud, cod_estado_anterior, cod_estado_nuevo, legajo_responsable, fecha_cambio, observacion)
    VALUES
      (p_nro_solicitud, v_estado_anterior, p_cod_estado_nuevo, p_legajo_responsable, NOW(), p_observacion);

    COMMIT;
    SET p_resultado = 'OK';
  END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Current Database: `talenthub2`
--

USE `talenthub2`;

--
-- Final view structure for view `v_empleado_detallado`
--

/*!50001 DROP VIEW IF EXISTS `v_empleado_detallado`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_empleado_detallado` AS select `e`.`legajo` AS `legajo`,`e`.`dni` AS `dni`,`e`.`nombre` AS `nombre`,`e`.`apellido` AS `apellido`,`e`.`email` AS `email`,`e`.`telefono` AS `telefono`,`e`.`fecha_ingreso` AS `fecha_ingreso`,`ed`.`calle` AS `calle`,`ed`.`altura` AS `altura`,`l`.`nombre_ciudad` AS `ciudad`,`p`.`nombre_provincia` AS `provincia` from (((`empleado` `e` left join `empleado_direccion` `ed` on(`e`.`legajo` = `ed`.`legajo` and `ed`.`es_principal` = 1)) left join `localidad` `l` on(`ed`.`cod_postal` = `l`.`cod_postal`)) left join `provincia` `p` on(`l`.`cod_provincia` = `p`.`cod_provincia`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `ve_organigramaactual`
--

/*!50001 DROP VIEW IF EXISTS `ve_organigramaactual`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `ve_organigramaactual` AS select `e`.`legajo` AS `legajo`,`e`.`nombre` AS `nombre`,`e`.`apellido` AS `apellido`,`c`.`nombre_cargo` AS `cargo`,`n`.`nombre_nivel` AS `nivel`,`d`.`nombre_depto` AS `departamento`,group_concat(concat(`s`.`nombre`,' ',`s`.`apellido`) separator ', ') AS `supervisores` from ((((((`empleado` `e` join `historial_cargo` `h` on(`e`.`legajo` = `h`.`legajo` and `h`.`fecha_fin` is null)) join `cargo` `c` on(`h`.`cod_cargo` = `c`.`cod_cargo`)) join `nivel_jerarquico` `n` on(`c`.`cod_nivel` = `n`.`cod_nivel`)) join `departamento` `d` on(`h`.`cod_depto` = `d`.`cod_depto`)) left join `supervisor_historial` `sh` on(`h`.`id_hist` = `sh`.`id_hist`)) left join `empleado` `s` on(`sh`.`legajo_supervisor` = `s`.`legajo`)) group by `e`.`legajo`,`e`.`nombre`,`e`.`apellido`,`c`.`nombre_cargo`,`n`.`nombre_nivel`,`d`.`nombre_depto` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `ve_reportelicencias`
--

/*!50001 DROP VIEW IF EXISTS `ve_reportelicencias`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `ve_reportelicencias` AS select `sl`.`nro_solicitud` AS `nro_solicitud`,concat(`e`.`nombre`,' ',`e`.`apellido`) AS `empleado`,`tl`.`nombre` AS `tipo_licencia`,`es`.`nombre_estado` AS `estado`,`sl`.`fecha_solicitud` AS `fecha_solicitud`,`sl`.`fecha_inicio` AS `fecha_inicio`,`sl`.`fecha_fin` AS `fecha_fin`,to_days(`sl`.`fecha_fin`) - to_days(`sl`.`fecha_inicio`) AS `dias_solicitados`,`sl`.`observacion` AS `observacion` from (((`solicitud_licencia` `sl` join `empleado` `e` on(`sl`.`legajo` = `e`.`legajo`)) join `tipo_licencia` `tl` on(`sl`.`cod_tipo` = `tl`.`cod_tipo`)) join `estado_solicitud` `es` on(`sl`.`cod_estado` = `es`.`cod_estado`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vista_dashboard`
--

/*!50001 DROP VIEW IF EXISTS `vista_dashboard`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vista_dashboard` AS select (select count(0) from `empleado`) AS `total_empleados`,(select count(0) from (`solicitud_licencia` `sl` join `estado_solicitud` `es` on(`sl`.`cod_estado` = `es`.`cod_estado`)) where `es`.`nombre_estado` = 'PENDIENTE') AS `licencias_pendientes`,(select count(0) from (`solicitud_licencia` `sl` join `estado_solicitud` `es` on(`sl`.`cod_estado` = `es`.`cod_estado`)) where `es`.`nombre_estado` = 'APROBADA') AS `licencias_aprobadas`,(select count(0) from `evaluacion_desempeno`) AS `total_evaluaciones`,(select count(0) from `departamento`) AS `total_departamentos` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed
