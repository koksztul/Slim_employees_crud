CREATE TABLE `employees` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `surname` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `pesel` char(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
);
