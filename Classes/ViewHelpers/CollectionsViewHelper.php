<?php
namespace Slub\SlubDigitalcollections\ViewHelpers;
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
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ViewHelper to get kitodo collections froms olr
 *
 * @package TYPO3
 */
class CollectionsViewHelper extends AbstractViewHelper
{
    /**
     * Initialize arguments.
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('kitodoId', 'integer', 'Id of Kitodo document', true);
        $this->registerArgument('solrHost', 'string', 'Id of Kitodo document', false, "http://example.com:8983/solr/dlfCore0/");
        $this->registerArgument('solrTimeout', 'integer', 'Timeout to Solr server', false, 5);
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
        $kitodoId = $arguments['kitodoId'];
        $solrHost = rtrim($arguments['solrHost'], "/");
        $solrTimeout = $arguments['solrTimeout'];
        if (\TYPO3\CMS\Core\Utility\MathUtility::canBeInterpretedAsInteger($kitodoId)) {
            // calculate cache identifier
            $cacheIdentifier = $kitodoId;
            $cache = GeneralUtility::makeInstance(CacheManager::class)->getCache('slub_digitalcollections_matomo_collections');

            if (($result = $cache->get($cacheIdentifier)) === FALSE) {
                /** @var RequestFactory $requestFactory */
                $requestFactory = GeneralUtility::makeInstance(RequestFactory::class);
                $configuration = [
                    'timeout' => $solrTimeout,
                    'headers' => [
                        'Content-type' => 'application/x-www-form-urlencoded',
                        'Accept' => 'application/json'
                    ],
                    'form_params' => [
                        'q' => 'uid:' . $kitodoId . ' AND toplevel:true',
                        'rows' => '1',
                        'fl' => 'collection',
                        'wt' => 'json',
                        'json.nl' => 'flat',
                        'omitHeader' => 'true'
                    ]
                ];

                $response = $requestFactory->request($solrHost . '/select?', 'POST', $configuration);
                $content  = $response->getBody()->getContents();
                $result = json_decode($content, true);

                // Save value in cache
                if ($result) {
                    $cache->set($cacheIdentifier, $result);
                }
            }
        } else {
          return FALSE;
        }
        return $result;
    }
}
