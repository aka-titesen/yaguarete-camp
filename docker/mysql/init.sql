-- Inicialización de la base de datos para desarrollo
CREATE DATABASE IF NOT EXISTS bd_yagaruete_camp;
USE bd_yagaruete_camp;

-- Crear usuario de aplicación
CREATE USER IF NOT EXISTS 'app_user'@'%' IDENTIFIED BY 'app_password';
GRANT ALL PRIVILEGES ON bd_yagaruete_camp.* TO 'app_user'@'%';

-- Base de datos para testing
CREATE DATABASE IF NOT EXISTS bd_yagaruete_camp_test;
GRANT ALL PRIVILEGES ON bd_yagaruete_camp_test.* TO 'app_user'@'%';

FLUSH PRIVILEGES;
