CREATE TABLE IF NOT EXISTS `pools` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `address` varchar(255) NOT NULL,
    `poolname` int(11) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `id_UNIQUE` (`id`)
);

CREATE TABLE IF NOT EXISTS `poolslist` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
     `name` varchar(45) NOT NULL,
     PRIMARY KEY (`id`),
     UNIQUE KEY `id_UNIQUE` (`id`),
     UNIQUE KEY `name_UNIQUE` (`name`)
);

INSERT INTO `poolslist` (name) VALUES ('Support XMR'),('Nano Pool');

CREATE TABLE IF NOT EXISTS `rigs` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `address` varchar(45) NOT NULL,
    `port` int(11) NOT NULL,
    `accesstoken` varchar(255) DEFAULT NULL,
    `update` boolean DEFAULT FALSE,
    `error` boolean DEFAULT FALSE,
    `notified` boolean DEFAULT FALSE,
    PRIMARY KEY (`id`),
    UNIQUE KEY `id_UNIQUE` (`id`)
);

CREATE TABLE IF NOT EXISTS `users` (
    `id` int(11) NOT NULL,
    `username` varchar(255) NOT NULL,
    `password` varchar(255) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `id_UNIQUE` (`id`)
);
