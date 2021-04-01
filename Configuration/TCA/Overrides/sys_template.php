<?php
defined('TYPO3_MODE') or die();

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
