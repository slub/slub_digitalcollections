<?php
namespace Slub\DigitalCollections\ViewHelpers;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2020 Alexander Bigga <alexander.bigga@slub-dresden.de>
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
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * ViewHelper to parse the ALTO fulltext
 *
 * # Example: Basic example
 * <code>
 * <dc:extractFulltext file="http://example.com/alto.xml" />
 * </code>
 * <output>
 * Will output all words out of the ALTO files
 * </output>
 *
 * @package TYPO3
 */
class ExtractFulltextViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
    use CompileWithRenderStatic;

    /**
     * Initialize arguments.
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('file', 'string', 'URI of the ALTO fulltext file', true);
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
        $file= $arguments['file'];

        $altoXml = simplexml_load_file($file);

        $altoXml->registerXPathNamespace('alto', 'http://www.loc.gov/standards/alto/ns-v2#');
        // Get all (presumed) words of the text.
        $words = $altoXml->xpath('./alto:Layout/alto:Page/alto:PrintSpace//alto:TextBlock/alto:TextLine/alto:String/@CONTENT');
        if (!empty($words)) {
            $rawText = implode(' ', $words);
        }
        return $rawText;

      }

}