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
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
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
    private ?DocumentRepository $documentRepository = null;

    /**
     * Logger instance
     *
     * @var Logger
     */
    private Logger $logger;

    /**
     * Initialize arguments.
     * @see AbstractViewHelper::initializeArguments()
     *
     * @access public
     *
     * @return void
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('xpath', 'string', 'Xpath Expression', true);
        $this->registerArgument('htmlspecialchars', 'boolean', 'Use htmlspecialchars() on the found result.', false, true);
        $this->registerArgument('returnArray', 'boolean', 'Return results in an array instead of string.', false, false);
    }

    /**
     * Render the supplied DateTime object as a formatted date.
     * @see AbstractViewHelper::render()
     *
     * @access public
     *
     * @return array|string|void
     * @phpstan-return array<string>|string|void
     */
    public function render() {
        $xpath = $this->arguments['xpath'];
        $htmlSpecialChars = $this->arguments['htmlspecialchars'];
        $returnArray = $this->arguments['returnArray'];

        $this->logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(static::class);

        $parameters =  $this->getParameters();

        $document =  $this->getDocumentRepository()->findOneByParameters($parameters);
        $currentDocument = $document?->getCurrentDocument();

        if (!($currentDocument instanceof MetsDocument)) {
            return;
        }

        $mets = $currentDocument->getMets();
        $mets->registerXPathNamespace('mets', 'http://www.loc.gov/METS/');
        $mets->registerXPathNamespace('mods', 'http://www.loc.gov/mods/v3');
        $mets->registerXPathNamespace('dv', 'http://dfg-viewer.de/');
        $mets->registerXPathNamespace('slub', 'http://slub-dresden.de/');

        try {
            $result = self::safeXpath($mets, $xpath);
        } catch (\Exception $e) {
            $result = false;
            $this->logger->info('Problem occurred while evaluating XPath (' . $xpath . ') in METS (' . $document->getLocation() . '): ' . $e->getMessage());
        }

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
     * @access private
     *
     * @return array<string,mixed>
     */
    private function getParameters(): array
    {
        $parameters = [];
        $parametersSet = [];
        $parametersDlf = [];

        // @phpstan-ignore-next-line
        if (method_exists(GeneralUtility::class, '_GPmerged')) {
            $parametersSet = GeneralUtility::_GPmerged('set');
            $parametersDlf = GeneralUtility::_GPmerged('tx_dlf');
        } else {
            $request = $GLOBALS['TYPO3_REQUEST'] ?? null;
            if ($request !== null) {
                try {
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
                } catch (\Throwable $e) {
                    $this->logger->info('Error occurred while fetching parameters: ' . $e->getMessage());
                }
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
     * @access private
     *
     * @return DocumentRepository
     */
    private function getDocumentRepository(): DocumentRepository
    {
        if ( $this->documentRepository === null) {
            $this->documentRepository = GeneralUtility::makeInstance(DocumentRepository::class);
        }

        return  $this->documentRepository;
    }

    /**
     * Execute XPath expression and throw exception on errors.
     *
     * @access private
     *
     * @param \SimpleXMLElement $xml
     * @param string $xpath
     *
     * @return array
     *
     * @throws \RuntimeException
     */
    private function safeXpath(\SimpleXMLElement $xml, string $xpath): array
    {
        $previousLibxml = libxml_use_internal_errors(true);
        libxml_clear_errors();

        $result = $xml->xpath($xpath);

        if ($result === false) {
            $errors = libxml_get_errors();
            $message = !empty($errors) ? trim($errors[0]->message) : 'Invalid XPath expression';
            libxml_clear_errors();
            libxml_use_internal_errors($previousLibxml);

            throw new \RuntimeException($message);
        }

        libxml_use_internal_errors($previousLibxml);
        return $result;
    }
}
