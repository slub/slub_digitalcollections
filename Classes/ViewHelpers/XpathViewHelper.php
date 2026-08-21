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
use Kitodo\Dlf\Common\MetsDocument;
use Kitodo\Dlf\Domain\Repository\DocumentRepository;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;

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
 */
class XpathViewHelper extends AbstractViewHelper
{
    /**
     * document repository
     *
     * @var DocumentRepository|null
     */
    private static ?DocumentRepository $documentRepository = null;

    /**
     * Initialize arguments.
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('xpath', 'string', 'Xpath Expression', true);
        $this->registerArgument('htmlspecialchars', 'boolean', 'Use htmlspecialchars() on the found result.', false, true);
        $this->registerArgument('returnArray', 'boolean', 'Return results in an array instead of string.', false, false);
    }

    /**
     * Render the supplied DateTime object as a formatted date.
     *
     *
     * @static
     *
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     *
     * @return array|string|void
     * @phpstan-return array<string>|string|void
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $xpath = $arguments['xpath'];
        $htmlSpecialChars = $arguments['htmlspecialchars'];
        $returnArray = $arguments['returnArray'];

        $parameters = self::getParameters();

        $document = self::getDocumentRepository()->findOneByParameters($parameters);
        $currentDocument = $document?->getCurrentDocument();

        if ($document === null || !($currentDocument instanceof MetsDocument)) {
            return;
        }

        $mets = $currentDocument->getMets();
        $mets->registerXPathNamespace('mets', 'http://www.loc.gov/METS/');
        $mets->registerXPathNamespace('mods', 'http://www.loc.gov/mods/v3');
        $mets->registerXPathNamespace('dv', 'http://dfg-viewer.de/');
        $mets->registerXPathNamespace('slub', 'http://slub-dresden.de/');

        $result = $mets->xpath($xpath);

        if ($returnArray) {
            $output = [];
        } else {
            $output = '';
        }

        if (is_array($result)) {
            foreach ($result as $row) {
                if ($returnArray) {
                    $output[] = $htmlSpecialChars ? htmlspecialchars(trim($row)) : trim($row);
                } else {
                    $output .= $htmlSpecialChars ? htmlspecialchars(trim($row)) : trim($row) . ' ';
                }
            }
        }

        if ($returnArray) {
            return $output;
        }
        return trim($output);
    }

    /**
     * Get parameters from the request.
     *
     *
     * @static
     *
     * @return array<string,mixed>
     */
    private static function getParameters(): array
    {
        $parameters = [];

        // @phpstan-ignore-next-line
        if (method_exists(GeneralUtility::class, '_GPmerged')) {
            $parametersSet = GeneralUtility::_GPmerged('set');
            $parametersDlf = GeneralUtility::_GPmerged('tx_dlf');
        } else {
            $request = $GLOBALS['TYPO3_REQUEST'] ?? null;
            if ($request === null) {
                $parametersSet = [];
                $parametersDlf = [];
            } else {
                $queryParams = $request->getQueryParams();
                $parsedBody  = $request->getParsedBody();
                $parametersSet = array_merge(
                    $queryParams['set'] ?? [],
                    $parsedBody['set'] ?? []
                );
                $parametersDlf = array_merge(
                    $queryParams['tx_dlf'] ?? [],
                    $parsedBody['tx_dlf'] ?? []
                );
            }
        }
        if (isset($parametersSet['mets']) && GeneralUtility::isValidUrl($parametersSet['mets'])) {
            $parameters['location'] = $parametersSet['mets'];
        } elseif (isset($parametersDlf['id'])) {
            if (MathUtility::canBeInterpretedAsInteger($parametersDlf['id'])) {
                $parameters['id'] = $parametersDlf['id'];
            } elseif (GeneralUtility::isValidUrl($parametersDlf['id'])) {
                $parameters['location'] = $parametersDlf['id'];
            }
        } elseif (isset($parametersDlf['recordId'])) {
            $parameters['recordId'] = $parametersDlf['recordId'];
        }

        return $parameters;
    }

    /**
     * Initialize the document repository
     *
     *
     * @static
     *
     * @return DocumentRepository
     */
    private static function getDocumentRepository(): DocumentRepository
    {
        if (self::$documentRepository === null) {
            self::$documentRepository = GeneralUtility::makeInstance(DocumentRepository::class);
        }

        return self::$documentRepository;
    }

}
