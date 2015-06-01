//Create user with admin privilegies in table 'users' with parameters:

username      : admin
email         : admin@blo.com
hash          : $m0JPkWfOxY/JBIJRxU1y8S2dWWoA1evw7wg93rgKRxATq0NcaRbiH.tqAzBVZp0B1iWGycUL/Bpjbuji9LFOP1
displayname   : NULL
rules         : 1
avatar        : NULL
forward       : 1
description   : NULL

Admin account credentials with upper setup:
username      : admin
password      : adminer

//Populate tables with internal admin script:
1) Sign-in as Admin user
2) Go to settings tab
3) Click populate to generate random posts with comments

//Required tables - stored in database 'quickstart':

CREATE TABLE `users` (
	`username` VARCHAR(255) NOT NULL COMMENT 'Pøihlašovací jméno',
	`email` VARCHAR(255) NOT NULL COMMENT 'E-mail',
	`hash` TEXT NOT NULL COMMENT 'Otisk hesla',
	`displayname` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Pøezdívka uživatele',
	`rules` INT(11) UNSIGNED ZEROFILL NOT NULL DEFAULT '00000000000',
	`avatar` TEXT NULL COMMENT 'Odkaz na avatar',
	`forward` BIT(1) NULL DEFAULT b'',
	`description` TEXT NULL COMMENT 'Popisek',
	PRIMARY KEY (`username`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
;

CREATE TABLE `posts` (
	`id` INT(255) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '# pøíspìvku',
	`title` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Titulek',
	`subtitle` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Podtitulek',
	`username` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Jméno autora',
	`editor` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Jméno editora',
	`content` MEDIUMTEXT NULL COMMENT 'Obsah',
	`ip` VARCHAR(50) NULL DEFAULT NULL COMMENT 'IP autora',
	`link` TEXT NULL COMMENT 'Odkaz na ikonu',
	`created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`),
	INDEX `FK_posts_users` (`username`),
	CONSTRAINT `FK_posts_users` FOREIGN KEY (`username`) REFERENCES `users` (`username`) ON UPDATE CASCADE ON DELETE SET NULL
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=70
;

CREATE TABLE `comments` (
	`id` INT(255) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '# komentáøe',
	`post_id` INT(11) UNSIGNED NOT NULL COMMENT '# pøíspìvku',
	`username` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Jméno autora',
	`visitor` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Jméno anonymního autora',
	`email` VARCHAR(255) NULL DEFAULT NULL COMMENT 'E-mail autora',
	`content` TEXT NOT NULL COMMENT 'Obsah',
	`ip` VARCHAR(50) NULL DEFAULT NULL COMMENT 'IP autora',
	`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`),
	INDEX `FK_comments_posts` (`post_id`),
	INDEX `FK_comments_users` (`username`),
	CONSTRAINT `FK_comments_posts` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT `FK_comments_users` FOREIGN KEY (`username`) REFERENCES `users` (`username`) ON UPDATE CASCADE ON DELETE SET NULL
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=126
;