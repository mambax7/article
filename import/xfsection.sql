# 

# 2004/03/27 K.OHWADA
# add excle powerpoint

# 2003/10/11 K.OHWADA
# rename module and table wfs to xfs
#   change this file name wfsection.sql to xfsection.sql
# view and edit for pure html file
#   add field nocr, enaamp

# phpMyAdmin MySQL-Dump
# version 2.2.6
# http://phpwizard.net/phpMyAdmin/
# http://www.phpmyadmin.net/ (download page)
#
# Host: localhost
# Generation Time: Jun 18, 2003 at 11:43 PM
# Server version: 3.23.51
# PHP Version: 4.3.0
# WF-Section Version 1 Stable
# Database : `WF-Sections`
# --------------------------------------------------------

#
# Table structure for table `xfs_article`
#

# add field nobr, enaamp
CREATE TABLE xfs_article (
  articleid  INT(8) UNSIGNED  NOT NULL AUTO_INCREMENT,
  categoryid INT(8) UNSIGNED  NOT NULL DEFAULT '0',
  uid        INT(5)           NOT NULL DEFAULT '0',
  title      VARCHAR(255)              DEFAULT NULL,
  maintext   TEXT             NOT NULL,
  counter    INT(8) UNSIGNED  NOT NULL DEFAULT '0',
  created    INT(10)          NOT NULL DEFAULT '0',
  changed    INT(10)          NOT NULL DEFAULT '0',
  nohtml     TINYINT(1)       NOT NULL DEFAULT '0',
  nosmiley   TINYINT(1)       NOT NULL DEFAULT '0',
  summary    TEXT             NOT NULL,
  url        VARCHAR(255)     NOT NULL DEFAULT '',
  page       INT(8) UNSIGNED  NOT NULL DEFAULT '1',
  groupid    VARCHAR(255)              DEFAULT NULL,
  submit     INT(10)          NOT NULL DEFAULT '1',
  published  INT(10)          NOT NULL DEFAULT '0',
  expired    INT(10)          NOT NULL DEFAULT '0',
  notifypub  TINYINT(1)       NOT NULL DEFAULT '0',
  type       VARCHAR(5)       NOT NULL DEFAULT '',
  ishtml     INT(10)          NOT NULL DEFAULT '0',
  htmlpage   VARCHAR(255)     NOT NULL DEFAULT '',
  rating     DOUBLE(6, 4)     NOT NULL DEFAULT '0.0000',
  votes      INT(11) UNSIGNED NOT NULL DEFAULT '0',
  hits       INT(11) UNSIGNED NOT NULL DEFAULT '0',
  urlname    VARCHAR(255)     NOT NULL DEFAULT '',
  offline    INT(10)          NOT NULL DEFAULT '0',
  weight     INT(4)           NOT NULL DEFAULT '1',
  noshowart  INT(10)          NOT NULL DEFAULT '0',
  nobr       TINYINT(1)       NOT NULL DEFAULT '0',
  enaamp     TINYINT(1)       NOT NULL DEFAULT '0',
  PRIMARY KEY (articleid),
  KEY categoryid (categoryid),
  KEY uid (uid),
  KEY changed (changed)
)
  ENGINE = MyISAM;

#
# Table structure for table `xfs_broken`
#

CREATE TABLE xfs_broken (
  reportid INT(5)           NOT NULL AUTO_INCREMENT,
  lid      INT(11) UNSIGNED NOT NULL DEFAULT '0',
  sender   INT(11) UNSIGNED NOT NULL DEFAULT '0',
  ip       VARCHAR(20)      NOT NULL DEFAULT '',
  PRIMARY KEY (reportid),
  KEY lid (lid),
  KEY sender (sender),
  KEY ip (ip)
)
  ENGINE = MyISAM;

#
# Table structure for table `xfs_category`
#

CREATE TABLE xfs_category (
  id             INT(4) UNSIGNED NOT NULL AUTO_INCREMENT,
  pid            INT(4) UNSIGNED NOT NULL DEFAULT '0',
  imgurl         VARCHAR(20)     NOT NULL DEFAULT '',
  displayimg     INT(10)         NOT NULL DEFAULT '0',
  title          VARCHAR(50)     NOT NULL DEFAULT '',
  description    TEXT            NOT NULL,
  catdescription TEXT            NOT NULL,
  groupid        VARCHAR(255)             DEFAULT NULL,
  catfooter      TEXT            NOT NULL,
  orders         INT(4)          NOT NULL DEFAULT '1',
  editaccess     VARCHAR(255)    NOT NULL DEFAULT '1 2 3',
  PRIMARY KEY (id),
  KEY pid (pid)
)
  ENGINE = MyISAM;

#
# Table structure for table `xfs_config`
#

# chnage indexheading
CREATE TABLE xfs_config (
  articlesapage INT(10)      NOT NULL DEFAULT '10',
  filesbasepath VARCHAR(255) NOT NULL DEFAULT '',
  graphicspath  VARCHAR(255) NOT NULL DEFAULT '',
  sgraphicspath VARCHAR(255) NOT NULL DEFAULT '',
  smiliepath    VARCHAR(255) NOT NULL DEFAULT '',
  htmlpath      VARCHAR(255) NOT NULL DEFAULT '',
  toppagetype   VARCHAR(255) NOT NULL DEFAULT '',
  wysiwygeditor INT(10)      NOT NULL DEFAULT '1',
  showcatpic    INT(10)      NOT NULL DEFAULT '0',
  comments      INT(10)      NOT NULL DEFAULT '0',
  blockscroll   INT(10)      NOT NULL DEFAULT '0',
  blockheight   INT(10)      NOT NULL DEFAULT '50',
  blockamount   INT(10)      NOT NULL DEFAULT '5',
  blockdelay    INT(10)      NOT NULL DEFAULT '1',
  submenus      INT(10)      NOT NULL DEFAULT '0',
  webmstonly    INT(10)      NOT NULL DEFAULT '0',
  lastart       INT(10)      NOT NULL DEFAULT '10',
  numuploads    INT(10)      NOT NULL DEFAULT '5',
  timestamp     TEXT         NOT NULL,
  autoapprove   INT(10)      NOT NULL DEFAULT '0',
  showauthor    INT(10)      NOT NULL DEFAULT '1',
  showcomments  INT(10)      NOT NULL DEFAULT '1',
  showfile      INT(10)      NOT NULL DEFAULT '1',
  showrated     INT(10)      NOT NULL DEFAULT '1',
  showvotes     INT(10)      NOT NULL DEFAULT '1',
  showupdated   INT(10)      NOT NULL DEFAULT '1',
  showhits      INT(10)      NOT NULL DEFAULT '1',
  showMarticles INT(10)      NOT NULL DEFAULT '1',
  showMupdated  INT(10)      NOT NULL DEFAULT '1',
  anonpost      INT(10)      NOT NULL DEFAULT '0',
  notifysubmit  INT(10)      NOT NULL DEFAULT '0',
  shortart      INT(10)      NOT NULL DEFAULT '0',
  shortcat      INT(10)      NOT NULL DEFAULT '0',
  novote        INT(10)      NOT NULL DEFAULT '1',
  realname      INT(10)      NOT NULL DEFAULT '0',
  indexheading  VARCHAR(255) NOT NULL DEFAULT 'XF-Sections',
  indexheader   TEXT         NOT NULL,
  indexfooter   TEXT         NOT NULL,
  groupid       VARCHAR(255) NOT NULL DEFAULT '1 2 3',
  indeximage    VARCHAR(255) NOT NULL DEFAULT '',
  noicons       INT(10)      NOT NULL DEFAULT '1',
  summary       VARCHAR(255) NOT NULL DEFAULT '1',
  aidxpathtype  TINYINT(4)   NOT NULL DEFAULT '1',
  aidxorder     VARCHAR(32)  NOT NULL DEFAULT 'weight',
  selmimetype   TEXT         NOT NULL,
  wfsmode       VARCHAR(50)  NOT NULL DEFAULT '666',
  imgwidth      INT(10)      NOT NULL DEFAULT '100',
  imgheight     INT(10)      NOT NULL DEFAULT '100',
  filesize      INT(10)      NOT NULL DEFAULT '2097152',
  picon         INT(10)      NOT NULL DEFAULT '1',
  PRIMARY KEY (articlesapage)
)
  ENGINE = MyISAM;

#
# Dumping data for table `xfs_config`
#

# wfs -> xfs
# add excle
INSERT INTO xfs_config VALUES
  (10, 'modules/xfsection/cache/uploaded', 'modules/xfsection/images/article', 'modules/xfsection/images/category', 'uploads', 'modules/xfsection/html', '1', 1, 0, 0, 0, 100, 1, 25, 0, 0, 10, 1, 'Y/n/j', 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 1,
                                                                                                                                                                                                                                                0,
                                                                                                                                                                                                                                                'XF-Sections',
                                                                                                                                                                                                                                                'This is a test header 2',
                                                                                                                                                                                                                                                'This is a test footer',
                                                                                                                                                                                                                                                '1 2 3',
                                                                                                                                                                                                                                                'logo.gif',
                                                                                                                                                                                                                                                1, '1', 1,
   'weight',
   'doc lha lzh pdf gtar swf tar tex texinfo texi zip Zip au XM snd mid midi kar mpga mp2 mp3 aif aiff aifc m3u ram rm rpm ra wav wax bmp gif ief jpeg jpg jpe png tiff tif ico pbm ppm rgb xbm xpm css html htm asc txt rtx rtf mpeg mpg mpe qt mov mxu avi xls ppt',
   '666', 100, 100, 2097152, 1);
# --------------------------------------------------------

#
# Table structure for table `xfs_files`
#

CREATE TABLE xfs_files (
  fileid       INT(8)          NOT NULL AUTO_INCREMENT,
  filerealname VARCHAR(255)    NOT NULL DEFAULT '',
  filetext     TEXT            NOT NULL,
  articleid    INT(8) UNSIGNED NOT NULL DEFAULT '0',
  fileshowname VARCHAR(255)    NOT NULL DEFAULT '',
  date         INT(10)         NOT NULL DEFAULT '0',
  ext          VARCHAR(64)     NOT NULL DEFAULT '',
  minetype     VARCHAR(64)     NOT NULL DEFAULT '',
  downloadname VARCHAR(255)    NOT NULL DEFAULT '',
  counter      INT(8) UNSIGNED NOT NULL DEFAULT '0',
  filedescript TEXT,
  groupid      VARCHAR(255)    NOT NULL DEFAULT '1 2 3',
  PRIMARY KEY (fileid),
  KEY articleid (articleid)
)
  ENGINE = MyISAM;

#
# Table structure for table `xfs_votedata`
#

CREATE TABLE xfs_votedata (
  ratingid        INT(11) UNSIGNED    NOT NULL AUTO_INCREMENT,
  lid             INT(11) UNSIGNED    NOT NULL DEFAULT '0',
  ratinguser      INT(11)             NOT NULL DEFAULT '0',
  rating          TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
  ratinghostname  VARCHAR(60)         NOT NULL DEFAULT '',
  ratingtimestamp INT(10)             NOT NULL DEFAULT '0',
  PRIMARY KEY (ratingid),
  KEY ratinguser (ratinguser),
  KEY ratinghostname (ratinghostname),
  KEY lid (lid)
)
  ENGINE = MyISAM;

