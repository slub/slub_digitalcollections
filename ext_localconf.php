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
// Cache for Collection ViewHelper (Matomo statistics)
if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['slub_digitalcollections_matomo_collections'])) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['slub_digitalcollections_matomo_collections'] = [];
}
if (!isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['slub_digitalcollections_matomo_collections']['backend'])) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['slub_digitalcollections_matomo_collections']['backend'] = 'TYPO3\\CMS\\Core\\Cache\\Backend\\SimpleFileBackend';
}
if (!isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['slub_digitalcollections_matomo_collections']['options']['defaultLifeTime'])) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['slub_digitalcollections_matomo_collections']['options']['defaultLifeTime'] = 87600; // 87600 seconds = 1 day
}

// Cache for Collection Plugin
if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['slub_digitalcollections_collections'])) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['slub_digitalcollections_collections'] = [];
}
if (!isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['slub_digitalcollections_collections']['backend'])) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['slub_digitalcollections_collections']['backend'] = 'TYPO3\\CMS\\Core\\Cache\\Backend\\SimpleFileBackend';
}
if (!isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['slub_digitalcollections_collections']['options']['defaultLifeTime'])) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['slub_digitalcollections_collections']['options']['defaultLifeTime'] = 3600; // 3600 = 1 hour
}
