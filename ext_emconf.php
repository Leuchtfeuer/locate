<?php

########################################################################
# Extension Manager/Repository config file for ext: "locate"
#
# Auto generated 24-06-2013 10:49
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
    'title' => 'Locate',
    'description' => 'The users country and prefered language and other facts will be detected. Depending on configurable rules the user can be redirected to other languages or pages.  New functionality can be added easily.',
    'category' => 'Bitmotion Extensions',
    'author' => 'Rene Fritz, Florian Wessels',
    'author_email' => 'typo3-ext(at)bitmotion.de',
    'shy' => '',
    'dependencies' => 'static_info_tables',
    'conflicts' => '',
    'priority' => '',
    'module' => 'mod1',
    'state' => 'stable',
    'internal' => '',
    'uploadfolder' => 0,
    'createDirs' => '',
    'modify_tables' => '',
    'clearCacheOnLoad' => 0,
    'lockType' => '',
    'author_company' => '<a href="http://www.bitmotion.de" target="_blank">Bitmotion</a>',
    'version' => '1.2.0',
    'constraints' => array(
        'depends' => array(
            'typo3' => '6.2.0-7.6.99'
			'static_info_tables' => '',
			'php' => '5.3',
		),
		'conflicts' => array(),
		'suggests' => array(),
	),
);

?>