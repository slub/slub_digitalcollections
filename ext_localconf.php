<?php

// plugins
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Slub.SlubDigitalcollections',
    'SingleCollection',
    [
        'SingleCollection' => 'show'
    ],
    // non-cacheable actions
    [
        'SingleCollection' => ''
    ]
);

// cache configurations
if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['slub_digitalcollections_collections'])) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['slub_digitalcollections_collections'] = [];
}
if (!isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['slub_digitalcollections_collections']['backend'])) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['slub_digitalcollections_collections']['backend'] = 'TYPO3\\CMS\\Core\\Cache\\Backend\\SimpleFileBackend';
}
