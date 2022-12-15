CREATE TABLE `permissions` (
	`type` ENUM('C','EC','ED','EM') NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`permissions` BIGINT(20) UNSIGNED,
	PRIMARY KEY (`type`) USING BTREE
)
COLLATE='utf8mb4_unicode_ci'
ENGINE=InnoDB
AUTO_INCREMENT=3025
;

INSERT INTO `permissions` (`type`, `permissions`) VALUES ("C", 0);
INSERT INTO `permissions` (`type`, `permissions`) VALUES ("EC", 3);
INSERT INTO `permissions` (`type`, `permissions`) VALUES ("ED", 5);
INSERT INTO `permissions` (`type`, `permissions`) VALUES ("EM", 57);

ALTER TABLE `users` ADD CONSTRAINT fk_users_type FOREIGN KEY (`type`) REFERENCES permissions(`type`)

CREATE TABLE `scopes` (
	`id` TINYINT UNSIGNED NOT NULL,
	`scope_name` VARCHAR(20),
	PRIMARY KEY (`id`) USING BTREE
)
COLLATE='utf8mb4_unicode_ci'
ENGINE=InnoDB
AUTO_INCREMENT=3025
;

INSERT INTO `scopes` VALUES (0, "view_orders");
INSERT INTO `scopes` VALUES (1, "prepare_orders");
INSERT INTO `scopes` VALUES (2, "deliver_orders");
INSERT INTO `scopes` VALUES (3, "cancel_orders");
INSERT INTO `scopes` VALUES (4, "manage_users");
INSERT INTO `scopes` VALUES (5, "manage_products");