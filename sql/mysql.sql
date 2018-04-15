-- phpMyAdmin SQL Dump
-- version 2.6.3-pl1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Jan 24, 2006 at 06:00 AM
-- Server version: 4.1.13
-- PHP Version: 5.1.0RC1
-- 
-- For Article 0.80
-- 
-- 
-- Database: `x230`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for TABLE `art_artcat`
-- 

-- the links for category-article for multiple category purpose
CREATE TABLE `art_artcat` (
  `ac_id`       INT(11) UNSIGNED      NOT NULL AUTO_INCREMENT,
  `art_id`      INT(11) UNSIGNED      NOT NULL DEFAULT '0', # article ID created by `art_article`
  `cat_id`      MEDIUMINT(4) UNSIGNED NOT NULL DEFAULT '0', # category ID created by `art_category`
  `uid`         MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0', # article submitter's UID
  `ac_register` INT(10) UNSIGNED      NOT NULL DEFAULT '0', # time for registered to the category
  `ac_publish`  INT(10) UNSIGNED      NOT NULL DEFAULT '0', # time for published to the category
  `ac_feature`  INT(10) UNSIGNED      NOT NULL DEFAULT '0', # time for marked as feature in the category
  PRIMARY KEY (`ac_id`),
  KEY `cat_id`    (`cat_id`),
  KEY `art_id`    (`art_id`)
)
  ENGINE = MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for TABLE `art_article`
-- 

-- structure for article
CREATE TABLE `art_article` (
  `art_id`           INT(11) UNSIGNED      NOT NULL AUTO_INCREMENT, # article ID
  `cat_id`           MEDIUMINT(4) UNSIGNED NOT NULL DEFAULT '0', # ID of basic category for the article
  `uid`              MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0', # submitter's UID
  `writer_id`        MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0', # author's ID
  `art_source`       VARCHAR(255)          NOT NULL DEFAULT '', # source of the article, could be text or url
  `art_title`        VARCHAR(255)          NOT NULL DEFAULT '', # article title
  `art_keywords`     VARCHAR(255)          NOT NULL DEFAULT '', # article keywords
  `art_summary`      TEXT, # summary text, optional
  `art_image`        VARCHAR(255)          NOT NULL DEFAULT '', # spotlight image for the article: file name, caption
  `art_template`     VARCHAR(255)          NOT NULL DEFAULT '', # specified template, will overwrite module template and category template
  `art_pages`        TEXT, # page info: page No, subject, page body ID
  `art_categories`   VARCHAR(255)          NOT NULL DEFAULT '', # serialized array for article categories
  `art_topics`       VARCHAR(255)          NOT NULL DEFAULT '', # serialized array for article topics
  `art_elinks`       TEXT, # serialized array for article external links: url, title
  `art_forum`        INT(10) UNSIGNED      NOT NULL DEFAULT '0', # forum ID for storing comments on the article
  `art_time_create`  INT(10) UNSIGNED      NOT NULL DEFAULT '0', # time for being created
  `art_time_submit`  INT(10) UNSIGNED      NOT NULL DEFAULT '0', # time for being submitted to its basic category
  `art_time_publish` INT(10) UNSIGNED      NOT NULL DEFAULT '0', # time for being published to its basic category
  `art_counter`      INT(10) UNSIGNED      NOT NULL DEFAULT '0', # view count
  `art_rating`       INT(10) UNSIGNED      NOT NULL DEFAULT '0', # total rating value sum
  `art_rates`        INT(10) UNSIGNED      NOT NULL DEFAULT '0', # total rating count
  `art_comments`     INT(10) UNSIGNED      NOT NULL DEFAULT '0', # total comment count
  `art_trackbacks`   INT(10) UNSIGNED      NOT NULL DEFAULT '0', # total trackback count
  PRIMARY KEY (`art_id`),
  KEY `cat_id`      (`cat_id`),
  KEY `art_title`    (`art_title`),
  KEY `uid`      (`uid`)
)
  ENGINE = MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for TABLE `art_arttop`
-- 

-- the linkship for topic-article
CREATE TABLE `art_arttop` (
  `at_id`   INT(11) UNSIGNED      NOT NULL AUTO_INCREMENT,
  `art_id`  INT(11) UNSIGNED      NOT NULL DEFAULT '0', # article ID
  `top_id`  MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0', # topic ID
  `uid`     MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0', # article submitter's UID
  `at_time` INT(10) UNSIGNED      NOT NULL DEFAULT '0', # time for added to the topic
  PRIMARY KEY (`at_id`),
  KEY `art_id`    (`art_id`, `top_id`)
)
  ENGINE = MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for TABLE `art_category`
-- 

-- structure for category
CREATE TABLE `art_category` (
  `cat_id`           MEDIUMINT(4) UNSIGNED NOT NULL AUTO_INCREMENT, # category ID
  `cat_title`        VARCHAR(255)          NOT NULL DEFAULT '', # category title
  `cat_pid`          MEDIUMINT(4) UNSIGNED NOT NULL DEFAULT '0', # ID of parent category
  `cat_description`  TEXT, # description
  `cat_image`        VARCHAR(255)          NOT NULL DEFAULT '', # category spotlight image: file name
  `cat_order`        MEDIUMINT(4) UNSIGNED NOT NULL DEFAULT '99', # sorting order
  `cat_template`     VARCHAR(255)          NOT NULL DEFAULT 'default', # specified template
  `cat_entry`        INT(11) UNSIGNED      NOT NULL DEFAULT '0', # Entry article ID
  `cat_sponsor`      TEXT, # serialized array for sponsors
  `cat_moderator`    VARCHAR(255)          NOT NULL DEFAULT '', # serialized array for moderator IDs
  `cat_track`        VARCHAR(255)          NOT NULL DEFAULT '', # serialized array for parent category IDs
  `cat_lastarticles` TEXT, # serialized array for last article IDs in the category
  PRIMARY KEY (`cat_id`),
  KEY `cat_order`    (`cat_order`),
  KEY `cat_pid`    (`cat_pid`),
  KEY `cat_title`    (`cat_title`)
)
  ENGINE = MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for TABLE `art_file`
-- 

-- for attachments
CREATE TABLE `art_file` (
  `file_id`   INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, # file ID
  `art_id`    INT(11) UNSIGNED NOT NULL DEFAULT '0', # article ID the file belonging to
  `file_name` VARCHAR(255)     NOT NULL DEFAULT '',
  PRIMARY KEY (`file_id`),
  KEY `art_id`    (`art_id`)
)
  ENGINE = MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for TABLE `art_pingback`
-- 

-- article pingbacks
CREATE TABLE `art_pingback` (
  `pb_id`   INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `art_id`  INT(11) UNSIGNED NOT NULL DEFAULT '0', # article ID
  `pb_time` INT(10) UNSIGNED NOT NULL DEFAULT '0', # pinged time
  `pb_host` VARCHAR(255)     NOT NULL DEFAULT '', # pinged hostname
  `pb_url`  VARCHAR(255)     NOT NULL DEFAULT '', # pinged url
  PRIMARY KEY (`pb_id`),
  KEY `art_id`    (`art_id`)
)
  ENGINE = MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for TABLE `art_rate`
-- 

-- article rating data
CREATE TABLE `art_rate` (
  `rate_id`     INT(11) UNSIGNED      NOT NULL AUTO_INCREMENT,
  `art_id`      INT(11)               NOT NULL DEFAULT '0', # article ID
  `uid`         MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0', # rating submitter's UID
  `rate_ip`     INT(11)               NOT NULL DEFAULT '0', # rating submitter's IP
  `rate_rating` TINYINT(3) UNSIGNED   NOT NULL DEFAULT '0', # rating value
  `rate_time`   INT(10) UNSIGNED      NOT NULL DEFAULT '0', # rating time
  PRIMARY KEY (`rate_id`),
  KEY `art_id`    (`art_id`),
  KEY `uid`    (`uid`)
)
  ENGINE = MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for TABLE `art_spotlight`
-- 

CREATE TABLE `art_spotlight` (
  `sp_id`         INT(11)               NOT NULL AUTO_INCREMENT,
  `art_id`        INT(11)               NOT NULL DEFAULT '0', # article ID
  `uid`           MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0', # UID for the moderator marking the spotlight
  `sp_time`       INT(10)               NOT NULL DEFAULT '0', # marking time
  `sp_image`      VARCHAR(255)          NOT NULL DEFAULT '', # specified spotlight image
  `sp_categories` VARCHAR(255)          NOT NULL DEFAULT '', # allowed categories for articles
  `sp_note`       TEXT, # editor's notes
  PRIMARY KEY (`sp_id`),
  KEY `art_id`    (`art_id`)
)
  ENGINE = MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for TABLE `art_text`
-- 

-- article body text
CREATE TABLE `art_text` (
  `text_id`    INT(11)      NOT NULL AUTO_INCREMENT,
  `art_id`     INT(11)      NOT NULL DEFAULT '0', # article ID
  `text_title` VARCHAR(255) NOT NULL DEFAULT '', # subtitle
  `text_body`  MEDIUMTEXT, # text body
  `dohtml`     TINYINT(1)   NOT NULL DEFAULT '1', # allow HTML
  `dosmiley`   TINYINT(1)   NOT NULL DEFAULT '1', # allow smiley
  `doxcode`    TINYINT(1)   NOT NULL DEFAULT '1', # allow xoopscode
  `doimage`    TINYINT(1)   NOT NULL DEFAULT '1', # allow image
  `dobr`       TINYINT(1)   NOT NULL DEFAULT '0', # allow line break
  PRIMARY KEY (`text_id`),
  KEY `art_id`    (`art_id`)
)
  ENGINE = MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for TABLE `art_topic`
-- 

-- article topic structure
CREATE TABLE `art_topic` (
  `top_id`          MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cat_id`          MEDIUMINT(4) UNSIGNED NOT NULL DEFAULT '0', # category ID
  `top_title`       VARCHAR(255)          NOT NULL DEFAULT '', # topic title
  `top_description` TEXT, # description
  `top_template`    VARCHAR(255)          NOT NULL DEFAULT 'default', # specified template
  `top_order`       MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '1', # sorting order
  `top_time`        INT(10) UNSIGNED      NOT NULL DEFAULT '0', # created time
  `top_expire`      INT(10) UNSIGNED      NOT NULL DEFAULT '0', # expiring time
  `top_sponsor`     TEXT, # serialized array for sponsors
  PRIMARY KEY (`top_id`),
  KEY `cat_id`    (`cat_id`),
  KEY `top_order`  (`top_order`),
  KEY `top_title`  (`top_title`)
)
  ENGINE = MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for TABLE `art_trackback`
-- 

-- trackback structure
CREATE TABLE `art_trackback` (
  `tb_id`        INT(11) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `art_id`       INT(11) UNSIGNED    NOT NULL DEFAULT '0', # article ID
  `tb_status`    TINYINT(1) UNSIGNED NOT NULL DEFAULT '0', # status, approved or not
  `tb_time`      INT(10) UNSIGNED    NOT NULL DEFAULT '0', # tracked time
  `tb_title`     VARCHAR(255)        NOT NULL DEFAULT '', # title
  `tb_url`       VARCHAR(255)        NOT NULL DEFAULT '', # url
  `tb_excerpt`   TEXT, # summary
  `tb_blog_name` VARCHAR(255)        NOT NULL DEFAULT '', # blog or site name
  `tb_ip`        INT(11)             NOT NULL DEFAULT '0', # sender's IP
  PRIMARY KEY (`tb_id`),
  KEY `art_id`    (`art_id`)
)
  ENGINE = MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for TABLE `art_tracked`
-- 

-- tracked urls
CREATE TABLE `art_tracked` (
  `td_id`   INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `art_id`  INT(11) UNSIGNED NOT NULL DEFAULT '0', # article ID
  `td_time` INT(10) UNSIGNED NOT NULL DEFAULT '0', # tracked time
  `td_url`  VARCHAR(255)     NOT NULL DEFAULT '', # tracked URL
  PRIMARY KEY (`td_id`),
  KEY `art_id`    (`art_id`)
)
  ENGINE = MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for TABLE `art_writer`
-- 

-- writer structure
CREATE TABLE `art_writer` (
  `writer_id`      MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `writer_name`    VARCHAR(255)          NOT NULL DEFAULT '', # writer's name
  `writer_avatar`  VARCHAR(64)           NOT NULL DEFAULT '', # writer's avatar
  `writer_profile` TEXT, # profile
  `writer_uid`     MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0', # UID if the writer is a registered member
  `uid`            MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0', # UID of user who adds the writer
  PRIMARY KEY (`writer_id`),
  KEY `writer_name`  (`writer_name`)
)
  ENGINE = MyISAM;
