DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
);

INSERT INTO `users` (name) VALUES ('admin'),('mr. robot');