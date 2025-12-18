<?php
defined('TYPO3') or die('Access denied.');

// cache configurations
// Cache for Collection ViewHelper (Matomo statistics)
$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['slub_digitalcollections_matomo_collections'] ??= [];

if (!isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['slub_digitalcollections_matomo_collections']['backend'])) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['slub_digitalcollections_matomo_collections']['backend'] = 'TYPO3\\CMS\\Core\\Cache\\Backend\\SimpleFileBackend';
}
if (!isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['slub_digitalcollections_matomo_collections']['options']['defaultLifeTime'])) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['slub_digitalcollections_matomo_collections']['options']['defaultLifeTime'] = 87600; // 87600 seconds = 1 day
}
