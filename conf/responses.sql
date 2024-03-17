CREATE TABLE `responses` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `quiz_id` int unsigned NOT NULL,
  `responder_id` int DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_bin DEFAULT NULL,
  `time` datetime DEFAULT CURRENT_TIMESTAMP,
  `duration` int unsigned DEFAULT NULL,
  `score` decimal(5,2) DEFAULT NULL,
  `max_score` decimal(5,2) DEFAULT NULL,
  `response` json NOT NULL,
  PRIMARY KEY (`id`),
  KEY `QUIZ_ID_KEY` (`quiz_id`),
  CONSTRAINT `FK_RESPONSES_QUIZ_ID` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
