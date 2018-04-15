# phpMyAdmin MySQL-Dump
# version 2.2.2
# http://phpwizard.net/phpMyAdmin/
# http://phpmyadmin.sourceforge.net/ (download page)
#
# --------------------------------------------------------

#
# Table structure for table `seccont`
#

CREATE TABLE seccont (
  artid   INT(11) NOT NULL AUTO_INCREMENT,
  secid   INT(11) NOT NULL DEFAULT '0',
  title   TEXT    NOT NULL,
  content TEXT    NOT NULL,
  counter INT(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (artid),
  KEY idxseccontsecid (secid),
  KEY idxseccontcounterdesc (counter)
)
  ENGINE = MyISAM;
# --------------------------------------------------------

#
# Table structure for table `sections`
#

CREATE TABLE sections (
  secid   INT(11)     NOT NULL AUTO_INCREMENT,
  secname VARCHAR(40) NOT NULL DEFAULT '',
  image   VARCHAR(50) NOT NULL DEFAULT '',
  PRIMARY KEY (secid),
  KEY idxsectionssecname (secname)
)
  ENGINE = MyISAM;
