<?php
defined('TYPO3') or die('Access denied.');

// Add default Typoscript
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'slub_digitalcollections',
    'Configuration/TypoScript',
    'SLUB Digital Collections'
);

// Add optional SitePackage Typoscript
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'slub_digitalcollections',
    'Configuration/TypoScript/SitePackage',
    'Optional Sitepackage for pure usage of SLUB Digital Collections.'
);

// Add optional find configuration Typoscript
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'slub_digitalcollections',
    'Configuration/TypoScript/Plugin/Find',
    'Optional find configuration for SLUB Digital Collections.'
);
