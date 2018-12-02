#
# Table structure for table 'static_ip2country'
#
CREATE TABLE static_ip2country (
  uid int(10) unsigned NOT NULL auto_increment,
  pid int(10) unsigned NOT NULL DEFAULT '0',
  ipfrom int(10) unsigned NOT NULL DEFAULT '0',
  ipto int(10) unsigned NOT NULL DEFAULT '0',
  iso2 varchar(2)  NOT NULL DEFAULT '',
  PRIMARY KEY (uid),
  KEY parent (pid)
);
