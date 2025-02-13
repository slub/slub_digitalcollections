<?php

$EM_CONF[$_EXTKEY] = array(
	'title' => 'SLUB Digital Collections',
	'description' => 'Templates, Styles and Configuration for the Kitodo.Presentation based Digital Collections by SLUB Dresden',
	'category' => 'plugin',
	'author' => 'Alexander Bigga',
	'author_email' => 'typo3@slub-dresden.de',
	'author_company' => 'SLUB Dresden',
	'shy' => '',
	'priority' => '',
	'module' => '',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => '0',
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'version' => '4.1.0',
	'constraints' => array(
        'depends' => [
            'php' => '8.1.0-8.3.99',
            'typo3' => '11.5.0-12.4.99'
        ],
	),
);
