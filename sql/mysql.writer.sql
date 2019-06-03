# 
# Table structure for table `art_writer`
# 

CREATE TABLE `art_writer` (
  `writer_id`      MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `writer_name`    VARCHAR(255)          NOT NULL DEFAULT '',
  `writer_avatar`  VARCHAR(64)           NOT NULL DEFAULT '',
  `writer_profile` TEXT,
  `writer_uid`     MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
  `uid`            MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`writer_id`),
  KEY `writer_name` (`writer_name`)
)
  ENGINE = MyISAM;
