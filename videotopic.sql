# Host: localhost  (Version: 5.7.26)
# Date: 2025-03-18 20:46:03
# Generator: MySQL-Front 5.3  (Build 4.234)

/*!40101 SET NAMES utf8 */;

#
# Structure for table "daily_tasks"
#

DROP TABLE IF EXISTS `daily_tasks`;
CREATE TABLE `daily_tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `task_name` varchar(255) NOT NULL,
  `task_date` date DEFAULT NULL,
  `is_completed` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

#
# Data for table "daily_tasks"
#

/*!40000 ALTER TABLE `daily_tasks` DISABLE KEYS */;
INSERT INTO `daily_tasks` VALUES (13,1,'航拍酒城','2025-03-18',1),(14,1,'航拍长江','2025-03-18',1),(15,1,'酒厂拍摄环境、人物专访、酿酒过程','2025-03-20',0),(16,1,'拍摄纳溪特早茶采摘、加工','2025-03-21',0),(17,1,'拍摄护国陈醋工厂、加工过程、文化底蕴','2025-03-22',0),(18,1,'拍摄护国岩','2025-03-26',0);
/*!40000 ALTER TABLE `daily_tasks` ENABLE KEYS */;

#
# Structure for table "projects"
#

DROP TABLE IF EXISTS `projects`;
CREATE TABLE `projects` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Data for table "projects"
#

/*!40000 ALTER TABLE `projects` DISABLE KEYS */;
/*!40000 ALTER TABLE `projects` ENABLE KEYS */;

#
# Structure for table "tasks"
#

DROP TABLE IF EXISTS `tasks`;
CREATE TABLE `tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `task_name` varchar(255) NOT NULL,
  `task_date` date NOT NULL,
  `is_completed` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Data for table "tasks"
#

/*!40000 ALTER TABLE `tasks` DISABLE KEYS */;
/*!40000 ALTER TABLE `tasks` ENABLE KEYS */;

#
# Structure for table "topic_files"
#

DROP TABLE IF EXISTS `topic_files`;
CREATE TABLE `topic_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `topic_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` enum('image','video') NOT NULL,
  PRIMARY KEY (`id`),
  KEY `topic_id` (`topic_id`)
) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=utf8;

#
# Data for table "topic_files"
#

/*!40000 ALTER TABLE `topic_files` DISABLE KEYS */;
INSERT INTO `topic_files` VALUES (40,34,'sucai/酒城烟火气，醉美泸州行/推广泸州文案 (3).png','image'),(41,34,'sucai/酒城烟火气，醉美泸州行/推广泸州文案 (2).png','image'),(42,34,'sucai/酒城烟火气，醉美泸州行/推广泸州文案 (1).png','image'),(43,34,'sucai/酒城烟火气，醉美泸州行/推广泸州文案.png','image'),(51,33,'sucai/成渝双城圈 C 位出道！泸州：左手诗意，右手机遇/推广泸州文案 (3).png','image'),(52,33,'sucai/成渝双城圈 C 位出道！泸州：左手诗意，右手机遇/推广泸州文案 (2).png','image'),(53,33,'sucai/成渝双城圈 C 位出道！泸州：左手诗意，右手机遇/推广泸州文案 (1).png','image'),(54,33,'sucai/成渝双城圈 C 位出道！泸州：左手诗意，右手机遇/推广泸州文案.png','image'),(70,30,'sucai/退休不躺平！泸州康养旅居天花板/推广泸州文案 (3).png','image'),(71,30,'sucai/退休不躺平！泸州康养旅居天花板/推广泸州文案 (2).png','image'),(72,30,'sucai/退休不躺平！泸州康养旅居天花板/推广泸州文案 (1).png','image'),(74,31,'sucai/吃货终极指南：在泸州胖 5 斤的 N 种方式/推广泸州文案 (3).png','image'),(75,31,'sucai/吃货终极指南：在泸州胖 5 斤的 N 种方式/推广泸州文案 (2).png','image'),(76,31,'sucai/吃货终极指南：在泸州胖 5 斤的 N 种方式/推广泸州文案 (1).png','image'),(77,31,'sucai/吃货终极指南：在泸州胖 5 斤的 N 种方式/推广泸州文案.png','image');
/*!40000 ALTER TABLE `topic_files` ENABLE KEYS */;

#
# Structure for table "topics"
#

DROP TABLE IF EXISTS `topics`;
CREATE TABLE `topics` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `topic_name` varchar(255) NOT NULL,
  `description` text,
  `expected_date` date DEFAULT NULL,
  `user_id` int(6) unsigned DEFAULT NULL,
  `is_completed` tinyint(1) DEFAULT '0',
  `video_path` varchar(255) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `category` varchar(50) NOT NULL DEFAULT '默认分类',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;

#
# Data for table "topics"
#

/*!40000 ALTER TABLE `topics` DISABLE KEYS */;
INSERT INTO `topics` VALUES (30,'退休不躺平！泸州康养旅居天花板','年均 20℃的天然氧吧，三甲医院全覆盖，社区食堂 15 元管饱！乘船游沱江，在忠山公园打太极，入住温泉民宿泡私汤 —— 银发族的理想养老地，正在被 \' 泸漂 \' 占领！# 来了就不想走',NULL,1,0,NULL,NULL,'2025-03-18 20:17:41','分类一'),(31,'吃货终极指南：在泸州胖 5 斤的 N 种方式','古蔺麻辣鸡配郎酒，护国陈醋拌豌豆黄，再来碗 \' 荤豆花 \' 暖心！深夜必冲 \' 珠子街 \' 夜市，烧烤 + 冰粉 + 凉糕的王炸组合 —— 体重秤警告：泸州美食太上头！# 碳水天堂',NULL,1,0,NULL,NULL,'2025-03-18 20:18:02','分类一'),(33,'成渝双城圈 C 位出道！泸州：左手诗意，右手机遇','当长江遇见美酒河，当古镇邂逅新能源！这里有西部最大的白酒产业集群，有全国首个跨境电商综试区，更有 \' 江阳夜市 \' 的人间烟火 —— 投资泸州，就是投资未来！# 一带一路新枢纽\"',NULL,1,0,NULL,NULL,'2025-03-18 20:18:44','分类一'),(34,'酒城烟火气，醉美泸州行','长江首城，千年窖池飘香！品泸州老窖的醇厚，尝麻辣火锅的滚烫，游黄荆老林的秘境 —— 来泸州，解锁 \' 诗酒趁年华 \' 的神仙日子！# 中国酒城 #川渝必打卡',NULL,1,0,NULL,NULL,'2025-03-18 20:18:59','分类一');
/*!40000 ALTER TABLE `topics` ENABLE KEYS */;

#
# Structure for table "users"
#

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

#
# Data for table "users"
#

/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'xiao','$2y$10$/uIlyS3iQLjMx33XlPkDqOkP/rqMUPbDbWW5SkuzuxhurIbQGvWLu');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
