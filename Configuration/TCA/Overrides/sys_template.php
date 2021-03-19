<?php
defined('TYPO3_MODE') or die();

// Add default Typoscript
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'slub_digitalcollections',
    'Configuration/TypoScript',
    'SLUB Digital Collections'
);
