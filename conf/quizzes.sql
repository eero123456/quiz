CREATE TABLE `quizzes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(36) NOT NULL DEFAULT (uuid()),
  `title` varchar(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `description` varchar(70) CHARACTER SET utf8mb4 DEFAULT NULL,
  `json` json NOT NULL,
  `owner` int unsigned DEFAULT NULL,
  `accepting_answers` int DEFAULT '1',
   `auth_only` int DEFAULT '0',
   `version` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `url_UNIQUE` (`url`),
  KEY `FK_user_idx` (`owner`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
