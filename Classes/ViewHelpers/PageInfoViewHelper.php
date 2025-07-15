<?php
namespace Slub\SlubDigitalcollections\ViewHelpers;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2017 Alexander Bigga <typo3@slub-dresden.de>
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
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ViewHelper to get page info
 *
 * # Example: Basic example
 * <code>
 * <si:pageInfo page="123">
 *	<span>123</span>
 * </code>
 * <output>
 * Will output the page record
 * </output>
 *
 * @package TYPO3
 */
class PageInfoViewHelper extends AbstractViewHelper
{
    /**
     * Initialize arguments.
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('uid', 'integer', 'uid of page', true);
        $this->registerArgument('field', 'string', 'field to fetch from page record', false, 'title');
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
        $pageUid = (int) $arguments['uid'];
        $field = $arguments['field'];
        if ($pageUid === 0) {
            /** @var RenderingContext $renderingContext */
            $request = $renderingContext->getRequest();
            $pageArguments = $request->getAttribute('routing');
            $pageUid = $pageArguments->getPageId();
        }
        $pageRepository = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Domain\Repository\PageRepository::class);
        $page = $pageRepository->getPage($pageUid);

        return array_key_exists($field, $page) ? (string) $page[$field] : '';
    }
}
