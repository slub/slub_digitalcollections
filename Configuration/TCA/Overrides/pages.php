<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

if (TYPO3_MODE === 'BE') {

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerPageTSConfigFile (
        'slub_digitalcollections',
        'Configuration/TSconfig/pageConfig.ts',
        'SLUB Digital Collections: Page TS'
    );

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerPageTSConfigFile(
        'slub_digitalcollections',
        'Configuration/TSconfig/BackendLayouts.ts',
        'SLUB Digital Collections: Backend Layouts'
    );

}
