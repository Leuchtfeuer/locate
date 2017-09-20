#
# Table structure for table 'tx_locate_ip2country'
#
CREATE TABLE tx_locate_ip2country (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	ipfrom int(11) unsigned DEFAULT '0' NOT NULL,
	ipto int(11) unsigned DEFAULT '0' NOT NULL,
	iso2 char(2) DEFAULT '' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);