<?php
namespace Slub\DigitalCollections\ViewHelpers;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2019 Alexander Bigga <alexander.bigga@slub-dresden.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * ViewHelper to get the provider logo from cache
 *
 * @package TYPO3
 */
class ProviderLogoCachedViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * Initialize arguments.
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('logo', 'string', 'URI of the provider logo', true);
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $logoUrl = $arguments['logo'];
        // is valid uri?
        if (GeneralUtility::isValidUrl($logoUrl)) {
            // calculate cache identifier
            $logoInfo = pathinfo($logoUrl);
            $cacheIdentifier = md5($logoUrl) . '.' . $logoInfo['extension'];
            $logoFile = Environment::getPublicPath() . '/fileadmin/_temp_/' . $cacheIdentifier;
            // if file exists and is not too old - take it
            if (file_exists($LogoFile)) {
                // if not older than one day:
                if ((time() - filemtime($calfile) < 86400)) {
                    return $cacheIdentifier;
                }
            }

            // file not present or too old --> fetch new
            $context = stream_context_create(array(
                'http' => array(
                    'timeout' => 10
                    )
                )
            );
            $logo = file_get_contents($logoUrl, false, $context, 0, 1024*100);
            // Save value in cache
            if ($logo) {
                GeneralUtility::writeFile($logoFile, $logo);
                return $cacheIdentifier;
            }
        }
        return FALSE;
    }
}
