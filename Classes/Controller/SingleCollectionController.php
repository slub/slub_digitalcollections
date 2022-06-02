<?php
namespace Slub\SlubDigitalcollections\Controller;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2020 Alexander Bigga <typo3@slub-dresden.de>
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

use Kitodo\Dlf\Common\Document;
use Kitodo\Dlf\Domain\Repository\DocumentRepository;
use Kitodo\Dlf\Domain\Repository\StructureRepository;
use Kitodo\Dlf\Domain\Repository\CollectionRepository;
use Kitodo\Dlf\Domain\Repository\MetadataRepository;

class SingleCollectionController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * DocumentRepository
     *
     * @var \Kitodo\Dlf\Domain\Repository\DocumentRepository
     */
    protected $documentRepository;

    /**
     * StructureRepository
     *
     * @var \Kitodo\Dlf\Domain\Repository\StructureRepository
     */
    protected $structureRepository;

    /**
     * CollectionRepository
     *
     * @var \Kitodo\Dlf\Domain\Repository\CollectionRepository
     */
    protected $collectionRepository;

    /**
     * MetadataRepository
     *
     * @var \Kitodo\Dlf\Domain\Repository\MetadataRepository
     */
    protected $metadataRepository;

    /**
     * @param \Kitodo\Dlf\Domain\Repository\DocumentRepository $documentRepository
     */
    public function injectDocumentRepository(DocumentRepository $documentRepository)
    {
        $this->documentRepository = $documentRepository;
    }

	/**
     * @param \Kitodo\Dlf\Domain\Repository\StructureRepository $structureRepository
     */
    public function injectStructureRepository(StructureRepository $structureRepository)
    {
        $this->structureRepository = $structureRepository;
    }

	/**
     * @param \Kitodo\Dlf\Domain\Repository\CollectionRepository $collectionRepository
     */
    public function injectCollectionRepository(CollectionRepository $collectionRepository)
    {
        $this->collectionRepository = $collectionRepository;
    }

	/**
     * @param \Kitodo\Dlf\Domain\Repository\MetadataRepository $metadataRepository
     */
    public function injectMetadataRepository(MetadataRepository $metadataRepository)
    {
        $this->metadataRepository = $metadataRepository;
    }

    /**
     * initializeAction
     *
     * @return
     */
    protected function initializeAction()
    {
    }

    /**
     * action search
     *
     * @return void
     */
    public function searchAction()
    {

        // if search was triggered, get search parameters from POST variables
        $searchParams = $this->getParametersSafely('searchParameter');

        // output is done by show action
        $this->redirect('show', null, null, ['searchParameter' => $searchParams]);
    }

    /**
     * action show
     *
     * @return void
     */
    public function showAction()
    {
        // if search was triggered, get search parameters from POST variables
        $searchParams = $this->getParametersSafely('searchParameter');

        // set default sorting
        if (!isset($searchParams['orderBy'])) {
            $searchParams['orderBy'] = 'title_usi';
            $searchParams['order'] = 'asc';
        }

        $collections = $this->collectionRepository->findAllByUids(GeneralUtility::intExplode(',', $this->settings['collections'], true));

        // get all metadata records to be shown in results
        $listedMetadata = $this->metadataRepository->findByIsListed(true);

        // find all documents from Solr
        $solrResults = $this->documentRepository->findSolrByCollection($collections, $this->settings, $searchParams, $listedMetadata);

        // get all sortable Metadata from Kitodo.Presentation
        $metadata = $this->metadataRepository->findByIsSortable(true);

        // Pagination of Results
        // pass the currentPage to the fluid template to calculate current index of search result
        $widgetPage = $this->getParametersSafely('@widget_0');
        if (empty($widgetPage)) {
            $widgetPage = ['currentPage' => 1];
        }

        $this->view->assign('documents', $solrResults['documents']);
        $this->view->assign('metadata', $metadata);
        $this->view->assign('widgetPage', $widgetPage);
        $this->view->assign('lastSearch', $searchParams);
        $this->view->assign('rawResults', $solrResults['solrResults']);

    }

    /**
     * Safely gets Parameters from request
     * if they exist
     *
     * @param string $parameterName
     *
     * @return null|string
     */
    protected function getParametersSafely($parameterName)
    {
        if ($this->request->hasArgument($parameterName)) {
            return $this->request->getArgument($parameterName);
        }
        return null;
    }

}
