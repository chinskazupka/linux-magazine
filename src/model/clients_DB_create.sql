CREATE DATABASE mvc;
USE mvc;
DROP TABLE IF EXISTS emp_clients;
CREATE TABLE emp_clients (
`id_client` int(11) NOT NULL AUTO_INCREMENT,
`firstName` varchar(50) NOT NULL,
`secondName` varchar(50) NOT NULL,
`email` varchar(50) NOT NULL UNIQUE,
`status` varchar(50) NOT NULL,
`telephone` varchar(50),
PRIMARY KEY (`id`))
DEFAULT CHARSET=utf8mb4;