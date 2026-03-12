<?php
defined('TYPO3') or die('Access denied.');

// Add default TypoScript
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'slub_digitalcollections',
    'Configuration/TypoScript/Kitodo/WorkView',
    'SLUB Digital Collections - Workview'
);

// Add optional Lists and Collection configuration TypoScript
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'slub_digitalcollections',
    'Configuration/TypoScript/Kitodo/Lists',
    'SLUB Digital Collections - Optional Lists and Collection configuration'
);

// Add optional SitePackage TypoScript
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'slub_digitalcollections',
    'Configuration/TypoScript/SitePackage',
    'SLUB Digital Collections - Optional Sitepackage'
);

// Add optional find configuration TypoScript
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'slub_digitalcollections',
    'Configuration/TypoScript/Find',
    'SLUB Digital Collections - Optional Find configuration'
);
