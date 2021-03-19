<?php
defined('TYPO3_MODE') or die();

call_user_func(function()
{
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerPageTSConfigFile (
        'slub_digitalcollections',
        'Configuration/TsConfig/All.tsconfig',
        'SLUB Digital Collections: Page TS incl. Backend Layouts'
    );
});
