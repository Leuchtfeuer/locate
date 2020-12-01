CREATE TABLE static_ip2country_v4 (
  ip_from int(10) unsigned DEFAULT NULL,
  ip_to int(10) unsigned DEFAULT NULL,
  country_code char(2) NOT NULL DEFAULT '',
  KEY idx_ip_from (ip_from),
  KEY idx_ip_to (ip_to),
  KEY idx_ip_from_to (ip_from,ip_to)
);

CREATE TABLE static_ip2country_v6 (
  ip_from decimal(39,0) unsigned DEFAULT NULL,
  ip_to decimal(39,0) unsigned NOT NULL,
  country_code char(2) NOT NULL DEFAULT '',
  KEY idx_ip_from (ip_from),
  KEY idx_ip_to (ip_to),
  KEY idx_ip_from_to (ip_from,ip_to)
);

CREATE TABLE tx_locate_region_country_mm (
  uid_local int(11) DEFAULT '0' NOT NULL,
  uid_foreign int(11) DEFAULT '0' NOT NULL,
  sorting int(11) DEFAULT '0' NOT NULL,

  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);

CREATE TABLE tx_locate_page_region_mm (
  uid_local int(11) DEFAULT '0' NOT NULL,
  uid_foreign int(11) DEFAULT '0' NOT NULL,
  sorting int(11) DEFAULT '0' NOT NULL,

  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);

CREATE TABLE tx_locate_domain_model_region (
  title varchar(255) DEFAULT '' NOT NULL,
  countries int(11) DEFAULT '0' NOT NULL
);

CREATE TABLE pages (
  tx_locate_regions int DEFAULT 0 NOT NULL,
  tx_locate_invert smallint(2) DEFAULT 0 NOT NULL
);
