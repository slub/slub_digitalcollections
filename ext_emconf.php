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
	'version' => '6.0.0',
    'constraints' => array(
        'depends' => [
            'php' => '8.2.0-8.4.99',
            'typo3' => '12.4.0-13.4.99'
        ],
    ),
);
